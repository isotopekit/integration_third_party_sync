<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class ActiveCampaign
{
	private $api_key;
	private $api_url;

	public function __construct($api_url, $api_key)
	{
        $this->api_key = $api_key;
        $this->api_url = $api_url;
    }

    private function _get_request($url)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Api-Token: '.$this->api_key
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

    public function _post_request($url, $data)
    {

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Api-Token: '.$this->api_key
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
        $url = $this->api_url."/api/3/accounts";
        return $this->_get_request($url);
    }

    public function getCampaigns()
    {
        $url = $this->api_url."/api/3/campaigns";
        return $this->_get_request($url);
    }

    public function getLists()
    {
        $url = $this->api_url."/api/3/lists";
        return $this->_get_request($url);
    }

    public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null)
    {
        $data = array(
            'contact' => array(
                "email"     =>  $user_email,
                "firstName" =>  $user_f_name,
                "lastName"  =>  $user_l_name
            )
        );

        $url = $this->api_url."/api/3/contacts";
        $res = $this->_post_request($url, $data);
        
        if($res == null)
        {
            return "connection_error";
        }
        else
        {
            $res = json_decode($res);

            if(isset($res->errors))
            {
                return $res->errors[0]->title;
            }
            else
            {
                if(isset($res->contact))
                {
                    $new_contact_id = $res->contact->id;
                    $listData = array(
                        'contactList' => array(
                            "list"    =>  $list_id,
                            "contact" =>  $new_contact_id,
                            "status"  =>  1
                        )
                    );
            
                    $list_url = $this->api_url."/api/3/contactLists";
                    $listRes = $this->_post_request($list_url, $listData);

                    $listRes = json_decode($listRes);

                    if(isset($listRes->errors))
                    {
                        return $listRes->errors[0]->title;
                    }
                    else
                    {
                        if(isset($listRes->contactList))
                        {
                            if($listRes->contactList->status == 1)
                            {
                                return "done";
                            }
                        }
                    }
                    
                }
            }
        }
    }
}