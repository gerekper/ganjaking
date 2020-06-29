<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class rs_whiteboard_fe_slide extends RevSliderFunctions {
	
	private $slug = 'whiteboard';
	
	public function __construct(){
		
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		return true;
	
	}
	
	private function shrinkObject(&$obj, $defaults) {
		
		foreach($obj as $key => $value) {
		
			if(array_key_exists($key, $defaults)) {
				
				if($key === 'direction' || $key === 'gotoLayer') $value = trim($value);
				else $value = intval($value);
				if($value === $defaults[$key]) unset($obj[$key]);
			
			}
		}
	}
	
	public function write_layer_attributes($layer, $slide, $slider){
		
		/*
			BEGIN SLIDER ENABLED CHECK
		*/
		$params = $this->get_val($slider, 'params', false);
		if(empty($params)) return;
		
		$addOns = $this->get_val($params, 'addOns', false);
		if(empty($addOns)) return;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->slug . '-addon', false);
		if(empty($addOn)) return;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return;
		/*
			END SLIDER ENABLED CHECK
		*/
		
		/*
			BEGIN LAYER ENABLED CHECK
		*/
		$addOns = $this->get_val($layer, 'addOns', false);
		if(empty($addOns)) return;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->slug . '-addon', false);
		if(empty($addOn)) return;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return;
		/*
			END LAYER ENABLED CHECK
		*/
		
		$hand = (array)$this->get_val($addOn, 'hand', array());
		$jitter = (array)$this->get_val($addOn, 'jitter', array());
		
		$hand = array_merge(array(
		
			'angle' => 15,
			'angleRepeat' => 10,
			'direction' => 'left_to_right',
			'rotation' => 0,
			'mode' => 'write',
			'gotoLayer' => 'off',
			'type' => 'right',
			'x' => 0,
			'y' => 0
		
		), $hand);
		
		$jitter = array_merge(array(
		
			'distance' => 80,
			'distanceHorizontal' => 100,
			'offset' => 10,
			'offsetHorizontal' => 0,
			'repeat' => 5
		
		), $jitter);
		
		$this->shrinkObject($hand, array(
		
			'angle' => 15,
			'angleRepeat' => 10,
			'direction' => 'left_to_right',
			'rotation' => 0,
			'gotoLayer' => 'off',
			'x' => 0,
			'y' => 0
		
		));
		
		$this->shrinkObject($jitter, array(
		
			'distance' => 80,
			'distanceHorizontal' => 100,
			'offset' => 10,
			'offsetHorizontal' => 0,
			'repeat' => 5
		
		));
		
		$attr = array('hand' => $hand, 'jitter' => $jitter);
		$slider->set_param('wb_is_used', true);
		echo "								data-whiteboard='".$this->json_encode_for_frontend($attr)."'"."\n";
		
	}
	
	
	private function json_encode_for_frontend($arr){
		$json = '';
		if(!empty($arr)){
			$json = json_encode($arr);
		}
		
		return($json);
	}
	
}
?>