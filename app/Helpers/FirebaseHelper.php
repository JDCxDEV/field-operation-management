<?php

namespace App\Helpers;

class FirebaseHelper 
{
	public function sendNotification($receivers, $request)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
        $tokens = [];
		foreach($receivers as $receiver) {
			foreach($receiver->device_tokens as $token) {
				array_push($tokens, $token->token);
			}
		}
          
        $serverKey = env('FIREBASE_SERVER_KEY');
        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $request["title"],
                "body" => $request["body"],  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
        // FCM response
	}
}