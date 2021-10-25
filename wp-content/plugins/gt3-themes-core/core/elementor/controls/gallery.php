<?php

namespace Elementor;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

use Elementor\Base_Data_Control;


if(!class_exists('\Elementor\GT3_Core_Elementor_Control_Gallery')) {
	class GT3_Core_Elementor_Control_Gallery extends Base_Data_Control {

		public function get_type(){
			return self::type();
		}

		public static function type(){
			return 'gt3-elementor-core-gallery';
		}

		public function get_default_value(){
			return '';
		}

		public function get_value($control, $widget){
			if(isset($widget[$control['name']]) && !empty($widget[$control['name']])) {
				$images = explode(',', $widget[$control['name']]);
			} else {
				$images = array();
			}

			return $images;
		}

		public function content_template(){
		}
	}
}






