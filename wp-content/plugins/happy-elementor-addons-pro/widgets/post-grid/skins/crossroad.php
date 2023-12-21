<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Crossroad extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'crossroad';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Crossroad', 'happy-addons-pro' );
	}

    protected function _register_controls_actions() {

		parent::_register_controls_actions();

        add_action( 'elementor/element/ha-post-grid/crossroad__section_content_style/before_section_end', [ $this, 'update_content_style' ] );

	}

    /**
	 * Update Content Style section
	 * @param Widget_Base $element
	 */
	public static function update_content_style( Widget_Base $element ) {

		$element->start_injection( [
			'type' => 'control',
			'at' => 'before',
			'of' => 'crossroad_content_area_padding',
		] );

		$element->add_responsive_control(
			'content_area_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-content-area' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->end_injection();

		$element->start_injection( [
			'type' => 'control',
			'at' => 'after',
			'of' => 'crossroad_content_area_padding',
		] );

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_area_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-content-area',
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_area_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-content-area',
			]
		);

		$element->add_responsive_control(
			'content_area_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$element->end_injection();
	}

	/**
	 * Update All Feature Image Style
	 */
	protected function all_style_of_feature_image() {

		$this->add_responsive_control(
			'feature_image_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => -20,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->image_border_radius_styles();

		$this->image_css_filter_styles();

	}

}
