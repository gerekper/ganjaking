<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsLiquideffectSliderAdmin extends RevSliderFunctions {
	
	public function __construct() {
		
		add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);
		add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);	
		
	}
	
	public function export_adddon_images($data, $slides, $sliderParams) {
		
		foreach($slides as &$slide) {
			
			$image = $this->get_val($slide, array('params', 'addOns', 'revslider-liquideffect-addon', 'map', 'custom'), false);
			if(!empty($image)) $data['used_images'][$image] = true;
			
		}
		
		return $data;
		
	}
	
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {

		$rev_five = $this->get_val($data, array('params', 'liquideffect_custommap'), false);
		$rev_six = empty($rev_five) ? $this->get_val($data, array('params', 'addOns', 'revslider-liquideffect-addon'), array()) : false;
		
		// importing from 5.0 structure
		if(!empty($rev_five)) {
			
			$alias = $this->get_val($data, array('sliderParams', 'params', 'alias'), '');	
			$imported = $this->get_val($data, 'imported', array());
			
			$url = $this->check_file_in_zip($image_path, $rev_five, $alias, $imported);
			$url = $this->get_image_url_from_path($url);

			if(!empty($url)) $data['params']['liquideffect_custommap'] = $url;
			
		}
		// importing from 6.0 structure
		else if(!empty($rev_six)) {
			
			$image = $this->get_val($rev_six, array('map', 'custom'), false);
			if(!empty($image)) {

				$alias = $this->get_val($data, array('sliderParams', 'alias'), '');	
				$imported = $this->get_val($data, 'imported', array());
				
				$url = $this->check_file_in_zip($image_path, $image, $alias, $imported);
				$url = $this->get_image_url_from_path($url);
				
				if(!empty($url)) $data['params']['addOns']['revslider-liquideffect-addon']['map']['custom'] = $url;

			}
		}
		
		return $data;
	}
	
}
?>