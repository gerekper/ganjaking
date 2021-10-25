<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Tabs')) {
	class GT3_Core_Elementor_Widget_Tabs extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-tabs';
		}

		public function get_title(){
			return esc_html__('Tabs', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-tabs';
		}

		protected function construct() {
			$this->add_script_depends('jquery-ui-tabs');
			wp_enqueue_style('jquery-ui');
		}

		public function get_repeater_fields() {
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

			$repeater->add_control(
				'icon',
				array(
					'label'     => esc_html__('Tab Icon', 'gt3_themes_core'),
					'type'      => Controls_Manager::ICON,
				)
			);

			return $repeater->get_controls();
		}

	}
}











