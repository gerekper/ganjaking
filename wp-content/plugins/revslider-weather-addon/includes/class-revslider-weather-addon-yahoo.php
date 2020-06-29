<?php

/**

 * @author    ThemePunch <info@themepunch.com>

 * @link      http://www.themepunch.com/

 * @copyright 2016 ThemePunch

 */



if( !defined( 'ABSPATH') ) exit();



class RevAddOnWeatherYahoo {

	private $type;

	private $woeid;

	private $name;

	private $unit;

	

	public function __construct($type,$woeid,$name,$unit) {

		$this->type = $type;

		$this->woeid = $woeid;

		$this->name = $name;

		$this->unit = $unit;

	}



	/**

	 * Connects to Yahoo Weather API with certain parameters

	 * @since    1.0.0

	 */

	public static function get_weather_infos($type,$woeid,$name,$unit){

		if($type == "woeid"){

			$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20%3D%20' . $woeid . '%20and%20u="' . $unit . '"&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';


		}

		else {

			$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22' . $name . '%2C%20ak%22)%20and%20u="' . $unit . '"&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

		}

		$transient_name = 'revslider_' . md5($url);
		if ( false === ( $weather = get_transient( $transient_name) ) ){
			$weather = wp_remote_fopen($url);
			set_transient( $transient_name, $weather, 3600 );
		}
		$weather = json_decode($weather);

