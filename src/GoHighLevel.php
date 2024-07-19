<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class GoHighLevel
{
	private $api_key;

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	public function getSiteInfo()
	{
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rest.gohighlevel.com/v1/tags/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
				'Authorization: Bearer ' . $this->api_key
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

		$res = json_decode($response);
		if(isset($res->tags))
		{
			return $res->tags;
		}
		else
		{
			return false;
		}
	}

	public function addSubscriber($email, $first_name, $last_name, $phone = null)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->api_key
			),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode(array(
				"email"		=>  $email,
				"firstName" =>  $first_name,
				"lastName"	=>  $last_name,
				"phone"		=>	$phone
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$res = json_decode($response);
		if(isset($res->contact))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
