<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if( !defined( 'ABSPATH') ) exit();

include_once(RS_REVEALER_PLUGIN_PATH . 'public/includes/preloaders.class.php');

if(class_exists('RevSliderFunctions')) {

	class RsRevealerSliderFront extends RevSliderFunctions {
		
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
			add_action('revslider_fe_javascript_option_output', array($this, 'write_init_options'), 10, 1);
			
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
			$base = $this->pluginUrl . 'public/assets/';
			
			wp_enqueue_style(
			
				$handle, 
				$base . 'css/revolution.addon.' . $this->pluginTitle . '.css', 
				array(), 
				$this->version
				
			);
			
			wp_enqueue_style(
			
				$handle . '-preloaders', 
				$base . 'css/revolution.addon.' . $this->pluginTitle . '.preloaders.css', 
				array(), 
				$this->version
				
			);
			
			wp_enqueue_script(
			
				$handle, 
				$base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js', 
				array('jquery'), 
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
			$addOn = $this->isEnabled($slider);
			if(!empty($addOn)) {
				
				$id = $slider->get_id();
				$preloader = $this->get_val($addOn, 'spinner', array());
				$preloader = $this->get_val($preloader, 'type', 'default');
				$preloaders = RsAddOnRevealPreloaders::getPreloaders();
				
				if($preloader !== 'default' && array_key_exists($preloader, $preloaders)) {
					$preloader = $preloaders[$preloader];
					$preloader = json_encode($preloader);
				}
				else {
					$preloader = 'false';
				}

				echo "\n";
				echo "\t\t\t\t\t\t" . 'if(typeof RsRevealerAddOn !== "undefined") RsRevealerAddOn(tpj, revapi' . $id . ', ' . $preloader . ');' . "\n";
				
			}
			
		}
		
		private function minMax($val) {
		
			$val = intval($val);
			$val = max(10, $val);
			return min(10000, $val);
		
		}
		
		public function write_init_options($slider) {
			
			// addon enabled
			$addOn = $this->isEnabled($slider);
			if($addOn) {
				
				$_title = $this->pluginTitle;
				$tabs = "\t\t\t\t\t\t\t\t";
				$tabsa = "\t\t\t\t\t\t\t\t\t";
				
				$color = $this->get_val($addOn, 'color', '#000000');
				$preloader = $this->get_val($addOn, 'spinner', array());
				$spinner = $this->get_val($preloader, 'type', 'default');
				$spinnerColor = $this->get_val($preloader, 'color', '#FFFFFF');
				$direction = $this->get_val($addOn, 'direction', 'open_horizontal');
				
				$overlay = $this->get_val($addOn, 'overlay', array());
				$overlay_enabled = $this->get_val($overlay, 'enable', false);
				$overlay_enabled = !$this->isFalse($overlay_enabled);
				
				if($overlay_enabled) $overlay_color = $this->get_val($overlay, 'color', '#000000');
				if(class_exists('RSColorpicker')) {
					
					if(strpos($direction, 'corner') === false) {
						$color = RSColorpicker::get($color);
					}
					else {
						$color = RSColorpicker::process($color, true);
						$color = strpos($color[1], 'gradient') === false ? $color[0] : json_encode($color[2]);
					}
					
					if($overlay_enabled) $overlay_color = RSColorpicker::get($overlay_color);
					if($spinner == '2') {
						$spinnerColor = RSColorpicker::processRgba($spinnerColor);
						$spinnerColor = str_replace('rgb', 'rgba', $spinnerColor);
						$spinnerColor = str_replace(')', ',', $spinnerColor);
					}
					
				}
				
				$duration = $this->get_val($addOn, 'duration', '500');
				$delay = $this->get_val($addOn, 'delay', '0');
				$overlay_duration = $this->get_val($overlay, 'duration', '500');
				$overlay_delay = $this->get_val($overlay, 'delay', '0');
				
				$duration = str_replace('ms', '', $duration);
				$delay = str_replace('ms', '', $delay);
				$overlay_duration = str_replace('ms', '', $overlay_duration);
				$overlay_delay = str_replace('ms', '', $overlay_delay);
				
				$delay = $this->minMax($delay);
				$overlay_delay = $this->minMax($overlay_delay);
				$duration = $this->minMax($duration);
				$overlay_duration = $this->minMax($overlay_duration);
				
				echo $tabs . 'revealer: {' . "\n";
				echo $tabsa . 'direction: "' . $direction . '",' . "\n";
				echo $tabsa . "color: '" . $color . "'," . "\n";
				echo $tabsa . 'duration: "' . $duration . '",' . "\n";
				echo $tabsa . 'delay: "' . $delay . '",' . "\n";
				echo $tabsa . 'easing: "' . $this->get_val($addOn, 'easing', 'Power2.easeOut') . '",' . "\n";
				
				if($overlay_enabled) {
					echo $tabsa . 'overlay_enabled: true,' . "\n";
					echo $tabsa . 'overlay_color: "' . $overlay_color . '",' . "\n";
					echo $tabsa . 'overlay_duration: "' . $overlay_duration . '",' . "\n";
					echo $tabsa . 'overlay_delay: "' . $overlay_delay . '",' . "\n";
					echo $tabsa . 'overlay_easing: "' . $this->get_val($overlay, 'easing', 'Power2.easeOut') . '",' . "\n";
				}
				
				echo $tabsa . 'spinner: "' . $spinner . '",' . "\n";
				echo $tabsa . 'spinnerColor: "' . $spinnerColor . '",' . "\n";
				echo $tabs . '},' . "\n";
				
			}
		
		}
		
	}
}
?>