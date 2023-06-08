<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Drip
{
    private $api_key;
    private $api_secret;
    private $api_url = "https://api.getdrip.com";

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
                // 'User-Agent: VK (www.vk.com)',
                'Authorization: Basic '.base64_encode($this->api_secret)
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
        $url = "/v2/".$this->api_key."/campaigns";
        $lists = $this->_get_request($url);
        return $lists;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        // create contact
        $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->api_url."/v2/".$this->api_key."/campaigns/".$listID."/subscribers",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($this->api_secret)
        ),
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode(array(
                "subscribers"   =>  array(
                    array(
                        "email"         =>  $email,
                        "first_name"    =>  $first_name,
                        "last_name"     =>  $last_name,
						"phone"			=>	$phone
                    )
                )
			))
		));

		$response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        
        if(isset($response->subscribers))
        {
            return "done";
        }

        if(isset($response->errors))
        {
            return "connection_error";
        }

        return json_encode($response);
    }
}