<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsTypewriterSlideFront extends RevSliderFunctions {
	
	private $title,
			$layers = array();
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
	
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
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->title . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	
	}
	
	public function write_layer_attributes($layer, $slide, $slider) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOn = $this->get_val($layer, 'addOns', array());
		$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
		$enabled = $this->get_val($addOn, 'enable', false);
		$enabled = !$this->isFalse($enabled);
		if(empty($enabled)) return;
		
		// bounce for non-TextLayers
		$type = $this->get_val($layer, 'type', false);
		if($type !== 'text') return;
		
		// bounce if Layer has no actual text
		$layerText = $this->get_val($layer, 'text', false);
		if(empty($layerText)) return;
		
		$styles = $this->get_val($layer, 'idle', array());
		$bgColor = $this->get_val($styles, 'backgroundColor', false);
		
		if(is_array($addOn)) $addOn['background'] = empty($bgColor) ? 'off' : 'on';
		else $addOn->background = empty($bgColor) ? 'off' : 'on';
				
		echo " 								data-" . $this->title . "='" . $this->jsonEncode($addOn) . "'" . "\n";
		
	}
	
	private function jsonEncode($obj) {
		
		return !empty($obj) ? json_encode($obj) : '';
		
	}
	
}
?>