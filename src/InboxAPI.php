<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class InboxAPI
{
    private $api_key;
    private $api_secret;
    private $api_url = "https://useapi.useinbox.com/inbox/v1";

    public function __construct($api_key, $api_secret)
	{
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    public function getToken()
    {
        $data = array(
            'EmailAddress'  =>  $this->api_key,
            'Password'      =>  $this->api_secret
		);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://useapi.useinbox.com/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function getLists()
    {
        $fetch_token = $this->getToken();
        $fetch_token = json_decode($fetch_token);
        if($fetch_token->resultStatus == false)
        {
            return false;
        }
        $token = $fetch_token->resultObject->access_token;

        $curl = curl_init();

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://useapi.useinbox.com/inbox/v1/contactlists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '. $token,
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

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null)
    {
        $fetch_token = $this->getToken();
        $fetch_token = json_decode($fetch_token);
        if($fetch_token->resultStatus == false)
        {
            return false;
        }
        
        $token = $fetch_token->resultObject->access_token;

        $data = array(
            'email'  =>  $email
            // 'customFields'  =>  [
            //     array(
            //         'customFieldId' =>  '5d81c3ddfe5cd10001149c95',
            //         'value'         =>  $first_name
            //     ),
            //     array(
            //         'customFieldId' =>  '5d81c3de8f1c4f0001bffec9',
            //         'value'         =>  $last_name
            //     )
            // ]
		);

        // return json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://useapi.useinbox.com/inbox/v1/contactlists/".$listID."/add",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS  =>  json_encode($data),
        // CURLOPT_POSTFIELDS =>"{\n    \"email\": \"john.doe3@example.com\",\n    \"customFields\":[{\n    \t\"customFieldId\":\"5d81c3ddfe5cd10001149c95\",\n    \t\"value\":\"John3\"\n    },\n    {\n    \t\"customFieldId\":\"5d81c3de8f1c4f0001bffec9\",\n    \t\"value\":\"Doe3\"\n    }]\n}",
        // CURLOPT_POSTFIELDS =>"{\n    \"email\": \"john.doe3@example.com\",\n    \"customFields\":[{\n    \t\"customFieldId\":\"5d81c3ddfe5cd10001149c95\",\n    \t\"value\":\"John3\"\n    },\n    {\n    \t\"customFieldId\":\"5d81c3de8f1c4f0001bffec9\",\n    \t\"value\":\"Doe3\"\n    }]\n}",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".$token,
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
}