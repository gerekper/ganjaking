<?php
require_once( __DIR__ . '/controls-manager.php' );

class PAFE_Sticky_Header extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-sticky-header';
	}

	public function pafe_register_controls( $element, $section_id ) {

		$element->start_controls_section(
			'pafe_sticky_header_section',
			[
				'label' => __( 'PAFE Sticky Header', 'pafe' ),
				'tab' => PAFE_Controls_Manager::TAB_PAFE,
			]
		);

		$element->add_control(
			'pafe_sticky_header_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __( 'If you have Elementor Pro please Go to Motion Effects > Sticky > None. This feature only works on the frontend.', 'pafe' ),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);
		
		$element->add_control(
			'pafe_sticky_header_on',
			[
				'label' => __( 'Sticky On', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => 'true',
				'default' => [ 'desktop', 'tablet', 'mobile' ],
				'options' => [
					'desktop' => __( 'Desktop', 'elementor-pro' ),
					'tablet' => __( 'Tablet', 'elementor-pro' ),
					'mobile' => __( 'Mobile', 'elementor-pro' ),
				],
				'condition' => [
					'pafe_sticky_header_enable!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'pafe_sticky_header_offset',
			[
				'label' => __( 'Sticky Offset (px)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min' => 0,
				'max' => 1000,
				'required' => true,
				'condition' => [
					'pafe_sticky_header_enable!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'pafe_sticky_header_active_background',
				'selector' => '{{WRAPPER}}.pafe-sticky-header-active',
				'label' => __( 'Background When Sticky', 'pafe' ),
				'condition' => [
					'pafe_sticky_header_enable!' => '',
				],
			]
		);

		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_sticky_header_active_box_shadow',
				'label' => __( 'Box Shadow When Sticky', 'pafe' ),
				'selector' => '{{WRAPPER}}.pafe-sticky-header-active',
				'condition' => [
					'pafe_sticky_header_enable!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'pafe_sticky_header_active_section_height',
			[
				'label' => __( 'Header Height When Sticky', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-container' => 'transition: all 0.4s ease-in-out 0s;',
					'{{WRAPPER}}.pafe-sticky-header-active .elementor-container' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pafe_sticky_header_enable!' => '',
				],
			]
		);

		$element->add_control(
			'pafe_sticky_header_show_on_scroll_up_enable',
			[
				'label' => __( 'Hide Header on scroll down, show on scroll up', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'separator' => 'before',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$element->end_controls_section();

	}

	public function before_render_section($element) {
		$settings = $element->get_settings_for_display();
		if( !empty($settings['pafe_sticky_header_enable']) ) { 
			$element->add_render_attribute( '_wrapper', [
				'data-pafe-sticky-header' => '',
				'class' => 'pafe-sticky-header',
			] );

			$element->add_render_attribute( '_wrapper', [
				'data-pafe-sticky-header-offset' => $settings['pafe_sticky_header_offset'],
			] );

			if (!empty($settings['pafe_sticky_header_show_on_scroll_up_enable'])) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-sticky-header-show-on-scroll-up' => '',
				] );
			}

			if (in_array('desktop', $settings['pafe_sticky_header_on'])) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-sticky-header-on-desktop' => '',
				] );

				if ($settings['pafe_sticky_header_offset'] == 0) {
					$element->add_render_attribute( '_wrapper', [
						'class' => 'pafe-sticky-header-fixed-start-on-desktop',
					] );
				}
			}

			if (in_array('tablet', $settings['pafe_sticky_header_on'])) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-sticky-header-on-tablet' => '',
				] );

				if ($settings['pafe_sticky_header_offset'] == 0) {
					$element->add_render_attribute( '_wrapper', [
						'class' => 'pafe-sticky-header-fixed-start-on-tablet',
					] );
				}
			}

			if (in_array('mobile', $settings['pafe_sticky_header_on'])) {
				$element->add_render_attribute( '_wrapper', [
					'data-pafe-sticky-header-on-mobile' => '',
				] );

				if ($settings['pafe_sticky_header_offset'] == 0) {
					$element->add_render_attribute( '_wrapper', [
						'class' => 'pafe-sticky-header-fixed-start-on-mobile',
					] );
				}
			}
		}
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'pafe_register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'before_render_section'], 10, 1 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_section'], 10, 1 );
	}

}
