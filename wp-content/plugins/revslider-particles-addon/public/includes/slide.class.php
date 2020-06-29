<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsParticlesSlideFront extends RevSliderFunctions {
	
	private $title,
			$strings = array('shape', 'color', 'zIndex', 'direction', 'hoverMode', 'clickMode'),
			$booleans = array('random', 'enable', 'opacityRandom', 'randomSpeed', 'straight', 'bounce', 'sync');
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
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
	
	private function sanitize($key, $val) {
		
		if(is_null($val)) return false;
		if(in_array($key, $this->strings)) return $val;
		if(in_array($key, $this->booleans)) return !$this->isFalse($val);
		
		if(is_string($val)) {
		
			if(strpos($val, '.') !== false) return floatval($val);
			return intval($val);
		
		}
		
		return $val;
		
	}
	
	private function shrinkObject(&$a, $b) {
	
		foreach($a as $key => $value) {
		
			if(array_key_exists($key, $b)) {
				
				if(is_array($value)) {
			
					$a[$key] = $this->shrinkObject($value, $b[$key]);
					if(empty($a[$key])) unset($a[$key]);
				
				}
				else {
					
					$a[$key] = $this->sanitize($key, $value);
					if($a[$key] === $b[$key]) unset($a[$key]);

				}
			
			}
		
		}
		
		return $a;
	
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOn = $this->isEnabled($slide);
		if(empty($addOn)) return;
		
		if(wp_is_mobile()) {
			
			$enabled = $this->get_val($addOn, 'hideOnMobile', false);
			if(!$this->isFalse($enabled)) return;
			
		}
		
		$addOn = json_encode($addOn);
		$addOn = json_decode($addOn, true);
		
		$this->shrinkObject($addOn, array(
			
			'particles' => array(
				'shape' => 'circle',
				'number' => 80,
				'size' => 6,
				'sizeMin' => 1,
				'random' => true
			),
			'styles' => array(
				'border' => array(
					'enable' => false,
					'color' => '#ffffff',
					'opacity' => 100,
					'size' => 1
				),
				'lines' => array(
					'enable' => false,
					'color' => '#ffffff',
					'width' => 1,
					'opacity' => 100,
					'distance' => 150
				),
				'particle' => array(
					'color' => '#ffffff',
					'opacity' => 100,
					'opacityMin' => 10,
					'opacityRandom' => false,
					'zIndex' => 'default'
				)
			),
			'movement' => array(
				'enable' => true,
				'randomSpeed' => true,
				'speed' => 1,
				'speedMin' => 1,
				'direction' => 'none',
				'straight' => true,
				'bounce' => false
			),
			'interactivity' => array(
				'hoverMode' => 'none',
				'clickMode' => 'none'
			),
			'bubble' => array(
				'distance' => 400,
				'size' => 40,
				'opacity' => 40
			),
			'grab' => array(
				'distance' => 400,
				'opacity' => 50
			),
			'repulse' => array(
				'distance' => 200,
				'easing' => 100
			),
			'pulse' => array(
				'size' => array(
					'enable' => false,
					'speed' => 40,
					'min' => 1,
					'sync' => false
				),
				'opacity' => array(
					'enable' => false,
					'speed' => 3,
					'min' => 0,
					'sync' => false
				)
			)
			
		));
		
		echo " data-rsparticles='" . json_encode($addOn) . "'";
		
	}
	
}
?>