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

class Theplus_Magic_Scroll_From_Style_Group extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'plus-magic-scroll-from';
	}

	protected function init_fields() {

		$fields = [];
		
		$fields['scroll_x_from'] = array(
			'label' => esc_html__( '(X) / Horizontal Distance', 'theplus' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => -2000,
			'max' => 2000,
			'step' => 5,
			'default' => 0,
		);
		$fields['scroll_y_from'] = array(
			'label' => esc_html__( '(Y) / Vertical Distance', 'theplus' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => -2000,
			'max' => 2000,
			'step' => 5,
			'default' => 0,
		);
		$fields['scroll_opacity_from'] = array(
			'label' => esc_html__( 'Opacity', 'theplus' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 0,
			'max' => 1,
			'step' => 0.01,
			'default' => 1,
		);
		$fields['scroll_scale_from'] = array(
			'label' => esc_html__( 'Scale Value', 'theplus' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 0,
			'max' => 2,
			'step' => 0.01,
			'default' => 1,
		);
		$fields['scroll_rotate_from'] = array(
			'label' => esc_html__( 'Rotate Value', 'theplus' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => -360,
			'max' => 360,
			'step' => 1,
			'default' => 0,
		);
		return $fields;
	}
}