<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Weather_Addon_Public extends RevSliderFunctions {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $weather;
	private $css_enqueued = false;
	private $js_enqueued = false;
	private $api_key = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$key = get_option('revslider_weather_addon');
		if(is_string($key) && !empty($key)) $this->api_key = str_replace('revslider-weather-addon-api=', '', $key);

	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		return true;
	
	}
	
	// generic "isEnabled" that can be used for the $slider or $slide Object
	private function isEnabled($obj) {
		
		$settings = $obj->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-weather-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	
	}
	
	/**
	 * Appends a data arttribute to applicable Layers
	 * @since    2.0.0
	 */
	public function write_layer_attributes($layer, $slide, $slider) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOns = $this->get_val($layer, 'addOns', array());
		$weather = $this->get_val($addOns, 'revslider-weather-addon', array());
		
		$location = $this->get_val($weather, 'location', false);
		if($location === false) return;
		
		$unit = $this->get_val($weather, 'unit', 'f');
		$unit = $unit === 'f' ? 'imperial' : 'metric';
		$data = array('location' => $location, 'unit'  => $unit);
		
		$tabs = "\t\t\t\t\t\t\t\t\t\t\t";
		echo $tabs . "data-weatheraddon='" . json_encode($data) . "' " . "\n";
		
	}
	
	/**
	 * Appends JS to the core RevSlider init script
	 * @since    2.0.0
	 */
	public function write_init_script($slider, $id) {
		
		$addOn = $this->isEnabled($slider);
		if(!empty($addOn)) {
			
			$id    = $slider->get_id();
			$refresh = $this->get_val($addOn, 'refresh', 'false');
			$tabs = "\t\t\t\t\t\t\t";
			
			echo "\n";
			echo $tabs . 'if(typeof RsWeatherAddOn !== "undefined") RsWeatherAddOn(tpj, revapi' . $id . ', "' . $refresh . '");' . "\n";
			
		}
		
	}

	/**
	 * Get Information from Slide and call the weather
	 * @since    1.0.0
	 */
	public function revslider_add_layer_html($slider, $slide) {
		
		if(empty($this->api_key)) return;
		if(!$this->js_enqueued) {

			$addOn = $this->isEnabled($slider);
			if(!empty($addOn)) {
				
				$this->js_enqueued = true;
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-weather-addon-public.css', array(), $this->version, 'all' );
				
				$refresh = $this->get_val($addOn, 'refresh', false);
				if(!empty($refresh)) {
					
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-weather-addon-public.js', array( 'jquery' ), $this->version, false );
					wp_localize_script( $this->plugin_name, 'rev_slider_weather_addon', array('api_key' => $this->api_key));
					
				}	
			}
		}
	}


	/**
	 * Connects to Weatherbit.io and collects Weather Info
	 * @since    1.0.0
	 */
	public function get_weather($location, $unit) {
		
		//Get weather information dependent from Slider options
		$revslider_weather = RevAddOnWeatherOWM::get_weather_infos($location, $unit, $this->api_key);
		
		if($this->get_val($revslider_weather, 'error', false)) {
			return false;
		}
		
		return $revslider_weather;

	}

	/**
	 * Filters the custom meta placeholders and calls function to replace
	 * @since    1.0.0
	 */
	public function rev_addon_insert_meta($text, $layer){
		
		if(empty($this->api_key)) return $text;
		
		$addOns = $this->get_val($layer, 'addOns', array());
		$weather = $this->get_val($addOns, 'revslider-weather-addon', array());
		
		$location = $this->get_val($weather, 'location', false);
		if($location === false) return $text;
		
		$unit = $this->get_val($weather, 'unit', 'c');
		$unit = $unit === 'f' ? 'imperial' : 'metric';

		//Get weather information dependent from Slider options
		$revslider_weather = $this->get_weather($location, $unit);

		if(!empty($revslider_weather) && isset($revslider_weather->data[0]->weather) ) {
			
			if(!$this->css_enqueued && strpos($text, "{{weather_icon}}")) {
				$this->css_enqueued = true;
				wp_enqueue_style( $this->plugin_name . '_icons', plugin_dir_url( __FILE__ ) . 'css/weather-icons.css', array(), $this->version, 'all' );
			}

			$unit_pressure = 'hPa';
			$unit_humidity = '%';
			if($unit === 'metric'){
				$unit_temp = 'C';
				$unit_wind_speed = 'm/s';
			}
			else{
				$unit_temp = 'F';
				$unit_wind_speed = 'mph';
			}
			
			//Check for forecasts
			$forecasts = preg_match_all('/\\{{weather_.*?_forecast:([0-9])\\}}/', $text, $matches);	
			array_unshift($matches[1],'0');		
			

			foreach($matches[1] as $day){

				if($day > 10) continue;
				if($day){
					$forecast = "_forecast:" . $day;
				}
				else {
					$forecast = "";
				}
				
				/**
				 * Shift day ahead if day 0 is not current day
				 * @since    2.0.1
				 */
				if( date_i18n( get_option( 'date_format' ), $revslider_weather->data[0]->ts ) != date_i18n( get_option( 'date_format' ), time() ) ){
					$day++;
				}

				$values = array(
					'revslider_data_weather_title' => $revslider_weather->data[$day]->weather->description,
					'revslider_data_weather_temp' => round($revslider_weather->data[$day]->temp),
					'revslider_data_weather_code' => $revslider_weather->data[$day]->weather->code,
					'revslider_data_weather_todayCode' => $revslider_weather->data[$day]->weather->code,
					'revslider_data_weather_date' => date_i18n( get_option( 'date_format' ), $revslider_weather->data[$day]->ts ),
					'revslider_data_weather_day' => date_i18n( 'D', $revslider_weather->data[$day]->ts ),
					'revslider_data_weather_currently' => $revslider_weather->data[$day]->weather->description,
					'revslider_data_weather_high' => round($revslider_weather->data[$day]->max_temp),
					'revslider_data_weather_low' => round($revslider_weather->data[$day]->min_temp),
					'revslider_data_weather_text' => $revslider_weather->data[$day]->weather->description,
					'revslider_data_weather_humidity' => $revslider_weather->data[$day]->rh . $unit_humidity,
					'revslider_data_weather_pressure' => $revslider_weather->data[$day]->pres . $unit_pressure,
					'revslider_data_weather_rising' => date_i18n( 'D', $revslider_weather->data[$day]->sunrise_ts ),
					'revslider_data_weather_visbility' => $revslider_weather->data[0]->vis,
					'revslider_data_weather_sunrise' => date_i18n( 'D', $revslider_weather->data[$day]->sunrise_ts ),
					'revslider_data_weather_sunset' => date_i18n( 'D', $revslider_weather->data[$day]->sunset_ts ),
					'revslider_data_weather_city' => $revslider_weather->city_name,
					'revslider_data_weather_country' => $revslider_weather->country_code,
					'revslider_data_weather_region' => $revslider_weather->state_code,
					'revslider_data_weather_updated' => '',
					'revslider_data_weather_link' => '',
					'revslider_data_weather_thumbnail' => 'https://www.weatherbit.io/static/img/icons/' . $revslider_weather->data[$day]->weather->icon . '.png',
					'revslider_data_weather_image' => 'https://www.weatherbit.io/static/img/icons/' . $revslider_weather->data[$day]->weather->icon . '.png',
					'revslider_data_weather_units_temp' => $unit_temp,
					'revslider_data_weather_units_distance' => '',
					'revslider_data_weather_units_pressure' => $unit_pressure,
					'revslider_data_weather_units_speed' => $unit_wind_speed,
					'revslider_data_weather_wind_chill' => '',
					'revslider_data_weather_wind_direction' => $revslider_weather->data[$day]->wind_cdir ,
					'revslider_data_weather_wind_speed' => $revslider_weather->data[$day]->wind_spd,
					'revslider_data_weather_alt_temp' => $this->get_alt_temp($unit,round($revslider_weather->data[$day]->temp)),
					'revslider_data_weather_alt_high' => $this->get_alt_temp($unit,round($revslider_weather->data[$day]->max_temp)),
					'revslider_data_weather_alt_low' => $this->get_alt_temp($unit,round($revslider_weather->data[$day]->min_temp)),
					'revslider_data_weather_alt_unit' => $unit == "f" ? "C" : "F",
					'revslider_data_weather_description' => $revslider_weather->data[$day]->weather->description,
					'revslider_data_weather_icon' => $revslider_weather->data[$day]->weather->code
				);
				

				//Replace Placeholders on Slide Layers
				$begin = '<span data-day=\"' . $day . '\" class=\"revslider-weather-data revslider_data_weather';
				$text = str_replace( 
					array(
						"{{weather_title" . $forecast . "}}",
						"{{weather_temp" . $forecast . "}}",
						"{{weather_code" . $forecast . "}}",
						"{{weather_todayCode" . $forecast . "}}",
						"{{weather_date" . $forecast . "}}",
						"{{weather_day" . $forecast . "}}",
						"{{weather_currently" . $forecast . "}}",
						"{{weather_high" . $forecast . "}}",
						"{{weather_low" . $forecast . "}}",
						"{{weather_text" . $forecast . "}}",
						"{{weather_humidity" . $forecast . "}}",
						"{{weather_pressure" . $forecast . "}}",
						"{{weather_rising" . $forecast . "}}",
						"{{weather_visbility" . $forecast . "}}",
						"{{weather_sunrise" . $forecast . "}}",
						"{{weather_sunset" . $forecast . "}}",
						"{{weather_city" . $forecast . "}}",
						"{{weather_country" . $forecast . "}}",
						"{{weather_region" . $forecast . "}}",
						"{{weather_updated" . $forecast . "}}",
						"{{weather_link" . $forecast . "}}",
						"{{weather_thumbnail" . $forecast . "}}",
						"{{weather_image" . $forecast . "}}",
						"{{weather_units_temp" . $forecast . "}}",
						"{{weather_units_distance" . $forecast . "}}",
						"{{weather_units_pressure" . $forecast . "}}",
						"{{weather_units_speed" . $forecast . "}}",
						"{{weather_wind_chill" . $forecast . "}}",
						"{{weather_wind_direction" . $forecast . "}}",
						"{{weather_wind_speed" . $forecast . "}}",
						"{{weather_alt_temp" . $forecast . "}}",
						"{{weather_alt_high" . $forecast . "}}",
						"{{weather_alt_low" . $forecast . "}}",
						"{{weather_alt_unit" . $forecast . "}}",
						"{{weather_description" . $forecast . "}}",
						"{{weather_icon" . $forecast . "}}"
					), 
					array(
						$begin . '_title\">' . $values['revslider_data_weather_title'] . '</span>',
						$begin . '_temp\">' . $values['revslider_data_weather_temp'] . '</span>',
						$begin . '_code\">' . $values['revslider_data_weather_code'] . '</span>',
						$begin . '_todayCode\">' . $values['revslider_data_weather_todayCode'] . '</span>',
						$begin . '_date revslider-weather-static\">' . $values['revslider_data_weather_date'] . '</span>',
						$begin . '_day revslider-weather-static\">' . $values['revslider_data_weather_day'] . '</span>',
						$begin . '_currently\">' . $values['revslider_data_weather_currently'] . '</span>',
						$begin . '_high\">' . $values['revslider_data_weather_high'] . '</span>',
						$begin . '_low\">' . $values['revslider_data_weather_low'] . '</span>',
						$begin . '_text\">' . $values['revslider_data_weather_text'] . '</span>',
						$begin . '_humidity\">' . $values['revslider_data_weather_humidity'] . '</span>',
						$begin . '_pressure\">' . $values['revslider_data_weather_pressure'] . '</span>',
						$begin . '_rising\">' . $values['revslider_data_weather_rising'] . '</span>',
						$begin . '_visbility\">' . $values['revslider_data_weather_visbility'] . '</span>',
						$begin . '_sunrise\">' . $values['revslider_data_weather_sunrise'] . '</span>',
						$begin . '_sunset\">' . $values['revslider_data_weather_sunset'] . '</span>',
						$begin . '_city revslider-weather-static\">' . $values['revslider_data_weather_city'] . '</span>',
						$begin . '_country revslider-weather-static\">' . $values['revslider_data_weather_country'] . '</span>',
						$begin . '_region revslider-weather-static\">' . $values['revslider_data_weather_region'] . '</span>',
						$begin . '_updated\">' . $values['revslider_data_weather_updated'] . '</span>',
						$begin . '_link\">' . $values['revslider_data_weather_link'] . '</span>',
						$begin . '_thumbnail\">' . $values['revslider_data_weather_thumbnail'] . '</span>',
						$begin . '_image\">' . $values['revslider_data_weather_image'] . '</span>',
						$begin . '_units_temp revslider-weather-static\">' . $values['revslider_data_weather_units_temp'] . '</span>',
						$begin . '_units_distance\">' . $values['revslider_data_weather_units_distance'] . '</span>',
						$begin . '_units_pressure revslider-weather-static\">' . $values['revslider_data_weather_units_pressure'] . '</span>',
						$begin . '_units_speed revslider-weather-static\">' . $values['revslider_data_weather_units_speed'] . '</span>',
						$begin . '_wind_chill\">' . $values['revslider_data_weather_wind_chill'] . '</span>',
						$begin . '_wind_direction\">' . $values['revslider_data_weather_wind_direction'] . '</span>',
						$begin . '_wind_speed\">' . $values['revslider_data_weather_wind_speed'] . '</span>',
						$begin . '_alt_temp\">' . $values['revslider_data_weather_alt_temp'] . '</span>',
						$begin . '_alt_high\">' . $values['revslider_data_weather_alt_high'] . '</span>',
						$begin . '_alt_low\">' . $values['revslider_data_weather_alt_low'] . '</span>',
						$begin . '_alt_unit revslider-weather-static\">' . $values['revslider_data_weather_alt_unit']. '</span>',
						$begin . '_description\">' . $values['revslider_data_weather_description'] . '</span>',
						$begin . '_icon\"><i class=\"wi wi-owm-' . $values['revslider_data_weather_code'] . '\"></i>' . '</span>'
					),
					$text
				);
			}
			
		}

		return $text;
	}

	/**
	 * Get alternative temp unit data
	 * @since    1.0.0
	 */
	public function get_alt_temp($unit, $temp) {
	    if($unit === 'F') {
	      return $this->fahrenheit_to_celsius($temp);
	    } 
	    else {
	      return $this->celsius_to_fahrenheit($temp);
	    }
	}

	/**
	 * Convert Temp Fahrenheit to Celsius
	 * @since    1.0.0
	 */
	public function fahrenheit_to_celsius($given_value)
    {
        $celsius=5/9*($given_value-32);
        return $celsius ;
    }

    /**
	 * Convert Temp Celsius to Fahrenheit
	 * @since    1.0.0
	 */
    public function celsius_to_fahrenheit($given_value)
    {
        $fahrenheit=$given_value*9/5+32;
        return $fahrenheit ;
	}
	
	/**
	 * Convert Wind Degrees to Cardinals
	 * @since    1.0.4
	 */
	public function wind_cardinals($deg) {
		$cardinalDirections = array(
			'N' => array(348.75, 360),
			'N2' => array(0, 11.25),
			'NNE' => array(11.25, 33.75),
			'NE' => array(33.75, 56.25),
			'ENE' => array(56.25, 78.75),
			'E' => array(78.75, 101.25),
			'ESE' => array(101.25, 123.75),
			'SE' => array(123.75, 146.25),
			'SSE' => array(146.25, 168.75),
			'S' => array(168.75, 191.25),
			'SSW' => array(191.25, 213.75),
			'SW' => array(213.75, 236.25),
			'WSW' => array(236.25, 258.75),
			'W' => array(258.75, 281.25),
			'WNW' => array(281.25, 303.75),
			'NW' => array(303.75, 326.25),
			'NNW' => array(326.25, 348.75)
		);
		foreach ($cardinalDirections as $dir => $angles) {

			if ($deg >= $angles[0] && $deg < $angles[1]) {
				$cardinal = str_replace("2", "", $dir);
			}

		}
		return $cardinal;
	}

}
