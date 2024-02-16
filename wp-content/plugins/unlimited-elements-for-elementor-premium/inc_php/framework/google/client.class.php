<?php

abstract class UEGoogleAPIClient{

	const PARAM_QUERY = "__query__";

	private $accessToken;
	private $apiKey;
	private $cacheTime = 0; // in seconds

	/**
	 * Set the access token.
	 *
	 * @param string $token
	 *
	 * @return void
	 */
	public function setAccessToken($token){

		$this->accessToken = $token;
	}

	/**
	 * Set the API key.
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function setApiKey($key){

		$this->apiKey = $key;
	}

	/**
	 * Set the cache time.
	 *
	 * @param int $seconds
	 *
	 * @return void
	 */
	public function setCacheTime($seconds){

		$this->cacheTime = $seconds;
	}

	/**
	 * Get the base URL for the API.
	 *
	 * @return string
	 */
	abstract protected function getBaseUrl();

	/**
	 * Make a GET request to the API.
	 *
	 * @param $endpoint
	 * @param $params
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function get($endpoint, $params = array()){

		return $this->request(UEHttpRequest::METHOD_GET, $endpoint, $params);
	}

	/**
	 * Make a PUT request to the API.
	 *
	 * @param $endpoint
	 * @param $params
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function put($endpoint, $params = array()){

		return $this->request(UEHttpRequest::METHOD_PUT, $endpoint, $params);
	}

	/**
	 * Make a POST request to the API.
	 *
	 * @param $endpoint
	 * @param $params
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function post($endpoint, $params = array()){

		return $this->request(UEHttpRequest::METHOD_POST, $endpoint, $params);
	}

	/**
	 * Make a request to the API.
	 *
	 * @param string $method
	 * @param string $endpoint
	 * @param array $params
	 *
	 * @return array
	 * @throws Exception
	 */
	private function request($method, $endpoint, $params = array()){

		$url = $this->getBaseUrl() . $endpoint;
		$query = ($method === UEHttpRequest::METHOD_GET) ? $params : array();
		$body = ($method !== UEHttpRequest::METHOD_GET) ? $params : array();

		if(empty($params[self::PARAM_QUERY]) === false){
			$query = array_merge($query, $params[self::PARAM_QUERY]);

			unset($params[self::PARAM_QUERY]);
		}

		$query = array_merge($query, $this->getAuthParams());

		$request = UEHttp::make();
		$request->asJson();
		$request->acceptJson();
		$request->cacheTime($this->cacheTime);
		$request->withQuery($query);
		$request->withBody($body);

		$request->validateResponse(function($response){

			$data = $response->json();

			if(empty($data["error"]) === false){
				$error = $data["error"];
				$message = $error["message"];
				$status = isset($error["status"]) ? $error["status"] : $error["code"];

				$this->throwError("$message ($status)");
			}elseif(empty($data["error_message"]) === false){
				$message = $data["error_message"];
				$status = isset($data["status"]) ? $data["status"] : $data["code"];

				$this->throwError("$message ($status)");
			}
		});

		$response = $request->request($method, $url);
		$data = $response->json();

		return $data;
	}

	/**
	 * Get parameters for the authorization.
	 *
	 * @return array
	 * @throws Exception
	 */
	private function getAuthParams(){

		if(empty($this->accessToken) === false)
			return array("access_token" => $this->accessToken);

		if(empty($this->apiKey) === false)
			return array("key" => $this->apiKey);

		$this->throwError("Either an access token or an API key must be specified.");
	}

	/**
	 * Thrown an exception with the given message.
	 *
	 * @param string $message
	 *
	 * @return void
	 * @throws Exception
	 */
	private function throwError($message){

		UniteFunctionsUC::throwError("Google API Error: $message");
	}

}
