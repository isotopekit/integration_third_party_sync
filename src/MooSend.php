<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class MooSend
{
	private $api_key;
    private $api_url = "https://api.moosend.com/v3";

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
    }

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api_url.$url.".json?apikey=".$this->api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
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
        $url = "/lists";
        $lists = $this->_get_request($url);
        return $lists;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/contacts",
        CURLOPT_URL => $this->api_url."/subscribers/".$listID."/subscribe.json?apikey=".$this->api_key,
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
                "Email"     =>  $email,
                "Name"      =>  $first_name." ".$last_name,
				"CustomFields" => [
					"CustomFieldID"	=> "728f6774-37ea-4d81-8607-ce8308136760",
        			"Name"	=> "Phone",
        			"Value"	=> $phone
				]
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);

        if($response->Code == 0)
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