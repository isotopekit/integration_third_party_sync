<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Systeme
{
    private $api_key;
    private $api_url = "https://api.systeme.io/api";

	public function __construct($api_key)
	{
        $this->api_key = $api_key;
    }

    public function getLists()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_URL => $this->api_url."/tags",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"X-API-Key: ".$this->api_key,
				"accept: application/json"
			]
		]);

		$response = curl_exec($curl);
        $err = curl_error($curl);

		curl_close($curl);

        // echo $response;
        // die();

        if ($err)
        {
            return "error";
        }
        else
        {
            $response = json_decode($response);

            if(isset($response->detail))
            {
                return "error";
            }
            return $response->items;
        }
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        $curl = curl_init();

		curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url."/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'fields' => [
                    [
                            'slug' => 'first_name',
                            'value' => $first_name
                    ],
                    [
                            'slug' => 'surname',
                            'value' => $last_name
                    ],
                    [
                            'slug' => 'phone_number',
                            'value' => $phone
                    ]
                ],
                'email' => $email
            ]),
            CURLOPT_HTTPHEADER => [
                "X-API-Key: ".$this->api_key,
                "accept: application/json",
                "content-type: application/json"
            ]
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err)
        {
			return "error";
		}
        else
        {
			$response = json_decode($response);

            if(isset($response->id))
            {
			    $contact_id =  $response->id;
                
                $tcurl = curl_init();

                curl_setopt_array($tcurl, [
                    CURLOPT_URL => $this->api_url."/contacts/".$contact_id."/tags",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode([
                        'tagId' => intval($listID)
                    ]),
                    CURLOPT_HTTPHEADER => [
                        "X-API-Key: ".$this->api_key,
                        "content-type: application/json"
                    ],
                ]);

                $tresponse = curl_exec($tcurl);
                $terr = curl_error($tcurl);

                curl_close($tcurl);

                if ($terr)
                {
                    // echo "cURL Error #:" . $terr;
                    // die();
                    return "error";
                }
                else
                {
                    return "done";
                }
            }
            else
            {
                // return $response->detail;
                return "error";
            }
		}
    }
}