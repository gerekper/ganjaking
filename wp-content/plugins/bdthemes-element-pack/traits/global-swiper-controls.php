<?php

namespace ElementPack\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;


defined('ABSPATH') || die();

trait Global_Swiper_Controls {

	//Navigation Controls
	protected function register_navigation_controls() {

		$this->add_control(
			'navigation',
			[
				'label'        => __('Navigation', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'arrows',
				'options'      => [
					'both'            => esc_html__('Arrows and Dots', 'bdthemes-element-pack'),
					'arrows-fraction' => esc_html__('Arrows and Fraction', 'bdthemes-element-pack'),
					'arrows'          => esc_html__('Arrows', 'bdthemes-element-pack'),
					'dots'            => esc_html__('Dots', 'bdthemes-element-pack'),
					'progressbar'     => esc_html__('Progress', 'bdthemes-element-pack'),
					'none'            => esc_html__('None', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'dynamic_bullets',
			[
				'label'     => __('Dynamic Bullets?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'show_scrollbar',
			[
				'label' => __('Show Scrollbar?', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'both_position',
			[
				'label'     => __('Arrows and Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],

			]
		);

		$this->add_control(
			'arrows_fraction_position',
			[
				'label'     => __('Arrows and Fraction Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows-fraction',
				],

			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => __('Arrows Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],

			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => __('Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],

			]
		);

		$this->add_control(
			'progress_position',
			[
				'label'     => __('Progress Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => [
					'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
					'top'    => esc_html__('Top', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => 'progressbar',
				],

			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'     => esc_html__('Arrows Icon', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => [
					'0'        => esc_html__('Default', 'bdthemes-element-pack'),
					'1'        => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2'        => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3'        => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4'        => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5'        => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6'        => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7'        => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8'        => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9'        => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10'       => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11'       => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12'       => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13'       => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14'       => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15'       => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16'       => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17'       => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18'       => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['arrows-fraction', 'both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __('Hide Arrow on Mobile', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navigation' => ['arrows-fraction', 'arrows', 'both'],
				],
			]
		);
	}

	//Carousel Settings Controls
	protected function register_carousel_settings_controls() {
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __('Carousel Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'skin',
			[
				'label'        => esc_html__('Layout', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'carousel',
				'options'      => [
					'carousel'  => esc_html__('Carousel', 'bdthemes-element-pack'),
					'coverflow' => esc_html__('Coverflow', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-carousel-style-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'coverflow_toggle',
			[
				'label'        => __('Coverflow Effect', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => [
					'skin' => 'coverflow'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'coverflow_rotate',
			[
				'label'       => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 50,
				],
				'range'       => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition'   => [
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_stretch',
			[
				'label'       => __('Stretch', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 0,
				],
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 10,
						'max'  => 100,
					],
				],
				'condition'   => [
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_modifier',
			[
				'label'       => __('Modifier', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 1,
				],
				'range'       => [
					'px' => [
						'min'  => 1,
						'step' => 1,
						'max'  => 10,
					],
				],
				'condition'   => [
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'coverflow_depth',
			[
				'label'       => __('Depth', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 100,
				],
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 10,
						'max'  => 1000,
					],
				],
				'condition'   => [
					'coverflow_toggle' => 'yes'
				],
				'render_type' => 'template',
			]
		);

		$this->end_popover();

		$this->add_control(
			'hr_005',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'skin' => 'coverflow'
				]
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __('Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type'           => Controls_Manager::SELECT,
				'label'          => esc_html__('Slides to Scroll', 'bdthemes-element-pack'),
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
			]
		);

		$this->add_control(
			'centered_slides',
			[
				'label'       => __('Center Slide', 'bdthemes-element-pack'),
				'description' => __('Use even items from Layout > Columns settings for better preview.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label' => __('Grab Cursor', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'free_mode',
			[
				'label' => __('Drag Free Mode', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'observer',
			[
				'label'       => __('Observer', 'bdthemes-element-pack'),
				'description' => __('When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_hidden_item',
			[
				'label' => __('Show Hidden Item', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-show-hidden-item--',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'hidden_item_opacity',
			[
				'label' => __('Hidden Item Opacity', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide:not(.swiper-slide-visible)' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'show_hidden_item' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	//Navigation Style Controls
	protected function register_navigation_style_controls($name) {

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __('ARROWS', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_navigation_arrows_style');

		$this->start_controls_tab(
			'tabs_nav_arrows_normal',
			[
				'label'     => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i, {{WRAPPER}} .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev, {{WRAPPER}} .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'nav_arrows_border',
				'selector'  => '{{WRAPPER}} .bdt-navigation-prev, {{WRAPPER}} .bdt-navigation-next',
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev, {{WRAPPER}} .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev, {{WRAPPER}} .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev i,
            {{WRAPPER}} .bdt-navigation-next i' => 'font-size: {{SIZE || 24}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_space',
			[
				'label'     => __('Space Between Arrows', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-navigation-prev, {{WRAPPER}} .bdt-navigation-next',
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_arrows_hover',
			[
				'label'     => __('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'nav_arrows_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-navigation-next:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'nav_arrows_border_border!' => '',
					'navigation!'               => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-navigation-next:hover',
				'condition' => [
					'navigation!' => ['dots', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hr_1',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'dots_heading',
			[
				'label'     => __('DOTS', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_navigation_dots_style');

		$this->start_controls_tab(
			'tabs_nav_dots_normal',
			[
				'label'     => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'dots_space_between',
			[
				'label'     => __('Space Between', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-dots-space-between: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_control(
			'advanced_dots_size',
			[
				'label'     => __('Advanced Size', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_width',
			[
				'label'     => __('Width(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_height',
			[
				'label'     => __('Height(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_box_shadow',
				'selector' => '{{WRAPPER}} .swiper-pagination-bullet',
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_dots_active',
			[
				'label'     => __('Active', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'active_dots_size',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ep-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => ''
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_width',
			[
				'label'     => __('Width(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_height',
			[
				'label'     => __('Height(px)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--ep-swiper-dots-active-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'active_advanced_dots_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-swiper-dots-align: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
					'advanced_dots_size' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dots_active_box_shadow',
				'selector' => '{{WRAPPER}} .swiper-pagination-bullet-active',
				'condition' => [
					'navigation!' => ['arrows', 'arrows-fraction', 'progressbar', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hr_2',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_heading',
			[
				'label'     => __('FRACTION', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'hr_12',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'active_fraction_color',
			[
				'label'     => __('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-current' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'fraction_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .swiper-pagination-fraction',
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'hr_3',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_heading',
			[
				'label'     => __('PROGRESBAR', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_13',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_color',
			[
				'label'     => __('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progres_color',
			[
				'label'     => __('Progress Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_4',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_heading',
			[
				'label'     => __('SCROLLBAR', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_14',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_color',
			[
				'label'     => __('Bar Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_drag_color',
			[
				'label'     => __('Drag Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_height',
			[
				'label'     => __('Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-container-horizontal > .swiper-scrollbar, {{WRAPPER}} .swiper-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_05',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'navi_offset_heading',
			[
				'label' => __('OFFSET', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'hr_6',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'          => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-arrows-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'          => __('Arrows Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-arrows-ncy: {{SIZE}}px;'
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'      => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => -60,
				],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'          => __('Dots Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-dots-nnx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'          => __('Dots Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 30,
				],
				'tablet_default' => [
					'size' => 30,
				],
				'mobile_default' => [
					'size' => 30,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-dots-nny: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'          => __('Arrows & Dots Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-both-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'          => __('Arrows & Dots Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-both-ncy: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'      => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => -60,
				],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'      => __('Dots Offset', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 30,
				],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncx_position',
			[
				'label'          => __('Arrows & Fraction Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-arrows-fraction-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncy_position',
			[
				'label'          => __('Arrows & Fraction Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'     => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $name . '-arrows-fraction-ncy: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cx_position',
			[
				'label'      => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => -60,
				],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cy_position',
			[
				'label'      => __('Fraction Offset', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 30,
				],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'progress_y_position',
			[
				'label'     => __('Progress Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
				],
				'range'     => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'transform: translateY({{SIZE}}px);',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_responsive_control(
			'scrollbar_vertical_offset',
			[
				'label'     => __('Scrollbar Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .swiper-container-horizontal > .swiper-scrollbar, {{WRAPPER}} .swiper-horizontal > .swiper-scrollbar' => 'bottom: {{SIZE}}px;',
				],
				'condition' => [
					'show_scrollbar' => 'yes'
				],
			]
		);
	}


	protected function render_swiper_header_attribute($name) {
		$id = 'bdt-' . $name . '-' . $this->get_id();
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('carousel', 'id', $id);

		$elementor_vp_lg = get_option('elementor_viewport_lg');
		$elementor_vp_md = get_option('elementor_viewport_md');
		$viewport_lg = !empty($elementor_vp_lg) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md = !empty($elementor_vp_md) ? $elementor_vp_md - 1 : 767;


		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute('carousel', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('carousel', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute('carousel', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute('carousel', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position']);
		}

		if ('arrows-fraction' == $settings['navigation']) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation']) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'carousel' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"              => ("yes" == $settings["autoplay"]) ? ["delay"                                                => $settings["autoplay_speed"]] : false,
							"loop"                  => ($settings["loop"] == "yes") ? true : false,
							"speed"                 => $settings["speed"]["size"],
							"pauseOnHover"          => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"         => isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 1,
							"slidesPerGroup"        => isset($settings["slides_to_scroll_mobile"]) ? (int)$settings["slides_to_scroll_mobile"] : 1,
							"spaceBetween"          => !empty($settings["item_gap"]["size"]) ? (int)$settings["item_gap"]["size"] : 20,
							"centeredSlides"        => ($settings["centered_slides"] === "yes") ? true : false,
							"grabCursor"            => ($settings["grab_cursor"] === "yes") ? true : false,
							"freeMode"              => ($settings["free_mode"] === "yes") ? true : false,
							"effect"                => $settings["skin"],
							"observer"              => ($settings["observer"]) ? true : false,
							"observeParents"        => ($settings["observer"]) ? true : false,
							"watchSlidesVisibility" => ($settings["show_hidden_item"]) ? true : false,
							"breakpoints"     => [
								(int)$viewport_md => [
									"slidesPerView"  => isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 2,
									"spaceBetween"   => !empty($settings["item_gap"]["size"]) ? (int)$settings["item_gap"]["size"] : 20,
									"slidesPerGroup" => isset($settings["slides_to_scroll_tablet"]) ? (int)$settings["slides_to_scroll_tablet"] : 1,
								],
								(int)$viewport_lg => [
									"slidesPerView"  => isset($settings["columns"]) ? (int)$settings["columns"] : 3,
									"spaceBetween"   => !empty($settings["item_gap"]["size"]) ? (int)$settings["item_gap"]["size"] : 20,
									"slidesPerGroup" => isset($settings["slides_to_scroll"]) ? (int)$settings["slides_to_scroll"] : 1,
								]
							],
							"navigation"      => [
								"nextEl" => "#" . $id . " .bdt-navigation-next",
								"prevEl" => "#" . $id . " .bdt-navigation-prev",
							],
							"pagination"      => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,
							],
							"scrollbar"       => [
								"el"   => "#" . $id . " .swiper-scrollbar",
								"hide" => "true",
							],
							'coverflowEffect' => [
								'rotate'       => ("yes" == $settings["coverflow_toggle"]) ? $settings["coverflow_rotate"]["size"] : 50,
								'stretch'      => ("yes" == $settings["coverflow_toggle"]) ? $settings["coverflow_stretch"]["size"] : 0,
								'depth'        => ("yes" == $settings["coverflow_toggle"]) ? $settings["coverflow_depth"]["size"] : 100,
								'modifier'     => ("yes" == $settings["coverflow_toggle"]) ? $settings["coverflow_modifier"]["size"] : 1,
								'slideShadows' => true,
							],
							"watchSlidesProgress" => true,
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-carousel ' . $swiper_class);
	}

	function render_navigation() {
		$settings = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';

		if ('arrows' == $settings['navigation']) : ?>
			<div class="bdt-position-z-index bdt-position-<?php
															echo esc_attr($settings['arrows_position'] . $hide_arrow_on_mobile); ?>">
				<div class="bdt-arrows-container bdt-slidenav-container">
					<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
						<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
					<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
						<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</div>
				</div>
			</div>
		<?php
		endif;
	}

	function render_pagination() {
		$settings = $this->get_settings_for_display();

		if ('dots' == $settings['navigation'] or 'arrows-fraction' == $settings['navigation']) : ?>
			<div class="bdt-position-z-index bdt-position-<?php
															echo esc_attr($settings['dots_position']); ?>">
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>

		<?php
		elseif ('progressbar' == $settings['navigation']) : ?>
			<div class="swiper-pagination bdt-position-z-index bdt-position-<?php echo esc_attr($settings['progress_position']); ?>"></div>
		<?php
		endif;
	}

	function render_both_navigation() {
		$settings = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
			<div class="bdt-arrows-dots-container bdt-slidenav-container ">

				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php
								echo esc_attr($hide_arrow_on_mobile); ?>">
						<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

					<?php
					if ('center' !== $settings['both_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php
					endif; ?>

					<div class="<?php
								echo esc_attr($hide_arrow_on_mobile); ?>">
						<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
	<?php
	}

	function render_arrows_fraction() {
		$settings = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';

	?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_fraction_position']); ?>">
			<div class="bdt-arrows-fraction-container bdt-slidenav-container ">

				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
						<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

					<?php
					if ('center' !== $settings['arrows_fraction_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php
					endif; ?>

					<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
						<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
	<?php
	}

	function render_footer() {
		$settings = $this->get_settings_for_display();

	?>
		</div>
		<?php
		if ('yes' === $settings['show_scrollbar']) : ?>
			<div class="swiper-scrollbar"></div>
		<?php
		endif; ?>
		</div>

		<?php
		if ('both' == $settings['navigation']) : ?>
			<?php $this->render_both_navigation(); ?>
			<?php
			if ('center' === $settings['both_position']) : ?>
				<div class="bdt-position-z-index bdt-position-bottom">
					<div class="bdt-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				</div>
			<?php
			endif; ?>
		<?php
		elseif ('arrows-fraction' == $settings['navigation']) : ?>
			<?php $this->render_arrows_fraction(); ?>
			<?php
			if ('center' === $settings['arrows_fraction_position']) : ?>
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			<?php
			endif; ?>
		<?php
		else : ?>
			<?php $this->render_pagination(); ?>
			<?php $this->render_navigation(); ?>
		<?php
		endif; ?>

		</div>

<?php
	}
}
