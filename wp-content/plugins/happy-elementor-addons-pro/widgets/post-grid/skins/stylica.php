<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Stylica extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'stylica';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Stylica', 'happy-addons-pro' );
	}

	protected function _register_controls_actions() {
		parent::_register_controls_actions();

        add_action( 'elementor/element/ha-post-grid/stylica__section_image_style/after_section_end', [ $this, 'devider_shape_style_controls' ] );

    }

	/**
	 * Update All Feature Image Style
	 */
	protected function all_style_of_feature_image() {

		$this->image_height_margin_style();

		$this->image_border_radius_styles();

		$this->image_css_filter_styles();
	}

	/**
	 * Added Devider Shape Style Control
	 */
	public function devider_shape_style_controls() {

		$this->start_controls_section(
			'_section_image_devider_shape_style',
			[
				'label' => __( 'Devider Shape', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
					// $this->get_control_id( 'devider_shape!' ) => 'none',
				],
			]
		);

		$this->add_control(
			'devider_shape',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Type', 'happy-addons-pro' ),
				'label_block' => false,
				'multiple' => true,
				'default' => 'clouds',
				'options' => [
					'none'     => __( 'None', 'happy-addons-pro' ),
					'clouds'     => __( 'Clouds', 'happy-addons-pro' ),
					'corner'     => __( 'Corner', 'happy-addons-pro' ),
					'cross-line' => __( 'Cross Line', 'happy-addons-pro' ),
					'curve'      => __( 'Curve', 'happy-addons-pro' ),
					'drops'      => __( 'Drops', 'happy-addons-pro' ),
					'mountains'  => __( 'Mountains', 'happy-addons-pro' ),
					'pyramids'   => __( 'Pyramids', 'happy-addons-pro' ),
					'splash'     => __( 'Splash', 'happy-addons-pro' ),
					'split'      => __( 'Split', 'happy-addons-pro' ),
					'tilt'       => __( 'Tilt', 'happy-addons-pro' ),
					'torn-paper' => __( 'Torn Paper', 'happy-addons-pro' ),
					'triangle'   => __( 'Triangle', 'happy-addons-pro' ),
					'wave'       => __( 'Wave', 'happy-addons-pro' ),
					'zigzag'     => __( 'Zigzag', 'happy-addons-pro' ),
				],
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'devider_shape_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'fill: {{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'devider_shape_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'width: calc({{SIZE}}{{UNIT}} + 1.3px)',
				],
				'condition' => [
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'devider_shape_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 500,
					],
				],
				'default' => [
					'size' => 90,
				],
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'devider_shape!' ) => 'none',
				],
			]
		);

		$this->end_controls_section();

	}

}
