<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class GetResponse
{
	private $api_key;

	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	public function getLists()
	{
		$client = \Getresponse\Sdk\GetresponseClientFactory::createWithApiKey($this->api_key);

		$campaignOperation = new \Getresponse\Sdk\Operation\Campaigns\GetCampaigns\GetCampaigns();
		$campaigns = $client->call($campaignOperation);
		return $campaigns->getData();
	}

	public function getContacts()
	{
		$client = \Getresponse\Sdk\GetresponseClientFactory::createWithApiKey($this->api_key);
		$contactOperation = new \Getresponse\Sdk\Operation\Contacts\GetContacts\GetContacts;
		$contacts = $client->call($contactOperation);
		return $contacts->getData();
	}

	public function addUserToList($campaignId, $email = null, $first_name = null, $last_name = null, $phone = null)
	{
		try
		{
			$client = \Getresponse\Sdk\GetresponseClientFactory::createWithApiKey($this->api_key);

			$newContact = new \Getresponse\Sdk\Operation\Model\NewContact(
				new \Getresponse\Sdk\Operation\Model\CampaignReference($campaignId),$email
			);
			$newContact->setName($first_name. " ".$last_name);
			$newContact->setDayOfCycle('0');
			// $newContact->setCustomFieldValues(
			// 	[
			// 		new \Getresponse\Sdk\Operation\Model\NewContactCustomFieldValue(
			// 			"Phone",
			// 			$phone
			// 		)
			// 	]
			// );

			$createContact = new \Getresponse\Sdk\Operation\Contacts\CreateContact\CreateContact($newContact);
			$createContactResponse = $client->call($createContact);
			$getresponse_res = $createContactResponse->getData();

			if($getresponse_res['httpStatus'] == "401")
			{
				return "connection_error";
			}
			elseif($getresponse_res['httpStatus'] == "200")
			{
				return "done";
			}
			else
			{
				return $getresponse_res;
			}
		}
		catch(\Getresponse\Sdk\Client\Exception\MalformedResponseDataException $ex)
		{
			return "done";
		}
	}
}