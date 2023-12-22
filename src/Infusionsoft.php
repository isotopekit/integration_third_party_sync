<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use App\Site;
use Log;

class Infusionsoft
{
    private $api_key;
    private $api_secret;
	private $api_callback_url;

	public function __construct($api_key = null, $api_secret = null, $api_callback_url = null)
	{
        $this->api_key = $api_key;
		$this->api_secret = $api_secret;
		$this->api_callback_url = $api_callback_url;
    }

    public function getAccessToken($code)
    {
        $infusionsoft = new \Infusionsoft\Infusionsoft(array(
			'clientId'     => $this->api_key,
			'clientSecret' => $this->api_secret,
			'redirectUri'  => $this->api_callback_url,
		));
		
		$data = $infusionsoft->requestAccessToken($code);
		return [
			"access_token"  =>  $data->accessToken,
			"refresh_token" =>  $data->refreshToken
		];
    }

    public function getRefreshToken($refreshToken)
    {	
		$url = 'https://api.infusionsoft.com/token';

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.base64_encode($this->api_key .':'.$this->api_secret),
                // 'Accept: application/x-www-form-urlencoded',
                'Content-Type: application/x-www-form-urlencoded'
            ),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=".$refreshToken
		));

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
    }

    public function getAccountInfo($access_token)
    {
        $curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/oauth/connect/userinfo",
            CURLOPT_HTTPHEADER => array($authorization),
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

    public function getLoginURL()
    {
        $infusionsoft = new \Infusionsoft\Infusionsoft(array(
			'clientId'     => $this->api_key,
			'clientSecret' => $this->api_secret,
			'redirectUri'  => $this->api_callback_url
		));
		
		// If the serialized token is available in the session storage, we tell the SDK
		// to use that token for subsequent requests.
		if (isset($_SESSION['token'])) {
			$infusionsoft->setToken(unserialize($_SESSION['token']));
		}
		
		// If we are returning from Infusionsoft we need to exchange the code for an
		// access token.
		if (isset($_GET['code']) and !$infusionsoft->getToken()) {
			$_SESSION['token'] = serialize($infusionsoft->requestAccessToken($_GET['code']));
		}
		
		if ($infusionsoft->getToken()) {
			// Save the serialized token to the current session for subsequent requests
			$_SESSION['token'] = serialize($infusionsoft->getToken());
		
		// MAKE INFUSIONSOFT REQUEST
		} else {
			return $infusionsoft->getAuthorizationUrl();
		}
    }

    public function getLists($access_token, $limit = null, $offset = null)
    {
        $curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		if($limit == null)
		{
			$url = "https://api.infusionsoft.com/crm/rest/v1/campaigns?optional_properties=sequences";
		}
		else
		{
			if($offset == null)
			{
				$url = "https://api.infusionsoft.com/crm/rest/v1/campaigns?order=id&order_direction=ascending&limit=".$limit;
			}
			else
			{
				$url = "https://api.infusionsoft.com/crm/rest/v1/campaigns?order=id&order_direction=ascending&limit=".$limit."&offset=".$offset;
			}
		}

		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array($authorization),
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
	
	public function getTags($access_token, $limit = null, $offset = null)
	{
		$curl = curl_init();
		$authorization = 'Authorization: Bearer ' . $access_token;
		
		if($limit == null)
		{
			$url = "https://api.infusionsoft.com/crm/rest/v1/tags";
		}
		else
		{
			if($offset == null)
			{
				$url = "https://api.infusionsoft.com/crm/rest/v1/tags?limit=".$limit;
			}
			else
			{
				$url = "https://api.infusionsoft.com/crm/rest/v1/tags?limit=".$limit."&offset=".$offset;
			}
		}


		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array($authorization),
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

	public function getTagsByCategory($access_token, $categoryId)
	{
		$curl = curl_init();
		$authorization = 'Authorization: Bearer ' . $access_token;

		$url = "https://api.infusionsoft.com/crm/rest/v1/tags?category=".$categoryId;

		curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array($authorization),
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

	public function getListItem($access_token, $item_id)
    {
        $curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/campaigns/".$item_id."?optional_properties=sequences",
            CURLOPT_HTTPHEADER => array($authorization),
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
	
	private function addContactToCampaign($access_token,$campaign_id, $sequence_id, $contact_id)
	{
		$data = [];

        $url = 'https://api.infusionsoft.com/crm/rest/v1/campaigns/'.$campaign_id.'/sequences/'.$sequence_id.'/contacts/'.$contact_id;

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
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
        
		$response = json_decode($response);
		
		if(isset($response->fault))
		{
			if($response->fault->faultstring == "Invalid Access Token")
			{
				return "connection_error";
			}
		}

		return "done";
	}

	public function addUserToList($access_token, $list_id, $seq_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
    {
        $data = array(
            'email_addresses' => array([
                "email"     =>  $user_email,
                "field"    =>  "EMAIL1"
			]),
			'phone_numbers' => array([
                "number"     =>  $phone,
                "field"    =>  "PHONE1"
			]),
            'given_name' =>  $user_f_name. " ".$user_l_name,
            'opt_in_reason'  =>  "Customer opted-in through webform"
		);

        $url = 'https://api.infusionsoft.com/crm/rest/v1/contacts';

        $authorization = 'Authorization: Bearer ' . $access_token;

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
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
        
		$response = json_decode($response);
		
		if(isset($response->id))
		{
			// add to list and sequence
			// return $response->id;

			$result = $this->addContactToCampaign($access_token, $list_id, $seq_id, $response->id);

			return $result;
		}
		else
		{
			return "error";
		}
	}

	public function addTagToContact($access_token, $tag_id, $contact_id)
	{
		$tag_id = (int) $tag_id;

		$data = array(
            'tagIds' => [
                $tag_id
			]
		);

        $url = 'https://api.infusionsoft.com/crm/rest/v1/contacts/'.$contact_id.'/tags';

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
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

		// return $response;
        
		$response = json_decode($response);
		
		if(isset($response->fault))
		{
			if($response->fault->faultstring == "Invalid Access Token")
			{
				return "connection_error";
			}
		}

		return "done";
	}

	public function searchContact($access_token, $email)
	{
		$curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/contacts/?email=".$email,
            CURLOPT_HTTPHEADER => array($authorization),
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

	public function addUserWithTag($access_token, $tagId, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
    {
        $data = array(
            'email_addresses' => array([
                "email"     =>  $user_email,
                "field"    =>  "EMAIL1"
			]),
			'phone_numbers' => array([
                "number"     =>  $phone,
                "field"    =>  "PHONE1"
			]),
            'given_name' =>  $user_f_name. " ".$user_l_name,
            'opt_in_reason'  =>  "Customer opted-in through webform"
		);

        $url = 'https://api.infusionsoft.com/crm/rest/v1/contacts';

        $authorization = 'Authorization: Bearer ' . $access_token;

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
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
        
		$response = json_decode($response);
		
		if(isset($response->id))
		{
			// add to list and sequence
			// return $response->id;

			$result = $this->addTagToContact($access_token, $tagId, $response->id);

			return $result;
		}
		else
		{
			return "error";
		}
	}

}