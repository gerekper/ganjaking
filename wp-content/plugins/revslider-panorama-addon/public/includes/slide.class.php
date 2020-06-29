<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsPanoramaSlideFront extends RevSliderFunctions {
	
	private $title,
			$defaults = false,
			$strings = array('direction', 'controls'),
			$booleans = array('enable', 'smooth');
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_filter('rs_action_output_layer_action', array($this, 'write_layer_actions'), 10, 7);
	
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
	
	public function write_layer_actions($events, $action_ar, $all_actions, $num, $slide) {
		
		$action = $this->get_val($action_ar, 'action', false);
		if(empty($action)) return $events;
		
		// check to make sure the action is a panorama
		if(strpos($action, 'panorama') === false) return $events;
		
		// mouse event
		$event = $this->get_val($action_ar, 'tooltip_event', 'click');
		
		// zoom percentage
		$perc = $this->get_val($action_ar, 'panorama_amount', '5');
		
		// normalize events for mobile
		if(wp_is_mobile()) {
			
			switch($event) {
				
				case 'mousedown':
				case 'mouseenter':
				
					$event = 'touchstart';
				
				break;
				
				case 'mouseup':
				case 'mouseleave':
				
					$event = 'touchend';
				
				break;
				
			}
			
		}
		
		$evt = array(
		
			'o' => $event,
			'a' => $action,
			'percentage' => $perc
			
		);
		
		$events[] = $evt;
		return $events;
		
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
				else if($this->sanitize($key, $value) === $b[$key]) {
					
					unset($a[$key]);

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
		
		$addOn = json_encode($addOn);
		$addOn = json_decode($addOn, true);
		
		$this->shrinkObject($addOn, array(
		
			'autoplay' => array(
				'enable' => false,
				'direction' => 'forward',
				'speed' => 100,
			),
			'interaction' => array(
				'controls' => 'throw',
				'speed' => 750,
				'onlyHorizontal' => false
			),
			'zoom' => array(
				'enable' => false,
				'smooth' => true,
				'min' => 75,
				'max' => 150,
			),
			'camera' => array(
				'fov' => 75,
				'far' => 1000,
			),
			'sphere' => array(
				'radius' => 100,
				'wsegments' => 100,
				'hsegments' => 40,
			)
		));
		
		$params = $slide->get_params();
		$bg = $this->get_val($params, 'bg', array());
		
		$type = $this->get_val($bg, 'type', 'trans');
		if($type !== 'image') return;
		
		$image = $this->get_val($bg, 'image', '');
		if(empty($image)) return;

		
		if(!wp_is_mobile()) unset($addOn['mobilelock']);
		
		$addOn['image'] = $image;
		echo " data-panorama='" . json_encode($addOn) . "'";
		
	}
	
}
?>