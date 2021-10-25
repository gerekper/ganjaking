<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Utils;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageProcessBar')) {
	class GT3_Core_Elementor_Widget_ImageProcessBar extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-imageprocessbar';
		}

		public function get_title(){
			return esc_html__('Image Process Bar', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-time-line';
		}

		protected function construct() {
			$this->add_script_depends('jquery-ui-tabs');
			wp_enqueue_style('jquery-ui');
		}

		public function get_repeater_fields() {
			$repeater = new Repeater();

			$repeater = new Repeater();
			$repeater->add_control(
				'proc_number',
				array(
					'label' => esc_html__('Number', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'proc_heading',
				array(
					'label' => esc_html__('Title', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'proc_descr',
				array(
					'label' => esc_html__('Content', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXTAREA,
				)
			);

			$repeater->add_control(
				'image',
				array(
					'label'     => esc_html__('Image', 'gt3_themes_core'),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			return $repeater->get_controls();
		}

	}
}











