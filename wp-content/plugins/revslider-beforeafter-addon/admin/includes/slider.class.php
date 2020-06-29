<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsBeforeAfterSliderAdmin extends RevSliderFunctions {
	
	public function __construct() {				
		
		add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);
		add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);	
		
	}

	public function export_adddon_images($data, $slides, $sliderParams) {
		
		foreach($slides as $slide) {
			
			$addOn = $this->get_val($slide, array('params', 'addOns', 'revslider-beforeafter-addon'), array());
			
			$bg = $this->get_val($addOn, 'bg', array());
			$type = $this->get_val($bg, 'type', false);
			if($type !== 'image') continue;
			
			$image = $this->get_val($bg, 'image', false);
			if(!empty($image)) $data['used_images'][$image] = true;
			
		}
		
		return $data;
		
	}
	
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {
		
		$rev_five = $this->get_val($data, array('params', 'background_type_beforeafter'), false);
		$rev_six = $this->get_val($data, array('params', 'addOns', 'revslider-beforeafter-addon'), array());
		
		// importing from 5.0 structure
		if($rev_five === 'image') {

			$imageId = '';
			$params = $this->get_val($data, 'params', array());
			$image = $this->get_val($params, 'image_url_beforeafter', false);
			
			if(!empty($image)) {
				
				$alias = $this->get_val($data, array('sliderParams', 'params', 'alias'), '');
				if(!empty($alias)) {
					
					$imported = $this->get_val($data, 'imported', array());
					$url = $this->check_file_in_zip($image_path, $image, $alias, $imported);
					$url = $this->get_image_url_from_path($url);

					if(!empty($url)) {
						$data['params']['image_url_beforeafter'] = $url;
						$id = $this->get_image_id_by_url($url);
						if(!empty($id)) $imageId = $id;
					}
				}
			}

			$data['params']['image_id_beforeafter'] = $imageId;
			
		}
		// importing from 6.0 structure
		else if(!empty($rev_six)) {
			
			$imageId = '';
			$bg = $this->get_val($rev_six, 'bg', array());
			$type = $this->get_val($bg, 'type', false);
			
			if(!empty($bg)) {
				
				$alias = $this->get_val($data, array('sliderParams', 'alias'), '');
				if(!empty($alias)) {
					
					$imported = $this->get_val($data, 'imported', array());
					$image = $this->get_val($bg, 'image', false);
					
					$url = $this->check_file_in_zip($image_path, $image, $alias, $imported);
					$url = $this->get_image_url_from_path($url);
					
					if(!empty($url)) {
						
						$data['params']['addOns']['revslider-beforeafter-addon']['bg']['image'] = $url;
						$id = $this->get_image_id_by_url($url);
						if(!empty($id)) $imageId = $id;
						
					}
				}
			}

			$data['params']['addOns']['revslider-beforeafter-addon']['bg']['imageId'] = $imageId;
			
		}
		
		return $data;
	}
		
}


?>