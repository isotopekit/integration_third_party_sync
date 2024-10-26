<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class MailWizz
{
    private $apiURL;
    private $apiKey;

    public function __construct($api_url, $api_key)
    {
        $this->apiURL = $api_url;
        $this->apiKey = $api_key;

        $config = new \EmsApi\Config([
            'apiUrl'    => $this->apiURL,
            'apiKey'    => $this->apiKey,

            // components
            'components' => [
                'cache' => [
                    'class'     => \EmsApi\Cache\File::class,
                    // 'filesPath' => __DIR__ . '/data/cache', // make sure it is writable by webserver
                    'filesPath' => public_path('/cache'), // make sure it is writable by webserver
                ]
            ],
        ]);
        \EmsApi\Base::setConfig($config);
        date_default_timezone_set('UTC');
    }

    public function getLists()
    {
        $endpoint = new \EmsApi\Endpoint\Lists();
        $response = $endpoint->getLists(1, 100);
        return $response->body;
    }

    public function addUserToList($listID, $email = null, $first_name = null, $last_name = null, $phone = null)
    {
        $endpoint = new \EmsApi\Endpoint\ListSubscribers();
        $response = $endpoint->create($listID, [
            'EMAIL'    => $email, // the confirmation email will be sent!!! Use valid email address
            'FNAME'    => $first_name,
            'LNAME'    => $last_name
        ]);

        return $response->body;
    }
}
