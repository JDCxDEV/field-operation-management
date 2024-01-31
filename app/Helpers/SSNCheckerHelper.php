<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SSNCheckerHelper 
{
    private $url;
	private $apiKey;

	public function __construct() 
	{
		$this->url = env('SSN_CHECKER_API_URL');
		$this->apiKey = env('SSN_CHECKER_API_KEY');
	}

	public function checkSSN($body)
	{
		$response = Http::withHeaders([
			'X-API-Key' => $this->apiKey,
			'accept' => 'application/json',
			'content-type' => 'application/json',
		])->post($this->url, [
			'firstName' => $body['firstName'],
			'lastName' => $body['lastName'],
			'phone' => $body['phone'],
			'email' => isset($body['email']) ? $body['email'] : "",
			'dob' => $body['dob'],
			"ssn" => $body['ssn']
		]);

		if(isset($response["result"]) && $response["result"]["ssnMatch"] == true) {
			return [
				"status" => "valid",
			];
		}

		if(isset($response["errors"])) {
			return [
				"status" => "invalid",
				"errors" => $response["errors"]
			];
		}
	}

}