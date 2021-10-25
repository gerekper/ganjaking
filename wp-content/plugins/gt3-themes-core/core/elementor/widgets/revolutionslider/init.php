<?php

namespace ElementorModal\Widgets;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists( 'RevSlider' )) {
	return;
}

use Elementor\Widget_Base;

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_RevolutionSlider')) {
	class GT3_Core_Elementor_Widget_RevolutionSlider extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-revolutionslider';
		}

		public function get_title(){
			return esc_html__('Revolution Slider', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-slider-device';
		}

	}
}











