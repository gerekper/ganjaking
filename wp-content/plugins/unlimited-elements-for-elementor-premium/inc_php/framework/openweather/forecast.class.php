<?php

class UEOpenWeatherAPIForecast extends UEOpenWeatherAPIModel{

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
	 * Get the pressure.
	 *
	 * @return int
	 */
	public function getPressure(){

		$pressure = $this->getAttribute("pressure");

		return $pressure;
	}

	/**
	 * Get the humidity.
	 *
	 * @return int
	 */
	public function getHumidity(){

		$humidity = $this->getAttribute("humidity");

		return $humidity;
	}

	/**
	 * Get the cloudiness.
	 *
	 * @return int
	 */
	public function getCloudiness(){

		$cloudiness = $this->getAttribute("clouds");

		return $cloudiness;
	}

	/**
	 * Get the rain.
	 *
	 * @return float
	 */
	public function getRain(){

		$rain = $this->getAttribute("rain", 0);

		return $rain;
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
	 * @return float
	 */
	public function getMinTemperature(){

		$temperature = $this->getTemperature("min");

		return $temperature;
	}

	/**
	 * Get the maximum temperature.
	 *
	 * @return float
	 */
	public function getMaxTemperature(){

		$temperature = $this->getTemperature("max");

		return $temperature;
	}

	/**
	 * Get the morning temperature.
	 *
	 * @return float
	 */
	public function getMorningTemperature(){

		$temperature = $this->getTemperature("morn");

		return $temperature;
	}

	/**
	 * Get the day temperature.
	 *
	 * @return float
	 */
	public function getDayTemperature(){

		$temperature = $this->getTemperature("day");

		return $temperature;
	}

	/**
	 * Get the evening temperature.
	 *
	 * @return float
	 */
	public function getEveningTemperature(){

		$temperature = $this->getTemperature("eve");

		return $temperature;
	}

	/**
	 * Get the night temperature.
	 *
	 * @return float
	 */
	public function getNightTemperature(){

		$temperature = $this->getTemperature("night");

		return $temperature;
	}

	/**
	 * Get the morning "feels like" temperature.
	 *
	 * @return float
	 */
	public function getMorningFeelsLike(){

		$temperature = $this->getFeelsLike("morn");

		return $temperature;
	}

	/**
	 * Get the day "feels like" temperature.
	 *
	 * @return float
	 */
	public function getDayFeelsLike(){

		$temperature = $this->getFeelsLike("day");

		return $temperature;
	}

	/**
	 * Get the evening "feels like" temperature.
	 *
	 * @return float
	 */
	public function getEveningFeelsLike(){

		$temperature = $this->getFeelsLike("eve");

		return $temperature;
	}

	/**
	 * Get the night "feels like" temperature.
	 *
	 * @return float
	 */
	public function getNightFeelsLike(){

		$temperature = $this->getFeelsLike("night");

		return $temperature;
	}

	/**
	 * Get the wind speed.
	 *
	 * @return float
	 */
	public function getWindSpeed(){

		$speed = $this->getAttribute("wind_speed");

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
	 * @return float
	 */
	public function getWindGust(){

		$gust = $this->getAttribute("wind_gust");

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
		$date = date($format, $time);

		return $date;
	}

	/**
	 * Get the temperature.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return float
	 */
	private function getTemperature($key, $fallback = null){

		$temperature = $this->getAttribute("temp");
		$temperature = UniteFunctionsUC::getVal($temperature, $key, $fallback);

		return $temperature;
	}

	/**
	 * Get the "feels like" temperature.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return float
	 */
	private function getFeelsLike($key, $fallback = null){

		$temperature = $this->getAttribute("feels_like");
		$temperature = UniteFunctionsUC::getVal($temperature, $key, $fallback);

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

}
