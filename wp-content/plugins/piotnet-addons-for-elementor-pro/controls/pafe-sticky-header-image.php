<?php
class PAFE_Sticky_Header_Image extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-sticky-header-image';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_sticky_header_image_section',
			[
				'label' => __( 'PAFE Sticky Header For Image', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->start_controls_tabs( 'pafe_sticky_header_image_tabs',
			[
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->start_controls_tab( 'pafe_sticky_header_image_tab_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_hidden',
			[
				'label' => __( 'Hidden', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-element:not(.pafe-sticky-header-active-element)' => 'display: none;',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_convert_image_to_black',
			[
				'label' => __( 'Convert Image To Black', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .elementor-image img,{{WRAPPER}} .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(0); filter: brightness(0) invert(0);',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_convert_image_to_white',
			[
				'label' => __( 'Convert Image To White', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .elementor-image img,{{WRAPPER}} .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab( 'pafe_sticky_header_image_tab_sticky',
			[
				'label' => __( 'Sticky', 'pafe' ),
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_hidden_sticky',
			[
				'label' => __( 'Hidden', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-element.pafe-sticky-header-active-element' => 'display: none;',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_convert_image_to_color',
			[
				'label' => __( 'Convert Image To Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-active-element .elementor-image img,{{WRAPPER}}.pafe-sticky-header-active-element .elementor-widget-container img' => '-webkit-filter: none; filter: none;',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_convert_image_to_black_sticky',
			[
				'label' => __( 'Convert Image To Black', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-active-element .elementor-image img,{{WRAPPER}}.pafe-sticky-header-active-element .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(0); filter: brightness(0) invert(0);',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_image_convert_image_to_white_sticky',
			[
				'label' => __( 'Convert Image To White', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.pafe-sticky-header-active-element .elementor-image img,{{WRAPPER}}.pafe-sticky-header-active-element .elementor-widget-container img' => '-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_sticky_header_image_active_image_width',
			[
				'label' => __( 'Image Width (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'transition: all 0.4s ease-in-out 0s;',
					'{{WRAPPER}}.pafe-sticky-header-active-element .elementor-image img,{{WRAPPER}}.pafe-sticky-header-active-element .elementor-widget-container img' => 'transition: all 0.4s ease-in-out; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_sticky_header_image_enable!' => '',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	protected function init_control() {
		add_action( 'elementor/element/image/section_style_image/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
	}

}
