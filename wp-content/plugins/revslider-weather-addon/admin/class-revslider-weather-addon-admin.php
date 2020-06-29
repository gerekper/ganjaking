<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_Weather_Addon
 * @subpackage Revslider_Weather_Addon/admin
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Weather_Addon_Admin {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Weather_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Weather_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, REV_ADDON_WEATHER_URL . 'public/css/revslider-weather-addon-icon.css', array(), $this->version, 'all' );

	}

	/**
	 * Add Weather Font Icons to Object Library
	 *
	 * @since    1.0.0
	 */

	public static function extend_font_icons_path($paths){
		$paths[] = REV_ADDON_WEATHER_PATH . 'public/css/revslider-weather-addon-icon.css'; 
	    
	    return $paths;
	}

	public static function extend_font_tags($tags){
		$tags["Weather"] = "Weather";	   
	   return $tags;
	}

    public static function extend_font_tags_on_weather($items){        
        foreach($items as $key => $item){
            if(strpos($item['handle'], '.revslider-weather-icon') !== false){
                $items[$key]['tags'] = array('Weather');
                //if (!isset($items[$key]['classextension'])) 
                //	$items[$key]['classextension'] = array(); 
                //$items[$key]['classextension'][] = 'revslider-weather-icon';
            }
        }
        
        return $items;
    }
	
	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_Weather_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_Weather_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 
		if (isset($_GET["page"]) && $_GET["page"]=="revslider"){			
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/revslider-weather-addon-admin.js', array( 'jquery','revbuilder-admin' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'revslider_weather_addon', $this->get_var() );
		}

	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-weather-addon') {
		if($slug == 'revslider-weather-addon'){
			return array(
				'bricks' => array(
					'weather'=> __('Weather','revslider-weather-addon'),
					'location'=> __('Location','revslider-weather-addon'),
					'refresh'=> __('Refresh','revslider-weather-addon'),
					'wfunctions'=> __('Weather Functions','revslider-weather-addon'),
					'wdefaults'=> __('Weather Defaults','revslider-weather-addon'),
					'wlocation'=> __('Weather from','revslider-weather-addon'),
					'weather_temp' => __('Temp','revslider-weather-addon'),
					'weather_code' => __('Code','revslider-weather-addon'),
					'weather_date' => __('Date','revslider-weather-addon'),
					'weather_day' => __('Day','revslider-weather-addon'),
					'weather_todayCode' => __('TodayCode','revslider-weather-addon'),
					'weather_currently' => __('Currently','revslider-weather-addon'),
					'weather_high' => __('High','revslider-weather-addon'),
					'weather_low' => __('Low','revslider-weather-addon'),
					'weather_text' => __('Text','revslider-weather-addon'),
					'weather_humidity' => __('Humidity','revslider-weather-addon'),
					'weather_pressure' => __('Pressure','revslider-weather-addon'),
					'weather_rising' => __('Rising','revslider-weather-addon'),
					'weather_visbility' => __('Visibility','revslider-weather-addon'),
					'weather_sunrise' => __('Sunrise','revslider-weather-addon'),
					'weather_sunset' => __('Sunset','revslider-weather-addon'),
					'weather_city' => __('City','revslider-weather-addon'),
					'weather_country' => __('Country','revslider-weather-addon'),
					'weather_region' => __('Region','revslider-weather-addon'),
					'weather_updated' => __('Updated','revslider-weather-addon'),
					'weather_link' => __('Link','revslider-weather-addon'),
					'weather_heatindex' => __('Heatindex','revslider-weather-addon'),
					'weather_thumbnail' => __('Thumbnail','revslider-weather-addon'),
					'weather_image' => __('Image','revslider-weather-addon'),
					'weather_icon' => __('Icon','revslider-weather-addon'),
					'weather_units_temp' => __('Units Temp','revslider-weather-addon'),
					'weather_temperature' => __('Temperature','revslider-weather-addon'),
					'weather_units_distance' => __('Units Distance','revslider-weather-addon'),
					'weather_units_pressure' => __('Units Pressure','revslider-weather-addon'),
					'weather_units_speed' => __('Units Speed','revslider-weather-addon'),
					'weather_wind_chill' => __('Wind Chill','revslider-weather-addon'),
					'weather_wind_direction' => __('Wind Direction','revslider-weather-addon'),
					'weather_wind_speed' => __('Wind Speed','revslider-weather-addon'),
					'weather_alt_temp' => __('Alt Temp','revslider-weather-addon'),
					'weather_alt_high' => __('Alt High','revslider-weather-addon'),
					'weather_alt_low' => __('Alt Low','revslider-weather-addon'),
					'weather_alt_unit' => __('Alt Unit','revslider-weather-addon'),
					'weather_description' => __('Description','revslider-weather-addon'),
					'weather_date_forecast' => __('ForeCast Date x Days from now (x:1-9)','revslider-weather-addon'),
					'weather_day_forecast' => __('ForeCast Day x Day','revslider-weather-addon'),
					'weather_code_forecast' => __('ForeCast Day x Code','revslider-weather-addon'),
					'weather_high_forecast' => __('ForeCast Day x High','revslider-weather-addon'),
					'weather_low_forecast' => __('ForeCast Day x Low','revslider-weather-addon'),
					'weather_alt_high_forecast' => __('ForeCast Day x Alt High','revslider-weather-addon'),
					'weather_alt_low_forecast' => __('ForeCast Day x Alt Low','revslider-weather-addon'),
					'weather_thumbnail_forecast' => __('ForeCast Day x Thumbnail','revslider-weather-addon'),
					'weather_image_forecast' => __('ForeCast Day x Image','revslider-weather-addon'),
					'weather_icon_forecast' => __('ForeCast Day x Icon','revslider-weather-addon'),
					'weather_text_forecast' => __('ForeCast Day x Text','revslider-weather-addon'),
					'weather_title' => __('Title','revslider-weather-addon'),
					'configuration' =>  __('Configuration','revslider-weather-addon'),
					'apikey' => __('API Key', 'revslider-weather-addon'),
					'save' => __('Save Configration','revslider-weather-addon'),					
					'loadvalues' => __('Loading Weather Add-On Configuration','revslider-weather-addon'),
					'savevalues' => __('Saving Weather Add-On Configration','revslider-weather-addon'),
					'info' => __('Insert a valid API key from <a href="https://www.weatherbit.io/pricing" target="_blank">Weatherbit.io</a>','revslider-weather-addon')
				)
			);
		}
		else{
			return $var;
		}
	}

	public function add_addon_settings_slider($_settings){			
		$_settings['weather'] = array(				
		   'title'		 => __('Weather', 'rs_weather'),			
			'slug'	     => 'revslider-weather-addon'		
		);		
		return $_settings;		
	}
	
	
	public function add_addon_settings_slider_2($_settings){
		$_settings['weather'] = __('Weather', 'rs_weather');
		
		return $_settings;	
	}
	
	/**
	 * Saves Values for this Add-On
	 *
	 * @since    2.0.0
	 */
	public function save_weather() {
		if(isset($_REQUEST['data']['revslider_weather_form'])){
			update_option( "revslider_weather_addon", $_REQUEST['data']['revslider_weather_form'] );
			return 1;
		}
		else{
			return 0;
		}
		
	}

	/**
	 * Load Values for this Add-On
	 *
	 * @since    2.0.0
	 */
	public function values_weather() {
		$revslider_weather_addon_values = array();
		parse_str(get_option('revslider_weather_addon'), $revslider_weather_addon_values);
		$return = json_encode($revslider_weather_addon_values);
		return array("message" => "Weather Settings Loaded", "data"=>$return);
	}
	
	/**
	 * Ajax actions for RevSlider calls
	 *
	 * @since    1.0.0
	 */
	public function do_ajax($return,$action) {
		switch ($action) {
			case 'wp_ajax_get_values_revslider-weather-addon':
				$return = $this->values_weather();
				if(empty($return)) $return = true;
				return $return;
				break;
			case 'wp_ajax_save_values_revslider-weather-addon':
				$return = $this->save_weather();
				if(empty($return) || !$return){
					return  __('Configuration could not be saved', 'revslider-weather-addon');
				} 
				else {
					return  __('Weather Configuration saved', 'revslider-weather-addon');	
				}
				break;
			default:
				return $return;
				break;
		}
	}
	
	/**
	 * Saves Values for this Add-On
	 *
	 * @since    1.0.0
	 */
	/*
	public function save_weather() {
		// Verify that the incoming request is coming with the security nonce
		if( wp_verify_nonce( $_REQUEST['nonce'], 'ajax_revslider_weather_addon_nonce' ) ) {
			if(isset($_REQUEST['revslider_weather_form'])){
				update_option( "revslider_weather_addon", $_REQUEST['revslider_weather_form'] );
				die( '1' );
			}
			else{
				die( '0' );
			}
		} 
		else {
			die( '-1' );
		}
	}
	*/

}
