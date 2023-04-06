<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Zapier
{
	private $hook_url;

	public function __construct($hook_url)
	{
		$this->hook_url = $hook_url;
    }

    public function postData($first_name, $last_name, $email, $phone = null)
    {
        $curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->hook_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => array(
				'first_name' => $first_name, 
				'last_name'	=>	$last_name,
                'email' => $email,
				'phone' => $phone
            ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
    }
}
