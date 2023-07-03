<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Growmatik
{
	private $api_key;
	private $api_secret;

	public function __construct($api_key, $api_secret)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}

	public function getSiteInfo()
	{
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.growmatik.ai/public/v1/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
				'apiKey: ' . $this->api_key
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET"
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

	public function addSubscriber($email, $first_name, $last_name, $phone = null)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.growmatik.ai/public/v1/contacts",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'apiKey: ' . $this->api_key
			),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode(array(
				"apiSecret"	=>	$this->api_secret,
				"users"   =>  array(
					array(
						"email"			=>  $email,
						"firstName"   	=>  $first_name,
						"lastName"		=>  $last_name,
						"phoneNumber"	=>	$phone
					)
				)
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$res = json_decode($response);
		return $res->success;
	}
}