		if(!isset($weather->query->results->channel->item->condition->temp)){
			return "";
		} 
		else{
			return $weather;
		}

		
	}



	/**

	 * Helper Function to enable translation of weather condition texts

	 * @since    1.0.0

	 */

	public static function condition_lang($text){

		switch($text){

			case 'AM Clouds/PM Sun':

				return __('AM Clouds/PM Sun','revslider-weather-addon');

				break;

			case 'AM Drizzle':

				return __('AM Drizzle','revslider-weather-addon');

				break;

			case 'AM Drizzle/Wind':

				return __('AM Drizzle/Wind','revslider-weather-addon');

				break;

			case 'AM Fog/PM Clouds':

				return __('AM Fog/PM Clouds','revslider-weather-addon');

				break;

			case 'AM Fog/PM Sun':

				return __('AM Fog/PM Sun','revslider-weather-addon');

				break;

			case 'AM Ice':

				return __('AM Ice','revslider-weather-addon');

				break;

			case 'AM Light Rain':

				return __('AM Light Rain','revslider-weather-addon');

				break;

			case 'AM Light Rain/Wind':

				return __('AM Light Rain/Wind','revslider-weather-addon');

				break;

			case 'AM Light Snow':

				return __('AM Light Snow','revslider-weather-addon');

				break;

			case 'AM Rain':

				return __('AM Rain','revslider-weather-addon');

				break;

			case 'AM Rain/Snow Showers':

				return __('AM Rain/Snow Showers','revslider-weather-addon');

				break;

			case 'AM Rain/Snow':

				return __('AM Rain/Snow','revslider-weather-addon');

				break;

			case 'AM Rain/Snow/Wind':

				return __('AM Rain/Snow/Wind','revslider-weather-addon');

				break;

			case 'AM Rain/Wind':

				return __('AM Rain/Wind','revslider-weather-addon');

				break;

			case 'AM Showers':

				return __('AM Showers','revslider-weather-addon');

				break;

			case 'AM Showers/Wind':

				return __('AM Showers/Wind','revslider-weather-addon');

				break;

			case 'AM Snow Showers':

				return __('AM Snow Showers','revslider-weather-addon');

				break;

			case 'AM Snow':

				return __('AM Snow','revslider-weather-addon');

				break;

			case 'AM Thundershowers':

				return __('AM Thundershowers','revslider-weather-addon');

				break;

			case 'Blowing Snow':

				return __('Blowing Snow','revslider-weather-addon');

				break;

			case 'Clear':

				return __('Clear','revslider-weather-addon');

				break;

			case 'Clear/Windy':

				return __('Clear/Windy','revslider-weather-addon');

				break;

			case 'Clouds Early/Clearing Late':

				return __('Clouds Early/Clearing Late','revslider-weather-addon');

				break;

			case 'Cloudy':

				return __('Cloudy','revslider-weather-addon');

				break;

			case 'Cloudy/Wind':

				return __('Cloudy/Wind','revslider-weather-addon');

				break;

			case 'Cloudy/Windy':

				return __('Cloudy/Windy','revslider-weather-addon');

				break;

			case 'Drifting Snow':

				return __('Drifting Snow','revslider-weather-addon');

				break;

			case 'Drifting Snow/Windy':

				return __('Drifting Snow/Windy','revslider-weather-addon');

				break;

			case 'Drizzle Early':

				return __('Drizzle Early','revslider-weather-addon');

				break;

			case 'Drizzle Late':

				return __('Drizzle Late','revslider-weather-addon');

				break;

			case 'Drizzle':

				return __('Drizzle','revslider-weather-addon');

				break;

			case 'Drizzle/Fog':

				return __('Drizzle/Fog','revslider-weather-addon');

				break;

			case 'Drizzle/Wind':

				return __('Drizzle/Wind','revslider-weather-addon');

				break;

			case 'Drizzle/Windy':

				return __('Drizzle/Windy','revslider-weather-addon');

				break;

			case 'Fair':

				return __('Fair','revslider-weather-addon');

				break;

			case 'Fair/Windy':

				return __('Fair/Windy','revslider-weather-addon');

				break;

			case 'Few Showers':

				return __('Few Showers','revslider-weather-addon');

				break;

			case 'Few Showers/Wind':

				return __('Few Showers/Wind','revslider-weather-addon');

				break;

			case 'Few Snow Showers':

				return __('Few Snow Showers','revslider-weather-addon');

				break;

			case 'Fog Early/Clouds Late':

				return __('Fog Early/Clouds Late','revslider-weather-addon');

				break;

			case 'Fog Late':

				return __('Fog Late','revslider-weather-addon');

				break;

			case 'Fog':

				return __('Fog','revslider-weather-addon');

				break;

			case 'Fog/Windy':

				return __('Fog/Windy','revslider-weather-addon');

				break;

			case 'Foggy':

				return __('Foggy','revslider-weather-addon');

				break;

			case 'Freezing Drizzle':

				return __('Freezing Drizzle','revslider-weather-addon');

				break;

			case 'Freezing Drizzle/Windy':

				return __('Freezing Drizzle/Windy','revslider-weather-addon');

				break;

			case 'Freezing Rain':

				return __('Freezing Rain','revslider-weather-addon');

				break;

			case 'Haze':

				return __('Haze','revslider-weather-addon');

				break;

			case 'Heavy Drizzle':

				return __('Heavy Drizzle','revslider-weather-addon');

				break;

			case 'Heavy Rain Shower':

				return __('Heavy Rain Shower','revslider-weather-addon');

				break;

			case 'Heavy Rain':

				return __('Heavy Rain','revslider-weather-addon');

				break;

			case 'Heavy Rain/Wind':

				return __('Heavy Rain/Wind','revslider-weather-addon');

				break;

			case 'Heavy Rain/Windy':

				return __('Heavy Rain/Windy','revslider-weather-addon');

				break;

			case 'Heavy Snow Shower':

				return __('Heavy Snow Shower','revslider-weather-addon');

				break;

			case 'Heavy Snow':

				return __('Heavy Snow','revslider-weather-addon');

				break;

			case 'Heavy Snow/Wind':

				return __('Heavy Snow/Wind','revslider-weather-addon');

				break;

			case 'Heavy Thunderstorm':

				return __('Heavy Thunderstorm','revslider-weather-addon');

				break;

			case 'Heavy Thunderstorm/Windy':

				return __('Heavy Thunderstorm/Windy','revslider-weather-addon');

				break;

			case 'Ice Crystals':

				return __('Ice Crystals','revslider-weather-addon');

				break;

			case 'Ice Late':

				return __('Ice Late','revslider-weather-addon');

				break;

			case 'Isolated T-storms':

				return __('Isolated T-storms','revslider-weather-addon');

				break;

			case 'Isolated Thunderstorms':

				return __('Isolated Thunderstorms','revslider-weather-addon');

				break;

			case 'Light Drizzle':

				return __('Light Drizzle','revslider-weather-addon');

				break;

			case 'Light Freezing Drizzle':

				return __('Light Freezing Drizzle','revslider-weather-addon');

				break;

			case 'Light Freezing Rain':

				return __('Light Freezing Rain','revslider-weather-addon');

				break;

			case 'Light Freezing Rain/Fog':

				return __('Light Freezing Rain/Fog','revslider-weather-addon');

				break;

			case 'Light Rain Early':

				return __('Light Rain Early','revslider-weather-addon');

				break;

			case 'Light Rain':

				return __('Light Rain','revslider-weather-addon');

				break;

			case 'Light Rain Late':

				return __('Light Rain Late','revslider-weather-addon');

				break;

			case 'Light Rain Shower':

				return __('Light Rain Shower','revslider-weather-addon');

				break;

			case 'Light Rain Shower/Fog':

				return __('Light Rain Shower/Fog','revslider-weather-addon');

				break;

			case 'Light Rain Shower/Windy':

				return __('Light Rain Shower/Windy','revslider-weather-addon');

				break;

			case 'Light Rain with Thunder':

				return __('Light Rain with Thunder','revslider-weather-addon');

				break;

			case 'Light Rain/Fog':

				return __('Light Rain/Fog','revslider-weather-addon');

				break;

			case 'Light Rain/Freezing Rain':

				return __('Light Rain/Freezing Rain','revslider-weather-addon');

				break;

			case 'Light Rain/Wind Early':

				return __('Light Rain/Wind Early','revslider-weather-addon');

				break;

			case 'Light Rain/Wind Late':

				return __('Light Rain/Wind Late','revslider-weather-addon');

				break;

			case 'Light Rain/Wind':

				return __('Light Rain/Wind','revslider-weather-addon');

				break;

			case 'Light Rain/Windy':

				return __('Light Rain/Windy','revslider-weather-addon');

				break;

			case 'Light Sleet':

				return __('Light Sleet','revslider-weather-addon');

				break;

			case 'Light Snow Early':

				return __('Light Snow Early','revslider-weather-addon');

				break;

			case 'Light Snow Grains':

				return __('Light Snow Grains','revslider-weather-addon');

				break;

			case 'Light Snow Late':

				return __('Light Snow Late','revslider-weather-addon');

				break;

			case 'Light Snow Shower':

				return __('Light Snow Shower','revslider-weather-addon');

				break;

			case 'Light Snow Shower/Fog':

				return __('Light Snow Shower/Fog','revslider-weather-addon');

				break;

			case 'Light Snow with Thunder':

				return __('Light Snow with Thunder','revslider-weather-addon');

				break;

			case 'Light Snow':

				return __('Light Snow','revslider-weather-addon');

				break;

			case 'Light Snow/Fog':

				return __('Light Snow/Fog','revslider-weather-addon');

				break;

			case 'Light Snow/Freezing Rain':

				return __('Light Snow/Freezing Rain','revslider-weather-addon');

				break;

			case 'Light Snow/Wind':

				return __('Light Snow/Wind','revslider-weather-addon');

				break;

			case 'Light Snow/Windy':

				return __('Light Snow/Windy','revslider-weather-addon');

				break;

			case 'Light Snow/Windy/Fog':

				return __('Light Snow/Windy/Fog','revslider-weather-addon');

				break;

			case 'Mist':

				return __('Mist','revslider-weather-addon');

				break;

			case 'Mostly Clear':

				return __('Mostly Clear','revslider-weather-addon');

				break;

			case 'Mostly Cloudy':

				return __('Mostly Cloudy','revslider-weather-addon');

				break;

			case 'Mostly Cloudy/Wind':

				return __('Mostly Cloudy/Wind','revslider-weather-addon');

				break;

			case 'Mostly Sunny':

				return __('Mostly Sunny','revslider-weather-addon');

				break;

			case 'Partial Fog':

				return __('Partial Fog','revslider-weather-addon');

				break;

			case 'Partly Cloudy':

				return __('Partly Cloudy','revslider-weather-addon');

				break;

			case 'Partly Cloudy/Wind':

				return __('Partly Cloudy/Wind','revslider-weather-addon');

				break;

			case 'Patches of Fog':

				return __('Patches of Fog','revslider-weather-addon');

				break;

			case 'Patches of Fog/Windy':

				return __('Patches of Fog/Windy','revslider-weather-addon');

				break;

			case 'PM Drizzle':

				return __('PM Drizzle','revslider-weather-addon');

				break;

			case 'PM Fog':

				return __('PM Fog','revslider-weather-addon');

				break;

			case 'PM Light Snow':

				return __('PM Light Snow','revslider-weather-addon');

				break;

			case 'PM Light Rain':

				return __('PM Light Rain','revslider-weather-addon');

				break;

			case 'PM Light Rain/Wind':

				return __('PM Light Rain/Wind','revslider-weather-addon');

				break;

			case 'PM Light Snow/Wind':

				return __('PM Light Snow/Wind','revslider-weather-addon');

				break;

			case 'PM Rain':

				return __('PM Rain','revslider-weather-addon');

				break;

			case 'PM Rain/Snow Showers':

				return __('PM Rain/Snow Showers','revslider-weather-addon');

				break;

			case 'PM Rain/Snow':

				return __('PM Rain/Snow','revslider-weather-addon');

				break;

			case 'PM Rain/Wind':

				return __('PM Rain/Wind','revslider-weather-addon');

				break;

			case 'PM Showers':

				return __('PM Showers','revslider-weather-addon');

				break;

			case 'PM Showers/Wind':

				return __('PM Showers/Wind','revslider-weather-addon');

				break;

			case 'PM Snow Showers':

				return __('PM Snow Showers','revslider-weather-addon');

				break;

			case 'PM Snow Showers/Wind':

				return __('PM Snow Showers/Wind','revslider-weather-addon');

				break;

			case 'PM Snow':

				return __('PM Snow','revslider-weather-addon');

				break;

			case 'PM T-storms':

				return __('PM T-storms','revslider-weather-addon');

				break;

			case 'PM Thundershowers':

				return __('PM Thundershowers','revslider-weather-addon');

				break;

			case 'PM Thunderstorms':

				return __('PM Thunderstorms','revslider-weather-addon');

				break;

			case 'Rain and Snow':

				return __('Rain and Snow','revslider-weather-addon');

				break;

			case 'Rain and Snow/Windy':

				return __('Rain and Snow/Windy','revslider-weather-addon');

				break;

			case 'Rain/Snow Showers/Wind':

				return __('Rain/Snow Showers/Wind','revslider-weather-addon');

				break;

			case 'Rain Early':

				return __('Rain Early','revslider-weather-addon');

				break;

			case 'Rain Late':

				return __('Rain Late','revslider-weather-addon');

				break;

			case 'Rain Shower':

				return __('Rain Shower','revslider-weather-addon');

				break;

			case 'Rain Shower/Windy':

				return __('Rain Shower/Windy','revslider-weather-addon');

				break;

			case 'Rain to Snow':

				return __('Rain to Snow','revslider-weather-addon');

				break;

			case 'Rain':

				return __('Rain','revslider-weather-addon');

				break;

			case 'Rain/Snow Early':

				return __('Rain/Snow Early','revslider-weather-addon');

				break;

			case 'Rain/Snow Late':

				return __('Rain/Snow Late','revslider-weather-addon');

				break;

			case 'Rain/Snow Showers Early':

				return __('Rain/Snow Showers Early','revslider-weather-addon');

				break;

			case 'Rain/Snow Showers Late':

				return __('Rain/Snow Showers Late','revslider-weather-addon');

				break;

			case 'Rain/Snow Showers':

				return __('Rain/Snow Showers','revslider-weather-addon');

				break;

			case 'Rain/Snow':

				return __('Rain/Snow','revslider-weather-addon');

				break;

			case 'Rain/Snow/Wind':

				return __('Rain/Snow/Wind','revslider-weather-addon');

				break;

			case 'Rain/Thunder':

				return __('Rain/Thunder','revslider-weather-addon');

				break;

			case 'Rain/Wind Early':

				return __('Rain/Wind Early','revslider-weather-addon');

				break;

			case 'Rain/Wind Late':

				return __('Rain/Wind Late','revslider-weather-addon');

				break;

			case 'Rain/Wind':

				return __('Rain/Wind','revslider-weather-addon');

				break;

			case 'Rain/Windy':

				return __('Rain/Windy','revslider-weather-addon');

				break;

			case 'Scattered Showers':

				return __('Scattered Showers','revslider-weather-addon');

				break;

			case 'Scattered Showers/Wind':

				return __('Scattered Showers/Wind','revslider-weather-addon');

				break;

			case 'Scattered Snow Showers':

				return __('Scattered Snow Showers','revslider-weather-addon');

				break;

			case 'Scattered Snow Showers/Wind':

				return __('Scattered Snow Showers/Wind','revslider-weather-addon');

				break;

			case 'Scattered T-storms':

				return __('Scattered T-storms','revslider-weather-addon');

				break;

			case 'Scattered Thunderstorms':

				return __('Scattered Thunderstorms','revslider-weather-addon');

				break;

			case 'Shallow Fog':

				return __('Shallow Fog','revslider-weather-addon');

				break;

			case 'Showers':

				return __('Showers','revslider-weather-addon');

				break;

			case 'Showers Early':

				return __('Showers Early','revslider-weather-addon');

				break;

			case 'Showers Late':

				return __('Showers Late','revslider-weather-addon');

				break;

			case 'Showers in the Vicinity':

				return __('Showers in the Vicinity','revslider-weather-addon');

				break;

			case 'Showers/Wind':

				return __('Showers/Wind','revslider-weather-addon');

				break;

			case 'Sleet and Freezing Rain':

				return __('Sleet and Freezing Rain','revslider-weather-addon');

				break;

			case 'Sleet/Windy':

				return __('Sleet/Windy','revslider-weather-addon');

				break;

			case 'Snow Grains':

				return __('Snow Grains','revslider-weather-addon');

				break;

			case 'Snow Late':

				return __('Snow Late','revslider-weather-addon');

				break;

			case 'Snow Shower':

				return __('Snow Shower','revslider-weather-addon');

				break;

			case 'Snow Showers Early':

				return __('Snow Showers Early','revslider-weather-addon');

				break;

			case 'Snow Showers Late':

				return __('Snow Showers Late','revslider-weather-addon');

				break;

			case 'Snow Showers':

				return __('Snow Showers','revslider-weather-addon');

				break;

			case 'Snow Showers/Wind':

				return __('Snow Showers/Wind','revslider-weather-addon');

				break;

			case 'Snow to Rain':

				return __('Snow to Rain','revslider-weather-addon');

				break;

			case 'Snow':

				return __('Snow','revslider-weather-addon');

				break;

			case 'Snow/Wind':

				return __('Snow/Wind','revslider-weather-addon');

				break;

			case 'Snow/Windy':

				return __('Snow/Windy','revslider-weather-addon');

				break;

			case 'Squalls':

				return __('Squalls','revslider-weather-addon');

				break;

			case 'Sunny':

				return __('Sunny','revslider-weather-addon');

				break;

			case 'Sunny/Wind':

				return __('Sunny/Wind','revslider-weather-addon');

				break;

			case 'Sunny/Windy':

				return __('Sunny/Windy','revslider-weather-addon');

				break;

			case 'T-showers':

				return __('T-showers','revslider-weather-addon');

				break;

			case 'Thunder in the Vicinity':

				return __('Thunder in the Vicinity','revslider-weather-addon');

				break;

			case 'Thunder':

				return __('Thunder','revslider-weather-addon');

				break;

			case 'Thundershowers Early':

				return __('Thundershowers Early','revslider-weather-addon');

				break;

			case 'Thundershowers':

				return __('Thundershowers','revslider-weather-addon');

				break;

			case 'Thunderstorm':

				return __('Thunderstorm','revslider-weather-addon');

				break;

			case 'Thunderstorm/Windy':

				return __('Thunderstorm/Windy','revslider-weather-addon');

				break;

			case 'Thunderstorms Early':

				return __('Thunderstorms Early','revslider-weather-addon');

				break;

			case 'Thunderstorms Late':

				return __('Thunderstorms Late','revslider-weather-addon');

				break;

			case 'Thunderstorms':

				return __('Thunderstorms','revslider-weather-addon');

				break;

			case 'Unknown Precipitation':

				return __('Unknown Precipitation','revslider-weather-addon');

				break;

			case 'Unknown':

				return __('Unknown','revslider-weather-addon');

				break;

			case 'Wintry Mix':

				return __('Wintry Mix','revslider-weather-addon');

				break;

			default:

				return $text;

				break;

		}

	}



}

?>