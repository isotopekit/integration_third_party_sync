<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Sendiio
{
    private $api_key;
    private $api_secret;
    private $api_url = "https://sendiio.com";

	public function __construct($api_key, $api_secret)
	{
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'token: '.$this->api_key,
                'secret: '.$this->api_secret
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

    public function getLists()
    {
        $url = "/api/v1/lists/email";
        $lists = $this->_get_request($url);
        return $lists;
    }

    public function checkAccount()
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/api/v1/auth/check",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            // 'X-MailerLite-ApiKey: '.$this->api_key
        ),
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode(array(
                "token"     =>  $this->api_key,
                "secret"    =>  $this->api_secret
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        // return $response;

        $response = json_decode($response);
        
        if(isset($response->msg))
        {
            if($response->msg == "OK")
            {
                return "done";
            }
        }

        if(isset($response->error))
        {
            if($response->msg == "Error")
            {
                return "connection_error";
            }
        }

        return json_encode($response);
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null)
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/api/v1/lists/subscribe/json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode(array(
                "token: ".$this->api_key,
                "secret: ".$this->api_secret,
                "email_list_id" =>  $listID,
                "email"         =>  $email,
                "name"          =>  $first_name." ".$last_name
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        
        if(isset($response->msg))
        {
            if($response->msg == "You were successfully subscribed" || $response->msg == "You are already subscribed")
            {
                return "done";
            }
        }

        if(isset($response->error))
        {
            if($response->msg == "Validation errors")
            {
                return "connection_error";
            }
        }

        return json_encode($response);
    }
}