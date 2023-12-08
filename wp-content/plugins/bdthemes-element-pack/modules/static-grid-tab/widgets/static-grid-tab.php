<?php

namespace ElementPack\Modules\StaticGridTab\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Mask_Controls;

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

class Static_Grid_Tab extends Module_Base
{

	use Global_Swiper_Controls;
	use Global_Mask_Controls;

	public function get_name()
	{
		return 'bdt-static-grid-tab';
	}

	public function get_title()
	{
		return BDTEP . esc_html__('Static Grid Tab', 'bdthemes-element-pack');
	}

	public function get_icon()
	{
		return 'bdt-wi-static-grid-tab';
	}

	public function get_categories()
	{
		return ['element-pack'];
	}

	public function get_keywords()
	{
		return ['post', 'grid', 'tab', 'blog', 'recent', 'news', 'static'];
	}

	public function get_style_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-static-grid-tab'];
		}
	}

	public function get_script_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['gridtab', 'ep-scripts'];
		} else {
			return ['gridtab', 'ep-static-grid-tab'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://www.youtube.com/watch?v=HIvQX9eLWU8';
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_carousel_content',
			[
				'label' => __('Items', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __('Image', 'bdthemes-element-pack'),
				'type' => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'dynamic' => [
					'active' => true,
				],
				// 'default'     => [
				//     'url' => Utils::get_placeholder_image_src(),
				// ],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-' . rand(1, 8) . '.svg',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is a title', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text',
			[
				'label' => __('Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your text', 'bdthemes-element-pack'),
			]
		);

		$repeater->add_control(
			'readmore_link',
			[
				'label' => esc_html__('Link', 'bdthemes-element-pack'),
				'type' => Controls_Manager::URL,
				'dynamic' => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'static_tabs_item',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-5.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-6.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-7.svg']],
					['image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-8.svg']],
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_additional_settings',
			[
				'label' => __('Additional Settings', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('Note:- The changes will reflect on Preview Page.', 'bdthemes-element-pack'),
				'default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'options' => [
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
			'layout_type',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid' => esc_html__('Grid', 'bdthemes-element-pack'),
					'tab' => esc_html__('Tab', 'bdthemes-element-pack'),
				],
			]
		);

		// $this->add_control(
		// 	'keep_open',
		// 	[
		// 		'label' => esc_html__( 'Keep Open', 'bdthemes-element-pack' ),
		// 		'type'  => Controls_Manager::SWITCHER,
		// 	]
		// );

		$this->add_control(
			'active_tab_no',
			[
				'label' => esc_html__('Active Tab Index', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => esc_html__('Speed', 'plugin-name'),
				'type' => Controls_Manager::NUMBER,
				'min' => 100,
				'max' => 1000,
				'step' => 10,
				'default' => 500,
			]
		);

		$this->add_control(
			'show_close',
			[
				'label' => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		// $this->add_control(
		// 	'show_arrows',
		// 	[
		// 		'label' => esc_html__( 'Arrows', 'bdthemes-element-pack' ),
		// 		'type'  => Controls_Manager::SWITCHER,
		// 	]
		// );

		$this->add_control(
			'scroll_to_tab',
			[
				'label' => esc_html__('Scroll To Tab', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grid_tab_type',
			[
				'label' => esc_html__('Grid Tab Type', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => esc_html__('Image', 'bdthemes-element-pack'),
					'title' => esc_html__('Title', 'bdthemes-element-pack'),
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumb_image_size',
				'default' => 'medium',
				'condition' => [
					'grid_tab_type' => 'image'
				]
			]
		);

		$this->add_responsive_control(
			'tab_text_align',
			[
				'label' => __('Text Align', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
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
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-title, {{WRAPPER}} .bdt-ep-static-grid-tab-thumbnail' => 'text-align: {{VALUE}};',
				],
				// 'condition' => [
				// 	'grid_tab_type' => 'title',
				// ],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => __('Show Title', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => __('Show Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_readmore',
			[
				'label' => esc_html__('Show Read More', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => __('Show Image', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'default' => 'medium',
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'content_reverse',
			[
				'label' => esc_html__('Content Reverse', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-sgt-content-reverse--',
				'render_type' => 'template',
				'separator' => 'before',
			]
		);


		$this->add_responsive_control(
			'text_align',
			[
				'label' => __('Alignment', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
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
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-item' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label' => esc_html__('Read More', 'bdthemes-element-pack'),
				'condition' => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label' => esc_html__('Read More Text', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_icon',
			[
				'label' => esc_html__('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'right',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore .bdt-button-icon-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_tab',
			[
				'label' => esc_html__('Tab', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_grid_tabs_style');

		$this->start_controls_tab(
			'tab_grid_tabs_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_tab_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gridtab > dt .bdt-ep-static-grid-tab-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'grid_tab_type' => 'title',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_tab_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab > dt',
			]
		);

		$this->add_control(
			'item_border_width',
			[
				'label' => esc_html__('Border Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'tab_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab > dt, {{WRAPPER}} .bdt-static-grid-tab .gridtab > dd' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab > dt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_text_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-title',
				'condition' => [
					'grid_tab_type' => 'title',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_grid_tabs_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'active_tab_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gridtab > dt.is-active .bdt-ep-static-grid-tab-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'grid_tab_type' => 'title',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_tab_active_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab > dt.is-active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tabs_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_background_color',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab > dd',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab > dd' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_space_between',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-item' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __('Image', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-image img'
			]
		);

		$this->add_control(
			'image_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-image img',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-image img'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-main-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-main-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-main-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-main-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __('Text', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes',
				]
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label' => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore' => 'yes',
				],
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
			'readmore_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_background',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-readmore',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'readmore_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-readmore',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'readmore_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-readmore',
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
			'readmore_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_hover_background',
				'selector' => '{{WRAPPER}} .bdt-ep-static-grid-tab-readmore:hover',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'readmore_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-static-grid-tab-readmore:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close_button',
			[
				'label' => esc_html__('Close Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_close' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_close_button_style');

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab__close:before, {{WRAPPER}} .bdt-static-grid-tab .gridtab__close:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'close_button_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab__close',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'close_button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab__close',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'close_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab__close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'close_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab__close',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab__close:hover::before, {{WRAPPER}} .bdt-static-grid-tab .gridtab__close:hover::after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'close_button_hover_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-static-grid-tab .gridtab__close:hover',
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'close_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-static-grid-tab .gridtab__close:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	//start here
	public function render()
	{
		$settings = $this->get_settings_for_display();
		$id = 'bdt-ep-static-grid-tab-' . $this->get_id();

		if (empty($settings['static_tabs_item'])) {
			return;
		}

		$this->render_header();

		?>
		<dl id="<?php echo esc_attr($id); ?>" class="gridtab">
			<?php foreach ($settings['static_tabs_item'] as $index => $item): ?>
				<?php $this->render_post($item, 'link_'.$index); ?>
			<?php endforeach; ?>
		</dl>
		</div>
		<?php
	}

	public function render_content_image($item)
	{
		$settings = $this->get_settings_for_display();

		if (!$settings['show_image']) {
			return;
		}


		?>
		<div class="bdt-ep-static-grid-tab-image">
			<div class="bdt-ep-static-grid-tab-image-inner bdt-gt-mh bdt-cover-container">

				<?php
				$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);
				if (!$thumb_url) {
					printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['title']));
				} else {
					print(
						wp_get_attachment_image(
							$item['image']['id'],
							$settings['thumbnail_size_size'],
							false,
							[
								'alt' => esc_html($item['title'])
							]
						)
					);
				}
				?>

			</div>
		</div>
		<?php
	}

	public function render_tab_image($item)
	{
		$settings = $this->get_settings_for_display();

		$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumb_image_size', $settings);
		if (!$thumb_url) {
			$thumb_url = $item['image']['url'];
		}
		?>
		<div class="bdt-ep-static-grid-tab-thumbnail">
			<img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_html($item['title']); ?>">
		</div>
		<?php
	}

	public function render_title($item)
	{
		$settings = $this->get_settings_for_display();

		if (!$settings['show_title']) {
			return;
		}

		$this->add_render_attribute('title-wrap', 'class', 'bdt-ep-static-grid-tab-main-title', true);

		?>
		<?php if ($item['title']): ?>
			<<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>
				<?php echo $this->get_render_attribute_string('title-wrap'); ?>>
				<?php echo wp_kses($item['title'], element_pack_allow_tags('title')); ?>
			</<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>>
		<?php endif; ?>
	<?php
	}

	public function render_tab_title($item)
	{
		$settings = $this->get_settings_for_display();

		?>
		<?php if ($item['title']): ?>
			<div class="bdt-ep-static-grid-tab-title">
				<?php echo wp_kses($item['title'], element_pack_allow_tags('title')); ?>
			</div>
		<?php endif; ?>
	<?php
	}

	public function render_excerpt($item)
	{
		$settings = $this->get_settings_for_display();

		if (!$settings['show_text']) {
			return;
		}

		?>
		<?php if ($item['text']): ?>
			<div class="bdt-ep-static-grid-tab-excerpt">
				<?php echo wp_kses_post($item['text']); ?>
			</div>
		<?php endif; ?>
	<?php
	}

	public function render_readmore($item, $readmore_key)
	{
		$settings = $this->get_settings_for_display();

		if (!$settings['show_readmore']) {
			return;
		}

		$this->add_render_attribute(
			[
				$readmore_key => [
					'class' => [
						'bdt-ep-static-grid-tab-readmore',
						$settings['readmore_hover_animation'] ? 'elementor-animation-' . $settings['readmore_hover_animation'] : '',
					],
				]
			],
			'',
			'',
			true
		);
		//url
		if (!empty($item['readmore_link']['url'])) {
			$this->add_link_attributes($readmore_key, $item['readmore_link']);
		}

		?>
		<?php if ((!empty($item['readmore_link']['url'])) && ($settings['show_readmore'])): ?>
			<div class="bdt-ep-static-grid-tab-readmore-wrap">
				<a <?php echo $this->get_render_attribute_string($readmore_key); ?>>
					<?php echo esc_html($settings['readmore_text']); ?>
					<?php if ($settings['readmore_icon']['value']): ?>
						<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">
							<?php Icons_Manager::render_icon($settings['readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
						</span>
					<?php endif; ?>
				</a>
			</div>
		<?php endif; ?>
	<?php
	}

	public function render_header()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('static-grid-tab', 'class', ['bdt-static-grid-tab']);

		$columns_tablet = isset($settings["columns_tablet"]) ? esc_attr($settings["columns_tablet"]) : 3;
		$columns_mobile = isset($settings["columns_mobile"]) ? esc_attr($settings["columns_mobile"]) : 2;

		if ($this->ep_is_edit_mode()) {
			$this->add_render_attribute(
				[
					'static-grid-tab' => [
						'data-settings' => [
							wp_json_encode(array_filter([
								'grid' => $settings['columns'],
								'borderWidth' => $settings['item_border_width']['size'],
								'config' => [
									'layout' => $settings['layout_type'],
									// 'keepOpen'    => ( $settings['keep_open'] ) ? true : false,
									'speed' => $settings['speed'],
									'activeTab' => $settings['active_tab_no'],
									'showClose' => ($settings['show_close']) ? true : false,
									// 'showArrows'  => ( $settings['show_arrows'] ) ? true : false,
									'scrollToTab' => ($settings['scroll_to_tab']) ? true : false,
									'rtl' => is_rtl() ? true : false,
								],
							]))
						]
					]
				]
			);
		} else {
			$this->add_render_attribute(
				[
					'static-grid-tab' => [
						'data-settings' => [
							wp_json_encode(array_filter([
								'grid' => $settings['columns'],
								'borderWidth' => $settings['item_border_width']['size'],
								'config' => [
									'layout' => $settings['layout_type'],
									// 'keepOpen'    => ( $settings['keep_open'] ) ? true : false,
									'speed' => $settings['speed'],
									'activeTab' => $settings['active_tab_no'],
									'showClose' => ($settings['show_close']) ? true : false,
									// 'showArrows'  => ( $settings['show_arrows'] ) ? true : false,
									'scrollToTab' => ($settings['scroll_to_tab']) ? true : false,
									'rtl' => is_rtl() ? true : false,
								],
								'responsive' => [
									[
										'breakpoint' => 1023,
										'settings' => [
											'grid' => $columns_tablet,
										]
									],
									[
										'breakpoint' => 767,
										'settings' => [
											'grid' => $columns_mobile,
										]
									]
								]
							]))
						]
					]
				]
			);
		}

		?>
		<div <?php echo $this->get_render_attribute_string('static-grid-tab'); ?>>
			<?php
	}

	public function render_static_content($item, $readmore_key)
	{
		$settings = $this->get_settings_for_display();

		?>
			<?php if ($settings['show_title'] == 'yes' or $settings['show_text'] == 'yes' or $settings['show_readmore'] == 'yes'): ?>
				<div class="bdt-ep-static-grid-tab-desc">
					<div class="bdt-post-grid-desc-inner bdt-gt-mh">
						<?php $this->render_title($item); ?>
						<?php $this->render_excerpt($item); ?>
						<?php $this->render_readmore($item, $readmore_key); ?>
					</div>
				</div>
			<?php endif;
	}

	public function render_static_post_tab_item($item, $readmore_key)
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('static-grid-tab-item', 'class', 'bdt-ep-static-grid-tab-item', true);

		?>
			<div <?php echo $this->get_render_attribute_string('static-grid-tab-item') ?>>
				<?php $this->render_content_image($item); ?>
				<?php $this->render_static_content($item, $readmore_key); ?>
			</div>
			<?php
	}

	public function render_post($item, $readmore_key)
	{
		$settings = $this->get_settings_for_display();

		?>
			<dt>
				<?php
				if ('title' === $settings['grid_tab_type']) {
					$this->render_tab_title($item);
				} else {
					$this->render_tab_image($item);
				}
				?>
			</dt>
			<dd>
				<?php $this->render_static_post_tab_item($item, $readmore_key); ?>
			</dd>
			<?php
	}
}
