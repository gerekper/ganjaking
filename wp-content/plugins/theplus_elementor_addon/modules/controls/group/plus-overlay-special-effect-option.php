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

class Theplus_Overlay_Special_Effect_Group extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'plus-overlay-special-effect-option';
	}

	protected function init_fields() {

		$fields = [];
		$fields['effect_color_1'] = array(
			'label'     => esc_html__( 'Effect Color 1', 'theplus' ),
			'type'      => Controls_Manager::COLOR,
			'default' => '#313131',
		);
		$fields['effect_color_2'] = array(
			'label'     => esc_html__( 'Effect Color 2', 'theplus' ),
			'type'      => Controls_Manager::COLOR,
			'default' => '#ff214f',
		);
		
		return $fields;
	}
}