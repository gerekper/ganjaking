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

class Theplus_Tooltips_Option_Group extends Elementor\Group_Control_Base {

	protected static $fields;
	
	public static function get_type() {
		return 'plus-tooltips-option';
	}

	protected function init_fields() {

		$fields = [];
		$fields['plus_tooltip_interactive'] = array(
			'label' => esc_html__( 'Tooltips Interactive', 'theplus' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Enable', 'theplus' ),
			'label_off' => esc_html__( 'Disable', 'theplus' ),
			'default' => 'yes',			
		);
		$fields['plus_tooltip_position'] = array(
			'label' => esc_html__( 'Position', 'theplus' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'top',
			'options' => [
				'left'  => esc_html__( 'Left', 'theplus' ),
				'right' => esc_html__( 'Right', 'theplus' ),
				'top' => esc_html__( 'Top', 'theplus' ),
				'top-start' => esc_html__( 'Top Start', 'theplus' ),
				'top-end' => esc_html__( 'Top End', 'theplus' ),
				'bottom' => esc_html__( 'Bottom', 'theplus' ),
			],
		);
		$fields['plus_tooltip_theme'] = array(
			'label' => esc_html__( 'Theme', 'theplus' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'dark',
			'options' => [
				'dark'  => esc_html__( 'Dark', 'theplus' ),
				'light' => esc_html__( 'Light', 'theplus' ),
				'translucent' => esc_html__( 'Translucent', 'theplus' ),
			],
			'separator' => 'after',
		);
		$fields['plus_tooltip_width'] = array(
			'label'      => esc_html__( 'Width', 'theplus' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px', 'em',
			],
			'range'      => [
				'px' => [
					'min' => 50,
					'max' => 700,
				],
				'em' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default' => [
				'unit' => 'px',
				'size' => 200,
			],
			'responsive' => true,
			'selectors'  => [
				'{{WRAPPER}} .tippy-popper,{{WRAPPER}} .pt_plus_social_list .social_list {{CURRENT_ITEM}} .tippy-popper,{{WRAPPER}} .cascading-image{{CURRENT_ITEM}} .tippy-popper,{{WRAPPER}} .pin-hotspot-loop{{CURRENT_ITEM}} .tippy-popper' => 'max-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
			],
			'separator' => 'after',
		);
		$fields['plus_tooltip_x_offset'] = array(
			'label'   => esc_html__( 'Offset', 'theplus' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0,
			'min'     => -1000,
			'max'     => 1000,
			'step'    => 1,
		);
		$fields['plus_tooltip_y_offset'] = array(
			'label'   => esc_html__( 'Distance', 'theplus' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0,
			'min'     => -1000,
			'max'     => 1000,
			'step'    => 1,
			'separator' => 'after',
		);
		$fields['plus_tooltip_arrow'] = array(
			'label'   => esc_html__( 'Arrows', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'sharp',
			'options' => [
				'none'   => esc_html__( 'None', 'theplus' ),
				'sharp'  => esc_html__( 'Default', 'theplus' ),
				'round'  => esc_html__( 'Round', 'theplus' ),
			],
		);
		$fields['plus_tooltip_arrow_color'] = array(
			'label'  => esc_html__( 'Arrow Color', 'theplus' ),
			'type'   => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tippy-popper[x-placement^=left] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
				'{{WRAPPER}} .tippy-popper[x-placement^=right] .tippy-arrow' => 'border-right-color: {{VALUE}}',
				'{{WRAPPER}} .tippy-popper[x-placement^=top] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
				'{{WRAPPER}} .tippy-popper[x-placement^=bottom] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',
				'{{WRAPPER}} .tippy-tooltip .tippy-roundarrow svg path'=> 'fill: {{VALUE}};stroke:none;',
			],
			'condition' => [
				'plus_tooltip_arrow!' => ['none'],
			],
		);
		$fields['plus_tooltip_triggger'] = array(
			'label'   => esc_html__( 'Trigger', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'mouseenter',
			'options' => [
				'mouseenter'   => esc_html__( 'Hover', 'theplus' ),
				'click'         => esc_html__( 'Click', 'theplus' ),
			],
			'separator' => 'after',
		);
		$fields['plus_tooltip_animation'] = array(
			'label'   => esc_html__( 'Animation', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'shift-toward',
			'options' => [
				'shift-away'   => esc_html__( 'Shift-Away', 'theplus' ),
				'shift-toward' => esc_html__( 'Shift-Toward', 'theplus' ),
				'fade'         => esc_html__( 'Fade', 'theplus' ),
				'scale'        => esc_html__( 'Scale', 'theplus' ),
				'perspective'  => esc_html__( 'Perspective', 'theplus' ),
			],
		);
		$fields['plus_tooltip_duration_in'] = array(
			'label'   => esc_html__( 'Duration In', 'theplus' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 250,
			'min'     => -2000,
			'max'     => 2000,
			'step'    => 10,
		);
		$fields['plus_tooltip_duration_out'] = array(
			'label'   => esc_html__( 'Duration Out', 'theplus' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 200,
			'min'     => -2000,
			'max'     => 2000,
			'step'    => 10,
			'separator' => 'after',
		);
		return $fields;
	}
}