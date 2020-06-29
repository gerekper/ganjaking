<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsSliceySlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_slicey'), 10, 3);
	
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
	
	// this function removes any potential "Slicey" Layers from the Layer list if the AddOn is disabled
	public function check_slicey($layers, $slider, $static_slide) {
		
		$slider = $this->get_val($slider, 'slider', array());
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) {
			
			$ar = array();
			foreach($layers as $layer) {
				
				$isSlicey = false;
				if(array_key_exists('subtype', $layer)) {
					
					$slicey = $this->get_val($layer, 'subtype', false);
					$isSlicey = $slicey === 'slicey';
					
				}
				
				if(!$isSlicey) $ar[] = $layer;
				
			}
			
			return $ar;
			
		}

		return $layers;
		
	}
	
	public function write_layer_attributes($layer, $slide, $slider) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$subtype = $this->get_val($layer, 'subtype', '');
		if($subtype !== 'slicey') return;
			
		$addOn = $this->get_val($layer, 'addOns', array());
		$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
		if(empty($addOn)) return;
			
		$offset = $this->get_val($addOn, 'scaleOffset', false);
		$offset = str_replace('%', '', $offset);
		$offset = intval($offset);
		if($offset === 0) return;
			
		$settings = array(
			'offset' => $offset,
			'blurstart' => $this->get_val($addOn, 'blurStart', 'inherit'),
			'blurend' => $this->get_val($addOn, 'blurEnd', 'inherit')
		);
		
		echo "								data-slicey='"    . json_encode($settings) . "' " . "\n";
	
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$params = $slide->get_params();
		$panZoom = $this->get_val($params, 'panzoom', array());
		$panZoom = $this->get_val($panZoom, 'set', false);
		
		$panZoom = !$this->isFalse($panZoom);
		if(empty($panZoom)) return;
		
		$addOn = $this->get_val($params, 'addOns', array());
		$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
		if(empty($addOn)) return;
		
		$shadow = $this->get_val($addOn, 'shadow', array());
		$blur = $this->get_val($shadow, 'blur', '0');
		$strength = $this->get_val($shadow, 'strength', '0');
		$color = $this->get_val($shadow, 'color', 'rgba(0, 0, 0, 0.35)');
		
		$blur = str_replace('px', '', $blur);
		$strength = str_replace('px', '', $strength);
		
		echo ' data-slicey="' . '0px 0px ' . $blur . 'px ' . $strength . 'px ' . $color . '"';
		
	}
	
}
?>