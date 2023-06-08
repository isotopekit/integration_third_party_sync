<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class ActiveDemand
{
    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function getLists()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.activedemand.com/v1/contact_lists',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->api_key
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    private function activedemand_field_string($args)
    {
        $fields_string = "";
        $fields = array();
        if (is_array($args)) {
            $fields = array_merge($fields, $args);
        }
        $fields_string = http_build_query($fields);
    
        return $fields_string;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        $fields = [
            "contact[first_name]"    =>  $first_name,
            "contact[last_name]"     =>  $last_name,
            "contact[emails.email_address]"  =>  $email,
			"contact[phones.phone_number]"	=>	$phone
        ];

        $data = $this->activedemand_field_string($fields);        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.activedemand.com/v1/contacts.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'x-api-key: '.$this->api_key
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);

        $contact_id = $data[0]->id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.activedemand.com/v1/contact_lists/add_members/'.$listID.'?contact_ids='.$contact_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'x-api-key: '.$this->api_key
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}