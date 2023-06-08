<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Kirim
{
	private $api_key;
	private $username;

	function __construct($username, $api_key)
	{
		$this->username = $username;
		$this->api_key = $api_key;
	}

	public function getLists()
	{
		$api_time = time();
		$generated_token = hash_hmac("sha256", $this->username . "::" . $this->api_key . "::" . $api_time, $this->api_key);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.kirim.email/v3/list',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Auth-Id: ' . $this->username,
				'Auth-Token: ' . $generated_token,
				'Timestamp: ' . $api_time
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	public function addSubscriber($list_id, $first_name, $last_name, $email, $phone = null)
	{
		$api_time = time();
		$generated_token = hash_hmac("sha256", $this->username . "::" . $this->api_key . "::" . $api_time, $this->api_key);

		$full_name = $first_name." ".$last_name;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.kirim.email/v3/subscriber/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'lists='.$list_id.'&full_name='.$full_name.'&email='.$email.'&fields[no_hp]='.$phone,
			CURLOPT_HTTPHEADER => array(
				'Auth-Id: ' . $this->username,
				'Auth-Token: ' . $generated_token,
				'Timestamp: ' . $api_time,
				'Content-Type: application/x-www-form-urlencoded'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}
}
