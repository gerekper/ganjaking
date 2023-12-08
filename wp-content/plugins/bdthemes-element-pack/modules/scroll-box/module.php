<?php
	
	namespace ElementPack\Modules\ScrollBox;
	
	use ElementPack;
	use Elementor\Controls_Manager;
	use Elementor\Group_Control_Box_Shadow;
	use Elementor\Group_Control_Background;
	use ElementPack\Base\Element_Pack_Module_Base;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	class Module extends Element_Pack_Module_Base {
		
		public function __construct() {
			parent::__construct();
			$this->add_actions();
		}
		
		public function get_name() {
			return 'bdt-scroll-box';
		}
		
		public function register_section( $element ) {
			$element->start_controls_section(
				'section_scroll_box',
				[
					'tab'   => Controls_Manager::TAB_STYLE,
					'label' => BDTEP_CP . esc_html__( 'Scroll Box', 'bdthemes-element-pack' ) . BDTEP_NC,
				]
			);
			
			$element->end_controls_section();
		}
		
		
		public function register_controls( $widget, $args ) {

			if ( 'section' === $widget->get_name() ) {
				$selector = '{{WRAPPER}}';
			} else {
				$selector = '{{WRAPPER}} .elementor-widget-container';
			}
			
			$widget->add_control(
				'ep_scroll_box_enable',
				[
					'label'              => esc_html__( 'Scroll Box', 'bdthemes-element-pack' ),
					'type'               => Controls_Manager::SWITCHER,
					'default'            => '',
					'return_value'       => 'yes',
					'frontend_available' => true,
					'selectors' => [
						$selector . '::-webkit-scrollbar-thumb' => 'background-color:#babac0;border-radius:16px;',
						$selector . '::-webkit-scrollbar-track' => '-webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);border-radius:16px;',
					],
				]
			);

			$widget->add_responsive_control(
				'ep_scroll_box_max_height',
				[
					'label'      => __( 'Max Height', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range'      => [
						'px' => [
							'min' => 20,
							'max' => 1000,
							'step' => 20,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 100,
					],
					'condition' => [
						'ep_scroll_box_enable' => 'yes',
					],
					'selectors' => [
						$selector => 'max-height: {{SIZE}}{{UNIT}}; overflow-y: scroll !important; oveflow-x: hidden !important;',
					],
				]
			);

			$widget->add_responsive_control(
				'ep_scroll_box_width',
				[
					'label'      => __( 'Width', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 5,
							'max' => 50,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 12,
					],
					'condition' => [
						'ep_scroll_box_enable' => 'yes',
					],
					'selectors' => [
						$selector . '::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$widget->add_control(
				'ep_scroll_box_border_type',
				[
					'label'     => __( 'Border Type', 'pafe' ),
					'separator' => 'before',
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'' => __( 'None', 'bdthemes-element-pack' ),
						'solid'  => _x( 'Solid', 'Border Control', 'bdthemes-element-pack' ),
						'double' => _x( 'Double', 'Border Control', 'bdthemes-element-pack' ),
						'dotted' => _x( 'Dotted', 'Border Control', 'bdthemes-element-pack' ),
						'dashed' => _x( 'Dashed', 'Border Control', 'bdthemes-element-pack' ),
						'groove' => _x( 'Groove', 'Border Control', 'bdthemes-element-pack' ),
					],
					'condition' => [
						'ep_scroll_box_enable' => 'yes',
					],
					'selectors' => [
						$selector . '::-webkit-scrollbar-track' => 'border-style: {{VALUE}};',
					],
				]
			);

			$widget->add_responsive_control(
				'ep_scroll_box_border_width',
				[
					'label'     => __( 'Border Width', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'condition' => [
						'ep_scroll_box_enable'       => 'yes',
						'ep_scroll_box_border_type!' => '',
					],
					'selectors' => [
						$selector . '::-webkit-scrollbar-track' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$widget->add_control(
				'ep_scroll_box_border_color',
				[
					'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'ep_scroll_box_enable'       => 'yes',
						'ep_scroll_box_border_type!' => '',
					],
					'selectors' => [
						$selector . '::-webkit-scrollbar-track' => 'border-color: {{VALUE}};',
					],
				]
			);

			$widget->add_responsive_control(
				'ep_scroll_box_border_radius',
				[
					'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'condition' => [
						'ep_scroll_box_enable' => 'yes',
					],
					'selectors' => [
						$selector . '::-webkit-scrollbar'       => 'border-radius: {{SIZE}}{{UNIT}};',
						$selector . '::-webkit-scrollbar-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
						$selector . '::-webkit-scrollbar-track' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$widget->start_controls_tabs(
				'tabs_thumb_style',
				[
					'condition' => [
						'ep_scroll_box_enable' => 'yes',
					],
				]
			);

			$widget->start_controls_tab(
				'tab_thumb',
				[
					'label' => esc_html__( 'Thumb', 'bdthemes-element-pack' ),
				]
			);

			$widget->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ep_scroll_box_thumb_color',
					'selector'  => $selector . '::-webkit-scrollbar-thumb',
				]
			);

			$widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'ep_scroll_box_thumb_shadow',
					'separator' => 'after',
					'selector'  => $selector . '::-webkit-scrollbar-thumb',
				]
			);

			$widget->end_controls_tab();

			$widget->start_controls_tab(
				'tab_track',
				[
					'label' => esc_html__( 'Track', 'bdthemes-element-pack' ),
				]
			);

			$widget->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ep_scroll_box_track_color',
					'separator' => 'after',
					'selector'  => $selector . '::-webkit-scrollbar-track',
				]
			);

			$widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'ep_scroll_box_track_shadow',
					'selector' => $selector . '::-webkit-scrollbar-track',
				]
			);

			$widget->end_controls_tab();

			$widget->end_controls_tabs();
		}	
		
		protected function add_actions() {

			add_action('elementor/element/text-editor/section_style/after_section_end', [$this, 'register_section']);
			add_action('elementor/element/text-editor/section_scroll_box/before_section_end', [$this, 'register_controls'], 10, 2);

			add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);
			add_action('elementor/element/section/section_scroll_box/before_section_end', [$this, 'register_controls'], 10, 2);

			add_action('elementor/element/container/section_shape_divider/after_section_end', [$this, 'register_section']);
			add_action('elementor/element/container/section_scroll_box/before_section_end', [$this, 'register_controls'], 10, 2);
			
		}
	}