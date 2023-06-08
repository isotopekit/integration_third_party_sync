<?php

// jrhVxwlNdbuQ

namespace IsotopeKit\IntegrationThirdPartySync;

class Pabbly
{
	private $api_key;
	private $api_secret;

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	public function getLists()
	{
        try
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://emails.pabbly.com/api/subscribers-list',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $this->api_key,
                    'Accept: application/json',
                    'Content-Type: application/json'
                ),
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }
        catch(\Exception $ex)
        {
            return $ex;
        }
    }

    public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://emails.pabbly.com/api/subscribers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json'
            ),
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
				"import"    =>  "single",
                "list_id"   =>  $list_id,
                "email"     =>  $user_email,
                "name"      =>  $user_f_name." ".$user_l_name,
				"mobile"	=>	$phone
			))
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}
      