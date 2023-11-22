<?php

namespace ElementPack\Modules\AnimatedCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use ElementPack\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Animated_Card extends Module_Base {

	public function get_name() {
		return 'bdt-animated-card';
	}

	public function get_title() {
		return BDTEP . esc_html__('Animated Card', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-animated-card';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['advanced', 'animated card', 'image', 'services', 'card', 'box'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-animated-card'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/gfXpQ-dTr9g';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_animated_layout',
			[
				'label' => esc_html__('Animated Card', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label'       => esc_html__('Image', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::MEDIA,
				'default'     => ['url' => BDTEP_ASSETS_URL . 'images/coco-can.svg']
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default'     => esc_html__('Animated Card Title', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your title', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_link_url',
			[
				'label'       => esc_html__('Title Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
			]
		);

		$this->add_control(
			'sub_title_text',
			[
				'label'   => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('This is a Label', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your sub title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label'   => esc_html__('Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your description', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_section();
		// additional_settings
		$this->start_controls_section(
			'section_additional_settings',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label' => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1080,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label' => esc_html__('Content Max Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'default'      => 'full',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'layout_direction',
			[
				'label'      => esc_html__('Image Position', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default'    => 'style-1',
				'options' => [
					'style-2' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'style-1' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_size',
			[
				'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'        => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'sub_title_size',
			[
				'label'   => esc_html__('Sub Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_sub_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'readmore',
			[
				'label'     => esc_html__('Show Read More', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-content' => 'text-align: {{VALUE}} !important;',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_link',
			[
				'label'     => esc_html__('Link to', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::URL,
				'separator' => 'before',
				'dynamic'   => [
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'readmore'       => 'yes',
				]
			]
		);

		$this->add_control(
			'onclick',
			[
				'label'     => esc_html__('OnClick', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'readmore'       => 'yes',
				]
			]
		);

		$this->add_control(
			'onclick_event',
			[
				'label'       => esc_html__('OnClick Event', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf(esc_html__('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp'),
				'condition' => [
					'readmore'       => 'yes',
					'onclick'        => 'yes'
				]
			]
		);

		$this->add_control(
			'advanced_readmore_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'readmore_icon',
				'separator'   => 'before',
				'label_block' => false,
				'skin' => 'inline',
				'condition'   => [
					'readmore'       => 'yes'
				]
			]
		);

		$this->add_control(
			'readmore_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => esc_html__('Left', 'bdthemes-element-pack'),
					'right'  => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'advanced_readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'readmore_icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'advanced_readmore_icon[value]!' => '',
					'readmore_text!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-animated-card-btn .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => esc_html__('Button ID', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
				'description' => esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_content_style');

		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card-circle::before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-animated-card-circle::before'
			]
		);

		$this->add_responsive_control(
			'content_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-animated-card-circle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-animated-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card:hover .bdt-ep-animated-card-circle::before',
			]
		);

		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card:hover .bdt-ep-animated-card-circle::before'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'content_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animated',
			[
				'label'      => esc_html__('Image', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_animated_image');

		$this->start_controls_tab(
			'tab_image_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);


		$this->add_responsive_control(
			'image_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1080,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card-img',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'image_hover_transition',
			[
				'label' => esc_html__('Transition Duration', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'image_height_hover',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1080,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-item:hover .bdt-ep-animated-card-img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_horizontal_offset',
			[
				'label' => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default' => [
					'unit' => '%',
					'size' => 80,
				],
				'range' => [
					'%' => [
						'min' => 50,
						'max' => 1080,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-style-1:hover .bdt-ep-animated-card-img' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-style-2:hover .bdt-ep-animated-card-img' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters_hover',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card:hover .bdt-ep-animated-card-img',
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card:hover .bdt-ep-animated-card-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_title'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_title_style');

		$this->start_controls_tab(
			'tab_title_style_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => esc_html__('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_style_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow_hover',
				'label' => esc_html__('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-title:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label' => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_sub_title'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_sub_title_style');

		$this->start_controls_tab(
			'tab_sub_title_style_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_bottom_space',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-sub-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_title_style_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sub_title_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-sub-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	  => [
					'show_description'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'desc_bottom_space',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-text' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'readmore'       => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_attention',
			[
				'label' => esc_html__('Attention', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'hr_divider_3',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs('tabs_readmore_style');

		$this->start_controls_tab(
			'tab_readmore_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-animated-card-btn svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-animated-card-btn'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-btn',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_hover_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-animated-card-btn:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-animated-card-btn:hover',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-card-btn:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'readmore_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-card-btn:hover',
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__('Hover Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_animated_image() {
		$settings  = $this->get_settings_for_display();

		echo '<div class="bdt-ep-animated-card-img-wrap">';
		print(wp_get_attachment_image(
			$settings['image']['id'],
			$settings['thumbnail_size_size'],
			false,
			[
				'class' => 'bdt-ep-animated-card-img',
				'alt'   => esc_html($settings['title_text'])
			]
		));
		echo '</div>';
	}

	public function render_animated_content() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('animated-title', 'class', 'bdt-ep-animated-card-title');
		if ($settings['title_link_url']['url']) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('animated-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
		}

		$this->add_render_attribute('animated_sub_title', 'class', 'bdt-ep-animated-card-sub-title');

		$this->add_render_attribute('description_text', 'class', 'bdt-ep-animated-card-text');

		$this->add_inline_editing_attributes('description_text');


		$this->add_render_attribute('readmore', 'class', ['bdt-ep-animated-card-btn', 'bdt-display-inline-block']);

		if (!empty($settings['readmore_link']['url'])) {
			$this->add_render_attribute('readmore', 'href', $settings['readmore_link']['url']);

			if ($settings['readmore_link']['is_external']) {
				$this->add_render_attribute('readmore', 'target', '_blank');
			}

			if ($settings['readmore_link']['nofollow']) {
				$this->add_render_attribute('readmore', 'rel', 'nofollow');
			}
		}

		if ($settings['readmore_attention']) {
			$this->add_render_attribute('readmore', 'class', 'bdt-ep-attention-button');
		}

		if ($settings['readmore_hover_animation']) {
			$this->add_render_attribute('readmore', 'class', 'elementor-animation-' . $settings['readmore_hover_animation']);
		}

		if ($settings['onclick']) {
			$this->add_render_attribute('readmore', 'onclick', $settings['onclick_event']);
		}

		if (!empty($settings['button_css_id'])) {
			$this->add_render_attribute('readmore', 'id', $settings['button_css_id']);
		}

?>

		<?php if ('yes' == $settings['show_sub_title']) : ?>
			<?php if ($settings['sub_title_text']) : ?>
				<<?php echo Utils::get_valid_html_tag($settings['sub_title_size']); ?> <?php echo $this->get_render_attribute_string('animated_sub_title'); ?>>
					<?php echo wp_kses_post($settings['sub_title_text'], element_pack_allow_tags('title')); ?>
				</<?php echo Utils::get_valid_html_tag($settings['sub_title_size']); ?>>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ('yes' == $settings['show_title']) : ?>
			<?php if ($settings['title_text']) : ?>
				<<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> <?php echo $this->get_render_attribute_string('animated-title'); ?>>
					<?php echo wp_kses_post($settings['title_text'], element_pack_allow_tags('title')); ?>
				</<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ('yes' == $settings['show_description']) : ?>
			<?php if ($settings['description_text']) : ?>
				<div <?php echo $this->get_render_attribute_string('description_text'); ?>>
					<?php echo wp_kses($settings['description_text'], element_pack_allow_tags('text')); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($settings['readmore']) : ?>
			<div class="bdt-ep-animated-card-btn-wrap">
				<a <?php echo $this->get_render_attribute_string('readmore'); ?>>
					<?php echo esc_html($settings['readmore_text']); ?>
					<?php if ($settings['advanced_readmore_icon']['value']) : ?>
						<span class="bdt-button-icon-align-<?php echo esc_attr($settings['readmore_icon_align']); ?>">
							<?php Icons_Manager::render_icon($settings['advanced_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
						</span>
					<?php endif; ?>
				</a>
			</div>
		<?php endif ?>
	<?php
	}

	public function render() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('animated-card', 'class', ['bdt-ep-animated-card', 'bdt-' . $settings['layout_direction'] . '']);

	?>
		<div <?php echo $this->get_render_attribute_string('animated-card'); ?>>
			<div class="bdt-ep-animated-card-item">
				<div class="bdt-ep-animated-card-circle"></div>
				<div class="bdt-ep-animated-card-content">
					<?php $this->render_animated_content(); ?>
				</div>
				<?php $this->render_animated_image(); ?>
			</div>
		</div>
<?php
	}
}
