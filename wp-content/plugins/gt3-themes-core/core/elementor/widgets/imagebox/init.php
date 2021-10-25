<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageBox')) {
	class GT3_Core_Elementor_Widget_ImageBox extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array()
			);
		}


		public function get_name(){
			return 'gt3-core-imagebox';
		}

		public function get_title(){
			return esc_html__('Image Box', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-image-box';
		}

	}
}











