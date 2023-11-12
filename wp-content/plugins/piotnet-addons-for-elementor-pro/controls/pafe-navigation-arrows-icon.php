<?php

class PAFE_Navigation_Arrows_Icon extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-navigation-arrows-icon';
	}

	public function pafe_register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_navigation_arrows_icon_section',
			[
				'label' => __( 'PAFE Navigation Arrows Icon', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_enable',
			[
				'label' => __( 'Enable Navigation Arrows Icon', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->add_responsive_control(
			'pafe_navigation_arrows_icon_size',
			[
				'label' => __( 'Size', 'pafe' ),
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
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_navigation_arrows_icon_opacity',
			[
				'label' => __( 'Opacity', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0.7,
				'min' => 0.1,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_navigation_arrows_icon_position',
			[
				'label' => __( 'Position', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g 20px, -15px, 5%',
				'default' => '20px',
				'selectors' => [
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows--previous' => 'left: {{VALUE}};',
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows--next' => 'right: {{VALUE}};',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'  => __( 'Icon', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_color',
			[
				'label' => __( 'Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .pafe-navigation-arrows-icon-arrows' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
					'pafe_navigation_arrows_icon_type' => 'icon',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_previous',
			[
				'label' => __( 'Previous', 'pafe' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
					'pafe_navigation_arrows_icon_type' => 'icon',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_next',
			[
				'label' => __( 'Next', 'pafe' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
					'pafe_navigation_arrows_icon_type' => 'icon',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_previous_image', [
				'label' => __( 'Previous', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
					'pafe_navigation_arrows_icon_type' => 'image',
				],
			]
		);

		$element->add_control(
			'pafe_navigation_arrows_icon_next_image', [
				'label' => __( 'Next', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [
					'pafe_navigation_arrows_icon_enable' => 'yes',
					'pafe_navigation_arrows_icon_type' => 'image',
				],
			]
		);

		$element->end_controls_section();
	}

	public function before_render_element($element) {
		$settings = $element->get_settings(); 	
		if ( ! empty( $settings['pafe_navigation_arrows_icon_enable'] ) ) {

			if ( $settings['pafe_navigation_arrows_icon_type'] == 'icon' && ! empty( $settings['pafe_navigation_arrows_icon_previous'] ) && ! empty( $settings['pafe_navigation_arrows_icon_next'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'class' => 'pafe-navigation-arrows-icon',
					'data-pafe-navigation-arrows-icon' => '',
					'data-pafe-navigation-arrows-icon-size' => $settings['pafe_navigation_arrows_icon_size']['size'] . $settings['pafe_navigation_arrows_icon_size']['unit'],
					'data-pafe-navigation-arrows-icon-opacity' => $settings['pafe_navigation_arrows_icon_opacity'] . $settings['pafe_navigation_arrows_icon_opacity'],
					'data-pafe-navigation-arrows-icon-position' => $settings['pafe_navigation_arrows_icon_position'],
					'data-pafe-navigation-arrows-icon-previous' => $settings['pafe_navigation_arrows_icon_previous'],
					'data-pafe-navigation-arrows-icon-next' => $settings['pafe_navigation_arrows_icon_next'],
				] );

			}

			if ( $settings['pafe_navigation_arrows_icon_type'] == 'image' && ! empty( $settings['pafe_navigation_arrows_icon_previous_image'] ) && ! empty( $settings['pafe_navigation_arrows_icon_next_image'] ) ) {

				$element->add_render_attribute( '_wrapper', [
					'class' => 'pafe-navigation-arrows-icon',
					'data-pafe-navigation-arrows-icon-image' => '',
					'data-pafe-navigation-arrows-icon-size' => $settings['pafe_navigation_arrows_icon_size']['size'] . $settings['pafe_navigation_arrows_icon_size']['unit'],
					'data-pafe-navigation-arrows-icon-opacity' => $settings['pafe_navigation_arrows_icon_opacity'] . $settings['pafe_navigation_arrows_icon_opacity'],
					'data-pafe-navigation-arrows-icon-position' => $settings['pafe_navigation_arrows_icon_position'],
					'data-pafe-navigation-arrows-icon-previous' => $settings['pafe_navigation_arrows_icon_previous_image']['url'],
					'data-pafe-navigation-arrows-icon-next' => $settings['pafe_navigation_arrows_icon_next_image']['url'],
				] );

			}

		}
	}

	protected function init_control() {
		add_action( 'elementor/element/image-carousel/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/posts/custom_section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/slides/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/pafe-slider-builder/section_style_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/media-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/testimonial-carousel/section_navigation/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render_element'], 10, 1 );
	}

}
