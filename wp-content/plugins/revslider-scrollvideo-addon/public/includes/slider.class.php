<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsScrollvideoSliderFront extends RevSliderFunctions {
	
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
		// Set Static Layer container to true
		add_filter('revslider_get_do_static_layers', array($this, 'enable_static_container'),10,2);
		add_filter('revslider_set_static_slide', array($this,'add_static_layer'),10,2);
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		//add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		
	}

	// ENABLE STATIC LAYER CONTAINERS
	public function enable_static_container($a,$slider) {		
		return true;
	}

	// ADD SIMPLE VIDEO LAYER
	public function add_static_layer($static_slide,$slider) {
		
		$addonparams = $this->isEnabled($slider->get_slider());
		$layers = $static_slide->get_layers();
		if (!empty($addonparams) && $addonparams['active']===true) {						
			$layers['scrollvideo'] = Array(
	            'alias' => 'Scroll Video',
	            'uid' => 'scrollvideo',	            
	            'size' => Array(
	            			'width' => Array ( 
	            				'd' => Array('v' => '100%','e' => 1),
	                            'n' => Array('v' => '100%'),
	                            't' => Array('v' => '100%'),
	                            'm' => Array('v' => '100%')),

	                    	'height' => Array (
	                       		'd' => Array('v' => '100%','e' => 1),
	                            'n' => Array('v' => '100%'),
	                            't' => Array('v' => '100%'),
	                            'm' => Array('v' => '100%')),	                    	
	                    	'covermode' => 'cover-proportional',
	                    	'scaleProportional' => 1,
	                    	'originalSize' => 1
	                ),
	            'idle' => Array (
	            		'backgroundColor' => 'transparent'
	            	),
	            'position' => Array
	                (                    
	                    'horizontal' => Array(
	                            'd' => Array( 'v' => 'center', 'e' => 1),
	                            'n' => Array( 'v' => 'center'),
	                            't' => Array( 'v' => 'center'),
	                            'm' => Array( 'v' => 'center')),

	                    'vertical' => Array(
	                            'd' => Array( 'v' => 'middle', 'e' => 1),
	                            'n' => Array( 'v' => 'middle'),
	                            't' => Array( 'v' => 'middle'),
	                            'm' => Array( 'v' => 'middle')),
	                    'zIndex' => 4
	                ),
	            'timeline' => Array
	                (	'scrollBased' => 'false',
	                    'frames' => Array(
	                            'frame_0' => Array('timeline' => Array()),
	                            'frame_1' => Array('timeline' => Array('frameLength' => 300)),
	                            'frame_999' => Array('transform' => Array('opacity' => 0), 'timeline' => Array('start' => 9000,'endWithSlide' => 1,'frameLength' => 300))
	                    ),                    
	                    'frameOrder' => Array(
	                            '0' => Array('id' => 'frame_0', 'start' => -1),
	                            '1' => Array('id' => 'frame_1', 'start' => 0),
	                            '2' => Array('id' => 'frame_999','start' => 9000)
	                        )                    
	                ),
	            'behavior' => Array('baseAlign' => 'slide'),
	            'group' => Array( 'groupOrder' => 5),
	            'type' => 'shape'
	        );			
			$static_slide->set_layers_raw($layers);
		}
		return $static_slide;
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
		$_jsPathMin = file_exists(RS_SCROLLVIDEO_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		wp_enqueue_script($handle, $base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('jquery'), $this->version, true);
		
		add_filter('revslider_modify_waiting_scripts', array($this, 'add_waiting_script_slugs'), 10, 1);
	}
	
	public function add_html_script_additions($return, $output){
		if($output instanceof RevSliderSlider){
			$addOn = $this->isEnabled($output);
			if(empty($addOn)) return $return;
		}else{
			if($output->ajax_loaded !== true) return $return;
			
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
		$_jsPathMin = file_exists(RS_SCROLLVIDEO_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['scrollvideo'] = '<script'. $addition .' src="'. $this->pluginUrl . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'scrollvideo';
		return $wait;
	}
	
	public function write_init_script($slider, $id) {
		
		$enabled = $this->isEnabled($slider);
		$id = $slider->get_id();		

		if (!empty($enabled) && $enabled['active']===true)  {	
				
				$spinner = 	$enabled['keepspinner'] === 1 || $enabled['keepspinner']===true ? 'true' : 'false';
				$scroll = 	isset($enabled['blockscroll']) ? $enabled['blockscroll'] === 1 || $enabled['blockscroll']===true ? 'true' : 'false' : 'false';
				$winoffset = isset($enabled['winoffset']) ? $enabled['winoffset'] : "0%";
				$winoffsetend = isset($enabled['winoffsetend']) ? $enabled['winoffsetend'] : "50%";
				$ver = isset($enabled['ver']) ? $enabled['ver'] : "6.3.0";
				echo                  		"\n";
				echo '                		if(typeof RevSliderScrollvideo !== "undefined") RevSliderScrollvideo(revapi' . $id . ',{mp4:"'.$enabled['mpeg'].'", winoffset:"'.$winoffset.'", ver:"'.$ver.'", winoffsetend:"'.$winoffsetend.'" ,spinner:'.$spinner.', scroll:'.$scroll.', dir:"'.$enabled['sequence']['dir'].'", first:1, last:'.$enabled['sequence']['last'].', fps:'.$enabled['sequence']['fps'].', quality:'.$enabled['sequence']['quality'].'});'."\n";
		}
	
		
	}
	
}
?>