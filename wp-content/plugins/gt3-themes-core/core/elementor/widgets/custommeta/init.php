<?php

namespace ElementorModal\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_CustomMeta')) {
	class GT3_Core_Elementor_Widget_CustomMeta extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-custommeta';
		}

		public function get_title(){
			return esc_html__('Custom Meta', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-meta-data';
		}

		public function get_repeater_fields(){
			$repeater = new Repeater();

			$repeater->add_control(
				'custom_meta_label',
				array(
					'label' => esc_html__('Label', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$post_type = get_post_type();
			$meta_typed_options = array(
				'type_name' => esc_html__('Name', 'gt3_themes_core'),
				'type_date' => esc_html__('Date', 'gt3_themes_core'),
				'type_author' => esc_html__('Author', 'gt3_themes_core'),
				'type_comment' => esc_html__('Comments', 'gt3_themes_core'),
				'type_custom' => esc_html__('Custom', 'gt3_themes_core'),
			);

			$allowed_post_types = array('post','portfolio');
			if (in_array($post_type, $allowed_post_types)) {
				$meta_typed_options = array_merge(array(
					'type_tags' => esc_html__('Tags', 'gt3_themes_core'),
					'type_category' => esc_html__('Categories', 'gt3_themes_core'),
				), $meta_typed_options);
			}

			$repeater->add_control(
				'custom_meta_type',
				array(
					'label'   => esc_html__('Value Type','gt3_themes_core'),
					'type'    => Controls_Manager::SELECT,
					'options' => $meta_typed_options,
					'default' => 'type_custom'
				)
			);

			$repeater->add_control(
				'custom_meta_value',
				array(
					'label' => esc_html__('Value', 'gt3_themes_core'),
					'type'  => Controls_Manager::TEXTAREA,
					'condition' => array(
						'custom_meta_type' => 'type_custom'
					),
				)
			);

			$repeater->add_control(
				'custom_meta_icon',
				array(
					'label'     => esc_html__('Icon', 'gt3_themes_core'),
					'type'      => Controls_Manager::ICON,
				)
			);

			$repeater->add_control(
				'custom_colors',
				array(
					'label' => esc_html__('Customize Colors?', 'gt3_themes_core'),
					'type'  => Controls_Manager::SWITCHER,
				)
			);


			$repeater->add_control(
				'custom_label_color',
				array(
					'label'       => esc_html__('Label Color', 'gt3_themes_core'),
					'type'        => Controls_Manager::COLOR,
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}}.elementor-widget-gt3-core-custommeta {{CURRENT_ITEM}} .gt3_meta_label_title' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'custom_colors' => 'yes'
					),
				)
			);

			$repeater->add_control(
				'custom_value_color',
				array(
					'label'       => esc_html__('Value Color', 'gt3_themes_core'),
					'type'        => Controls_Manager::COLOR,
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}}.elementor-widget-gt3-core-custommeta {{CURRENT_ITEM}} .gt3_meta_value' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'custom_colors' => 'yes'
					),
				)
			);

			$repeater->add_control(
				'custom_icon_color',
				array(
					'label'       => esc_html__('Icon Color', 'gt3_themes_core'),
					'type'        => Controls_Manager::COLOR,
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}}.elementor-widget-gt3-core-custommeta {{CURRENT_ITEM}} .custom_meta_icon' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'custom_colors' => 'yes',
						'custom_meta_icon!' => ''
					),
				)
			);

			return $repeater->get_controls();
		}

	}
}











