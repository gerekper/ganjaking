<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsBubblemorphSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_bubblemorph'), 10, 3);
	
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
	
	// removes bubblemorth layers that may exist if the AddOn is not officially enabled
	public function check_bubblemorph($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
			
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) {
			
			$ar = array();
			foreach($layers as $layer) {
				
				$isBubblemorph = false;
				if(array_key_exists('subtype', $layer)) {
					
					$bubblemorph = $this->get_val($layer, 'subtype', false);
					$isBubblemorph = $bubblemorph === 'bubblemorph';
					
				}
				
				if(!$isBubblemorph) $ar[] = $layer;
				
			}
			
			return $ar;
			
		}

		return $layers;
		
	}
	
	private function convertSizes($val, $def) {
		$min = $def;
		$max = 0;
		$isNumeric = false;
		$isEnabled = 0;

		$d = $this->get_val($val, 'd', array());
		$n = $this->get_val($val, 'n', array());
		$t = $this->get_val($val, 't', array());
		$m = $this->get_val($val, 'm', array());

		foreach($val as $key => $value){
			if( isset($value['e']) && $value['e'] === true){
				if($value['v'] < $min) $min = $value['v'];
				if($value['v'] > $max) $max = $value['v'];
			}
			$isNumeric = is_numeric($value['v']);

			if (isset($value['e'])) $isEnabled++;	
		}

		if($isNumeric && $isEnabled > 1){
			$d = (isset($d['e']) && $d['e'] === true) ? $this->get_val($d, 'v', $def) : $max;
			$n = (isset($n['e']) && $n['e'] === true) ? $this->get_val($n, 'v', $def) : $max;
			$t = (isset($t['e']) && $t['e'] === true) ? $this->get_val($t, 'v', $def) : $min;
			$m = (isset($m['e']) && $m['e'] === true) ? $this->get_val($m, 'v', $def) : $min;
		} else {
			$d = $this->get_val($d, 'v', $def);
			$n = $this->get_val($n, 'v', $def);
			$t = $this->get_val($t, 'v', $def);
			$m = $this->get_val($m, 'v', $def);
		}
		
		
		return array($d, $n, $t, $m);
		
	}
	
	public function write_layer_attributes($layer, $slide, $slider) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$subtype = $this->get_val($layer, 'subtype', '');
		if($subtype && $subtype === 'bubblemorph') {
			
			$addOns = $this->get_val($layer, 'addOns', array());
			$addOn = $this->get_val($addOns, 'revslider-' . $this->title . '-addon', false);
			if(empty($addOn)) return;
				
			$settings = $this->get_val($addOn, 'settings', array());
			$shadow   = $this->get_val($addOn, 'shadow', array());

			$border   = $this->get_val($addOn, 'border', array());
				
			$max           = $this->get_val($settings, 'maxmorphs', '6');
			$blur_strength = $this->get_val($shadow,   'strength',  '0');
			$border_size   = $this->get_val($border,   'size',      '0');
			$bufferx       = $this->get_val($settings, 'bufferx',   '0');
			$buffery       = $this->get_val($settings, 'buffery',   '0');
			$speedx        = $this->get_val($settings, 'speedx',    '0.25');
			$speedy        = $this->get_val($settings, 'speedy',    '1');
			
			$max = $this->convertSizes($max, '6');
			$blur_strength = $this->convertSizes($blur_strength, '0');
			$border_size = $this->convertSizes($border_size, '0');
			$bufferx = $this->convertSizes($bufferx, '0');
			$buffery = $this->convertSizes($buffery, '0');
			$speedx = $this->convertSizes($speedx, '0.25');
			$speedy = $this->convertSizes($speedy, '1');
			
			for($i = 0; $i < count($speedx); $i++) {
				
				if($speedx[$i] === 'inherit') $speedx[$i] = $speedx[$i - 1];
				if(floatval($speedx[$i]) <= 0) $speedx[$i] = 1;
				
			}
			
			for($i = 0; $i < count($speedy); $i++) {
				
				if($speedy[$i] === 'inherit') $speedy[$i] = $speedy[$i - 1];
				if(floatval($speedy[$i]) <= 0) $speedy[$i] = 1;
				
			}
			
			$max     = implode('|', $max);
			$bufferx = implode('|', $bufferx);
			$buffery = implode('|', $buffery);
			$speedx  = implode('|', $speedx);
			$speedy  = implode('|', $speedy);
			
			$idle    = $this->get_val($layer, 'idle', array());
			$bgcolor = $this->get_val($idle, 'backgroundColor', 'transparent');
			
			if(class_exists('RSColorpicker')) {
				
				if(strpos($bgcolor, '-gradient') === false) {
			
					$bgcolor = RSColorpicker::process($bgcolor, false);
					$bgcolor = $bgcolor[1] !== 'gradient' ? $bgcolor[0] : $bgcolor[2];
				
				}
				else {
					
					$gradient = RSColorpicker::process($bgcolor, false);
					if($gradient[1] === 'gradient_css') $bgcolor = $gradient[2];
				
				}
				
			}
			
			$options = array();
			$options['bg'] = $bgcolor;
			$options['num'] = $max;
			$options['bufferx'] = $bufferx;
			$options['buffery'] = $buffery;
			$options['speedx'] = $speedx;
			$options['speedy'] = $speedy;
			
			for($i = 0; $i < count($blur_strength); $i++) {
			
				if($blur_strength[$i] === 'inherit') $blur_strength[$i] = $blur_strength[$i - 1];

				if(intval($blur_strength[$i]) > 0) {
					
					$blur_color = $this->get_val($shadow, 'color', 'rgba(0, 0, 0, 0.35)');
					$blur_x     = $this->get_val($shadow, 'offsetx', '0');
					$blur_y     = $this->get_val($shadow, 'offsety', '0');
										
					if(!is_array($blur_color)) $blur_color = array($blur_color, $blur_color, $blur_color, $blur_color);
					else $blur_color = $this->convertSizes($blur_color, 'rgba(0, 0, 0, 0.35)');
					
					if(!is_array($blur_x)) $blur_x = array($blur_x, $blur_x, $blur_x, $blur_x);
					else $blur_x = $this->convertSizes($blur_x, '0');
					
					if(!is_array($blur_y)) $blur_y = array($blur_y, $blur_y, $blur_y, $blur_y);
					else $blur_y = $this->convertSizes($blur_y, '0');
					
					$blur_strength = implode('|', $blur_strength);
					$blur_color    = implode('|', $blur_color);
					$blur_x        = implode('|', $blur_x);
					$blur_y        = implode('|', $blur_y);
					
					$options['blur'] = $blur_strength;
					$options['blurcolor'] = $blur_color;
					$options['blurcolor'] = $blur_color;
					$options['blurx'] = $blur_x;
					$options['blury'] = $blur_y;					
					break;
					
				}
				
			}
			
			for($i = 0; $i < count($border_size); $i++) {				
				if($border_size[$i] === 'inherit') $border_size[$i] = $border_size[$i - 1];
				$border_size_temp = str_replace("px", "", $border_size[$i]);				
				if(intval($border_size_temp) > 0) {					
					$border_color = $this->get_val($border, 'color', '#000000');
					if(!is_array($border_color)) $border_color = array($border_color, $border_color, $border_color, $border_color);
					else $border_color = $this->convertSizes($border_color, '0');					
					$border_color = implode('|', $border_color);
					$border_size  = implode('|', $border_size);					
					$options['bordercolor'] = $border_color;
					$options['bordersize'] = $border_size;					
					break;					
				}
				
			}
			
			echo "\t\t\t\t\t\t\t\t" . "data-bubblemorph='" . json_encode($options) . "' " . "\n";
		
		}
	
	}
	
}
?>