<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2020 ThemePunch
*/

if(!defined('ABSPATH')) exit();
if(!class_exists('RevSliderFunctions')) exit();

class RsAddOnLottieLayers extends RevSliderFunctions{
	
	public $path = '/';
	public $upload_dir;
	
	public function __construct(){
		$this->upload_dir = wp_upload_dir();
		
		add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);
		add_filter('revslider_importSliderFromPost_modify_data', array($this, 'import_update_addon_image_urls'), 10, 3);
	}
	
	
	public function export_adddon_images($data, $slides, $sliderParams){
		
		foreach($slides as $slide){
			$layers = $this->get_val($slide, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $layer){
					$addOn = $this->get_val($layer, array('addOns', 'revslider-lottie-addon'), array());
					$json = $this->get_val($addOn, array('config', 'jsonUrl'), '');
					
					if(!empty($json)) $data['used_images'][$json] = true;
				}
			}
		}
		
		return $data;
	}
	
	
	public function import_update_addon_image_urls($data, $slidetype, $image_path) {
		global $wp_filesystem;
		
		
		$alias = $this->get_val($data, array('sliderParams', 'alias'), '');
		if(!empty($alias)) {
			$layers = $this->get_val($data, 'layers', array());
			if(!empty($layers)){
				foreach($layers as $k => $layer){
					$json = $this->get_val($layer, array('addOns', 'revslider-lottie-addon', 'config', 'jsonUrl'), '');
					if(empty($json)) continue;
					
					$imported = $this->get_val($data, 'imported', array());
					
					$strip	= false;
					$zimage	= $wp_filesystem->exists($image_path.'images/'.$json);
					if(!$zimage){
						$zimage	= $wp_filesystem->exists(str_replace('//', '/', $image_path.'images/'.$json));
						$strip	= true;
					}
					
					if($zimage){
						if(!isset($imported['images/'.$json])){
							//check if we are object folder, if yes, do not import into media library but add it to the object folder
							$uimg = ($strip == true) ? str_replace('//', '/', 'images/'.$json) : $json; //pclzip
							
							$file = $this->upload_dir['basedir'] . $this->path . $json;
							$_file = $this->upload_dir['baseurl'] . $this->path . $json;
							
							@mkdir(dirname($file), 0777, true);
							@copy($image_path.'images/'.$json, $file);
							
							$imported['images/'.$json] = $_file;
							$json = $_file;
						}else{
							$json = $imported['images/'.$json];
						}
					}
					
					if(!empty($json)){
						$data['layers'][$k]['addOns']['revslider-lottie-addon']['config']['jsonUrl'] = $json;
					}
				}
			}
		}
		//sliderParams->static_slides->0->layers
		//sliderParams->slides->0->layers
		
		return $data;
	}
}

?>