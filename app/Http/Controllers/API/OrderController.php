<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    public function paymentWithMomo(int $orderId, string $orderInfo, int $amount)
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
            Log::info($response->body());
            return $responseData->payUrl;
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function resultPayWithMomo(Request $request)
    {
        try {
            $body = $request->all();
            if ($body['resultCode'] !== 0) {
                //delete order;
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
            $momoPayUrl = $this->paymentWithMomo(time(), 'test order info Cineverse', '123000');
            return $this->sendResponse($momoPayUrl, 'Create order successfully.');
        } catch (\Exception $th) {
            Log::error($th);
            return $this->sendError('An error occurred during create order.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
