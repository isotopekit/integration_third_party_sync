<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class MailingBoss
{
    private $api_key;

	function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_HTTPHEADER => array(
            //     'Api-Token: '.$this->api_key
            // ),
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

    public function _post_request($url, $email, $list_id, $fname, $lname)
    {

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "email=".$email."&list_uid=".$list_id."&fname=".$fname."&lname=".$lname
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
    }

    public function getLists()
    {
        $res = $this->_get_request("https://member.mailingboss.com/integration/index.php/lists/".$this->api_key);
        return $res;
    }

    public function addSubscriber($list_id, $email, $first_name, $last_name)
    {
        $res = $this->_post_request(
            "https://member.mailingboss.com/integration/index.php/lists/subscribers/create/".$this->api_key, $email, $list_id, $first_name, $last_name);

        return $res;
    }
}