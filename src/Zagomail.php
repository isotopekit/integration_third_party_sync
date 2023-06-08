<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Zagomail
{
    private $api_key;
    private $api_url = "https://api.zagomail.com";

	public function __construct($api_key)
	{
        $this->api_key = $api_key;
    }

    public function getLists()
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url."/lists/all-lists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode(array(
                "publicKey"    =>  $this->api_key
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response);

        if(isset($response->data))
        {
            $data = (array) $response->data;
            if(empty($data))
            {
                return "error";
            }
            else
            {
                return $response->data->records;
            }
        }
        else
        {
            return "error";
        }
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url."/lists/subscriber-create?list_uid=".$listID,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "publicKey"     =>  $this->api_key,
                "fname"     =>  $first_name,
                "lname"     =>  $last_name,
                "email"     =>  $email,
				"phone"		=>	$phone
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

        // return $response;

        $response = json_decode($response);

        if(isset($response->data))
        {
            return "done";
        }
        else
        {
		    return "error";
        }
    }
}