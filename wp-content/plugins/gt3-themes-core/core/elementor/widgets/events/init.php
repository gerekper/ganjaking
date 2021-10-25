<?php

namespace ElementorModal\Widgets;

use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Events')) {
	class GT3_Core_Elementor_Widget_Events extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-events';
		}

		public function get_title(){
			return esc_html__('Events', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-table-of-contents';
		}

	}
}











