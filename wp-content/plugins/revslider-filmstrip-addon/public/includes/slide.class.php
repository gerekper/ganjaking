<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsFilmstripSlideFront extends RevSliderFunctions {
	
	private $title;
	
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
	
	public function write_slide_attributes($slider, $slide) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOn = $this->isEnabled($slide);
		if(empty($addOn)) return;
		
		if(wp_is_mobile()) {
		
			$mobile = $this->get_val($addOn, 'mobile', false);
			$mobile = !$this->isFalse($mobile);
			if(!empty($mobile)) return;
			
		}
		
		$datas = $this->get_val($addOn, 'settings', array()); 
		if(empty($datas)) return;
		
		$times = $this->get_val($addOn, 'times', array());
		if(!empty($times)) {
			
			$times = json_decode(json_encode($times), true);
			$st = '';
			
			foreach($times as $time) {
				
				if(isset($time['v']) && !empty($time['v'])) $st .= $time['v'] . ',';
				else $st .= '40,';
				
			}
			
			$times = substr($st, 0, -1);
			
		}
		else {
			$times = '40,40,40,40';
		}
		
		
		$datas = json_decode(json_encode($datas), true);
		$imgs  = array();
		
		$bg        = $this->get_val($slide, 'bg', array());
		$filter    = $this->get_val($bg, 'mediaFilter', 'none');
		$direction = $this->get_val($addOn, 'direction', 'right-to-left');
		
		foreach($datas as $data) {
			
			$alt     = '';
			$url     = isset($data['url']) ? $data['url'] : '';
			$type    = isset($data['type']) ? $data['type'] : '';
			$altText = isset($data['alt']) ? $data['alt'] : '';
			$custom  = isset($data['custom']) ? $data['custom'] : '';
			
			if($type === 'medialibrary') {
				
				$ids = isset($data['ids']) ? $data['ids'] : false;
				if($ids) {
					
					$size = isset($data['size']) ? $data['size'] : 'full';
					if(!$size) $size = 'full';
					
					$url = wp_get_attachment_image_src($ids, $size);
					$url = $url ? $url[0] : '';
					
					if($altText === 'media_library') {
						
						$alt = get_post_meta($ids, '_wp_attachment_image_alt', true);
						if(!$alt) $alt = ''; 
						
					}
				}
			}
			
			if(!$alt) {
			
				if($altText === 'file_name') {
					
					$info = pathinfo($url);
					$alt = $info['filename'];
					
				}
				else {
					$alt = $custom;
				}
				
			}
			
			if(!empty($url)) $imgs[] = array('url' => $url, 'alt' => $alt);
			
		}
		
		if(!empty($imgs)) {
		
			$settings = array(
			
				'direction' => $direction,
				'filter' => $filter,
				'times' => $times,
				'imgs' => $imgs
			
			);
			
			echo " data-filmstrip='" . json_encode($settings) . "'";
			
		}
		
	}
	
}
?>