<?php

class PAFE_Advanced_Nav_Menu_Styling extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-advanced-nav-menu-styling';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_advanced_nav_menu_styling_section',
			[
				'label' => __( 'PAFE Advanced Nav Menu Styling', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_advanced_nav_menu_styling_mobile_dropdown_absolute_enable',
			[
				'label' => __( 'Enable Mobile Dropdown Absolute', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown.elementor-nav-menu__container' => 'position: absolute; top: 100%; z-index: 999; width: 100%;',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_nav_menu_styling_image_enable',
			[
				'label' => __( 'Enable Custom Toggle Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}}.elementor-tabs-view-horizontal .elementor-tabs-wrapper' => 'display: flex;',
					'{{WRAPPER}} .elementor-tab-desktop-title' => 'display: block;',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_nav_menu_styling_image_toggle', [
				'label' => __( 'Toggle Image', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
					'pafe_advanced_nav_menu_styling_image_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_nav_menu_styling_image_toggle_size',
			[
				'label' => __( 'Toggle Image Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-advanced-nav-menu-styling-image-toggle' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_nav_menu_styling_image_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_advanced_nav_menu_styling_image_close', [
				'label' => __( 'Close Image', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
					'pafe_advanced_nav_menu_styling_image_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_advanced_nav_menu_styling_image_close_size',
			[
				'label' => __( 'Close Image Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-advanced-nav-menu-styling-image-close' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_advanced_nav_menu_styling_image_enable' => 'yes',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_advanced_nav_menu_styling_image_enable'] ) ) {

			if ( ! empty( $settings['pafe_advanced_nav_menu_styling_image_toggle'] ) && ! empty( $settings['pafe_advanced_nav_menu_styling_image_close'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'class' => 'pafe-advanced-nav-menu-styling-image',
					'data-pafe-advanced-nav-menu-styling-image-toggle' =>  $settings['pafe_advanced_nav_menu_styling_image_toggle']['url'],
					'data-pafe-advanced-nav-menu-styling-image-close' =>  $settings['pafe_advanced_nav_menu_styling_image_close']['url'],
				] );

			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/nav-menu/style_toggle/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
