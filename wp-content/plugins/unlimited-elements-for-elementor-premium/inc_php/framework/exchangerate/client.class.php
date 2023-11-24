<?php

class UEExchangeRateAPIClient{

	const BASE_URL = "https://v6.exchangerate-api.com/v6";

	const METHOD_GET = "GET";

	private $apiKey;
	private $cacheTime = 0; // in seconds

	/**
	 * Create a new client instance.
	 *
	 * @param string $apiKey
	 *
	 * @return void
	 */
	public function __construct($apiKey){

		$this->apiKey = $apiKey;
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
	 * Get a list of rates for the given currency (ISO 4217).
	 *
	 * @param string $currency
	 *
	 * @return array
	 */
	public function getRates($currency){

		try{
			$currency = urlencode($currency);

			$response = $this->get("/latest/$currency");
			$rates = array();

			foreach($response["conversion_rates"] as $code => $rate){
				$rates[] = UEExchangeRateAPIRate::transform(array(
					'code' => $code,
					'rate' => $rate,
				));
			}

			return $rates;
		}catch(Exception $exception){
			if ($exception->getCode() === 404)
				throw new Exception("Invalid currency.");

			throw $exception;
		}
	}

	/**
	 * Make a GET request to the API.
	 *
	 * @param $endpoint
	 * @param $params
	 *
	 * @return array
	 */
	private function get($endpoint, $params = array()){

		return $this->request(self::METHOD_GET, $endpoint, $params);
	}

	/**
	 * Make a request to the API.
	 *
	 * @param string $method
	 * @param string $endpoint
	 * @param array $params
	 *
	 * @return array
	 */
	private function request($method, $endpoint, $params = array()){

		$query = ($method === self::METHOD_GET && $params) ? "?" . http_build_query($params) : "";
		$body = ($method !== self::METHOD_GET && $params) ? json_encode($params) : null;

		$url = self::BASE_URL . "/" . $this->apiKey . $endpoint . $query;

		$cacheKey = $this->getCacheKey($url);
		$cacheTime = ($method === self::METHOD_GET) ? $this->cacheTime : 0;

		$response = UniteProviderFunctionsUC::rememberTransient($cacheKey, $cacheTime, function() use ($method, $url, $body){

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
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

			if($response["result"] !== "success")
				throw new Exception($response["error-type"]);

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

		$key = "exchangerate:" . md5($url);

		return $key;
	}

}
