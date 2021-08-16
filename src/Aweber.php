<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use App\Site;
use Log;

class Aweber
{
	private $api_key;
	private $api_secret;
	private $api_callback_url;

	public function __construct($agency_id = null)
	{
		// Log::channel('queue')->info('Agency_ID_X: ' .$agency_id);

		if($agency_id == null)
		{
			$this->api_key = Site::settings()['DEFAULT_API_KEY_AWEBER'];
			$this->api_secret = Site::settings()['DEFAULT_API_SECRET_AWEBER'];
			$this->api_callback_url = url("/callback/aweber");
		}
		else
		{
			if($agency_id == "default")
			{
				$site_details = Site::where('id', '1')->first();
				if($site_details)
				{
					$this->api_key = $site_details->DEFAULT_API_KEY_AWEBER;
					$this->api_secret = $site_details->DEFAULT_API_SECRET_AWEBER;
					$this->api_callback_url = url("/callback/aweber");
				}
			}
			else
			{
				$site_details = Site::where('agency_id', $agency_id)->first();
				if($site_details)
				{
					$this->api_key = $site_details->DEFAULT_API_KEY_AWEBER;
					$this->api_secret = $site_details->DEFAULT_API_SECRET_AWEBER;
					$this->api_callback_url = url("/callback/aweber");
				}
			}
		}
	}

	public function getLoginURL()
	{
		$OAUTH_URL = 'https://auth.aweber.com/oauth2/';
		$TOKEN_URL = 'https://auth.aweber.com/oauth2/token';

		$scopes = array(
			'account.read',
			'list.read',
			'list.write',
			'subscriber.read',
			'subscriber.write',
			'email.read',
			'email.write',
			'subscriber.read-extended'
		);

		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId' => $this->api_key,
			'clientSecret' => $this->api_secret,
			'redirectUri' => $this->api_callback_url,
			'scopes' => $scopes,
			'scopeSeparator' => ' ',
			'urlAuthorize' => $OAUTH_URL . 'authorize',
			'urlAccessToken' => $OAUTH_URL . 'token',
			'urlResourceOwnerDetails' => 'https://api.aweber.com/1.0/accounts'
		]);

		$authorizationUrl = $provider->getAuthorizationUrl();

		return $authorizationUrl;
	}

	public function getAccessToken($code)
	{
		$OAUTH_URL = 'https://auth.aweber.com/oauth2/';
		$TOKEN_URL = 'https://auth.aweber.com/oauth2/token';

		$scopes = array(
			'account.read',
			'list.read',
			'list.write',
			'subscriber.read',
			'subscriber.write',
			'email.read',
			'email.write',
			'subscriber.read-extended'
		);

		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId' => $this->api_key,
			'clientSecret' => $this->api_secret,
			'redirectUri' => $this->api_callback_url,
			'scopes' => $scopes,
			'scopeSeparator' => ' ',
			'urlAuthorize' => $OAUTH_URL . 'authorize',
			'urlAccessToken' => $OAUTH_URL . 'token',
			'urlResourceOwnerDetails' => 'https://api.aweber.com/1.0/accounts'
		]);

		$token = $provider->getAccessToken('authorization_code', [
			'code' => $code
		]);
	
		$accessToken = $token->getToken();
		$refreshToken = $token->getRefreshToken();
		
		return [
			"access_token"  =>  $accessToken,
			"refresh_token" =>  $refreshToken
		];
	}

	public function getRefreshToken($refresh_token)
	{
		try
		{
			$client = new \GuzzleHttp\Client();
			$clientId = $this->api_key;
			$clientSecret = $this->api_secret;
			$response = $client->post(
				'https://auth.aweber.com/oauth2/token', [
					'auth' => [
						$clientId, $clientSecret
					],
					'json' => [
						'grant_type' => 'refresh_token',
						'refresh_token' => $refresh_token
					]
				]
			);
			$body = $response->getBody();
			$newCreds = json_decode($body, true);

			return $newCreds;
		}
		catch(\Exception $ex)
		{
			return false;
		}
	}

	public function getAccountInfo($accessToken)
	{
		try
		{
			$headers = [
				'User-Agent' => 'AWeber-PHP-code-sample/1.0',
				'Accept' => 'application/json',
				'Authorization'	=>	'Bearer '.$accessToken
			];
			
			$client = new \GuzzleHttp\Client();

			$url = 'https://api.aweber.com/1.0/accounts';
			$response = $client->get($url, ['headers' => $headers]);
			$body = json_decode($response->getBody(), true);
			
			return $body;
		}
		catch(\Exception $ex)
		{
			return false;
		}
	}

	public function getLists($accessToken, $accountId)
	{
		$headers = [
			'User-Agent' => 'AWeber-PHP-code-sample/1.0',
			'Accept' => 'application/json',
			'Authorization'	=>	'Bearer '.$accessToken
		];
		
		$client = new \GuzzleHttp\Client();
		
		$url = "https://api.aweber.com/1.0/accounts/".$accountId."/lists";
		$response = $client->get($url, ['headers' => $headers]);
		$body = json_decode($response->getBody(), true);
		$lists = $body;

		return $lists;
	}

	public function addUserToList($accessToken, $accountId, $listID, $email, $first_name = null, $last_name = null, $tags = null)
	{
		try
		{
			if($tags != null)
			{
				$body = [
					'email'	=> $email,
					'name'	=> $first_name. " ".$last_name,
					'tags'	=>	$tags
				];
			}
			else
			{
				$body = [
					'email'	=> $email,
					'name'	=> $first_name. " ".$last_name
				];
			}
			$headers = [
				'User-Agent' => 'AWeber-PHP-code-sample/1.0',
				'Accept' => 'application/json',
				'Authorization'	=>	'Bearer '.$accessToken
			];
			$client = new \GuzzleHttp\Client();
			$url = "https://api.aweber.com/1.0/accounts/".$accountId."/lists/".$listID."/subscribers";
			$response = $client->post($url, ['json' => $body, 'headers' => $headers]);
			// return $response->getHeader('Location')[0];
			return "done";
		}
		catch(\GuzzleHttp\Exception\ClientException $ex)
		{
			$error = $ex->getResponse()->getBody(true);
			$error = json_decode($error);
			if(isset($error->error))
			{
				if($error->error == "invalid_token")
				{
					return "connection_error";
				}
				return $error->error->status;
			}
		}
		catch(\Exception $ex)
		{
			return $ex;
		}
	}
}