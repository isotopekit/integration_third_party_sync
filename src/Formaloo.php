<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Formaloo
{
    private $api_key;
    private $api_secret;
    private $api_token;
    private $api_url = "https://api.formaloo.net";

	public function __construct($api_key, $api_secret)
	{
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    public function get_auth_key()
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url."/v2.0/oauth2/authorization-token/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.$this->api_secret
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "grant_type"   => "client_credentials"
			))
		));

		$response = curl_exec($curl);

		curl_close($curl);

        $response = json_decode($response);

        if(isset($response->error))
        {
            return "error";
        }
        else
        {
            $this->api_token = $response->authorization_token;
		    return $response->authorization_token;
        }
    }

    public function getLists()
    {
        $this->get_auth_key();
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url."/v1.0/businesses/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: JWT '.$this->api_token,
                'x-api-key: '. $this->api_key,
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

        if(isset($response->data))
        {
            $data = (array) $response->data;
            if(empty($data))
            {
                return "error";
            }
            else
            {
                return $response->data->businesses;
            }
        }
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url."/v1.0/customers/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: JWT '.$this->api_token,
                'x-api-key: '. $this->api_key,
                'active_workspace: '.$listID
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array(
                "first_name"    =>  $first_name,
                "last_name"     =>  $last_name,
                "email"         =>  $email
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