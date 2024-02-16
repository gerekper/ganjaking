<?php

class UEOpenWeatherAPIForecast extends UEOpenWeatherAPIModel{

	const UNITS_STANDARD = "standard";
	const UNITS_METRIC = "metric";
	const UNITS_IMPERIAL = "imperial";

	/**
	 * Get the identifier.
	 *
	 * @return int
	 */
	public function getId(){

		$id = $this->getTime();

		return $id;
	}

	/**
	 * Get the description.
	 *
	 * @return string
	 */
	public function getDescription(){

		$description = $this->getAttribute("summary");
		
		return $description;
	}

	/**
	 * Get the icon name.
	 *
	 * @return string
	 */
	public function getIconName(){
		
		$iconName = $this->getWeatherArrayAttribute("icon");
		
		return $iconName;
	}
	
	/**
	 * get current description
	 */
	public function getCurrentDescription(){
		
		$description = $this->getWeatherArrayAttribute("description");
		
		return $description;
	}
	
	/**
	 * get current description
	 */
	public function getCurrentState(){
		
		$state = $this->getWeatherArrayAttribute("main");
		
		return $state;
	}
	
	
	/**
	 * Get the icon URL.
	 *
	 * @return string
	 */
	public function getIconUrl(){

		$name = $this->getIconName();
		$url = "https://openweathermap.org/img/wn/" . $name . "@2x.png";

		return $url;
	}

	/**
	 * Get the pressure.
	 *
	 * @return string
	 */
	public function getPressure(){

		$pressure = $this->getAttribute("pressure");
		$pressure = sprintf(__("%s hPa", "unlimited-elements-for-elementor"), $pressure);

		return $pressure;
	}

	/**
	 * Get the humidity.
	 *
	 * @return string
	 */
	public function getHumidity(){

		$humidity = $this->getAttribute("humidity");
		$humidity = $this->formatPercentage($humidity);

		return $humidity;
	}

	/**
	 * Get the cloudiness.
	 *
	 * @return string
	 */
	public function getCloudiness(){

		$cloudiness = $this->getAttribute("clouds");
		$cloudiness = $this->formatPercentage($cloudiness);

		return $cloudiness;
	}

	/**
	 * Get the rain.
	 *
	 * @return string
	 */
	public function getRain(){

		$rain = $this->getAttribute("rain", 0);
		$rain = $this->formatPrecipitation($rain);

		return $rain;
	}

	/**
	 * Get the snow.
	 *
	 * @return string
	 */
	public function getSnow(){

		$snow = $this->getAttribute("snow", 0);
		$snow = $this->formatPrecipitation($snow);

		return $snow;
	}

	/**
	 * Get the UVI.
	 *
	 * @return float
	 */
	public function getUvi(){

		$uvi = $this->getAttribute("uvi");

		return $uvi;
	}

	/**
	 * Get the minimum temperature.
	 *
	 * @return string
	 */
	public function getMinTemperature(){

		$temperature = $this->getTemperature("min");

		return $temperature;
	}

	/**
	 * Get the maximum temperature.
	 *
	 * @return string
	 */
	public function getMaxTemperature(){

		$temperature = $this->getTemperature("max");

		return $temperature;
	}

	/**
	 * Get the morning temperature.
	 *
	 * @return string
	 */
	public function getMorningTemperature(){

		$temperature = $this->getTemperature("morn");

		return $temperature;
	}

	/**
	 * Get the day temperature.
	 *
	 * @return string
	 */
	public function getDayTemperature(){

		$temperature = $this->getTemperature("day");

		return $temperature;
	}

	/**
	 * Get the evening temperature.
	 *
	 * @return string
	 */
	public function getEveningTemperature(){

		$temperature = $this->getTemperature("eve");

		return $temperature;
	}

	/**
	 * Get the night temperature.
	 *
	 * @return string
	 */
	public function getNightTemperature(){

		$temperature = $this->getTemperature("night");

		return $temperature;
	}

	
	
	/**
	 * get current temperature
	 */
	public function getCurrentTemperature(){
		
		$temperature = $this->getAttributeTemperature("temp");
		
		return($temperature);
	}

	/**
	 * get current feels like
	 */
	public function getCurrentFeelsLike(){
		
		$temperature = $this->getAttributeTemperature("feels_like");
		
		return($temperature);
	}
	
	
	/**
	 * Get the morning "feels like" temperature.
	 *
	 * @return string
	 */
	public function getMorningFeelsLike(){

		$temperature = $this->getFeelsLike("morn");

		return $temperature;
	}

	/**
	 * Get the day "feels like" temperature.
	 *
	 * @return string
	 */
	public function getDayFeelsLike(){

		$temperature = $this->getFeelsLike("day");

		return $temperature;
	}

	/**
	 * Get the evening "feels like" temperature.
	 *
	 * @return string
	 */
	public function getEveningFeelsLike(){

		$temperature = $this->getFeelsLike("eve");

		return $temperature;
	}

