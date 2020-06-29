<?php

/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RevAddOnWeatherOWM {

	public function __construct() {
	}

	/**
	 * Connects to Open Weather Map API with certain parameters
	 * @since    1.0.0
	 */
	public static function get_weather_infos($location, $unit, $key){
		
		$city = is_numeric($location) ? 'city_id' : 'city';
		$url = 'https://api.weatherbit.io/v2.0/forecast/daily?' . $city . '=' . $location . '&key=' . $key . '&units=' . $unit;
		$transient_name = 'revslider_' . md5($url);
		


		if (false === ( $weather = get_transient( $transient_name) ) ){
			
			$weather = wp_remote_fopen($url);
			set_transient( $transient_name, $weather, 3600 );
			
		}
		
		$weather = json_decode($weather);

		return $weather;
	}

}

?>