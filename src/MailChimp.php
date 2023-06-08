<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class MailChimp
{
	private $api_key;

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
    }

    public function getLists()
    {
		try
		{
			$mailChimp = new \DrewM\MailChimp\MailChimp($this->api_key);
			$lists = $mailChimp->get('lists');
			if($lists)
			{
				return $lists;
			}
			else
			{
				return "err";
			}
		}
		catch(\Exception $ex)
		{
			return false;
		}
	}
	
	public function addUserToList($list_id, $user_email = null, $user_f_name = null, $user_l_name = null, $phone = null)
	{
		try
		{
			$mailChimp = new \DrewM\MailChimp\MailChimp($this->api_key);
			$res = $mailChimp->post("lists/".$list_id."/members", [
				'email_address'	=>	$user_email,
				'status'		=>	'subscribed',
				'merge_fields' => [
					'FNAME'	=>	$user_f_name,
					'LNAME'	=>	$user_l_name,
					'PHONE'	=>	$phone
				]
			]);

			return $res['status'];
		}
		catch(\Exception $ex)
		{
			if($ex->getMessage() == "Invalid MailChimp API key supplied.")
			{
				return "connection_error";
			}
			return $ex->getMessage();
		}
	}
}