<?php

namespace IsotopeKit\IntegrationThirdPartySync;

use stdClass;

class SendinBlue
{
    private $api_key;
    // private $api_secret;
    // private $api_url = "https://sendiio.com";

	public function __construct($api_key)
	{
        $this->api_key = $api_key;
    }

    public function getAccountInfo()
    {
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->api_key);

        $apiInstance = new \SendinBlue\Client\Api\AccountApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );

        try {
            $result = $apiInstance->getAccount();
            // print_r($result);
            return $result;
        } catch (\Exception $e) {
            // echo 'Exception when calling AccountApi->getAccount: ', $e->getMessage(), PHP_EOL;
            return "error";
        }
    }

    public function getLists()
	{
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->api_key);
        
        $apiInstance = new \SendinBlue\Client\Api\ContactsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        $limit = 50; // int | Number of documents per page
        $offset = 0; // int | Index of the first document of the page
        $sort = "desc"; // string | Sort the results in the ascending/descending order of record creation. Default order is **descending** if `sort` is not passed
        
        try {
            $result = $apiInstance->getLists($limit, $offset, $sort);
            return $result->getLists();
        } catch (\Exception $e) {
            // echo 'Exception when calling ContactsApi->getLists: ', $e->getMessage(), PHP_EOL;
            return null;
        }
    }

    public function addUserToList($list_id, $user_email = null)
	{
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->api_key);

        $apiInstance = new \SendinBlue\Client\Api\ContactsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        
        try {
            $list_id = intval($list_id);
            
            // create contact

            $createContact = new \SendinBlue\Client\Model\CreateContact(); // \SendinBlue\Client\Model\CreateContact | Values to create a contact
            $createContact->setEmail($user_email);
            $createContact->setListIds([$list_id]);

            // $att = new stdClass();
            // $att->FNAME = $user_f_name;
            // $att->LNAME =  $user_l_name;

            // $createContact->setAttributes($att);
            $result = $apiInstance->createContact($createContact);

            return "done";

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}