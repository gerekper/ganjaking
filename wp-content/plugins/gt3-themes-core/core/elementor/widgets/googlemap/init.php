<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_GoogleMap')) {
	class GT3_Core_Elementor_Widget_GoogleMap extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-googlemap';
		}

		public function get_title(){
			return esc_html__('GoogleMap', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-google-maps';
		}

		protected function construct(){
			$this->add_script_depends('google-maps-api');
		}
	}
}











