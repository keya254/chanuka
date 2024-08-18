<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;

class SmsController extends Controller
{
    
    public function atsendSms(Request $request)
    {
        // Validate the request data
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        // Extract phone number and message from the request
        $phone = $request->input('phone');
        $message = $request->input('message');

        // Validate and format the phone number
        $formattedPhone = $this->validatePhone($phone);
        if (!$formattedPhone) {
            return response()->json(['error' => 'Invalid phone number'], 400);
        }

        // Initialize the AfricasTalking SDK
        $username = 'itAirtime'; // Your AfricasTalking username
        $apiKey = 'atsk_f24b925d824618fccef8be447042e03909448508a67f6bf60953f1c0ce07a71dfef5dbca'; // Your AfricasTalking API key
        $AT = new AfricasTalking($username, $apiKey);

        // Get the SMS service
        $sms = $AT->sms();

        // Send the SMS
        $result = $sms->send([
            'to' => $formattedPhone,
            'message' => $message
        ]);

        // Check if the SMS was sent successfully
        if ($result['status'] === 'success') {
            return response()->json(['message' => 'SMS sent successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to send SMS'], 500);
        }
        
    }

    // Helper function to validate and format the phone number
    public function validatePhone($phone)
    {
        if (strpos($phone, '0') === 0 && strlen($phone) == 10) {
            $formattedPhone = "+254" . substr($phone, 1);
        } elseif (strpos($phone, '+') === 0 && strlen($phone) == 13) {
            $formattedPhone = $phone;
        } elseif (strpos($phone, '2') === 0 && strlen($phone) == 12) {
            $formattedPhone = "+" . $phone;
        } else {
            $formattedPhone = false;
        }
        return $formattedPhone;
    }
}