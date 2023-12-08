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

class Theplus_Tooltips_Option_Style_Group extends Elementor\Group_Control_Base {

	protected static $fields;
	
	public static function get_type() {
		return 'plus-tooltips-option-style';
	}

	protected function init_fields() {

		$fields = [];
		$fields['toolip_padding'] = array(
			'label'       => esc_html__( 'Padding', 'theplus' ),
			'type'        => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
			'separator' => 'after',
		);
		$fields['toolip_background'] = array(
			'label'       => esc_html__( 'Background Type', 'theplus' ),
			'type'        => Controls_Manager::CHOOSE,
			'options'     => array(
				'color' => array(
					'title' => esc_html__( 'Classic', 'theplus' ),
					'icon'  => 'eicon-paint-brush',
				),
				'gradient' => array(
					'title' => esc_html__( 'Gradient', 'theplus' ),
					'icon'  => 'eicon-barcode',
				),
			),
			'label_block' => false,
			
			'render_type' => 'ui',
		);

		$fields['toolip_color'] = array(
			'label'     => esc_html__( 'Color', 'theplus' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'title'     => esc_html__( 'Background Color', 'theplus' ),
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'background-color: {{VALUE}};',
			),
			'condition' => array(
				'toolip_background' => array( 'color', 'gradient' ),
			),
		);

		$fields['toolip_color_stop'] = array(
			'label'      => esc_html__( 'Location', 'theplus' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default'    => array(
				'unit' => '%',
				'size' => 0,
			),
			'render_type' => 'ui',
			'condition' => array(
				'toolip_background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['toolip_color_b'] = array(
			'label'       => esc_html__( 'Second Color', 'theplus' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '#f2295b',
			'render_type' => 'ui',
			'condition'   => array(
				'toolip_background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['toolip_color_b_stop'] = array(
			'label'      => esc_html__( 'Location', 'theplus' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default'    => array(
				'unit' => '%',
				'size' => 100,
			),
			'render_type' => 'ui',
			'condition'   => array(
				'toolip_background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['toolip_gradient_type'] = array(
			'label'   => esc_html__( 'Type', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'linear' => esc_html__( 'Linear', 'theplus' ),
				'radial' => esc_html__( 'Radial', 'theplus' ),
			),
			'default'     => 'linear',
			'render_type' => 'ui',
			'condition'   => array(
				'toolip_background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['toolip_gradient_angle'] = array(
			'label'      => esc_html__( 'Angle', 'theplus' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'deg' ),
			'default'    => array(
				'unit' => 'deg',
				'size' => 180,
			),
			'range' => array(
				'deg' => array(
					'step' => 10,
				),
			),
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{toolip_color.VALUE}} {{toolip_color_stop.SIZE}}{{toolip_color_stop.UNIT}}, {{toolip_color_b.VALUE}} {{toolip_color_b_stop.SIZE}}{{toolip_color_b_stop.UNIT}})',
			),
			'condition' => array(
				'toolip_background'    => array( 'gradient' ),
				'toolip_gradient_type' => 'linear',
			),
			'of_type' => 'gradient',
		);

		$fields['toolip_gradient_position'] = array(
			'label'   => esc_html__( 'Position', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'center center' => esc_html__( 'Center Center', 'theplus' ),
				'center left'   => esc_html__( 'Center Left', 'theplus' ),
				'center right'  => esc_html__( 'Center Right', 'theplus' ),
				'top center'    => esc_html__( 'Top Center', 'theplus' ),
				'top left'      => esc_html__( 'Top Left', 'theplus' ),
				'top right'     => esc_html__( 'Top Right', 'theplus' ),
				'bottom center' => esc_html__( 'Bottom Center', 'theplus' ),
				'bottom left'   => esc_html__( 'Bottom Left', 'theplus' ),
				'bottom right'  => esc_html__( 'Bottom Right', 'theplus' ),
			),
			'default' => 'center center',
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{toolip_color.VALUE}} {{toolip_color_stop.SIZE}}{{toolip_color_stop.UNIT}}, {{toolip_color_b.VALUE}} {{toolip_color_b_stop.SIZE}}{{toolip_color_b_stop.UNIT}})',
			),
			'condition' => array(
				'toolip_background'    => array( 'gradient' ),
				'toolip_gradient_type' => 'radial',
			),
			'of_type' => 'gradient',
		);
		$fields['plus_tooltip_border'] = array(
			'label'   => esc_html__( 'Border Type', 'theplus' ),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				''       => esc_html__( 'None', 'theplus' ),
				'solid'  => esc_html__( 'Solid', 'theplus' ),
				'double' => esc_html__( 'Double', 'theplus' ),
				'dotted' => esc_html__( 'Dotted', 'theplus' ),
				'dashed' => esc_html__( 'Dashed', 'theplus' ),
			),
			'separator' => 'before',
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'border-style: {{VALUE}};',
			),
		);

		$fields['plus_tooltip_border_width'] = array(
			'label'     => esc_html__( 'Border Width', 'theplus' ),
			'type'      => Controls_Manager::DIMENSIONS,
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
			'condition' => array(
				'plus_tooltip_border!' => '',
			),
		);

		$fields['plus_tooltip_border_color'] = array(
			'label' => esc_html__( 'Border Color', 'theplus' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'border-color: {{VALUE}};',
			),
			'condition' => array(
				'plus_tooltip_border!' => '',
			),
		);

		$fields['plus_tooltip_border_radius'] = array(
			'label'      => esc_html__( 'Border Radius', 'theplus' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .tippy-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
			'separator' => 'after',
		);

		$fields['plus_tooltip_shadow'] = array(
			'label' => esc_html__( 'Box Shadow', 'theplus' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Yes', 'theplus' ),
			'label_off' => esc_html__( 'No', 'theplus' ),
			'return_value' => 'yes',
			'separator' => 'before',
			'render_type' => 'ui',
		);

		$fields['plus_tooltip_box_shadow'] = array(
			'label'     => esc_html__( 'Box Shadow', 'theplus' ),
			'type'      => Controls_Manager::BOX_SHADOW,
			'condition' => array(
				'plus_tooltip_shadow!' => '',
			),
			'selectors' => array(
				'{{WRAPPER}} .tippy-tooltip' => 'box-shadow:  {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
			),
		);
		return $fields;
	}
}