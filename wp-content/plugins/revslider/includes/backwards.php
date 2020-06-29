<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

/**
 * backwards compatibility prior 6.0.0 code
 * @START
 **/
//mostly needed for RevSlider AddOns
class RevSliderGlobals {
	const SLIDER_REVISION = RS_REVISION;
	const TABLE_SLIDERS_NAME = RevSliderFront::TABLE_SLIDER;
	const TABLE_SLIDES_NAME = RevSliderFront::TABLE_SLIDES;
	const TABLE_STATIC_SLIDES_NAME = RevSliderFront::TABLE_STATIC_SLIDES;
	const TABLE_SETTINGS_NAME = RevSliderFront::TABLE_SETTINGS;
	const TABLE_CSS_NAME = RevSliderFront::TABLE_CSS;
	const TABLE_LAYER_ANIMS_NAME = RevSliderFront::TABLE_LAYER_ANIMATIONS;
	const TABLE_NAVIGATION_NAME = RevSliderFront::TABLE_NAVIGATIONS;
	public static $table_sliders;
	public static $table_slides;
	public static $table_static_slides;
}
global $wpdb;

RevSliderGlobals::$table_sliders = $wpdb->prefix.'revslider_sliders';
RevSliderGlobals::$table_slides = $wpdb->prefix.'revslider_slides';
RevSliderGlobals::$table_static_slides = $wpdb->prefix.'revslider_static_slides';
class RevSliderBase {
	
	public static function check_file_in_zip($d_path, $image, $alias, $alreadyImported = false){
		$f = new RevSliderFunctions();
		
		return $f->check_file_in_zip($d_path, $image, $alias, $alreadyImported, $add_path = false);
	}
}

class RevSliderFunctionsWP {
	public static function getImageUrlFromPath($url){
		$f = new RevSliderFunctions();
		return $f->get_image_url_from_path($url);
	}
	
	public static function get_image_id_by_url($image_url){
		$f = new RevSliderFunctions();
		return $f->get_image_id_by_url($image_url);
	}
}

class RevSliderOperations {
	public function getGeneralSettingsValues(){
		$f = new RevSliderFunctions();
		return $f->get_global_settings();
	}
}

class RevSlider extends RevSliderSlider {
	public function __construct(){
		//echo '<!-- Slider Revolution Notice: Please do not use the class "RevSlider" anymore, use "RevSliderSlider" instead -->'."\n";
	}
}

class UniteFunctionsRev extends RevSliderFunctions {}

if(!function_exists('set_revslider_as_theme')){
	function set_revslider_as_theme(){
	}
}

/**
 * backwards compatibility prior 6.0.0 code
 * @END
 **/
 ?>