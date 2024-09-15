<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Mail\TicketOrderMail;
use App\Models\Screening;
use App\Models\TicketOrder;
use App\Models\TicketOrderItem;
use App\Models\TicketPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Services\SmsService;

class OrderController extends BaseController
{
    public function paymentWithMomo(string $orderId, string $orderInfo, int $amount)
    {
        try {
            $partnerCode = env('MOMO_PARTNER_CODE');
            $accessKey = env('MOMO_ACCESS_KEY');
            $secretKey = env('MOMO_SECRET_KEY');
            $requestId = $partnerCode . time();
            $redirectURL = env('MOMO_REDIRECT_URL');
            $ipnURL = env('MOMO_IPN_URL');
            $requestType = 'captureWallet';
            $extraData = '';
            $rawSignature = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnURL . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectURL . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawSignature, $secretKey);
            $autoCapture = true;

            $data = array(
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                'storeId' => 'MomoTestStore',
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'requestType' => $requestType,
                'ipnUrl' => $ipnURL,
                'lang' => 'vi',
                'redirectUrl' => $redirectURL,
                'autoCapture' => $autoCapture,
                'extraData' => $extraData,
                'signature' => $signature
            );

            $momoCreatePaymentURL = env('MOMO_CREATE_PAYMENT_URL');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Content-Length' => strlen(json_encode($data))
            ])->post($momoCreatePaymentURL, $data);

            $responseData = json_decode($response->body());
            return $responseData->payUrl;
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function resultPayWithMomo(Request $request)
    {
        try {
            $body = $request->all();

            $exploreOrderId = explode('cnv_order_', $body['orderId']);
            if ($body['resultCode'] !== 0 && count($exploreOrderId) > 0) {
                $orderId = $exploreOrderId[1];

                //Test send sms
                $order = TicketOrder::with('screening.auditorium.cinemaBranch', 'ticketOrderItems.seatingArrangement')->find($orderId);


                //end test send sms
                TicketOrderItem::where('ticket_order_id', '=', $orderId)->delete();
                TicketOrder::find($orderId)->delete();
                return $this->sendResponse('', 'Payment with momo was failed.');
            }
            if (count($exploreOrderId) > 0) {
                $orderId = $exploreOrderId[1];

                $order = TicketOrder::with('screening.auditorium.cinemaBranch', 'ticketOrderItems.seatingArrangement')->find($orderId);

                Mail::to($order->email)->send(new TicketOrderMail(
                    $order->screening->film->title,
                    Carbon::parse($order->screening->screening_time)
                        ->format('H:i d/m/Y'),
                    $order->ticketOrderItems->map(function ($item) {
                        return $item->seatingArrangement->label;
                    })->implode(', '),
                    $order->screening->auditorium->name,
                    $order->screening->auditorium->cinemaBranch->name
                ));

                $smsContent = 'Vé xem phim: ' . $order->screening->film->title .  '. Xuất chiếu: ' . Carbon::parse($order->screening->screening_time)
                    ->format('H:i d/m/Y') . '. Số ghế: '
                    . $order->ticketOrderItems->map(function ($item) {
                        return $item->seatingArrangement->label;
                    })->implode(', ') . '. Phòng chiếu ' . $order->screening->auditorium->name . ', rạp ' . $order->screening->auditorium->cinemaBranch->name;

                $smsService = new SmsService();
                $smsService->sendSms($order->phone, $smsContent);
            }

            return $this->sendResponse('', 'Payment with Momo successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during check payment status with Momo.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'screening_id' => 'required|numeric|exists:screenings,id',
                'seatings' => 'required|array|min:1',
                'seatings.*.id' => 'required|numeric|exists:seating_arrangements,id',
                'seatings.*.label' => 'required|string',
                'seatings.*.seat_type' => 'required|string',
                'user_info' => 'nullable|array',
                'user_info.email' => 'nullable|email',
                'user_info.phone' => 'nullable|string',
                'user_info.name' => 'nullable|string',
            ]);
            if ($validated->fails()) {
                return $this->sendError($validated->errors());
            }

            $screening = Screening::with('film', 'auditorium.cinemaBranch')->find($body['screening_id']);
            // if (Carbon::now()->gte(Carbon::parse($screening->screening_time))) {
            //     return $this->sendError('Time to buy tickets has expired.', [], Response::HTTP_BAD_REQUEST);
            // }

            $ticketItems = TicketOrderItem::whereHas('ticketOrder', function ($query) use ($body) {
                $query->where('screening_id', '=', $body['screening_id']);
            })->get();

            $orderedSeat = false;

            $seatingsCollect = collect($body['seatings']);
            foreach ($ticketItems as $ticketItem) {
                $foundOrderedSeat = $seatingsCollect->first(function ($seat) use ($ticketItem) {
                    return $seat['id'] === $ticketItem['seating_arrangement_id'];
                });
                if ($foundOrderedSeat) {
                    $orderedSeat = true;
                    break;
                }
            }

            if ($orderedSeat) {
                return $this->sendError('Seats have been ordered by someone.', [], Response::HTTP_BAD_REQUEST);
            }

            $ticketPrices = TicketPrice::where('screening_id', '=', $body['screening_id'])->get();
            $total = 0;
            foreach ($body['seatings'] as $seat) {
                $ticketPrice = $ticketPrices->first(function ($item) use ($seat) {
                    return $item->seat_type === $seat['seat_type'];
                });
                if ($ticketPrice) {
                    $total += $ticketPrice['price'];
                }
            }

            $newOrder = null;
            if ($body['user_info']) {
                $newOrder = TicketOrder::create(attributes: [
                    'screening_id' => $body['screening_id'],
                    'total' => $total,
                    'email' => $body['user_info']['email'],
                    'phone' => $body['user_info']['phone'],
                    'name' => $body['user_info']['name']
                ]);
            }


            foreach ($body['seatings'] as $seat) {
                $ticketPrice = $ticketPrices->first(function ($item) use ($seat) {
                    return $item->seat_type === $seat['seat_type'];
                });
                if ($ticketPrice) {
                    TicketOrderItem::create([
                        'ticket_order_id' => $newOrder->id,
                        'seating_arrangement_id' => $seat['id'],
                        'price' => $ticketPrice['price']
                    ]);
                }
            }

            $orderDescription = 'Vé xem phim: ' . $screening->film->title .  '. Xuất chiếu: ' . Carbon::parse($screening->screening_time)->format('H:i d/m/Y') . '. Số ghế: '
                . array_reduce($body['seatings'], function ($carry, $item) {
                    return strlen($carry) > 0 ? $carry . ',' . $item['label'] : $item['label'];
                }, '') . '. Phòng chiếu ' . $screening->auditorium->name . ', rạp ' . $screening->auditorium->cinemaBranch->name;
            $momoPayUrl = $this->paymentWithMomo('cnv_order_' . $newOrder->id, $orderDescription, $total);
            return $this->sendResponse($momoPayUrl, 'Create order successfully.');
        } catch (\Exception $th) {
            Log::error($th);
            return $this->sendError('An error occurred during create order.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
