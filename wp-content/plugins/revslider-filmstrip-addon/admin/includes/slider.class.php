<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsFilmstripSliderAdmin extends RevSliderFunctions {
	
	public function __construct() {				
		
		add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);	
		add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);	
		
	}
	
	public function export_adddon_images($data, $slides, $sliderParams) {
		
		foreach($slides as $slide) {
			
			$images = $this->get_val($slide, array('params', 'addOns', 'revslider-filmstrip-addon', 'settings'), array());
			foreach($images as $image) {
				
				$url = $this->get_val($image, 'url', false);
				$thumb = $this->get_val($image, 'thumb', false);
				
				if(!empty($url)) $data['used_images'][$url] = true;	
				if(!empty($thumb)) $data['used_images'][$thumb] = true;
			
			}
			
		}
		
		return $data;
		
	}
	
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {
		
		$rev_five = $this->get_val($data, array('params', 'filmstrip_settings'), false);
		$rev_six = $this->get_val($data, array('params', 'addOns', 'revslider-filmstrip-addon'), array());
		
		// importing from 5.0 structure
		if(!empty($rev_five)) {
			
			$settings = json_decode(stripslashes($rev_five), true);
			$alias = $this->get_val($data, array('sliderParams', 'params', 'alias'), '');
			
			if(!empty($settings) && !empty($alias)) {
				
				$imported = $this->get_val($data, 'imported', array());
				foreach($settings as $key => $image) {
					
					$imageId = '';
					$main = $this->get_val($image, 'url', false);
					$thumb = $this->get_val($image, 'thumb', false);
					
					if(!empty($main)) {
						
						$url = $this->check_file_in_zip($image_path, $main, $alias, $imported);
						$url = $this->get_image_url_from_path($url);
						
						if(!empty($url)) {
							$settings[$key]['url'] = $url;
							$id = $this->get_image_id_by_url($url);
							if(!empty($id)) $imageId = $id;
						}
						
					}
					if(!empty($thumb)) {
						
						$url = $this->check_file_in_zip($image_path, $thumb, $alias, $imported);
						$url = $this->get_image_url_from_path($url);
						if(!empty($url)) $settings[$key]['thumb'] = $url;
						
					}
					
					$settings[$key]['ids'] = $imageId;
				
				}
				
				$data['params']['filmstrip_settings'] = json_encode($settings);
			
			}
		}
		// importing from 6.0 structure
		else if(!empty($rev_six)) {
			
			$settings = $this->get_val($rev_six, 'settings', array());
			$alias = $this->get_val($data, array('sliderParams', 'alias'), '');
			
			if(!empty($settings) && !empty($alias)) {
			
				$imported = $this->get_val($data, 'imported', array());
				foreach($settings as $key => $image) {
					
					$imageId = '';
					$main = $this->get_val($image, 'url', false);
					$thumb = $this->get_val($image, 'thumb', false);
					
					if(!empty($main)) {
						
						$url = $this->check_file_in_zip($image_path, $main, $alias, $imported);
						$url = $this->get_image_url_from_path($url);
						
						if(!empty($url)) {
							
							$data['params']['addOns']['revslider-filmstrip-addon']['settings'][$key]['url'] = $url;
							$id = $this->get_image_id_by_url($url);
							if(!empty($id)) $imageId = $id;
							
						}
						
					}
					if(!empty($thumb)) {
						
						$url = $this->check_file_in_zip($image_path, $thumb, $alias, $imported);
						$url = $this->get_image_url_from_path($url);
						if(!empty($url)) $data['params']['addOns']['revslider-filmstrip-addon']['settings'][$key]['thumb'] = $url;
						
					}
					
					$data['params']['addOns']['revslider-filmstrip-addon']['settings'][$key]['ids'] = $imageId;
					
				}
			}
		}
		
		return $data;
	}
		
}


?>