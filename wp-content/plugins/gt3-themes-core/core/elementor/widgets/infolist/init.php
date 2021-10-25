<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_InfoList')) {
	class GT3_Core_Elementor_Widget_InfoList extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-info-list';
		}

		public function get_title(){
			return esc_html__('Info List', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-time-line';
		}

		public function get_repeater_fields() {
			$repeater = new Repeater();
			$repeater->add_control(
				'title',
				array(
					'label'       => esc_html__('Title', 'gt3_themes_core'),
					'label_block' => true,
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__('Enter Item Title', 'gt3_themes_core'),
				)
			);

			$repeater->add_control(
				'description',
				array(
					'label'       => esc_html__('Description', 'gt3_themes_core'),
					'type'        => Controls_Manager::TEXTAREA,
					'description' => esc_html__('Enter Item description', 'gt3_themes_core'),
				)
			);

			$repeater->add_control(
				'icon_type',
				array(
					'label'   => esc_html__('Icon type', 'gt3_themes_core'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'icon',
					'options' => array(
						'icon'  => esc_html__('Icon', 'gt3_themes_core'),
						'image' => esc_html__('Image', 'gt3_themes_core'),
					),
				)
			);

			$repeater->add_control(
				'icon',
				array(
					'label'     => esc_html__('Icon', 'gt3_themes_core'),
					'type'      => Controls_Manager::ICON,
					'condition' => array(
						'icon_type' => 'icon'
					),
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
					'condition' => array(
						'icon_type' => 'image',
					),
				)
			);

			return $repeater->get_controls();
		}

	}
}











