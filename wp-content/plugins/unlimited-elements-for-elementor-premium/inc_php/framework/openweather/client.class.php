<?php

class UEOpenWeatherAPIClient{

	const DATA_BASE_URL = "https://api.openweathermap.org/data/3.0";
	const GEO_BASE_URL = "http://api.openweathermap.org/geo/1.0";

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
	 * Get a daily forecast for the given location.
	 *
	 * @param string $country
	 * @param string $city
	 * @param string $units (standard, metric or imperial)
	 *
	 * @return UEOpenWeatherAPIForecast[]
	 */
	public function getDailyForecast($country, $city, $units = "standard"){

		$location = $this->findLocation($country, $city);

		$params = array(
			"lat" => $location["lat"],
			"lon" => $location["lon"],
			"units" => $units,
			"exclude" => "current,hourly,alerts",
			"lang" => get_locale(),
		);

		$response = $this->get(self::DATA_BASE_URL . "/onecall", $params);
		$forecast = UEOpenWeatherAPIForecast::transformAll($response["daily"]);

		return $forecast;
	}

	/**
	 * Find a location by the given country and city.
	 *
	 * @param string $country
	 * @param string $city
	 *
	 * @return false|mixed
	 */
	private function findLocation($country, $city){

		$params = array(
			"q" => "$city, $country",
			"limit" => 1,
		);

		$response = $this->get(self::GEO_BASE_URL . "/direct", $params);
		$location = reset($response);

		if(empty($location) === true)
			throw new Exception("Location not found.");

		return $location;
	}

	/**
	 * Make a GET request to the API.
	 *
	 * @param $url
	 * @param $params
	 *
	 * @return array
	 */
	private function get($url, $params = array()){

		return $this->request(self::METHOD_GET, $url, $params);
	}

	/**
	 * Make a request to the API.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array $params
	 *
	 * @return array
	 */
	private function request($method, $url, $params = array()){

		$params["appid"] = $this->apiKey;

		$query = ($method === self::METHOD_GET && $params) ? "?" . http_build_query($params) : "";
		$body = ($method !== self::METHOD_GET && $params) ? json_encode($params) : null;

		$url .= $query;

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

			if ($response === null)
				throw new Exception("Unable to parse the response (status code $code).", $code);

			if(isset($response["cod"]))
				throw new Exception($response["message"] . " (" . $response["cod"] . ")");

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

		$key = "openweather:" . md5($url);

		return $key;
	}

}
