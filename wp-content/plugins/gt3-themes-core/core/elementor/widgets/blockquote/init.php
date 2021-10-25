<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Blockquote')) {
	class GT3_Core_Elementor_Widget_Blockquote extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-blockquote';
		}

		public function get_title(){
			return esc_html__('Blockquote', 'gt3_themes_core');
		}

		public function get_icon(){
            return 'gt3-core-elementor-icon eicon-blockquote';
		}

	}
}











