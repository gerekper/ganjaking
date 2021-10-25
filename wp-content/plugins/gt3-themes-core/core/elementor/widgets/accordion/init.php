<?php

namespace ElementorModal\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Accordion')) {
	class GT3_Core_Elementor_Widget_Accordion extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array( 'jquery-ui-accordion' )
			);
		}

		public function get_name(){
			return 'gt3-core-accordion';
		}

		public function get_title(){
			return esc_html__('Accordion', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-accordion';
		}

		protected function construct(){
			$this->add_style_depends('jquery-ui');
		}

		public function get_repeater_fields(){
			$repeater = new Repeater();
			$repeater->add_control(
				'title',
				array(
					'label' => esc_html__('Title', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'content',
				array(
					'label' => esc_html__('Content', 'gt3_themes_core'),
					'type'  => Controls_Manager::WYSIWYG,
				)
			);

			return $repeater->get_controls();
		}

	}
}











