<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Standard extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'standard';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Standard', 'happy-addons-pro' );
	}

	/**
	 * Added Read More Control to layout section
	 */
	protected function layout_content_tab_controls( ) {

		parent::layout_content_tab_controls();

        $this->readmore_controls();

	}

	/**
	 * Read More Control
	 */
	protected function readmore_controls() {

		$this->add_control(
			'read_more',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'placeholder' => __( 'Read More Text', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide it.', 'happy-addons-pro' ),
				'default' => __( 'View Details', 'happy-addons-pro' ),
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
	 * Update Badge Control
	 */
	protected function badge_controls() {

		$this->add_control(
			'show_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'taxonomy_badge',
			[
				'label' => __( 'Badge Taxonomy', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'category',
				'options' => ha_pro_get_taxonomies(),
				'condition' => [
					$this->get_control_id( 'show_badge' ) => 'yes',
				],
			]
		);

	}

	/**
	 * Added Meta Position Control
	 */
	protected function meta_controls(){
		parent::meta_controls();

		$this->add_control(
			'meta_position',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Position', 'happy-addons-pro' ),
				'label_block' => false,
				'multiple' => true,
				'default' => 'after',
				'options' => [
					'before' => __( 'Before Title', 'happy-addons-pro' ),
					'after' => __( 'After Title', 'happy-addons-pro' ),
				],
				'condition' => [
					$this->get_control_id( 'active_meta!' ) => [],
				],
			]
		);
	}

	/**
	 * Update All Feature Image Style
	 */
	protected function all_style_of_feature_image() {

		$this->image_height_margin_style();

		$this->image_boxshadow_style();

		$this->image_border_styles();

		$this->image_border_radius_styles();
	}

	/**
	 * Update Image boxshadow Style
	 */
	protected function image_boxshadow_style() {

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'feature_image_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-thumb-area',
			]
		);
	}

	/**
	 * Update Image border Style
	 */
	protected function image_border_styles() {

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'feature_image_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-thumb-area',
			]
		);

	}

	/**
	 * Update Image border radius Style
	 */
	protected function image_border_radius_styles() {

		$this->add_responsive_control(
			'feature_image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'readmore_overlay_heading',
			[
				'label' => __( 'Overlay', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_overlay',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-standard .ha-pg-item .ha-pg-readmore::before',
			]
		);

		//Read More style
		$this->add_control(
			'readmore_heading',
			[
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
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
