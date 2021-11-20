<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class rs_whiteboard_fe_slider extends RevSliderFunctions {
	
	private $slug = 'whiteboard';
	private $pluginTitle = 'whiteboard';
	
	public function __construct() {
		add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1);
		if(is_admin()){
			//add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
		}
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		//add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		
		add_action('revslider_fe_javascript_option_output', array($this, 'add_whiteboard_javascript_options'));
		
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

		$_jsPathMin = file_exists(WHITEBOARD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		if(!$export->usepcl){
			$export->zip->addFile(WHITEBOARD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js');
		}else{
			$export->pclzip->add(WHITEBOARD_PLUGIN_PATH.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', PCLZIP_OPT_REMOVE_PATH, WHITEBOARD_PLUGIN_PATH.'public/assets/js/', PCLZIP_OPT_ADD_PATH, 'js/');
		}
		
		$html = str_replace(array(WHITEBOARD_PLUGIN_URL.'public/assets/js/revolution.addon.' . $this->pluginTitle . '.min.js', WHITEBOARD_PLUGIN_URL.'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js'), $export->path_js .'revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', $html);
		
		return $html;
	}

	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider) {
		$params = $this->get_val($slider, 'params', false);
		if(empty($params)) return false;
		
		$addOns = $this->get_val($params, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->slug . '-addon', false);
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
	
	public function add_scripts(){
		$base = WHITEBOARD_PLUGIN_URL . 'public/assets/';
		$path = $base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js';
		$_jsPathMin = file_exists(WHITEBOARD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';

		wp_enqueue_script('rs-whiteboard', $base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('jquery', 'revmin'), WHITEBOARD_VERSION, true);
		
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
		
		$whiteboardTitle = 'whiteboard';
		$base = WHITEBOARD_PLUGIN_URL . 'public/assets/';
		
		$global = $output->get_global_settings();
		$addition = ($output->_truefalse($output->get_val($global, array('script', 'defer'), false)) === true) ? ' async="" defer=""' : '';
		$_jsPathMin = file_exists(WHITEBOARD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['whiteboard'] = '<script'. $addition .' src="'. $base . 'js/revolution.addon.' . $whiteboardTitle . $_jsPathMin . '.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'whiteboard';
		return $wait;
	}
	
	public function write_init_script($slider, $id) {
		
		$enabled = $this->isEnabled($slider);
		if($enabled) {
				
			$enabled = $slider->get_param('wb_is_used', false);
			if(!$this->isFalse($enabled)) echo 'jQuery("#'.$id.'").rsWhiteBoard();'."\n";
			
		}

	}
	
	
	public function add_whiteboard_javascript_options($slider) {
		
		$addOn = $this->isEnabled($slider);
		if($addOn) {
			
			$enabled = $slider->get_param('wb_is_used', false);
			if($this->isFalse($enabled)) return;
			
			$write_defaults = array(
			
				'width' => 572,
				'height' => 691,
				'originX' => 49,
				'originY' => 50,
				'source' => plugins_url('assets/images/write_right_angle.png', __FILE__)
			
			);
			
			$move_defaults = array(
			
				'width' => 400,
				'height' => 1000,
				'originX' => 185,
				'originY' => 66,
				'source' => plugins_url('assets/images/hand_point_right.png', __FILE__)
			
			);
			
			$writehand = (array)$this->get_val($addOn, 'writehand', array());
			$movehand = (array)$this->get_val($addOn, 'movehand', array());
			
			$write = array_merge($write_defaults, $writehand);
			$move = array_merge($move_defaults, $movehand);
			
			$tabs1 = "\t\t\t\t\t\t\t\t";
			$tabs2 = "\t\t\t\t\t\t\t\t\t";
			$tabs3 = "\t\t\t\t\t\t\t\t\t\t";
			
			echo $tabs1 . 'whiteboard: {' . "\n";
			echo $tabs2 . 'writehand: {' . "\n";
			
			$i = 0;
			foreach($write as $prop => $value) {
				
				if($i++ > 0) echo ', ' . "\n"; 
				echo $tabs3;
				echo $prop;
				echo ': "';
				echo $value;
				echo '"';
			
			}
			
			echo "\n";
			echo $tabs2 . '}, ' . "\n";
			echo $tabs2 . 'movehand: {' . "\n";
			
			$i = 0;
			foreach($move as $prop => $value) {
				
				if($i++ > 0) echo ', ' . "\n"; 
				echo $tabs3;
				echo $prop;
				echo ': "';
				echo $value;
				echo '"';
			
			}
			
			echo "\n";
			echo $tabs2 . '}' . "\n";
			echo $tabs1 . '},' . "\n";
			
		}
	}
	
}
?>