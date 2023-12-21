<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Hawai  extends Skin_Base {


	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'hawai';
    }

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Hawai', 'happy-addons-pro' );
    }

    protected function _register_controls_actions() {

		parent::_register_controls_actions();

		add_action( 'elementor/element/ha-post-grid/_section_layout_style/before_section_end', [ $this, 'add_allignment_style_controls' ] );

	}

	/**
	 * Added Read More Control to layout section
	 */
	protected function layout_content_tab_controls( ) {

		parent::layout_content_tab_controls();

        $this->readmore_controls();

	}

	/**
	 * Readmore Control
	 */
	protected function readmore_controls() {

		$this->add_control(
			'read_more',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'placeholder' => __( 'Read More Text', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide it.', 'happy-addons-pro' ),
			]
		);


		$this->add_control(
			'read_more_new_tab',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Open in new window', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					$this->get_control_id( 'read_more!' ) => ''
				],
			]
		);


	}

    /**
	 * Added Allignment Style control
	 */
    public function add_allignment_style_controls() {

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item' => 'text-align: {{VALUE}};',
				],
			]
		);

    }

    /**
	 * Update Image Style controls
	 */
	protected function image_height_margin_style() {

		$this->add_responsive_control(
			'feature_image_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
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
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

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
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}

	/**
	 * Hooking Read more style after meta style
	 */
	protected function meta_style_tab_controls() {
		parent::meta_style_tab_controls();

		$this->readmore_style_tab_controls();
	}

	/**
	 * Added Read More Style controls
	 */
	protected function readmore_style_tab_controls() {

		$this->start_controls_section(
			'_section_readmore_style',
			[
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'readmore_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'exclude' => [
					'color',
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);

		$this->start_controls_tabs( 'readmore_tabs',
			[
				'condition' => [
					$this->get_control_id( 'read_more!' ) => '',
				],
			]
		);
		$this->start_controls_tab(
			'readmore_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'readmore_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
			]
		);

		$this->add_control(
			'readmore_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'readmore_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'readmore_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a:hover',
			]
		);

		$this->add_control(
			'readmore_border_hover_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

}