	/**
	 * Get the night "feels like" temperature.
	 *
	 * @return string
	 */
	public function getNightFeelsLike(){

		$temperature = $this->getFeelsLike("night");

		return $temperature;
	}

	/**
	 * Get the wind speed.
	 *
	 * @return string
	 */
	public function getWindSpeed(){

		$speed = $this->getAttribute("wind_speed");
		$speed = $this->formatSpeed($speed);

		return $speed;
	}

	/**
	 * Get the wind degrees.
	 *
	 * @return int
	 */
	public function getWindDegrees(){

		$degrees = $this->getAttribute("wind_deg");

		return $degrees;
	}

	/**
	 * Get the wind gust.
	 *
	 * @return string
	 */
	public function getWindGust(){

		$gust = $this->getAttribute("wind_gust");
		$gust = $this->formatSpeed($gust);

		return $gust;
	}

	/**
	 * Get the date.
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getDate($format){
		
		$time = $this->getTime();
		$date = $this->formatTime($time,$format); 

		return $date;
	}
	
	/**
	 * get sunrise
	 */
	public function getSunrise(){
		
		$sunrise = $this->getAttribute("sunrise");
		
		$sunrise = $this->formatTime($sunrise,"H:i");
				
		return($sunrise);
	}
	
	/**
	 * get sunrise
	 */
	public function getSunset(){
		
		$sunset = $this->getAttribute("sunset");

		$sunset = $this->formatTime($sunset,"H:i");
		
		return($sunset);
	}

	
	/**
	 * format hours for sunset
	 */
	private function formatTime($timestemp, $format){
		
		$timezone = $this->getParameter("timezone");
		
		$date = new DateTime();
		$objTimezone = new DateTimeZone($timezone);
		
		$date->setTimestamp($timestemp);
		
		$date->setTimezone($objTimezone);
		
		$hours = $date->format($format);
		
		return($hours);
	}
	
	/**
	 * Get the current description.
	 *
	 * @return string
	 */
	private function getWeatherArrayAttribute($key){
		
		$weather = $this->getAttribute("weather");
		$weather = UniteFunctionsUC::getVal($weather, 0, array()); // the first weather condition is primary
		$value = UniteFunctionsUC::getVal($weather, $key);
		
		return $value;
	}
	
	
	/**
	 * get temperature from numeric attribute
	 */
	private function getAttributeTemperature($key){
		
		$temperature = $this->getAttribute($key);
		$temperature = $this->formatTemperature($temperature);
		
		return($temperature);
	}

	/**
	 * Get the temperature.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return string
	 */
	private function getTemperature($key, $fallback = null){
		
		$temperature = $this->getAttribute("temp");
		
		$temperature = UniteFunctionsUC::getVal($temperature, $key, $fallback);
		
		$temperature = $this->formatTemperature($temperature);

		return $temperature;
	}

	/**
	 * Get the "feels like" temperature.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return string
	 */
	private function getFeelsLike($key, $fallback = null){

		$temperature = $this->getAttribute("feels_like");
		$temperature = UniteFunctionsUC::getVal($temperature, $key, $fallback);
		$temperature = $this->formatTemperature($temperature);

		return $temperature;
	}

	/**
	 * Get the time.
	 *
	 * @return int
	 */
	private function getTime(){

		$time = $this->getAttribute("dt");

		return $time;
	}

	/**
	 * Get the units.
	 *
	 * @return string
	 */
	private function getUnits(){

		$units = $this->getParameter("units");

		return $units;
	}

	/**
	 * Format the percentage.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function formatPercentage($value){

		return sprintf(__("%s%%", "unlimited-elements-for-elementor"), $value);
	}

	/**
	 * Format the precipitation.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function formatPrecipitation($value){
		
		if(is_array($value))
			$value = UniteFunctionsUC::getArrFirstValue($value);
		
		if(is_array($value))
			$value = 0;
					
		return sprintf(__("%s mm", "unlimited-elements-for-elementor"), $value);
	}

	/**
	 * Format the speed.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function formatSpeed($value){

		switch($this->getUnits()){
			case self::UNITS_IMPERIAL:
				return sprintf(__("%s mph", "unlimited-elements-for-elementor"), $value);
			default:
				return sprintf(__("%s m/s", "unlimited-elements-for-elementor"), $value);
		}
	}

	/**
	 * Format the temperature.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function formatTemperature($value){
		
		if(is_numeric($value))
			$value = round($value);
				
		return sprintf(__("%s°", "unlimited-elements-for-elementor"), $value);
		
		/*
		switch($this->getUnits()){
			case self::UNITS_METRIC:
				return sprintf(__("%s°C", "unlimited-elements-for-elementor"), $value);
			case self::UNITS_IMPERIAL:
				return sprintf(__("%s°F", "unlimited-elements-for-elementor"), $value);
			default:
				return sprintf(__("%sK", "unlimited-elements-for-elementor"), $value);
		}
		*/
		
	}

}
