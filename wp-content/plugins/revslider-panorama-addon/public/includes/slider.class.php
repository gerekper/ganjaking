<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

class RsPanoramaSliderFront extends RevSliderFunctions {
	
	private $version,
			$pluginUrl, 
			$pluginTitle;
					 
	public function __construct($version, $pluginUrl, $pluginTitle, $isAdmin = false) {
		
		$this->version     = $version;
		$this->pluginUrl   = $pluginUrl;
		$this->pluginTitle = $pluginTitle;
		
		if(!$isAdmin) add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1);	
		else add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		add_action('revslider_add_slider_base_post', array($this, 'write_ajax_script'), 10, 1);
		
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
	
	public function add_scripts() {
		
		$handle = 'rs-' . $this->pluginTitle . '-front';
		$base   = $this->pluginUrl . 'public/assets/';
		
		wp_enqueue_style(
		
			$handle, 
			$base . 'css/revolution.addon.' . $this->pluginTitle . '.css', 
			array(), 
			$this->version
			
		);
		
		wp_enqueue_script(

			'three', 
			$base . 'js/three.min.js', 
			array('jquery'), 
			$this->version, 
			true

		);
		
		wp_enqueue_script(
		
			$handle, 
			$base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js', 
			array('jquery', 'three'), 
			$this->version, 
			true
			
		);
		
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

	public function write_init_script($slider, $id) {

		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(!empty($enabled)) {

			$id = $slider->get_id();
			echo "						\n";
			echo '						if(typeof RsAddonPanorama !== "undefined") RsAddonPanorama(tpj, revapi' . $id . ');'."\n";

		}

	}
	
	public function write_ajax_script($output){
		$enabled = $this->isEnabled($output->slider);
		
		if(!empty($enabled)) {
			if($output->ajax_loaded === true){
				$base   = $this->pluginUrl . 'public/assets/';
				echo '						<link rel="stylesheet" href="'.$base . 'css/revolution.addon.' . $this->pluginTitle . '.css?ver='.$this->version.'" type="text/css" media="alL" />'."\n";
				echo '						<script type="text/javascript" src="'.$base . 'js/three.min.js?ver='.$this->version.'"></script>'."\n";
				echo '						<script type="text/javascript" src="'.$base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js?ver='.$this->version.'"></script>'."\n";
			}
		}
	}
	
}
?>