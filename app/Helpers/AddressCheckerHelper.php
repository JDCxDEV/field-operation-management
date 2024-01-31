<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class AddressCheckerHelper
{
    private $auth_id;
    private $auth_token;

    public function __construct() 
    {
        $this->auth_id = getenv("SMARTY_AUTH_ID");
        $this->auth_token = getenv("SMARTY_AUTH_TOKEN");
    }

	public function checkAddress($street, $city, $state, $zipcode) 
	{
		try {
			$addressType = "";
			$response = Http::get('https://us-street.api.smartystreets.com/street-address', [
				'street' => $street,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'auth-id' => $this->auth_id,
				'auth-token' => $this->auth_token
			]);
			if(isset($response[0])) {
				$addressType = $response[0]['metadata']['rdi'];
			}
			return $addressType;
		} catch (\Throwable $th) {
			\Log::info($th);
		}
	}
}