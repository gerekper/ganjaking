<?php

namespace ElementorModal\Widgets;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_PriceTable')) {
	class GT3_Core_Elementor_Widget_PriceTable extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-pricetable';
		}

		public function get_title(){
			return esc_html__('Price Table', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-price-table';
		}

		protected function construct() {

		}

	}
}
