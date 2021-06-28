<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsPolyfoldSliderFront extends RevSliderFunctions {
	
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
		add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider){
		$enabled = false;

		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;

		$settings = $slider->get_params();
		if(empty($settings)) return $enabled;
		$addOns = $this->get_val($settings, 'addOns', false);
	 	if(empty($addOns)) return false;
		
		if(
			$this->get_val($settings, array('addOns', 'revslider-polyfold-addon', 'bottom', 'enabled'), false) === true ||
			$this->get_val($settings, array('addOns', 'revslider-polyfold-addon', 'top', 'enabled'), false) === true
		){
			$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
			if(empty($addOn)) return false;
			$enabled = $this->get_val($addOn, 'enable', false);
	 		if($this->isFalse($enabled)) return false;
			return $addOn;
		}
		
		return $enabled;
	}
	
	// private function isEnabled($slider) {
	// 	$settings = $slider->get_params();
	// 	if(empty($settings)) return false;
		
	// 	$addOns = $this->get_val($settings, 'addOns', false);
	// 	if(empty($addOns)) return false;
		
	// 	$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
	// 	if(empty($addOn)) return false;
		
	// 	$enabled = $this->get_val($addOn, 'enable', false);
	// 	if($this->isFalse($enabled)) return false;
		
	// 	return $addOn;
	// }
	
	public function check_addon_active($record) {
		if(empty($record)) return $record;
		
		// addon enabled
		$addOn = $this->isEnabled($record);
		if(empty($addOn)) return $record;
		
		
		$top = $this->get_val($addOn, 'top', false);
		$enabled = $this->get_val($top, 'enabled', false);
		if($this->isFalse($enabled)) {
			$bottom = $this->get_val($addOn, 'bottom', false);
			$enabled = $this->get_val($bottom, 'enabled', false);
		}
		
		if($this->isFalse($enabled)) return $record;
		
		$this->add_scripts();
		remove_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10);
		
		return $record;
		
	}
	
	public function add_scripts() {
	
		$handle = 'rs-' . $this->pluginTitle . '-front';
		$base   = $this->pluginUrl . 'public/assets/';
		$path = $base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js';
		$_jsPathMin = file_exists(RS_POLYFOLD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		wp_enqueue_style($handle, $base . 'css/revolution.addon.' . $this->pluginTitle . '.css', array(), $this->version);
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
		$_jsPathMin = file_exists(RS_POLYFOLD_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['polyfold'] = '<script'. $addition .' src="'. $this->pluginUrl . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'polyfold';
		return $wait;
	}
	
	public function check_if_ajax_loaded($r, $output) {
		if($output->ajax_loaded !== true) return $r;
		
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $r;
		
		$html = '<link rel="stylesheet" href="'. $this->pluginUrl . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css">'."\n";
		return $html . $r;
	}
	
	public function write_init_script($slider, $id) {
		
		// addon enabled
		$addOn = $this->isEnabled($slider);
		if(empty($addOn)) return;
		
		// edges enabled
		$top = $this->get_val($addOn, 'top', false);
		$bottom = $this->get_val($addOn, 'bottom', false);
		
		$topEnabled = $this->get_val($top, 'enabled', false);
		$bottomEnabled = $this->get_val($bottom, 'enabled', false);
		
		$topEnabled = !$this->isFalse($topEnabled);
		$bottomEnabled = !$this->isFalse($bottomEnabled);
		
		if(wp_is_mobile()) {
			
			if($topEnabled) {
				
				$topEnabled = $this->get_val($top, 'hideOnMobile', false);
				$topEnabled = !$this->isFalse($topEnabled);
				
			}
			if($bottomEnabled) {
				
				$topEnabled = $this->get_val($bottom, 'hideOnMobile', false);
				$bottomEnabled = !$this->isFalse($bottomEnabled);
				
			}
			
		}
		
		$id = $slider->get_id();
		$title = $this->pluginTitle;
		$tabs1 = "\t\t\t\t\t\t";
		$tabs2 = "\t\t\t\t\t\t\t";
		$tabs3 = "\t\t\t\t\t\t\t\t";
		
		for($i = 0; $i < 2; $i++) {
			
			if($i === 0) {
				
				if(!$topEnabled) continue;
				$alias = $top;
				$edge = 'top';
				
			}
			else {
				
				if(!$bottomEnabled) break;
				$alias = $bottom;
				$edge = 'bottom';
				
			}
			
			$scroll     = $this->get_val($alias, 'scroll',     true);
			$responsive = $this->get_val($alias, 'responsive', true);
			$negative   = $this->get_val($alias, 'negative',   false);
			$animated   = $this->get_val($alias, 'animated',   false);
			$inverted   = $this->get_val($alias, 'inverted',   false);
			
			$scroll     = $this->isFalse($scroll)     ? 'false' : 'true';
			$responsive = $this->isFalse($responsive) ? 'false' : 'true';
			$negative   = $this->isFalse($negative)   ? 'false' : 'true';
			$animated   = $this->isFalse($animated)   ? 'false' : 'true';
			$inverted   = $this->isFalse($inverted)   ? 'false' : 'true';
			
			$color      =            $this->get_val($alias, 'color',       '#ffffff');
			$range      =            $this->get_val($alias, 'range',       'slider');
			$point      =            $this->get_val($alias, 'point',       'sides');
			$placement  =     intval($this->get_val($alias, 'placement',   1));
			$height     = abs(intval($this->get_val($alias, 'height',      100)));
			$leftWidth  = abs(intval($this->get_val($alias, 'leftWidth',  50)) * .01);
			$rightWidth = abs(intval($this->get_val($alias, 'rightWidth', 50)) * .01);
			
			if(!$color) $color = '#ffffff';
			$maxWidth = $point === 'sides' ? 1 : 0.5;
			
			$leftWidth  = max(min($leftWidth, $maxWidth), 0);
			$rightWidth = max(min($rightWidth, $maxWidth), 0);
			
			if($i === 1) echo "\n" . $tabs1;
			echo 'if(typeof RsPolyfoldAddOn !== "undefined") {' . "\n";
			echo $tabs2 . 'RsPolyfoldAddOn(jQuery, revapi' . $id . ', {' . "\n";
			echo $tabs3 . 'position: "'  . $edge . '",'. "\n";
			echo $tabs3 . 'color: "'     . $color . '",'. "\n";
			echo $tabs3 . 'scroll: '     . $scroll . ','. "\n";
			echo $tabs3 . 'height: '     . $height . ','. "\n";
			echo $tabs3 . 'range: "'     . $range . '",'. "\n";
			echo $tabs3 . 'point: "'     . $point . '",'. "\n";
			echo $tabs3 . 'placement: '  . $placement . ','. "\n";
			echo $tabs3 . 'responsive: ' . $responsive . ','. "\n";
			echo $tabs3 . 'negative: '   . $negative . ','. "\n";
			echo $tabs3 . 'leftWidth: '  . $leftWidth . ','. "\n";
			echo $tabs3 . 'rightWidth: ' . $rightWidth;
			
			if($scroll === 'true') {
				
				echo ',' . "\n" . $tabs3 . 'inverted: ' . $inverted . ',' . "\n";
				echo $tabs3 . 'animated: '   . $animated;
				if($animated === 'true') {

					echo ',' . "\n" . $tabs3 . 'ease: "' . $this->get_val($alias, 'ease', 'ease-out') . '",' . "\n";
					echo $tabs3 . 'time: ' . abs(floatval($this->get_val($alias, 'time', 0.3)));
					
				}
				
			}
			
			echo "\n" . $tabs2 . '});' . "\n";
			echo $tabs1 . '}' . "\n";
			
		}
		
	}
	
}
?>