<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class IContact
{
    private $api_key;
    private $api_secret;
	private $api_username;

	public function __construct($api_key, $api_secret, $api_username)
	{
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_username = $api_username;
    }

    public function getContacts()
    {
        try
        {
            $icontact = \iContact\iContactApi::getInstance()->setConfig(array(
                'appId'       => $this->api_key, 
                'apiPassword' => $this->api_secret, 
                'apiUsername' => $this->api_username
            ));

            return $icontact->getContacts();
        }
        catch(\Exception $ex)
        {
            return false;
        }
    }

    public function getLists()
    {
        try
        {
            $icontact = \iContact\iContactApi::getInstance()->setConfig(array(
                'appId'       => $this->api_key, 
                'apiPassword' => $this->api_secret, 
                'apiUsername' => $this->api_username
            ));

            return $icontact->getLists();
        }
        catch(\Exception $ex)
        {
            return false;
        }
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null)
    {
        try
        {
            $icontact = \iContact\iContactApi::getInstance()->setConfig(array(
                'appId'       => $this->api_key, 
                'apiPassword' => $this->api_secret, 
                'apiUsername' => $this->api_username
            ));

            $contact_info = $icontact->addContact($email, null, null, $first_name, $last_name);
            $res = $icontact->subscribeContactToList($contact_info->contactId,$listID);
            if(sizeof($res) > 0)
            {
                if($res[0]->status == "normal")
                {
                    return "done";
                }
            }
            return $res;
        }
        catch(\Exception $ex)
        {
            return "connection_error";
        }
    }

}