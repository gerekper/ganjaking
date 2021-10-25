<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_AdvancedTabs')) {
	class GT3_Core_Elementor_Widget_AdvancedTabs extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'jquery-ui-tabs',
					'jquery-ui-accordion'
				)
			);
		}

		public function get_name(){
			return 'gt3-core-advanced-tabs';
		}

		public function get_title(){
			return esc_html__('Advanced Tabs', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-library-download';
		}

		protected function construct(){
			$this->add_style_depends('jquery-ui');
		}

	}
}











