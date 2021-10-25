<?php

namespace ElementorModal\Widgets;

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Stripes')) {
	class GT3_Core_Elementor_Widget_Stripes extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-stripes';
		}

		public function get_title(){
			return esc_html__('Stripes', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-barcode';
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
					'label' => esc_html__('Description', 'gt3_themes_core'),
					'type'  => Controls_Manager::WYSIWYG,
				)
			);

			$repeater->add_control(
				'link',
				[
					'label' => __( 'Link', 'gt3_themes_core' ),
					'type' => Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => __( 'https://your-link.com', 'gt3_themes_core' ),
				]
			);

			$repeater->add_control(
				'image',
				array(
					'label'   => esc_html__('Background Image'),
					'type'    => Controls_Manager::MEDIA,
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			return $repeater->get_controls();
		}

	}
}











