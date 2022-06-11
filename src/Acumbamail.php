<?php

namespace IsotopeKit\IntegrationThirdPartySync;

class Acumbamail
{
	private $auth_token;

	function __construct($auth_token)
	{
		$this->auth_token = $auth_token;
	}

	// public function setAuthToken($auth_token)
	// {
	// 	$this->auth_token = $auth_token;
	// }

	function callAPI($request, $data = array())
	{
		$url = "https://acumbamail.com/api/1/" . $request . '/';

		$fields = array(
			'auth_token' => $this->auth_token,
			'response_type' => 'json',
		);

		if (count($data) != 0) {
			$fields = array_merge($fields, $data);
		}

		$postdata = http_build_query($fields);

		$opts = array('http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		));

		$response = @file_get_contents(
			$url,
			false,
			stream_context_create($opts)
		);
		$json = json_decode($response, true);

		if (is_array($json)) {
			return $json;
		} else {
			return $response;
		}
	}

	public function getLists()
	{
		$request = "getLists";
		return $this->callAPI($request);
	}

	public function addSubscriber($list_id, $merge_fields, $double_optin = '', $welcome_email = '')
	{
		$request = "addSubscriber";
		$merge_fields_send = array();

		foreach (array_keys($merge_fields) as $merge_field) {
			$merge_fields_send['merge_fields[' . $merge_field . ']'] = $merge_fields[$merge_field];
		}

		$data = array(
			'list_id' => $list_id,
			'double_optin' => $double_optin,
			'welcome_email' => $welcome_email,
		);

		$data = array_merge($data, $merge_fields_send);

		return $this->callAPI($request, $data);
	}
}
