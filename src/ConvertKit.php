<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class ConvertKit
{
    private $api_key;
    private $api_secret;
    private $api_url = "https://api.convertkit.com";

	public function __construct($api_key, $api_secret)
	{
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.convertkit.com".$url.'?api_secret='.$this->api_secret,
            CURLOPT_RETURNTRANSFER => true,
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

    public function _post_request($url, $data)
    {

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.convertkit.com".$url.'?api_secret='.$this->api_secret,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=utf-8'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data)
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
    }

    public function getAccount()
    {
        $url = "/v3/account";
        return $this->_get_request($url);
    }

    public function getForms()
    {
        $url = "/v3/forms";
        return $this->_get_request($url);
    }

    public function addUserToForm($form_id, $user_email = null, $user_f_name = null, $user_l_name = null)
    {
        $data = array(
            "api_key"   =>  $this->api_key,
            "email"     =>  $user_email,
            "first_name" =>  $user_f_name,
            "fileds"    =>  array(
                "last_name"  =>  $user_l_name
            )
        );

        $url = "/v3/forms/".$form_id."/subscribe";
        $res = $this->_post_request($url, $data);
        
        $res = json_decode($res);
        if(isset($res->error))
        {
            if($res->error == "Authorization Failed")
            {
                return "connection_error";
            }
        }
        if(isset($res->subscription))
        {
            if($res->subscription->state == "inactive")
            {
                return "done";
            }
        }
        
        return json_encode($res);

    }

}