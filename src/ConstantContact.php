<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use App\Site;
use Log;

class ConstantContact
{
    private $api_key;
    private $api_secret;
	private $api_callback_url;

	public function __construct($agency_id = null)
	{
        // Log::channel('queue')->info('Agency_ID_X: ' .$agency_id);
        if($agency_id == null)
		{
            $this->api_key = Site::settings()['DEFAULT_API_KEY_CONSTANTC'];
            $this->api_secret = Site::settings()['DEFAULT_API_SECRET_CONSTANTC'];
            $this->api_callback_url = url("/callback/constantcontact");
        }
        else
		{
			if($agency_id == "default")
			{
				$site_details = Site::where('id', '1')->first();
				if($site_details)
				{
					$this->api_key = $site_details->DEFAULT_API_KEY_CONSTANTC;
					$this->api_secret = $site_details->DEFAULT_API_SECRET_CONSTANTC;
					$this->api_callback_url = url("/callback/constantcontact");
				}
			}
			else
			{
				$site_details = Site::where('agency_id', $agency_id)->first();
				if($site_details)
				{
					$this->api_key = $site_details->DEFAULT_API_KEY_CONSTANTC;
					$this->api_secret = $site_details->DEFAULT_API_SECRET_CONSTANTC;
					$this->api_callback_url = url("/callback/constantcontact");
				}
			}
		}
    }

    public function getAccessToken($code)
    {
        $base = 'https://idfed.constantcontact.com/as/token.oauth2';
        $url = $base . '?code=' . $code . '&redirect_uri=' . $this->api_callback_url . '&grant_type=authorization_code&scope=contact_data';

        $auth = $this->api_key . ':' . $this->api_secret;
        $credentials = base64_encode($auth);
        $authorization = 'Authorization: Basic ' . $credentials;

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array($authorization),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST"
			// CURLOPT_POSTFIELDS => array('api' => $this->api_key, 'hash' => $this->api_secret),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
    }

    public function getRefreshToken($refreshToken)
    {
        $base = 'https://idfed.constantcontact.com/as/token.oauth2';
        $url = $base . '?refresh_token=' . $refreshToken . '&grant_type=refresh_token';

        $auth = $this->api_key . ':' . $this->api_secret;
        $credentials = base64_encode($auth);
        $authorization = 'Authorization: Basic ' . $credentials;

        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array($authorization),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST"
			// CURLOPT_POSTFIELDS => array('api' => $this->api_key, 'hash' => $this->api_secret),
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
        $url = "https://api.cc.email/v3/idfed?client_id=".$this->api_key."&redirect_uri=".$this->api_callback_url."&response_type=code&scope=contact_data+campaign_data+account_read";
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

    public function addUserToList($access_token, $list_id, $user_email = null, $user_f_name = null, $user_l_name = null)
    {
        $data = array(
            'email_address' => array(
                "address"     =>  $user_email,
                "permission_to_send"    =>  "implicit"
            ),
            "create_source" => "Account",
            'first_name' =>  $user_f_name,
            'last_name'  =>  $user_l_name,
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