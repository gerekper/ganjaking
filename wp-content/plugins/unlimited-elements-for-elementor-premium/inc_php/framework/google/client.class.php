<?php

abstract class UEGoogleAPIClient{

	const METHOD_GET = "GET";
	const METHOD_PUT = "PUT";
	const METHOD_POST = "POST";

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

		return $this->request(self::METHOD_GET, $endpoint, $params);
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

		return $this->request(self::METHOD_PUT, $endpoint, $params);
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

		return $this->request(self::METHOD_POST, $endpoint, $params);
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

		$query = ($method === self::METHOD_GET && $params) ? $params : array();

		if(empty($params[self::PARAM_QUERY]) === false){
			$query = array_merge($query, $params[self::PARAM_QUERY]);

			unset($params[self::PARAM_QUERY]);
		}

		$query = array_merge($query, $this->getAuthParams());

		$body = ($method !== self::METHOD_GET && $params) ? json_encode($params) : null;

		$url = $this->getBaseUrl() . $endpoint . "?" . http_build_query($query);

		$cacheKey = $this->getCacheKey($url);
		$cacheTime = ($method === self::METHOD_GET) ? $this->cacheTime : 0;

		$response = UniteProviderFunctionsUC::rememberTransient($cacheKey, $cacheTime, function() use ($method, $url, $body){

			$headers = array(
				"Accept: application/json",
				"Content-Type: application/json",
			);

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);
			$response = json_decode($response, true);

			$error = curl_error($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			curl_close($curl);

			if($error)
				throw new Exception($error);

			if($response === null)
				throw new Exception("Unable to parse the response (status code $code).", $code);

			if(empty($response["error"]) === false){
				$error = $response["error"];
				$message = $error["message"];
				$status = isset($error["status"]) ? $error["status"] : $error["code"];

				throw new Exception("$message ($status)");
			}elseif(empty($response["error_message"]) === false){
				$message = $response["error_message"];
				$status = isset($response["status"]) ? $response["status"] : $response["code"];

				throw new Exception("$message ($status)");
			}

			return $response;
		});

		return $response;
	}

	/**
	 * Get the cache key for the URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	private function getCacheKey($url){

		$key = "google:" . md5($url);

		return $key;
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

		throw new Exception("Either an access token or an API key must be specified.");
	}

}
