<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsSnowSliderFront extends RevSliderFunctions {
	
	private $version,
			$pluginUrl, 
			$pluginTitle;
			
	public function __construct($version, $pluginUrl, $pluginTitle, $isAdmin = false) {
		
		$this->version     = $version;
		$this->pluginUrl   = $pluginUrl;
		$this->pluginTitle = $pluginTitle;
		
		add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1);
		if($isAdmin){
			//add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
		}
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		//add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		
		add_action('revslider_fe_javascript_option_output', array($this, 'write_init_options'), 10, 1);
		
		add_action('revslider_export_html_write_footer', array($this, 'write_export_footer'), 10, 1);
		add_filter('revslider_export_html_file_inclusion', array($this, 'add_addon_files'), 10, 2);
	}
	
	public function write_export_footer($export){
		$output = $export->slider_output;
		$array = $this->add_html_script_additions(array(), $output);
		$toload = $this->get_val($array, 'toload', array());
		if(!empty($toload)){
			foreach($toload as $script){
				echo $script;
			}
		}
	}

	public function add_addon_files($html, $export){
		$output = $export->slider_output;
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $html;

		$_jsPathMin = file_exists(RS_SNOW_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		if(!$export->usepcl){
			$export->zip->addFile(RS_SNOW_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js');
		}else{
			$export->pclzip->add(RS_SNOW_PLUGIN_PATH.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', PCLZIP_OPT_REMOVE_PATH, RS_SNOW_PLUGIN_PATH.'public/assets/js/', PCLZIP_OPT_ADD_PATH, 'js/');
		}

		$html = str_replace($this->pluginUrl.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $export->path_js .'revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $html);

		return $html;
	}

	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider) {
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	}
	
	public function check_addon_active($record) {
		if(empty($record)) return $record;
		
		// addon enabled
		$addOn = $this->isEnabled($record);
		if(empty($addOn)) return $record;
		
		$this->add_scripts();
		remove_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10);
		
		return $record;
		
	}

	public function add_scripts() {
		$handle = 'rs-' . $this->pluginTitle . '-front';
		$base   = $this->pluginUrl . 'public/assets/';
		$path = $base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js';
		$_jsPathMin = file_exists(RS_SNOW_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';

		wp_enqueue_script($handle, $base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('jquery'), $this->version, true);
		
		add_filter('revslider_modify_waiting_scripts', array($this, 'add_waiting_script_slugs'), 10, 1);
	}
	
	public function add_html_script_additions($return, $output){
		if($output instanceof RevSliderSlider){
			$addOn = $this->isEnabled($output);
			if(empty($addOn)) return $return;
		}else{
			$me = $output->get_markup_export();
			if($me !== true && $output->ajax_loaded !== true) return $return;
			
			$addOn = $this->isEnabled($output->slider);
			if(empty($addOn)) return $return;
		}
		
		$waiting = array();
		$waiting = $this->add_waiting_script_slugs($waiting);
		if(!empty($waiting)){
			if(!isset($return['waiting'])) $return['waiting'] = array();
			foreach($waiting as $wait){
				$return['waiting'][] = $wait;
			}
		}
		
		$global = $output->get_global_settings();
		$addition = ($output->_truefalse($output->get_val($global, array('script', 'defer'), false)) === true) ? ' async="" defer=""' : '';
		$_jsPathMin = file_exists(RS_SNOW_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['snow'] = '<script'. $addition .' src="'. $this->pluginUrl . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'snow';
		return $wait;
	}
	
	
	public function write_init_script($slider, $id) {
		
		$title = $this->pluginTitle;
		$enabled = $this->isEnabled($slider);
		
		if(!empty($enabled)) {
		
			$id    = $slider->get_id();
			
			echo                  		"\n";
			echo '                		if(typeof RsSnowAddOn !== "undefined") RsSnowAddOn(jQuery, revapi' . $id . ');' . "\n";
			
		}
		
	}
	
	public function write_init_options($slider) {
		
		$tabs = "\t\t\t\t\t\t\t\t";
		$tabsa = "\t\t\t\t\t\t\t\t\t";
		$addOn = $this->isEnabled($slider);
		
		if(!empty($addOn)) {
			
			$max = $this->get_val($addOn, 'max', array());
			$min = $this->get_val($addOn, 'min', array());
			
			echo $tabs . 'snow: {' . "\n";
			echo $tabsa . 'startSlide: "' . $this->get_val($addOn, 'startSlide', 'first') . '",' . "\n";
			echo $tabsa . 'endSlide: "'   . $this->get_val($addOn, 'endSlide', 'last') . '",' . "\n";
			echo $tabsa . 'maxNum: "'     . $this->get_val($max, 'number', '400') . '",' . "\n";
			echo $tabsa . 'minSize: "'    . $this->get_val($min, 'size', '0.2') . '",' . "\n";
			echo $tabsa . 'maxSize: "'    . $this->get_val($max, 'size', '6') . '",' . "\n";
			echo $tabsa . 'minOpacity: "' . $this->get_val($min, 'opacity', '0.3') . '",' . "\n";
			echo $tabsa . 'maxOpacity: "' . $this->get_val($max, 'opacity', '1') . '",' . "\n";
			echo $tabsa . 'minSpeed: "'   . $this->get_val($min, 'speed', '30') . '",' . "\n";
			echo $tabsa . 'maxSpeed: "'   . $this->get_val($max, 'speed', '100') . '",' . "\n";
			echo $tabsa . 'minSinus: "'   . $this->get_val($min, 'sinus', '1') . '",' . "\n";
			echo $tabsa . 'maxSinus: "'   . $this->get_val($max, 'sinus', '100') . '",' . "\n";
			echo $tabs . '},' . "\n";
			
		}
	
	}
	
}
?>