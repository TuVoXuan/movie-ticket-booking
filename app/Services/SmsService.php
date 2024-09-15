<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsService
{
  protected $client;
  protected $from;

  public function __construct()
  {
    // Twilio credentials from your .env file
    $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    $this->from = env('TWILIO_FROM'); // Your Twilio phone number
  }

  public function sendSms($to, $message)
  {
    try {
      $this->client->messages->create($to, [
        'from' => $this->from,
        'body' => $message,
      ]);
      return true;
    } catch (\Exception $e) {
      // Handle exception (e.g., log it)
      Log::error('Twilio SMS error: ' . $e->getMessage());
      return false;
    }
  }
}
