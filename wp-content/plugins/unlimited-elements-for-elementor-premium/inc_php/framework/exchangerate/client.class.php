<?php

class UEExchangeRateAPIClient{

	const BASE_URL = "https://v6.exchangerate-api.com/v6";

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
	 * @throws Exception
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
		}catch(UEHttpResponseException $exception){
			if($exception->getResponse()->status() === 404)
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
	 * @throws Exception
	 */
	private function get($endpoint, $params = array()){

		return $this->request(UEHttpRequest::METHOD_GET, $endpoint, $params);
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

		$url = self::BASE_URL . "/" . $this->apiKey . $endpoint;
		$query = ($method === UEHttpRequest::METHOD_GET) ? $params : array();
		$body = ($method !== UEHttpRequest::METHOD_GET) ? $params : array();

		$request = UEHttp::make();
		$request->asJson();
		$request->acceptJson();
		$request->cacheTime($this->cacheTime);
		$request->withQuery($query);
		$request->withBody($body);

		$request->validateResponse(function($response){

			$data = $response->json();

			if($data["result"] !== "success")
				$this->throwError($data["error-type"]);
		});

		$response = $request->request($method, $url);
		$data = $response->json();

		return $data;
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

		UniteFunctionsUC::throwError("ExchangeRate API Error: $message");
	}

}
