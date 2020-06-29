<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsPaintBrushSlideFront extends RevSliderFunctions {
	
	private $title,
			$strings = array('style'),
			$booleans = array('fixedges', 'blur', 'scaleblur', 'responsive', 'disappear', 'carousel');
	
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
				else if($this->sanitize($key, $value) === $b[$key]) {
					
					unset($a[$key]);

				}
			
			}
		
		}
		
		return $a;
	
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		
		if(empty($enabled)) return;
		
		// check if enabled for slide
		$addOn = $this->isEnabled($slide);
		
		if(empty($addOn)) return;
		
		$settings = $slide->get_params();
		
		if(empty($settings)) return false;
		
		$image = $this->get_val($addOn, 'image', array());
		$source = $this->get_val($image, 'source', 'local');
		$carousel = $this->get_val($slider, 'settings', array());
		$carousel = $this->get_val($carousel, 'type', 'standard');
		
		$src = false;
		switch($source) {
			
			case 'local':
				
				$src = $this->get_val($image, 'custom', '');
			
			break;
			
			case 'main':
				
				$bg = $this->get_val($settings, 'bg', array());
				$tpe = $this->get_val($bg, 'type', 'trans');
				
				if($tpe === 'image') $src = $this->get_val($bg, 'image', '');
			
			break;
			
		}
		
		if(!empty($src)) {
			
			$mobile = wp_is_mobile();
			if($mobile) {
				
				$addonMobile = $this->get_val($addOn, 'mobile', array());
				$bounce = $this->get_val($addonMobile, 'disable', false);
				$bounce = !$this->isFalse($bounce);
				
				if(!empty($bounce)) {
					
					$fallback = $this->get_val($addonMobile, 'fallback', false);
					$fallback = !$this->isFalse($fallback);
					
					if(!empty($fallback)) echo ' data-revaddonpaintbrushfallback="' . $src . '"';
					return;
					
				}
				
			}
			
			$brush     = $this->get_val($addOn, 'brush', array());
			$disappear = $this->get_val($brush, 'disappear', array());
			$blur      = $this->get_val($image, 'blur', array());
			$fixEdges  = $this->get_val($blur, 'fixedges', array());
			
			$size     = $this->get_val($brush, 'size', '80');
			$strength     = $this->get_val($brush, 'strength', '30');
		
		
			$amount   = $this->get_val($blur, 'amount', '10');
			$time     = $this->get_val($disappear, 'time', '1000');
			$edgeFix  = $this->get_val($fixEdges, 'amount', '10');
			
			$scaleblur  = $this->get_val($blur, 'responsive', false);
			$responsive = $this->get_val($brush, 'responsive', false);
			
			$disappear  = $this->get_val($disappear, 'enable', false);
			$blur       = $this->get_val($blur, 'enable', false);
			$fixEdges   = $this->get_val($fixEdges, 'enable', false);
			$style      = $this->get_val($brush, 'style', 'round');
			
			$fixEdges   = $this->isFalse($fixEdges) ? false : true;
			$blur       = $this->isFalse($blur) ? false : true;
			$scaleblur  = $this->isFalse($scaleblur) ? false : true;
			$responsive = $this->isFalse($responsive) ? false : true;
			$disappear  = $this->isFalse($disappear) ? false : true;
			$carousel   = $carousel !== 'carousel' ? false : true;
			
			$size     = intval($size);
			$strength = intval($strength);
			$amount   = intval($amount);
			$time     = intval($time);
			$edgeFix  = intval($edgeFix);
			
			$settings = array(
			
				'size'       => $size,
				'origsize'   => $size,
				'strength'	 => $strength,
				'blurAmount' => $amount,
				'fadetime'   => $time,
				'image'      => $src,
				'edgefix'    => $edgeFix,
				'fixedges'   => $fixEdges,
				'style'      => $style,
				'blur'       => $blur,
				'scaleblur'  => $scaleblur,
				'responsive' => $responsive,
				'disappear'  => $disappear,
				'carousel'   => $carousel
			
			);
			
			$this->shrinkObject($settings, array(
			
				'blurAmount' => 10,
				'fadetime'   => 1000,
				'edgefix'    => 10,
				'fixedges'   => false,
				'style'      => 'round',
				'blur'       => false,
				'scaleblur'  => false,
				'responsive' => false,
				'disappear'  => false,
				'carousel'   => false
				
			));
			
			echo " data-revaddonpaintbrush='" . json_encode($settings) . "'";
			if(!empty($fixEdges)) echo ' data-revaddonpaintbrushedges="true"';
			
		}
		
	}
	
}
?>