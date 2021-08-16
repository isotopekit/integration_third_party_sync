<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Mailvio
{
    private $api_key;
    private $api_url = "https://api.mailvio.com/v3/";

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
                'api-key: '.$this->api_key
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
        $url = "/contacts/lists";
        $lists = $this->_get_request($url);
        return $lists;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null)
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/contacts",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'api-key: '.$this->api_key
        ),
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode(array(
                "email"         =>  $email,
                "attributes"    =>  array(
                    "FIRSTNAME"     =>  $first_name,
                    "LASTNAME"      =>  $last_name
                ),
                "listIds"   =>  [
                    $listID
                ]
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        
        if(isset($response->id))
        {
            return "done";
        }

        if(isset($response->message))
        {
            if($response->message != "Contact already exist")
            {
                return "connection_error";
            }
        }

        return json_encode($response);
    }
}