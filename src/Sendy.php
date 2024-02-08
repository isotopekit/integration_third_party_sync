<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Sendy
{
	private $api_key;
	private $api_url;

	public function __construct($api_url, $api_key)
	{
        $this->api_key = $api_key;
        $this->api_url = $api_url;
    }

	public function _post_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => 'api_key='.$this->api_key
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if(!$err)
		{
			if($response == "Invalid API key" || $response == "API key not passed" || $response == "No data passed")
			{
				return false;
			}
			else
			{
				return $response;
			}
		}
		else
		{
			return false;
		}
    }

	public function _post_request_data($url, $data)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => 'api_key='.$this->api_key.$data
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if(!$err)
		{
			if($response == "Invalid API key" || $response == "API key not passed" || $response == "No data passed")
			{
				return false;
			}
			else
			{
				return $response;
			}
		}
		else
		{
			return false;
		}
    }

	public function getBrands()
    {
        $url = $this->api_url."/api/brands/get-brands.php";
        return $this->_post_request($url);
    }

	public function getLists($brand_id)
    {
        $url = $this->api_url."/api/lists/get-lists.php";
        return $this->_post_request_data($url, "&brand_id=".$brand_id."&include_hidden=yes");
    }

	public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null)
    {
		$url = $this->api_url."/subscribe";
        return $this->_post_request_data($url, "&list=".$list_id."&name=".$user_f_name." ".$user_l_name."&email=".$user_email);
	}
}