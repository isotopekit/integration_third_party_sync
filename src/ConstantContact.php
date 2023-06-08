<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use App\Site;
use Log;

class ConstantContact
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
        $ch = curl_init();

        // Define base URL
        $base = 'https://authz.constantcontact.com/oauth2/default/v1/token';

        // Create full request URL
        $url = $base . '?code=' . $code . '&redirect_uri=' . $this->api_callback_url . '&grant_type=authorization_code';
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set authorization header
        // Make string of "API_KEY:SECRET"
        $auth = $this->api_key . ':' . $this->api_secret;
        // Base64 encode it
        $credentials = base64_encode($auth);
        // Create and set the Authorization header to use the encoded credentials, and set the Content-Type header
        $authorization = 'Authorization: Basic ' . $credentials;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/x-www-form-urlencoded'));

        // Set method and to expect response
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Make the call
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getRefreshToken($refreshToken)
    {
        // Use cURL to get a new access token and refresh token
        $ch = curl_init();

        // Define base URL
        $base = 'https://authz.constantcontact.com/oauth2/default/v1/token';

        // Create full request URL
        $url = $base . '?refresh_token=' . $refreshToken . '&grant_type=refresh_token';
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set authorization header
        // Make string of "API_KEY:SECRET"
        $auth = $this->api_key . ':' . $this->api_secret;
        // Base64 encode it
        $credentials = base64_encode($auth);
        // Create and set the Authorization header to use the encoded credentials, and set the Content-Type header
        $authorization = 'Authorization: Basic ' . $credentials;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/x-www-form-urlencoded'));

        // Set method and to expect response
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Make the call
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getAccountInfo($access_token)
    {
        $curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.cc.email/v3/account/summary",
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
        $url = "https://authz.constantcontact.com/oauth2/default/v1/authorize?client_id=".$this->api_key."&redirect_uri=".$this->api_callback_url."&response_type=code&scope=contact_data+campaign_data+account_read+offline_access&state=leadpal";
        return $url;
    }

    public function getLists($access_token)
    {
        $curl = curl_init();
        $authorization = 'Authorization: Bearer ' . $access_token;

		curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.cc.email/v3/contact_lists",
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

    public function addUserToList($access_token, $list_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
    {
        $data = array(
            'email_address' => array(
                "address"     =>  $user_email,
                "permission_to_send"    =>  "implicit"
            ),
            "create_source" => "Account",
            'first_name' =>  $user_f_name,
            'last_name'  =>  $user_l_name,
			'phone_numbers'	=>	array(
				"phone_number"	=>	$phone,
				"kind"			=>	"home"
			),
            'list_memberships'  =>  [
                $list_id
            ] 
        );

        $url = 'https://api.cc.email/v3/contacts';

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

        if(isset($response->contact_id))
        {
            return "done";
        }

        if(isset($response->error_key))
        {
            if($response->error_key == "unauthorized")
            {
                return "connection_error";
            }
            return $response;
        }

        return $response;
    }

}