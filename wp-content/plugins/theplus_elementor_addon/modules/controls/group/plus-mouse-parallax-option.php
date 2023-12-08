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

class Theplus_Mouse_Move_Parallax_Group extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'plus-mouse-parallax-option';
	}

	protected function init_fields() {

		$fields = [];
		
		$fields['speed_x'] = array(
				'label' => esc_html__( 'Move Parallax (X)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => -100,
						'max' => 100,
						'step' => 2,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 30,
				),
		);
		$fields['speed_y'] = array(
				'label' => esc_html__( 'Move Parallax (Y)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => array(
					'' => array(
						'min' => -100,
						'max' => 100,
						'step' => 2,
					),
				),
				'default' => array(
					'unit' => '',
					'size' => 30,
				),
		);
		
		return $fields;
	}
}