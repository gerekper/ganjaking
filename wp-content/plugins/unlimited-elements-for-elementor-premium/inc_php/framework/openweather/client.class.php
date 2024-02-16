<?php

class UEOpenWeatherAPIClient{

	const DATA_BASE_URL = "https://api.openweathermap.org/data/3.0";
	const GEO_BASE_URL = "http://api.openweathermap.org/geo/1.0";
	const TEST_URL = "https://api.openweathermap.org/data/2.5/weather";

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
	 * Get the test URL for the API key.
	 *
	 * @return string
	 */
	public function getApiKeyTestUrl(){

		return self::DATA_BASE_URL . "/onecall?" . http_build_query(array(
				// London, GB
				"lat" => "51.5073219",
				"lon" => "-0.1276474",
				"appid" => $this->apiKey,
			));
	}

	/**
	 * Get a daily forecast for the given location.
	 *
	 * @param string $country
	 * @param string $city
	 * @param string $units
	 *
	 * @return UEOpenWeatherAPIForecast[]
	 * @throws Exception
	 */
	public function getForecasts($country, $city, $units = UEOpenWeatherAPIForecast::UNITS_STANDARD){

		$location = $this->findLocation($country, $city);

		$params = array(
			"lat" => $location["lat"],
			"lon" => $location["lon"],
			"units" => $units,
			"exclude" => "",	//current,hourly,alerts
			"lang" => get_locale(),
		);

		$response = $this->get(self::DATA_BASE_URL . "/onecall", $params);
				
		$daily = UniteFunctionsUC::getVal($response, "daily",array());
		$hourly = UniteFunctionsUC::getVal($response, "hourly",array());
		$current = UniteFunctionsUC::getVal($response, "current",array());
		$alerts = UniteFunctionsUC::getVal($response, "alerts");
		
		$params = array("units" => $units);
		$params["timezone"] = UniteFunctionsUC::getVal($response, "timezone");
		$params["timezone_offset"] = UniteFunctionsUC::getVal($response, "timezone_offset");
		$params["lat"] = UniteFunctionsUC::getVal($response, "lat");
		$params["lon"] = UniteFunctionsUC::getVal($response, "lon");
		
		$daily = UEOpenWeatherAPIForecast::transformAll($daily, $params);
		$hourly = UEOpenWeatherAPIForecast::transformAll($hourly, $params);
		
		$current = UEOpenWeatherAPIForecast::transform($current, $params);
		
		$forecast = array();
		$forecast["current"] = $current;
		$forecast["daily"] = $daily;
		$forecast["hourly"] = $hourly;
		$forecast["alerts"] = $alerts;

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
	 * @throws Exception
	 */
	private function get($url, $params = array()){

		return $this->request(UEHttpRequest::METHOD_GET, $url, $params);
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

			if(isset($data["cod"]) === true)
				$this->throwError("{$data["message"]} ({$data["cod"]})");
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

		UniteFunctionsUC::throwError("OpenWeather API Error: $message");
	}

}
