<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class SendPulse
{
	private $api_key;
	private $api_secret;

	public function __construct($api_key, $api_secret)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;
	}

	public function getLists()
	{
        try
        {
            $SPApiClient = new ApiClient($this->api_key, $this->api_secret, new FileStorage());
            return $SPApiClient->listAddressBooks();
        }
        catch(\Exception $ex)
        {
            return "error";
        }
    }

    public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null)
    {
        try
        {
            $bookID = $list_id;
            $emails = array(
                array(
                    'email' => $user_email,
                    'variables' => array(
                        // 'phone' => '+12345678900',
                        'name' => $user_f_name." ".$user_l_name,
                    )
                )
            );

            $SPApiClient = new ApiClient($this->api_key, $this->api_secret, new FileStorage());
        
            // Without confirmation
            $SPApiClient->addEmails($bookID, $emails);
        }
        catch(\Exception $ex)
        {
            return $ex;
        }
    }
}