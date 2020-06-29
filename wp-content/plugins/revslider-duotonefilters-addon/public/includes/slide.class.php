<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsDuotoneFiltersSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($_title) {
		
		$this->title = $_title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 2);
	
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		return true;
	
	}
	
	private function isEnabled($clas, $checkEnable = false) {
		
		$settings = $clas->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->title . 'filters-addon', false);
		if(empty($addOn)) return false;
		
		if($checkEnable) {
			$enabled = $this->get_val($addOn, 'enable', false);
			if($this->isFalse($enabled)) return false;
		}
		
		return $addOn;
	
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		// check if enabled from slider
		$addOn = $this->isEnabled($slider, true);
		if(empty($addOn)) return;
		
		// check if enabled from slider
		$addOn = $this->isEnabled($slide);
		if(empty($addOn)) return;
		
		// check if enabled for slide
		$filter = $this->get_val($addOn, 'filter', 'none');
		if($filter !== 'none') echo " data-duotonefilter='rs-duotone-" . $filter . "'";
		
	}
	
}
?>