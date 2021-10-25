<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_PieChart')) {
	class GT3_Core_Elementor_Widget_PieChart extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-piechart';
		}

		public function get_title(){
			return esc_html__('Pie Chart', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-counter-circle';
		}

		protected function construct() {
			$this->add_script_depends('circle-progress');
		}

	}
}











