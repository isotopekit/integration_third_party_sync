<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class SendLane
{
	private $api_key;
	private $api_secret;
	private $domain;

	public function __construct($api_key, $api_secret, $domain)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
		$this->domain = $domain;
	}

	public function getLists()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://".$this->domain.".sendlane.com/api/v1/lists",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => array('api' => $this->api_key, 'hash' => $this->api_secret),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://".$this->domain.".sendlane.com/api/v1/list-subscriber-add",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => array(
				'api'			=>  $this->api_key,
				'hash'			=>	$this->api_secret,
				'email'			=>	$user_email,
				'list_id'		=>	$list_id,
				'first_name'	=>	$user_f_name,
				'last_name'		=>	$user_l_name,
				'phone'			=>	$phone
			)
		));

		$response = curl_exec($curl);

		curl_close($curl);

		if(strpos($response, '<html>'))
		{
			return "connection_error";
		}

		$response = json_decode($response);

		if(isset($response->success))
		{
			return "done";
		}

		if(isset($response->error))
		{
			$key = array_keys(get_object_vars($response->error));
			if($key[0] == 401)
			{
				return "connection_error";
			}
		}
		return $response;
	}
}