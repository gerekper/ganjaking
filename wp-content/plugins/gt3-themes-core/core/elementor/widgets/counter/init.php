<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Counter')) {
	class GT3_Core_Elementor_Widget_Counter extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-counter';
		}

		public function get_title(){
			return esc_html__('Counter', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-counter';
		}

		protected function construct() {
//			$this->add_script_depends('countUp');
		}

	}
}











