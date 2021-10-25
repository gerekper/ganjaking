<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_ProcessBar')) {
	class GT3_Core_Elementor_Widget_ProcessBar extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'jquery-ui-tabs',
				)
			);
		}

		public function get_name(){
			return 'gt3-core-processbar';
		}

		public function get_title(){
			return esc_html__('ProcessBar', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-time-line';
		}

		protected function construct() {
			wp_enqueue_style('jquery-ui');
		}

		public function get_repeater_fields() {
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
				'circle_size',
				[
					'label' => __( 'Circle size', 'gt3_themes_core' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'small',
					'options' => [
						'mini'  => __( 'mini', 'gt3_themes_core' ),
						'small' => __( 'small', 'gt3_themes_core' ),
						'normal' => __( 'normal', 'gt3_themes_core' ),
						'large' => __( 'large', 'gt3_themes_core' ),
						'e_large' => __( 'extra large', 'gt3_themes_core' ),
					]/*,
					'condition'  => [
						'type' => 'horizontal',
					],*/
				]
			);


			return $repeater->get_controls();
		}

	}
}











