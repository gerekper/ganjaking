<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAPIIntegrations{

	const FORMAT_DATE = "d.m.Y";
	const FORMAT_DATETIME = "d.m.Y H:i";
	const FORMAT_MYSQL_DATETIME = "Y-m-d H:i:s";

	const TYPE_CURRENCY_EXCHANGE = "currency_exchange";
	const TYPE_GOOGLE_EVENTS = "google_events";
	const TYPE_GOOGLE_REVIEWS = "google_reviews";
	const TYPE_GOOGLE_SHEETS = "google_sheets";
	const TYPE_WEATHER_FORECAST = "weather_forecast";
	const TYPE_YOUTUBE_PLAYLIST = "youtube_playlist";

	const SETTINGS_OPEN_WEATHER_API_KEY = "openweather_api_key";
	const SETTINGS_EXCHANGE_RATE_API_KEY = "exchangerate_api_key";

	const CURRENCY_EXCHANGE_FIELD_EMPTY_API_KEY = "currency_exchange:empty_api_key";
	const CURRENCY_EXCHANGE_FIELD_CURRENCY = "currency_exchange:currency";
	const CURRENCY_EXCHANGE_FIELD_PRECISION = "currency_exchange:precision";
	const CURRENCY_EXCHANGE_FIELD_INCLUDE_CURRENCIES = "currency_exchange:include_currencies";
	const CURRENCY_EXCHANGE_FIELD_CACHE_TIME = "currency_exchange:cache_time";
	const CURRENCY_EXCHANGE_MIN_PRECISION = 2;
	const CURRENCY_EXCHANGE_MAX_PRECISION = 6;
	const CURRENCY_EXCHANGE_DEFAULT_PRECISION = 2;
	const CURRENCY_EXCHANGE_DEFAULT_CACHE_TIME = 60;

	const GOOGLE_EVENTS_FIELD_EMPTY_CREDENTIALS = "google_events:empty_credentials";
	const GOOGLE_EVENTS_FIELD_CALENDAR_ID = "google_events:calendar_id";
	const GOOGLE_EVENTS_FIELD_RANGE = "google_events:range";
	const GOOGLE_EVENTS_FIELD_ORDER = "google_events:order";
	const GOOGLE_EVENTS_FIELD_LIMIT = "google_events:limit";
	const GOOGLE_EVENTS_FIELD_CACHE_TIME = "google_events:cache_time";
	const GOOGLE_EVENTS_DEFAULT_LIMIT = 250;
	const GOOGLE_EVENTS_DEFAULT_CACHE_TIME = 10;
	const GOOGLE_EVENTS_RANGE_UPCOMING = "upcoming";
	const GOOGLE_EVENTS_RANGE_TODAY = "today";
	const GOOGLE_EVENTS_RANGE_TOMORROW = "tomorrow";
	const GOOGLE_EVENTS_RANGE_WEEK = "week";
	const GOOGLE_EVENTS_RANGE_MONTH = "month";
	const GOOGLE_EVENTS_ORDER_DATE_ASC = "date:asc";
	const GOOGLE_EVENTS_ORDER_DATE_DESC = "date:desc";

	const GOOGLE_REVIEWS_FIELD_EMPTY_API_KEY = "google_reviews:empty_api_key";
	const GOOGLE_REVIEWS_FIELD_PLACE_ID = "google_reviews:place_id";
	const GOOGLE_REVIEWS_FIELD_CACHE_TIME = "google_reviews:cache_time";
	const GOOGLE_REVIEWS_DEFAULT_CACHE_TIME = 10;

	const GOOGLE_SHEETS_FIELD_EMPTY_CREDENTIALS = "google_sheets:empty_credentials";
	const GOOGLE_SHEETS_FIELD_ID = "google_sheets:id";
	const GOOGLE_SHEETS_FIELD_SHEET_ID = "google_sheets:sheet_id";
	const GOOGLE_SHEETS_FIELD_CACHE_TIME = "google_sheets:cache_time";
	const GOOGLE_SHEETS_DEFAULT_CACHE_TIME = 10;

	const WEATHER_FORECAST_FIELD_EMPTY_API_KEY = "weather_forecast:empty_api_key";
	const WEATHER_FORECAST_FIELD_COUNTRY = "weather_forecast:country";
	const WEATHER_FORECAST_FIELD_CITY = "weather_forecast:city";
	const WEATHER_FORECAST_FIELD_UNITS = "weather_forecast:units";
	const WEATHER_FORECAST_FIELD_CACHE_TIME = "weather_forecast:cache_time";
	const WEATHER_FORECAST_DEFAULT_CACHE_TIME = 60;
	const WEATHER_FORECAST_UNITS_METRIC = "metric";
	const WEATHER_FORECAST_UNITS_IMPERIAL = "imperial";

	const YOUTUBE_PLAYLIST_FIELD_EMPTY_CREDENTIALS = "youtube_playlist:empty_credentials";
	const YOUTUBE_PLAYLIST_FIELD_ID = "youtube_playlist:id";
	const YOUTUBE_PLAYLIST_FIELD_ORDER = "youtube_playlist:order";
	const YOUTUBE_PLAYLIST_FIELD_LIMIT = "youtube_playlist:limit";
	const YOUTUBE_PLAYLIST_FIELD_CACHE_TIME = "youtube_playlist:cache_time";
	const YOUTUBE_PLAYLIST_DEFAULT_LIMIT = 5;
	const YOUTUBE_PLAYLIST_DEFAULT_CACHE_TIME = 10;
	const YOUTUBE_PLAYLIST_ORDER_DEFAULT = "default";
	const YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_ASC = "date_added:asc";
	const YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_DESC = "date_added:desc";
	const YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_RANDOM = "date_added:random";
	const YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_ASC = "date_published:asc";
	const YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_DESC = "date_published:desc";
	const YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_RANDOM = "date_published:random";

	const ORDER_FIELD = "__order_field";
	const ORDER_DIRECTION_ASC = "asc";
	const ORDER_DIRECTION_DESC = "desc";
	const ORDER_DIRECTION_RANDOM = "random";

	private static $instance = null;

	private $params = array();

	/**
	 * create a new instance
	 *
	 * @return void
	 */
	private function __construct(){

		$this->init();
	}

	/**
	 * get the class instance
	 *
	 * @return self
	 */
	public static function getInstance(){

		if(self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * get the api types
	 *
	 * @return array
	 */
	public function getTypes(){

		$types = array();

		if(GlobalsUnlimitedElements::$enableGoogleAPI === true){
			$types[self::TYPE_GOOGLE_EVENTS] = "Google Events";
			$types[self::TYPE_GOOGLE_REVIEWS] = "Google Reviews";
			$types[self::TYPE_GOOGLE_SHEETS] = "Google Sheets";
			$types[self::TYPE_YOUTUBE_PLAYLIST] = "Youtube Playlist";
		}

		if(GlobalsUnlimitedElements::$enableWeatherAPI === true)
			$types[self::TYPE_WEATHER_FORECAST] = "Weather Forecast";

		if(GlobalsUnlimitedElements::$enableCurrencyAPI === true)
			$types[self::TYPE_CURRENCY_EXCHANGE] = "Currency Exchange";

		return $types;
	}

	/**
	 * get the api data
	 *
	 * @return array
	 */
	public function getData($type, $params){

		// add api keys
		$params[self::SETTINGS_OPEN_WEATHER_API_KEY] = HelperProviderCoreUC_EL::getGeneralSetting(self::SETTINGS_OPEN_WEATHER_API_KEY);
		$params[self::SETTINGS_EXCHANGE_RATE_API_KEY] = HelperProviderCoreUC_EL::getGeneralSetting(self::SETTINGS_EXCHANGE_RATE_API_KEY);

		$this->params = $params;

		// get data
		$data = array();

		switch($type){
			case self::TYPE_CURRENCY_EXCHANGE:
				$data = $this->getCurrencyExchangeData();
			break;
			case self::TYPE_GOOGLE_EVENTS:
				$data = $this->getGoogleEventsData();
			break;
			case self::TYPE_GOOGLE_REVIEWS:
				$data = $this->getGoogleReviewsData();
			break;
			case self::TYPE_GOOGLE_SHEETS:
				$data = $this->getGoogleSheetsData();
			break;
			case self::TYPE_WEATHER_FORECAST:
				$data = $this->getWeatherForecastData();
			break;
			case self::TYPE_YOUTUBE_PLAYLIST:
				$data = $this->getYoutubePlaylistData();
			break;
			default:
				UniteFunctionsUC::throwError(__FUNCTION__ . " error - API type \"$type\" is not implemented");
		}

		return $data;
	}

	/**
	 * get the api data for multisource
	 */
	public function getDataForMultisource($type, $params){

		$data = $this->getData($type, $params);

		switch($type){
			case self::TYPE_CURRENCY_EXCHANGE:
				$data = UniteFunctionsUC::getVal($data, "rates_chosen");
			break;
		}

		return $data;
	}

	/**
	 * add the api data to params
	 *
	 * @return array
	 */
	public function addDataToParams($data, $name){

		$params = UniteFunctionsUC::getVal($data, $name, array());
		$params = UniteFunctionsUC::clearKeysFirstUnderscore($params);

		try{
			$apiType = UniteFunctionsUC::getVal($params, "api_type");
			$apiData = $this->getData($apiType, $params);

			$params["success"] = true;
			$params = array_merge($params, $apiData);
		}catch(Exception $exception){
			$params["success"] = false;
			$params["error"] = $exception->getMessage();
		}

		$data[$name] = $params;

		return $data;
	}

	/**
	 * get settings fields
	 *
	 * @return array
	 */
	public function getSettingsFields(){

		$fields = array();

		if(GlobalsUnlimitedElements::$enableGoogleAPI === true){
			$fields[self::TYPE_GOOGLE_EVENTS] = $this->getGoogleEventsSettingsFields();
			$fields[self::TYPE_GOOGLE_REVIEWS] = $this->getGoogleReviewsSettingsFields();
			$fields[self::TYPE_GOOGLE_SHEETS] = $this->getGoogleSheetsSettingsFields();
			$fields[self::TYPE_YOUTUBE_PLAYLIST] = $this->getYoutubePlaylistSettingsFields();
		}

		if(GlobalsUnlimitedElements::$enableCurrencyAPI === true)
			$fields[self::TYPE_CURRENCY_EXCHANGE] = $this->getCurrencyExchangeSettingsFields();

		if(GlobalsUnlimitedElements::$enableWeatherAPI === true)
			$fields[self::TYPE_WEATHER_FORECAST] = $this->getWeatherForecastSettingsFields();

		return $fields;
	}

	/**
	 * add settings fields
	 *
	 * @return void
	 */
	public function addSettingsFields($settingsManager, $fields, $name, $condition = null){

		foreach($fields as $field){
			$params = array();
			$params["origtype"] = $field["type"];
			$params["description"] = isset($field["desc"]) ? $field["desc"] : "";

			if(!empty($condition))
				$params["elementor_condition"] = $condition;

			$paramName = $name . "_" . $field["id"];
			$paramDefault = isset($field["default"]) ? $field["default"] : "";

			switch($field["type"]){
				case UniteCreatorDialogParam::PARAM_STATIC_TEXT:
					$settingsManager->addStaticText($field["text"], $paramName, $params);
				break;
				case UniteCreatorDialogParam::PARAM_TEXTAREA:
					$settingsManager->addTextArea($paramName, $paramDefault, $field["text"], $params);
				break;
				case UniteCreatorDialogParam::PARAM_TEXTFIELD:
					$settingsManager->addTextBox($paramName, $paramDefault, $field["text"], $params);
				break;
				case UniteCreatorDialogParam::PARAM_DROPDOWN:
					$settingsManager->addSelect($paramName, array_flip($field["options"]), $field["text"], $paramDefault, $params);
				break;
				default:
					UniteFunctionsUC::throwError(__FUNCTION__ . " error - Field type \"{$field["type"]}\" is not implemented");
			}
		}

		return $settingsManager;
	}

	/**
	 * add service settings fields
	 *
	 * @return void
	 */
	public function addServiceSettingsFields($settingsManager, $type, $name, $condition = null){

		$fields = $this->getSettingsFields();
		$fields = UniteFunctionsUC::getVal($fields, $type);

		if(empty($fields))
			return;

		// add api type
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HIDDEN;

		$settingsManager->addHiddenInput($name . "_api_type", $type, "API Type", $params);

		// add the fields
		$this->addSettingsFields($settingsManager, $fields, $name, $condition);
	}

	/**
	 * init the api integrations
	 */
	private function init(){

		$this->includeServices();
	}

	/**
	 * include service files
	 */
	private function includeServices(){

		$services = new UniteServicesUC();

		if(GlobalsUnlimitedElements::$enableGoogleAPI === true)
			$services->includeGoogleAPI();

		if(GlobalsUnlimitedElements::$enableCurrencyAPI === true)
			$services->includeExchangeRateAPI();

		if(GlobalsUnlimitedElements::$enableWeatherAPI === true)
			$services->includeOpenWeatherAPI();
	}

	/**
	 * get the param value
	 */
	private function getParam($key, $fallback = null){

		$value = empty($this->params[$key]) ? $fallback : $this->params[$key];

		return $value;
	}

	/**
	 * get the param value, otherwise throw an exception
	 */
	private function getRequiredParam($key, $label = null){

		$value = $this->getParam($key);

		if(!empty($value))
			return $value;

		if(!empty($label))
			$error = "$label is required.";
		else
			$error = "$key is required.";

		UniteFunctionsUC::throwError($error);
	}

	/**
	 * get the cache time param
	 */
	private function getCacheTimeParam($key, $default = 10){

		$time = $this->getParam($key, $default);
		$time = intval($time);
		$time = max($time, 1); // minimum is 1 minute
		$time *= 60; // convert to seconds

		return $time;
	}

	/**
	 * authorize google service
	 */
	private function authorizeGoogleService($service){

		try{
			$service->setAccessToken(UEGoogleAPIHelper::getFreshAccessToken());
		}catch(Exception $exception){
			$this->authorizeGoogleServiceWithApiKey($service);
		}
	}

	/**
	 * authorize google service with api key
	 */
	private function authorizeGoogleServiceWithApiKey($service){

		$service->setApiKey(UEGoogleAPIHelper::getApiKey());
	}

	/**
	 * has google credentials
	 */
	private function hasGoogleCredentials(){

		try{
			$token = UEGoogleAPIHelper::getFreshAccessToken();

			$hasCredentials = empty($token) === false;
		}catch(Exception $exception){
			$key = UEGoogleAPIHelper::getApiKey();

			$hasCredentials = empty($key) === false;
		}

		return $hasCredentials;
	}

	/**
	 * validate google api key
	 */
	private function validateGoogleApiKey(){

		$key = UEGoogleAPIHelper::getApiKey();

		if(empty($key) === true)
			UniteFunctionsUC::throwError("Google API key is missing.");
	}

	/**
	 * validate google credentials
	 */
	private function validateGoogleCredentials(){

		$hasCredentials = $this->hasGoogleCredentials();

		if($hasCredentials === false)
			UniteFunctionsUC::throwError("Google credentials are missing.");
	}

	/**
	 * get exchange rate api key
	 */
	private function getExchangeRateApiKey(){

		$key = $this->getRequiredParam(self::SETTINGS_EXCHANGE_RATE_API_KEY, "Exchange Rate API key");

		return $key;
	}

	/**
	 * get open weather api key
	 */
	private function getOpenWeatherApiKey(){

		$key = $this->getRequiredParam(self::SETTINGS_OPEN_WEATHER_API_KEY, "OpenWeather API key");

		return $key;
	}

	/**
	 * get currency exchange settings fields
	 */
	private function getCurrencyExchangeSettingsFields(){

		$fields = array();

		$key = HelperProviderCoreUC_EL::getGeneralSetting(self::SETTINGS_EXCHANGE_RATE_API_KEY);

		$fields = $this->addEmptyApiKeyField($fields, $key, self::CURRENCY_EXCHANGE_FIELD_EMPTY_API_KEY, "Exchange Rate API");

		$fields = array_merge($fields, array(
			array(
				"id" => self::CURRENCY_EXCHANGE_FIELD_CURRENCY,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Currency Code", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Enter the three-letter <a href='%s' target='_blank'>currency code</a>.", "unlimited-elements-for-elementor"), "https://exchangerate-api.com/docs/supported-currencies"),
			),
			array(
				"id" => self::CURRENCY_EXCHANGE_FIELD_PRECISION,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Rate Precision", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the number of decimals for the rate: from %d to %d. The default value is %d.", "unlimited-elements-for-elementor"), self::CURRENCY_EXCHANGE_MIN_PRECISION, self::CURRENCY_EXCHANGE_MAX_PRECISION, self::CURRENCY_EXCHANGE_DEFAULT_PRECISION),
			),
			array(
				"id" => self::CURRENCY_EXCHANGE_FIELD_INCLUDE_CURRENCIES,
				"type" => UniteCreatorDialogParam::PARAM_TEXTAREA,
				"text" => __("Include Currencies", "unlimited-elements-for-elementor"),
				"desc" => __("Optional. You can specify a comma separated list of currency codes to include, otherwise all currencies will be displayed.", "unlimited-elements-for-elementor"),
			),
			array(
				"id" => self::CURRENCY_EXCHANGE_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::CURRENCY_EXCHANGE_DEFAULT_CACHE_TIME),
				"default" => self::CURRENCY_EXCHANGE_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get google events settings fields
	 */
	private function getGoogleEventsSettingsFields(){

		$fields = array();

		if(GlobalsUnlimitedElements::$enableGoogleCalendarScopes === true)
			$fields = $this->addGoogleEmptyCredentialsField($fields, self::GOOGLE_EVENTS_FIELD_EMPTY_CREDENTIALS);
		else
			$fields = $this->addGoogleEmptyApiKeyField($fields, self::GOOGLE_EVENTS_FIELD_EMPTY_CREDENTIALS);

		$fields = array_merge($fields, array(
			array(
				"id" => self::GOOGLE_EVENTS_FIELD_CALENDAR_ID,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Calendar ID", "unlimited-elements-for-elementor"),
				"desc" => __("You can find the calendar ID on a calendar's \"Settings\" page under \"Integrate Calendar\".", "unlimited-elements-for-elementor"),
			),
			array(
				"id" => self::GOOGLE_EVENTS_FIELD_RANGE,
				"type" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"text" => __("Date Range", "unlimited-elements-for-elementor"),
				"options" => array(
					self::GOOGLE_EVENTS_RANGE_UPCOMING => __("Upcoming", "unlimited-elements-for-elementor"),
					self::GOOGLE_EVENTS_RANGE_TODAY => __("Today's", "unlimited-elements-for-elementor"),
					self::GOOGLE_EVENTS_RANGE_TOMORROW => __("Tomorrow's", "unlimited-elements-for-elementor"),
					self::GOOGLE_EVENTS_RANGE_WEEK => __("This week", "unlimited-elements-for-elementor"),
					self::GOOGLE_EVENTS_RANGE_MONTH => __("This month", "unlimited-elements-for-elementor"),
				),
				"default" => self::GOOGLE_EVENTS_RANGE_UPCOMING,
			),
			array(
				"id" => self::GOOGLE_EVENTS_FIELD_ORDER,
				"type" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"text" => __("Order By", "unlimited-elements-for-elementor"),
				"options" => array(
					self::GOOGLE_EVENTS_ORDER_DATE_DESC => __("Date (newest)", "unlimited-elements-for-elementor"),
					self::GOOGLE_EVENTS_ORDER_DATE_ASC => __("Date (oldest)", "unlimited-elements-for-elementor"),
				),
				"default" => self::GOOGLE_EVENTS_ORDER_DATE_DESC,
			),
			array(
				"id" => self::GOOGLE_EVENTS_FIELD_LIMIT,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Events Limit", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the maximum number of events: from 1 to 2500. The default value is %d.", "unlimited-elements-for-elementor"), self::GOOGLE_EVENTS_DEFAULT_LIMIT),
				"default" => self::GOOGLE_EVENTS_DEFAULT_LIMIT,
			),
			array(
				"id" => self::GOOGLE_EVENTS_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::GOOGLE_EVENTS_DEFAULT_CACHE_TIME),
				"default" => self::GOOGLE_EVENTS_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get google reviews settings fields
	 */
	private function getGoogleReviewsSettingsFields(){

		$fields = array();

		$fields = $this->addGoogleEmptyApiKeyField($fields, self::GOOGLE_REVIEWS_FIELD_EMPTY_API_KEY);

		$fields = array_merge($fields, array(
			array(
				"id" => self::GOOGLE_REVIEWS_FIELD_PLACE_ID,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Place ID", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("You can find the place ID by using <a href='%s' target='_blank'>Place ID Finder</a>.", "unlimited-elements-for-elementor"), "https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder"),
			),
			array(
				"id" => self::GOOGLE_REVIEWS_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::GOOGLE_REVIEWS_DEFAULT_CACHE_TIME),
				"default" => self::GOOGLE_REVIEWS_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get google sheets settings fields
	 */
	private function getGoogleSheetsSettingsFields(){

		$fields = array();

		$fields = $this->addGoogleEmptyCredentialsField($fields, self::GOOGLE_SHEETS_FIELD_EMPTY_CREDENTIALS);

		$fields = array_merge($fields, array(
			array(
				"id" => self::GOOGLE_SHEETS_FIELD_ID,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Spreadsheet ID", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("You can find the spreadsheet ID in a Google Sheets URL: %s", "unlimited-elements-for-elementor"), "https://docs.google.com/spreadsheets/d/<b>[YOUR_SPREADSHEET_ID]</b>/edit#gid=0"),
			),
			array(
				"id" => self::GOOGLE_SHEETS_FIELD_SHEET_ID,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Sheet ID", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can find the sheet ID in a Google Sheets URL: %s", "unlimited-elements-for-elementor"), "https://docs.google.com/spreadsheets/d/aBC-123_xYz/edit#gid=<b>[YOUR_SHEET_ID]</b>"),
			),
			array(
				"id" => self::GOOGLE_SHEETS_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::GOOGLE_SHEETS_DEFAULT_CACHE_TIME),
				"default" => self::GOOGLE_SHEETS_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get weather forecast settings fields
	 */
	private function getWeatherForecastSettingsFields(){

		$fields = array();

		$key = HelperProviderCoreUC_EL::getGeneralSetting(self::SETTINGS_OPEN_WEATHER_API_KEY);

		$fields = $this->addEmptyApiKeyField($fields, $key, self::WEATHER_FORECAST_FIELD_EMPTY_API_KEY, "OpenWeather API");

		$fields = array_merge($fields, array(
			array(
				"id" => self::WEATHER_FORECAST_FIELD_COUNTRY,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Country Code", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Specify the two-letter <a href='%s' target='_blank'>country code</a>.", "unlimited-elements-for-elementor"), "https://en.wikipedia.org/wiki/ISO_3166-2#Current_codes"),
			),
			array(
				"id" => self::WEATHER_FORECAST_FIELD_CITY,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("City Name", "unlimited-elements-for-elementor"),
			),
			array(
				"id" => self::WEATHER_FORECAST_FIELD_UNITS,
				"type" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"text" => __("Units", "unlimited-elements-for-elementor"),
				"options" => array(
					self::WEATHER_FORECAST_UNITS_METRIC => __("Metric", "unlimited-elements-for-elementor"),
					self::WEATHER_FORECAST_UNITS_IMPERIAL => __("Imperial", "unlimited-elements-for-elementor"),
				),
				"default" => self::WEATHER_FORECAST_UNITS_METRIC,
			),
			array(
				"id" => self::WEATHER_FORECAST_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::CURRENCY_EXCHANGE_DEFAULT_CACHE_TIME),
				"default" => self::WEATHER_FORECAST_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get youtube playlist settings fields
	 */
	private function getYoutubePlaylistSettingsFields(){

		$fields = array();

		if(GlobalsUnlimitedElements::$enableGoogleYoutubeScopes === true)
			$fields = $this->addGoogleEmptyCredentialsField($fields, self::YOUTUBE_PLAYLIST_FIELD_EMPTY_CREDENTIALS);
		else
			$fields = $this->addGoogleEmptyApiKeyField($fields, self::YOUTUBE_PLAYLIST_FIELD_EMPTY_CREDENTIALS);

		$fields = array_merge($fields, array(
			array(
				"id" => self::YOUTUBE_PLAYLIST_FIELD_ID,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Playlist ID", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("You can find the playlist ID in a YouTube URL: <br />— %s<br />— %s", "unlimited-elements-for-elementor"), "https://youtube.com/playlist?list=<b>[YOUR_PLAYLIST_ID]</b>", "https://youtube.com/watch?v=aBC-123xYz&list=<b>[YOUR_PLAYLIST_ID]</b>"),
			),
			array(
				"id" => self::YOUTUBE_PLAYLIST_FIELD_ORDER,
				"type" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"text" => __("Order By", "unlimited-elements-for-elementor"),
				"options" => array(
					self::YOUTUBE_PLAYLIST_ORDER_DEFAULT => __("Default", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_DESC => __("Date added (newest)", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_ASC => __("Date added (oldest)", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_RANDOM => __("Date added (random)", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_DESC => __("Date published (newest)", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_ASC => __("Date published (oldest)", "unlimited-elements-for-elementor"),
					self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_RANDOM => __("Date published (random)", "unlimited-elements-for-elementor"),
				),
				"default" => self::YOUTUBE_PLAYLIST_ORDER_DEFAULT,
			),
			array(
				"id" => self::YOUTUBE_PLAYLIST_FIELD_LIMIT,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Videos Limit", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the maximum number of videos: from 1 to 50. The default value is %d.", "unlimited-elements-for-elementor"), self::YOUTUBE_PLAYLIST_DEFAULT_LIMIT),
				"default" => self::YOUTUBE_PLAYLIST_DEFAULT_LIMIT,
			),
			array(
				"id" => self::YOUTUBE_PLAYLIST_FIELD_CACHE_TIME,
				"type" => UniteCreatorDialogParam::PARAM_TEXTFIELD,
				"text" => __("Cache Time", "unlimited-elements-for-elementor"),
				"desc" => sprintf(__("Optional. You can specify the cache time of results in minutes. The default value is %d minutes.", "unlimited-elements-for-elementor"), self::YOUTUBE_PLAYLIST_DEFAULT_CACHE_TIME),
				"default" => self::YOUTUBE_PLAYLIST_DEFAULT_CACHE_TIME,
			),
		));

		return $fields;
	}

	/**
	 * get currency exchange data
	 */
	private function getCurrencyExchangeData(){

		$data = array();

		$currency = $this->getRequiredParam(self::CURRENCY_EXCHANGE_FIELD_CURRENCY, "Currency");
		$precision = $this->getParam(self::CURRENCY_EXCHANGE_FIELD_PRECISION, self::CURRENCY_EXCHANGE_DEFAULT_PRECISION);
		$includeCurrencies = $this->getParam(self::CURRENCY_EXCHANGE_FIELD_INCLUDE_CURRENCIES, "");
		$cacheTime = $this->getCacheTimeParam(self::CURRENCY_EXCHANGE_FIELD_CACHE_TIME, self::CURRENCY_EXCHANGE_DEFAULT_CACHE_TIME);

		$precision = intval($precision);
		$precision = max($precision, self::CURRENCY_EXCHANGE_MIN_PRECISION);
		$precision = min($precision, self::CURRENCY_EXCHANGE_MAX_PRECISION);

		$includeCurrencies = strtoupper($includeCurrencies);
		$includeCurrencies = explode(",", $includeCurrencies);
		$includeCurrencies = array_map("trim", $includeCurrencies);
		$includeCurrencies = array_filter($includeCurrencies);
		$includeCurrencies = array_unique($includeCurrencies);

		$exchangeService = new UEExchangeRateAPIClient($this->getExchangeRateApiKey());
		$exchangeService->setCacheTime($cacheTime);

		$rates = $exchangeService->getRates($currency);

		foreach($rates as $rate){
			$code = $rate->getCode();

			$data[$code] = array(
				"id" => $rate->getId(),
				"code" => $code,
				"name" => $rate->getName(),
				"symbol" => $rate->getSymbol(),
				"flag" => $rate->getFlagUrl(),
				"rate" => $rate->getRate($precision),
			);
		}

		$filteredData = array();

		if(empty($includeCurrencies) === true)
			$filteredData = UniteFunctionsUC::assocToArray($data);

		foreach($includeCurrencies as $code){
			$rate = UniteFunctionsUC::getVal($data, $code);

			if(empty($rate) === true)
				continue;

			$filteredData[] = $rate;
		}

		$baseCode = strtoupper($currency);
		$baseRate = UniteFunctionsUC::getVal($data, $baseCode);

		$data = UniteFunctionsUC::assocToArray($data);
		$filteredDataJson = UniteFunctionsUC::jsonEncodeForClientSide($filteredData);

		$output = array();
		$output["base"] = $baseRate;
		$output["rates_all"] = $data;
		$output["rates_chosen"] = $filteredData;
		$output["rates_chosen_json"] = $filteredDataJson;

		return $output;
	}

	/**
	 * get google events data
	 */
	private function getGoogleEventsData(){

		$data = array();

		if(GlobalsUnlimitedElements::$enableGoogleCalendarScopes === true)
			$this->validateGoogleCredentials();
		else
			$this->validateGoogleApiKey();

		$calendarId = $this->getRequiredParam(self::GOOGLE_EVENTS_FIELD_CALENDAR_ID, "Calendar ID");
		$eventsRange = $this->getParam(self::GOOGLE_EVENTS_FIELD_RANGE);
		$eventsRange = $this->getGoogleEventsDatesRange($eventsRange);
		$eventsOrder = $this->getParam(self::GOOGLE_EVENTS_FIELD_ORDER);
		$eventsLimit = $this->getParam(self::GOOGLE_EVENTS_FIELD_LIMIT, self::GOOGLE_EVENTS_DEFAULT_LIMIT);
		$eventsLimit = intval($eventsLimit);
		$cacheTime = $this->getCacheTimeParam(self::GOOGLE_EVENTS_FIELD_CACHE_TIME, self::GOOGLE_EVENTS_DEFAULT_CACHE_TIME);

		$orderFieldMap = array(
			self::GOOGLE_EVENTS_ORDER_DATE_ASC => "date",
			self::GOOGLE_EVENTS_ORDER_DATE_DESC => "date",
		);

		$orderDirectionMap = array(
			self::GOOGLE_EVENTS_ORDER_DATE_ASC => self::ORDER_DIRECTION_ASC,
			self::GOOGLE_EVENTS_ORDER_DATE_DESC => self::ORDER_DIRECTION_DESC,
		);

		$orderField = isset($orderFieldMap[$eventsOrder]) ? $orderFieldMap[$eventsOrder] : null;
		$orderDirection = isset($orderDirectionMap[$eventsOrder]) ? $orderDirectionMap[$eventsOrder] : null;

		$calendarService = new UEGoogleAPICalendarService();
		$calendarService->setCacheTime($cacheTime);

		if(GlobalsUnlimitedElements::$enableGoogleCalendarScopes === true)
			$this->authorizeGoogleService($calendarService);
		else
			$this->authorizeGoogleServiceWithApiKey($calendarService);

		$eventsParams = array(
			"singleEvents" => "true",
			"orderBy" => "startTime",
			"maxResults" => $eventsLimit,
		);

		if(isset($eventsRange["start"]) === true)
			$eventsParams["timeMin"] = $eventsRange["start"];

		if(isset($eventsRange["end"]) === true)
			$eventsParams["timeMax"] = $eventsRange["end"];

		$events = $calendarService->getEvents($calendarId, $eventsParams);

		foreach($events as $event){
			$orderValue = ($orderField === "date")
				? $event->getStartDate(self::FORMAT_MYSQL_DATETIME)
				: null;

			$data[] = array(
				"id" => $event->getId(),
				"start_date" => $event->getStartDate(self::FORMAT_DATETIME),
				"end_date" => $event->getEndDate(self::FORMAT_DATETIME),
				"title" => $event->getTitle(),
				"description" => $event->getDescription(true),
				"location" => $event->getLocation(),
				"link" => $event->getUrl(),
				self::ORDER_FIELD => $orderValue,
			);
		}

		$data = $this->sortData($data, $orderDirection);

		return $data;
	}

	/**
	 * get google events dates range
	 */
	private function getGoogleEventsDatesRange($key){

		$currentTime = current_time("timestamp");
		$startTime = null;
		$endTime = null;

		switch($key){
			case self::GOOGLE_EVENTS_RANGE_UPCOMING:
				$startTime = strtotime("now", $currentTime);
			break;
			case self::GOOGLE_EVENTS_RANGE_TODAY:
				$startTime = strtotime("today", $currentTime);
				$endTime = strtotime("tomorrow", $startTime);
			break;
			case self::GOOGLE_EVENTS_RANGE_TOMORROW:
				$startTime = strtotime("tomorrow", $currentTime);
				$endTime = strtotime("tomorrow", $startTime);
			break;
			case self::GOOGLE_EVENTS_RANGE_WEEK:
				$startTime = strtotime("this week midnight", $currentTime);
				$endTime = strtotime("next week midnight", $currentTime);
			break;
			case self::GOOGLE_EVENTS_RANGE_MONTH:
				$startTime = strtotime("first day of this month midnight", $currentTime);
				$endTime = strtotime("first day of next month midnight", $currentTime);
			break;
		}

		$range = array(
			"start" => $startTime ? date("c", $startTime) : null,
			"end" => $endTime ? date("c", $endTime) : null,
		);

		return $range;
	}

	/**
	 * get google reviews data
	 */
	private function getGoogleReviewsData(){

		$data = array();

		$this->validateGoogleApiKey();

		$placeId = $this->getRequiredParam(self::GOOGLE_REVIEWS_FIELD_PLACE_ID, "Place ID");
		$cacheTime = $this->getCacheTimeParam(self::GOOGLE_REVIEWS_FIELD_CACHE_TIME, self::GOOGLE_REVIEWS_DEFAULT_CACHE_TIME);

		$placesService = new UEGoogleAPIPlacesService();
		$placesService->setCacheTime($cacheTime);

		$this->authorizeGoogleServiceWithApiKey($placesService);

		$place = $placesService->getDetails($placeId, array(
			"fields" => "reviews",
			"reviews_sort" => "newest",
		));

		foreach($place->getReviews() as $review){
			$data[] = array(
				"id" => $review->getId(),
				"date" => $review->getDate(self::FORMAT_DATETIME),
				"text" => $review->getText(true),
				"rating" => $review->getRating(),
				"author_name" => $review->getAuthorName(),
				"author_photo" => $review->getAuthorPhotoUrl(),
			);
		}

		return $data;
	}

	/**
	 * get google sheets data
	 */
	private function getGoogleSheetsData(){

		$data = array();

		$this->validateGoogleCredentials();

		$spreadsheetId = $this->getRequiredParam(self::GOOGLE_SHEETS_FIELD_ID, "Spreadsheet ID");
		$sheetId = $this->getParam(self::GOOGLE_SHEETS_FIELD_SHEET_ID, 0);
		$sheetId = intval($sheetId);
		$cacheTime = $this->getCacheTimeParam(self::GOOGLE_SHEETS_FIELD_CACHE_TIME, self::GOOGLE_SHEETS_DEFAULT_CACHE_TIME);

		$sheetsService = new UEGoogleAPISheetsService();
		$sheetsService->setCacheTime($cacheTime);

		$this->authorizeGoogleService($sheetsService);

		// get sheet title for the range
		$spreadsheet = $sheetsService->getSpreadsheet($spreadsheetId);
		$range = null;

		foreach($spreadsheet->getSheets() as $sheet){
			if($sheet->getId() === $sheetId){
				$range = $sheet->getTitle();

				break;
			}
		}

		// get spreadsheet values
		$spreadsheet = $sheetsService->getSpreadsheetValues($spreadsheetId, $range);
		$values = $spreadsheet->getValues();

		$headers = array_shift($values); // extract first row as headers

		foreach($values as $rowIndex => $row){
			$attributes = array("id" => $rowIndex + 1);

			foreach($headers as $columnIndex => $header){
				if(empty($row[$columnIndex]))
					continue 2; // continue both loops

				$attributes[$header] = $row[$columnIndex];
			}

			$data[] = $attributes;
		}

		return $data;
	}

	/**
	 * get weather forecast data
	 */
	private function getWeatherForecastData(){

		$data = array();

		$country = $this->getRequiredParam(self::WEATHER_FORECAST_FIELD_COUNTRY, "Country");
		$city = $this->getRequiredParam(self::WEATHER_FORECAST_FIELD_CITY, "City");
		$units = $this->getRequiredParam(self::WEATHER_FORECAST_FIELD_UNITS, "Units");
		$cacheTime = $this->getCacheTimeParam(self::WEATHER_FORECAST_FIELD_CACHE_TIME, self::WEATHER_FORECAST_DEFAULT_CACHE_TIME);

		$weatherService = new UEOpenWeatherAPIClient($this->getOpenWeatherApiKey());
		$weatherService->setCacheTime($cacheTime);

		$forecasts = $weatherService->getDailyForecast($country, $city, $units);

		foreach($forecasts as $forecast){
			$data[] = array(
				"id" => $forecast->getId(),
				"date" => $forecast->getDate(self::FORMAT_DATE),
				"description" => $forecast->getDescription(),
				"temp_min" => $forecast->getMinTemperature(),
				"temp_max" => $forecast->getMaxTemperature(),
				"temp_morning" => $forecast->getMorningTemperature(),
				"temp_day" => $forecast->getDayTemperature(),
				"temp_evening" => $forecast->getEveningTemperature(),
				"temp_night" => $forecast->getNightTemperature(),
				"feels_like_morning" => $forecast->getMorningFeelsLike(),
				"feels_like_day" => $forecast->getDayFeelsLike(),
				"feels_like_evening" => $forecast->getEveningFeelsLike(),
				"feels_like_night" => $forecast->getNightFeelsLike(),
				"wind_speed" => $forecast->getWindSpeed(),
				"wind_degree" => $forecast->getWindDegrees(),
				"wind_gust" => $forecast->getWindGust(),
				"pressure" => $forecast->getPressure(),
				"humidity" => $forecast->getHumidity(),
				"cloudiness" => $forecast->getCloudiness(),
				"rain" => $forecast->getRain(),
				"uvi" => $forecast->getUvi(),
			);
		}

		return $data;
	}

	/**
	 * get youtube playlist data
	 */
	private function getYoutubePlaylistData(){

		$data = array();

		if(GlobalsUnlimitedElements::$enableGoogleYoutubeScopes === true)
			$this->validateGoogleCredentials();
		else
			$this->validateGoogleApiKey();

		$playlistId = $this->getRequiredParam(self::YOUTUBE_PLAYLIST_FIELD_ID, "Playlist ID");
		$itemsOrder = $this->getParam(self::YOUTUBE_PLAYLIST_FIELD_ORDER);
		$itemsLimit = $this->getParam(self::YOUTUBE_PLAYLIST_FIELD_LIMIT, self::YOUTUBE_PLAYLIST_DEFAULT_LIMIT);
		$itemsLimit = intval($itemsLimit);
		$cacheTime = $this->getCacheTimeParam(self::YOUTUBE_PLAYLIST_FIELD_CACHE_TIME, self::YOUTUBE_PLAYLIST_DEFAULT_CACHE_TIME);

		$orderFieldMap = array(
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_ASC => "date",
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_DESC => "date",
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_RANDOM => "date",
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_ASC => "video_date",
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_DESC => "video_date",
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_RANDOM => "video_date",
		);

		$orderDirectionMap = array(
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_ASC => self::ORDER_DIRECTION_ASC,
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_DESC => self::ORDER_DIRECTION_DESC,
			self::YOUTUBE_PLAYLIST_ORDER_DATE_ADDED_RANDOM => self::ORDER_DIRECTION_RANDOM,
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_ASC => self::ORDER_DIRECTION_ASC,
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_DESC => self::ORDER_DIRECTION_DESC,
			self::YOUTUBE_PLAYLIST_ORDER_DATE_PUBLISHED_RANDOM => self::ORDER_DIRECTION_RANDOM,
		);

		$orderField = isset($orderFieldMap[$itemsOrder]) ? $orderFieldMap[$itemsOrder] : null;
		$orderDirection = isset($orderDirectionMap[$itemsOrder]) ? $orderDirectionMap[$itemsOrder] : null;

		$youtubeService = new UEGoogleAPIYouTubeService();
		$youtubeService->setCacheTime($cacheTime);

		if(GlobalsUnlimitedElements::$enableGoogleYoutubeScopes === true)
			$this->authorizeGoogleService($youtubeService);
		else
			$this->authorizeGoogleServiceWithApiKey($youtubeService);

		$items = $youtubeService->getPlaylistItems($playlistId, array("maxResults" => $itemsLimit));

		foreach($items as $item){
			$orderValue = ($orderField === "date")
				? $item->getDate(self::FORMAT_MYSQL_DATETIME)
				: $item->getVideoDate(self::FORMAT_MYSQL_DATETIME);

			$data[] = array(
				"id" => $item->getId(),
				"date" => $item->getDate(self::FORMAT_DATETIME),
				"title" => $item->getTitle(),
				"description" => $item->getDescription(true),
				"image" => $item->getImageUrl(UEGoogleAPIPlaylistItem::IMAGE_SIZE_MAX),
				"video_id" => $item->getVideoId(),
				"video_date" => $item->getVideoDate(self::FORMAT_DATETIME),
				"video_link" => $item->getVideoUrl(),
				self::ORDER_FIELD => $orderValue,
			);
		}

		$data = $this->sortData($data, $orderDirection);

		return $data;
	}

	/**
	 * sort the data
	 */
	private function sortData($data, $direction){

		$field = self::ORDER_FIELD;

		usort($data, function($a, $b) use ($field, $direction){

			if(isset($a[$field]) === false || isset($b[$field]) === false)
				return 0;

			if($a[$field] == $b[$field])
				return 0;

			switch($direction){
				case self::ORDER_DIRECTION_RANDOM:
					$results = array(rand(-1, 1), rand(-1, 1));
				break;
				case self::ORDER_DIRECTION_DESC:
					$results = array(1, -1);
				break;
				default: // asc
					$results = array(-1, 1);
				break;
			}

			if(is_numeric($a[$field]) && is_numeric($b[$field]))
				return ($a[$field] < $b[$field]) ? $results[0] : $results[1];

			return (strcmp($a[$field], $b[$field]) <= 0) ? $results[0] : $results[1];
		});

		foreach($data as &$values){
			unset($values[$field]);
		}

		return $data;
	}

	/**
	 * add empty api key field
	 */
	private function addEmptyApiKeyField($fields, $key, $id, $name){

		if(empty($key) === true){
			$fields[] = array(
				"id" => $id,
				"type" => UniteCreatorDialogParam::PARAM_STATIC_TEXT,
				"text" => sprintf(__("%s key is missing. Please add the key in the \"General Settings > Integrations\".", "unlimited-elements-for-elementor"), $name),
			);
		}

		return $fields;
	}

	/**
	 * add google empty api key field
	 */
	private function addGoogleEmptyApiKeyField($fields, $id){

		$key = UEGoogleAPIHelper::getApiKey();

		$fields = $this->addEmptyApiKeyField($fields, $key, $id, "Google API");

		return $fields;
	}

	/**
	 * add google empty credentials field
	 */
	private function addGoogleEmptyCredentialsField($fields, $id){

		$hasCredentials = $this->hasGoogleCredentials();

		if($hasCredentials === false){
			$fields[] = array(
				"id" => $id,
				"type" => UniteCreatorDialogParam::PARAM_STATIC_TEXT,
				"text" => __("Google credentials are missing. Please connect to Google or add an API key in the \"General Settings > Integrations\".", "unlimited-elements-for-elementor"),
			);
		}

		return $fields;
	}

}
