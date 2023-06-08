<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Fluentcrm
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
            CURLOPT_URL => $this->domain."/wp-json/fluent-crm/v2/lists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$this->api_key.':'.$this->api_secret
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

		$response = json_decode($response);

        if(isset($response->lists))
        {
            return $response->lists;
        }
        else
        {
            return "error";
        }
    }

    public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->domain."/wp-json/fluent-crm/v2/subscribers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$this->api_key.':'.$this->api_secret
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "first_name"    =>  $user_f_name,
                "last_name"     =>  $user_l_name,
                "email"         =>  $user_email,
				"phone"			=>	$phone,
                "lists"         =>  array([
                    $list_id
                ])
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

        // return $response;

        $response = json_decode($response);

        if(isset($response->contact))
        {
            return "done";
        }
        else
        {
		    return "error";
        }
    }


}