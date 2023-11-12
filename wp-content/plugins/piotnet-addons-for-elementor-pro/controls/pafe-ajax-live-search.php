<?php

class PAFE_Ajax_Live_Search extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-ajax-live-search';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_ajax_live_search_section',
			[
				'label' => __( 'PAFE Ajax Live Search', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$post_types = get_post_types( [], 'objects' );
		$post_types_array = array( '' => 'None' );
		foreach ( $post_types as $post_type ) {
	        $post_types_array[$post_type->name] = $post_type->label;
	    }

		$element->add_control(
			'pafe_ajax_live_search_post_type',
			[
				'label' => __( 'Post Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $post_types_array,
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_ajax_live_search_box_shadow',
				'label' => __( 'Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}} .pafe-ajax-live-search-results',
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_border_radius',
			[
				'label' => __( 'Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_border',
			[
				'label' => __( 'Border Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'elementor' ),
					'double' => _x( 'Double', 'Border Control', 'elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_ajax_live_search_border_width',
			[
				'label' => __( 'Border Width', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
					'pafe_ajax_live_search_border!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_border_color',
			[
				'label' => __( 'Border Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
					'pafe_ajax_live_search_border!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_item_note',
			[
				'label' => __( 'Result Item', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->start_controls_tabs(
			'pafe_ajax_live_search_item',
			[	
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'pafe_ajax_live_search_item_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$element->add_responsive_control(
			'pafe_ajax_live_search_item_padding',
			[
				'label' => __( 'Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_item_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_item_background_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_ajax_live_search_item_typography',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .pafe-ajax-live-search-results-item',
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'pafe_ajax_live_search_item_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$element->add_responsive_control(
			'pafe_ajax_live_search_item_padding_hover',
			[
				'label' => __( 'Padding', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_item_color_hover',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_ajax_live_search_item_background_color_hover',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pafe-ajax-live-search-results-item:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pafe_ajax_live_search_enable' => 'yes',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_ajax_live_search_item_typography_hover',
				'label' => __( 'Typography', 'pafe' ),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .pafe-ajax-live-search-results-item:hover',
			]
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	public function after_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_ajax_live_search_enable'] ) ) {

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-ajax-live-search' => $settings['pafe_ajax_live_search_post_type'],
			] );

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/search-form/search_content/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'after_render_element'], 10, 1 );
	}

}
