<?php

namespace GT3\PhotoVideoGallery\Block\Traits;
defined('ABSPATH') OR exit;

trait Get_Attachment_Image_Trait {
	protected $active_image_size = 'full';

	public function max_srcset_image_width($max){
		$w = $h = false;
		if($this->active_image_size !== 'full') {
			$w = get_option($this->active_image_size.'_size_w');
			$h = get_option($this->active_image_size.'_size_h');
		}

		return ($w !== false && $h !== false) ? max($w, $h) : $max;
	}

	protected function wp_get_attachment_image($id, $imageSize = 'full', $icon = false, $attr = array()){
		$this->active_image_size = $imageSize;
		add_filter('max_srcset_image_width', array( $this, 'max_srcset_image_width' ), 101);
		$attr = array_merge($attr,
			array(
				'class' => 'skip-lazy',
			));
		$img  = wp_get_attachment_image($id, $imageSize, $icon, $attr);
		remove_filter('max_srcset_image_width', array( $this, 'max_srcset_image_width' ), 101);

		return $img;
	}
}
