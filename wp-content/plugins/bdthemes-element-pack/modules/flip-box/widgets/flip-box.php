<?php

namespace ElementPack\Modules\FlipBox\Widgets;

use ElementPack\Utils;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Flip_Box extends Module_Base {

	public function get_name() {
		return 'bdt-flip-box';
	}

	public function get_title() {
		return BDTEP . __('Flip Box', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-flip-box';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['flip', 'box', '3d'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-flip-box'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-flip-box'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/FLmKzk9KbQg';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_side_a_content',
			[
				'label' => __('Front', 'bdthemes-element-pack'),
			]
		);

		$this->start_controls_tabs('front_content_tabs');

		$this->start_controls_tab('front_content_tab', ['label' => __('Content', 'bdthemes-element-pack')]);

		$this->add_control(
			'graphic_element',
			[
				'label'   => __('Icon Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => __('None', 'bdthemes-element-pack'),
						'icon'  => 'fas fa-ban',
					],
					'image' => [
						'title' => __('Image', 'bdthemes-element-pack'),
						'icon'  => 'far fa-image',
					],
					'icon' => [
						'title' => __('Icon', 'bdthemes-element-pack'),
						'icon'  => 'fas fa-star',
					],
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => __('Choose Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'graphic_element' => 'image',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'label'     => __('Image Size', 'bdthemes-element-pack'),
				'default'   => 'thumbnail',
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_control(
			'flip_box_icon',
			[
				'label'       => __('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-heart',
					'library' => 'fa-solid',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_view',
			[
				'label'   => __('View', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'bdthemes-element-pack'),
					'stacked' => __('Stacked', 'bdthemes-element-pack'),
					'framed'  => __('Framed', 'bdthemes-element-pack'),
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_shape',
			[
				'label'   => __('Shape', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => __('Circle', 'bdthemes-element-pack'),
					'square' => __('Square', 'bdthemes-element-pack'),
				],
				'condition' => [
					'icon_view!'      => 'default',
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'front_title_text',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('This is the heading', 'bdthemes-element-pack'),
				'placeholder' => __('Your Title', 'bdthemes-element-pack'),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'front_description_text',
			[
				'label'       => __('Description', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'bdthemes-element-pack'),
				'placeholder' => __('Your Description', 'bdthemes-element-pack'),
				'title'       => __('Input image text here', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'front_title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('front_background_tab', ['label' => __('Background', 'bdthemes-element-pack')]);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'front_background',
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bdt-flip-box-front',
			]
		);

		$this->add_control(
			'front_background_overlay',
			[
				'label'     => __('Background Overlay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-overlay' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'front_background_image[id]!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_back_content',
			[
				'label' => __('Back', 'bdthemes-element-pack'),
			]
		);

		$this->start_controls_tabs('back_content_tabs');

		$this->start_controls_tab('back_content_tab', ['label' => __('Content', 'bdthemes-element-pack')]);

		$this->add_control(
			'back_title_text',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('This is the heading', 'bdthemes-element-pack'),
				'placeholder' => __('Your Title', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'back_description_text',
			[
				'label'       => __('Description', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => ['active' => true],
				'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'bdthemes-element-pack'),
				'placeholder' => __('Your Description', 'bdthemes-element-pack'),
				'title'       => __('Input image text here', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => __('Button Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => ['active' => true],
				'default'   => __('Click Here', 'bdthemes-element-pack'),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => __('http://your-link.com', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'   => __('Apply Link On', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'box'    => __('Whole Box', 'bdthemes-element-pack'),
					'button' => __('Button Only', 'bdthemes-element-pack'),
				],
				'default'   => 'button',
				'condition' => [
					'link[url]!' => '',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __('Extra Small', 'bdthemes-element-pack'),
					'sm' => __('Small', 'bdthemes-element-pack'),
					'md' => __('Medium', 'bdthemes-element-pack'),
					'lg' => __('Large', 'bdthemes-element-pack'),
					'xl' => __('Extra Large', 'bdthemes-element-pack'),
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'back_title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('back_background_tab', ['label' => __('Background', 'bdthemes-element-pack')]);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'back_background',
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bdt-flip-box-back',
			]
		);

		$this->add_control(
			'back_background_overlay',
			[
				'label' => __('Background Overlay', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-overlay' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'back_background_image[id]!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_settings',
			[
				'label' => __('Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => ['px', 'vh'],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-layer, {{WRAPPER}} .bdt-flip-box-layer-overlay' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'flip_effect',
			[
				'label'   => __('Flip Effect', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'flip',
				'options' => [
					'flip'     => __('Flip', 'bdthemes-element-pack'),
					'slide'    => __('Slide', 'bdthemes-element-pack'),
					'push'     => __('Push', 'bdthemes-element-pack'),
					'zoom-in'  => __('Zoom In', 'bdthemes-element-pack'),
					'zoom-out' => __('Zoom Out', 'bdthemes-element-pack'),
					'fade'     => __('Fade', 'bdthemes-element-pack'),
					'slide-overflow'  => __('Slide Overflow', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-flip-box-effect-',
			]
		);

		$this->add_control(
			'flip_direction',
			[
				'label'   => __('Flip Direction', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __('Left', 'bdthemes-element-pack'),
					'right' => __('Right', 'bdthemes-element-pack'),
					'up'    => __('Up', 'bdthemes-element-pack'),
					'down'  => __('Down', 'bdthemes-element-pack'),
				],
				'condition' => [
					'flip_effect!' => [
						'fade',
						'zoom-in',
						'zoom-out',
						'slide-overflow',
					],
				],
				'prefix_class' => 'bdt-flip-box-direction-',
			]
		);

		$this->add_control(
			'flip_direction_content',
			[
				'label'   => __('Flip Direction', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'up',
				'options' => [
					'up'    => __('Up', 'bdthemes-element-pack'),
					'down'  => __('Down', 'bdthemes-element-pack'),
				],
				'condition' => [
					'flip_effect' => [
						'slide-overflow',
					],
				],
				'prefix_class' => 'bdt-flip-box-direction-',
			]
		);

		$this->add_control(
			'flip_3d',
			[
				'label'        => __('3D Depth', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'bdt-flip-box-3d-',
				'condition' => [
					'flip_effect' => 'flip',
				],
			]
		);
		$this->add_control(
			'flip_transition',
			[
				'label'     => esc_html__('Transition', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'flip_transition_easing',
			[
				'label'      			=> esc_html__('Easing type', 'bdthemes-element-pack'),
				'type'       			=> Controls_Manager::SELECT,
				'default'    			=> 'ease-out',
				'options'    			=> [
					'ease-out'     => esc_html__('Default', 'bdthemes-element-pack'),
					'circ' 		  => esc_html__('Circ', 'bdthemes-element-pack'),
					'cubic' 	  => esc_html__('Cubic', 'bdthemes-element-pack'),
					'expo'   	  => esc_html__('Expo', 'bdthemes-element-pack'),
					'quad'  	  => esc_html__('Quad', 'bdthemes-element-pack'),
					'quart' 	  => esc_html__('Quart', 'bdthemes-element-pack'),
					'quint' 	  => esc_html__('Quint', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-flip-box-easing-',
			]
		);

		$this->add_control(
			'flip_transiton_duration',
			[
				'label'         => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'range'         => [
					'px'        => [
						'min'   => 0.1,
						'max'   => 5,
						'step'  => 0.1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-layer' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$this->add_control(
			'flip_trigger',
			[
				'label'   => esc_html__('Trigger Type', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
					'click' => esc_html__('Click', 'bdthemes-element-pack'),
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_front',
			[
				'label' => __('Front', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'front_border',
				'selector'  => '{{WRAPPER}} .bdt-flip-box-front',
			]
		);

		$this->add_responsive_control(
			'front_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'front_alignment',
			[
				'label' => __('Alignment', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justify', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-overlay' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'front_vertical_position',
			[
				'label' => __('Vertical Position', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);
		$this->start_controls_tabs('front_style_tabs');

		$this->start_controls_tab(
			'front_icon_style_tab',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_primary_color',
			[
				'label' => __('Icon Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}} .bdt-flip-box .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color',
			[
				'label' => __('Secondary Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'graphic_element' => 'icon',
					'icon_view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label' => __('Icon Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					'graphic_element' => 'icon',
					'icon_view!' => 'default',
				],
			]
		);

		$this->add_control(
			'icon_rotate',
			[
				'label' => __('Icon Rotate', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_border_width',
			[
				'label' => __('Border Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'graphic_element' => 'icon',
					'icon_view' => 'framed',
				],
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'graphic_element' => 'icon',
					'icon_view!' => 'default',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'front_image_style_tab',
			[

				'label'     => __('Image', 'bdthemes-element-pack'),
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_control(
			'image_width',
			[
				'label'      => __('Size (%)', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default'    => [
					'unit' => '%',
					'size' => 10
				],
				'range' => [
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-image img' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'   => __('Opacity (%)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-image' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_border',
				'label'     => __('Image Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-flip-box-image img',
				'condition' => [
					'graphic_element' => 'image',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'graphic_element' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'front_image_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'front_title_style_tab',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'condition' => [
					'front_title_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'front_title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'front_description_text!' => '',
				],
			]
		);

		$this->add_control(
			'front_title_color',
			[
				'label' => __('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-title' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'front_title_typography',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'front_description_style_tab',
			[
				'label' => __('Description', 'bdthemes-element-pack'),
				'condition' => [
					'front_description_text!' => '',
				],
			]
		);

		$this->add_control(
			'front_description_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-desc' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'front_description_typography',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-flip-box-front .bdt-flip-box-layer-desc',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_back',
			[
				'label' => __('Back', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'back_border',
				'selector'  => '{{WRAPPER}} .bdt-flip-box-back',
			]
		);

		$this->add_responsive_control(
			'back_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'back_alignment',
			[
				'label' => __('Alignment', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-overlay' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .bdt-flip-box-button' => 'margin-{{VALUE}}: 0',
				],
			]
		);

		$this->add_responsive_control(
			'back_vertical_position',
			[
				'label'       => __('Vertical Position', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-overlay' => 'justify-content: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);


		$this->start_controls_tabs('back_style_tabs');

		$this->start_controls_tab(
			'back_title_style_tab',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'condition' => [
					'back_title_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'back_title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'back_title_text!' => '',
				],
			]
		);

		$this->add_control(
			'back_title_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-title' => 'color: {{VALUE}}',

				],
				'condition' => [
					'back_title_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'back_title_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-title',
				'condition' => [
					'back_title_text!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'back_description_style_tab',
			[
				'label' => __('Description', 'bdthemes-element-pack'),
				'condition' => [
					'back_description_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'back_description_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'back_description_color',
			[
				'label' => __('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-desc' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'description_typography_b',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-flip-box-back .bdt-flip-box-layer-desc',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __('Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'button_text!' => '',
				],
			]
		);


		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-flip-box-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-flip-box-button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-flip-box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-flip-box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-flip-box-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-flip-box-button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-flip-box-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$animation   = ($settings['button_hover_animation']) ? ' elementor-animation-' . $settings['button_hover_animation'] : '';
		$wrapper_tag = 'div';
		$button_tag  = 'a';

		$this->add_render_attribute(
			'button',
			'class',
			[
				'bdt-flip-box-button',
				'elementor-button',
				'elementor-size-' . $settings['button_size'],
				$animation,
			]
		);

		$this->add_render_attribute('wrapper', 'class', 'bdt-flip-box-layer bdt-flip-box-back');

		if (!empty($settings['link']['url'])) {
			if ('box' === $settings['link_click']) {
				$wrapper_tag = 'a';
				$button_tag = 'button';
				$this->add_link_attributes('wrapper', $settings['link']);
			} else {
				$this->add_link_attributes('button', $settings['link']);
			}
		}

		if ('icon' === $settings['graphic_element']) {
			$this->add_render_attribute('icon-wrapper', 'class', 'elementor-icon-wrapper');
			$this->add_render_attribute('icon-wrapper', 'class', 'elementor-view-' . $settings['icon_view']);
			if ('default' != $settings['icon_view']) {
				$this->add_render_attribute('icon-wrapper', 'class', 'elementor-shape-' . $settings['icon_shape']);
			}
			if (!empty($settings['icon'])) {
				$this->add_render_attribute('icon', 'class', $settings['icon']);
			}
		}

		$this->add_render_attribute('box_front_title_tags', 'class', 'bdt-flip-box-layer-title');

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-heart';
		}

		$migrated  = isset($settings['__fa4_migrated']['flip_box_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();


		$this->add_render_attribute(
			[
				'flip-box' => [
					'class' => 'bdt-flip-box',
					'data-settings' => [
						wp_json_encode([
							"flipTrigger"     => ("click" == $settings["flip_trigger"]) ? 'click' : 'hover',
						])
					]
				]
			]
		);


?>
		<div <?php $this->print_render_attribute_string('flip-box'); ?>>
			<div class="bdt-flip-box-layer bdt-flip-box-front">
				<div class="bdt-flip-box-layer-overlay">
					<div class="bdt-flip-box-layer-inner">
						<?php if ('image' === $settings['graphic_element'] && !empty($settings['image']['url'])) : ?>
							<div class="bdt-flip-box-image">
								<?php echo Group_Control_Image_Size::get_attachment_image_html($settings); ?>
							</div>
						<?php elseif ('icon' === $settings['graphic_element'] && !empty($settings['flip_box_icon']['value'])) : ?>
							<div <?php echo $this->get_render_attribute_string('icon-wrapper'); ?>>
								<div class="elementor-icon">

									<?php if ($is_new || $migrated) :
										Icons_Manager::render_icon($settings['flip_box_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
									else : ?>
										<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
									<?php endif; ?>

								</div>
							</div>
						<?php endif; ?>

						<?php if (!empty($settings['front_title_text'])) : ?>
							<<?php echo Utils::get_valid_html_tag($settings['front_title_tags']); ?> <?php echo $this->get_render_attribute_string('box_front_title_tags'); ?>>
								<?php echo wp_kses($settings['front_title_text'], element_pack_allow_tags('title')); ?>
							</<?php echo Utils::get_valid_html_tag($settings['front_title_tags']); ?>>
						<?php endif; ?>

						<?php if (!empty($settings['front_description_text'])) : ?>
							<div class="bdt-flip-box-layer-desc">
								<?php echo wp_kses($settings['front_description_text'], element_pack_allow_tags('text')); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<<?php echo esc_attr($wrapper_tag); ?> <?php echo $this->get_render_attribute_string('wrapper'); ?>>
				<div class="bdt-flip-box-layer-overlay">
					<div class="bdt-flip-box-layer-inner">
						<?php if (!empty($settings['back_title_text'])) : ?>
							<<?php echo Utils::get_valid_html_tag($settings['back_title_tags']); ?> <?php echo $this->get_render_attribute_string('box_front_title_tags'); ?>>
								<?php echo wp_kses($settings['back_title_text'], element_pack_allow_tags('title')); ?>
							</<?php echo Utils::get_valid_html_tag($settings['back_title_tags']); ?>>
						<?php endif; ?>

						<?php if (!empty($settings['back_description_text'])) : ?>
							<div class="bdt-flip-box-layer-desc">
								<?php echo wp_kses($settings['back_description_text'], element_pack_allow_tags('text')); ?>
							</div>
						<?php endif; ?>

						<?php if (!empty($settings['button_text'])) : ?>
							<<?php echo esc_attr($button_tag); ?> <?php echo $this->get_render_attribute_string('button'); ?>>
								<?php echo wp_kses($settings['button_text'], element_pack_allow_tags('title')); ?>
							</<?php echo esc_attr($button_tag); ?>>
						<?php endif; ?>
					</div>
				</div>
			</<?php echo esc_attr($wrapper_tag); ?>>
		</div>
	<?php
	}
}
