<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Theplus_Tilt_Parallax_Group extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'plus-tilt-parallax-option';
	}

	protected function init_fields() {

		$fields = [];
		
		$fields['tilt_max'] = array(
				'label' => esc_html__( 'Max', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => 0,
						'max' => 400,
						'step' => 5,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 20,
				),
		);
		$fields['tilt_perspective'] = array(
				'label' => esc_html__( 'Transform Perspective', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => 500,
						'max' => 7000,
						'step' => 100,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 400,
				),
		);
		$fields['tilt_scale'] = array(
				'label' => esc_html__( 'Scale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => 0.5,
						'max' => 1.8,
						'step' => 0.02,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 1.1,
				),
		);
		$fields['tilt_speed'] = array(
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'description' => esc_html__('Speed of the enter/exit transition','theplus'),
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => 0,
						'max' => 4000,
						'step' => 10,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 400,
				),
		);
		$fields['tilt_easing'] = array(
			'label' => esc_html__( 'Easing', 'theplus' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'cubic-bezier(.03,.98,.52,.99)',
			'description' => esc_html__('Easing on enter/exit','theplus'),
			'options' => [
				'cubic-bezier(.03,.98,.52,.99)'  => esc_html__( 'Default', 'theplus' ),
				'custom' => esc_html__( 'Custom', 'theplus' ),
			],
		);
		$fields['tilt_easing_custom'] = array(
			'label' => esc_html__( 'Custom Easing', 'theplus' ),
			'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'cubic-bezier(.03,.98,.52,.99)', 'theplus' ),
			'condition'    => [
				'tilt_easing' => [ 'custom' ],
			],
		);
		
		return $fields;
	}
}