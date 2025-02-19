<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Mailerlite
{
    private $api_key;
    private $api_url = "https://connect.mailerlite.com/api";

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
    }

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer '.$this->api_key
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
        $url = "/groups";
        $lists = $this->_get_request($url);
        return $lists;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/subscribers",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$this->api_key
        ),
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode(array(
                "email"         =>  $email,
                "fields"        =>  array(
                    "name"      =>  $first_name,
                    "last_name" =>  $last_name,
                    "phone"		=>	$phone
                ),
                "groups"        =>  [
                    $listID
                ]
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        
        if(isset($response->data->id))
        {
            return "done";
        }

        if(isset($response->message))
        {
            // if($response->error->code == 302)
            // {
                return "connection_error";
            // }
        }

        return json_encode($response);
    }
}