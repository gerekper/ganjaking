<?php

namespace ElementPack\Modules\FancyTabs\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Fancy_Tabs extends Module_Base {

	public function get_name() {
		return 'bdt-fancy-tabs';
	}

	public function get_title() {
		return BDTEP . esc_html__('Fancy Tabs', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-fancy-tabs';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['fancy', 'tabs', 'toggle', 'accordion'];
	}

	public function is_reload_preview_required() {
		return false;
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-fancy-tabs'];
		}
	}
	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-fancy-tabs'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/wBTRSjofce4';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_tabs_item',
			[
				'label' => __('Fancy Tabs Items', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			[
				'label'        => esc_html__('Icon Type', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'icon',
				'prefix_class' => 'bdt-icon-type-',
				'render_type'  => 'template',
				'options'      => [
					'icon' => [
						'title' => esc_html__('Icon', 'bdthemes-element-pack'),
						'icon'  => 'fas fa-star'
					],
					'image' => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'far fa-image',
					],
				],
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label'            => __('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition'        => [
					'icon_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'       => __('Image Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'tab_title',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('Tab Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_sub_title',
			[
				'label'       => __('Sub Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tabs_button',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'         => esc_html__('Button Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => '#'],
				'show_external' => false,
				'dynamic'       => ['active' => true],
				'condition'     => [
					'tabs_button!' => ''
				]
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => ['active' => true],
				'default'    => __('Tab Content', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tabs',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_sub_title'   => __('Subtitle Goes Here', 'bdthemes-element-pack'),
						'tab_title'   	  => __('Fancy Tabs Item One', 'bdthemes-element-pack'),
						'tab_content' 	  => __('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Recusandae voluptate repellendus magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'far fa-laugh', 'library' => 'fa-regular'],
					],
					[
						'tab_sub_title'   => __('Subtitle Goes Here', 'bdthemes-element-pack'),
						'tab_title'   => __('Fancy Tabs Item Two', 'bdthemes-element-pack'),
						'tab_content' => __('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Recusandae voluptate repellendus magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-cog', 'library' => 'fa-solid'],
					],
					[
						'tab_sub_title'   => __('Subtitle Goes Here', 'bdthemes-element-pack'),
						'tab_title'   => __('Fancy Tabs Item Three', 'bdthemes-element-pack'),
						'tab_content' => __('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Recusandae voluptate repellendus magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-dice-d6', 'library' => 'fa-solid'],
					],
					[
						'tab_sub_title'   => __('Subtitle Goes Here', 'bdthemes-element-pack'),
						'tab_title'   => __('Fancy Tabs Item Four', 'bdthemes-element-pack'),
						'tab_content' => __('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Recusandae voluptate repellendus magni illo ea animi.', 'bdthemes-element-pack'),
						'selected_icon'  => ['value' => 'fas fa-ring', 'library' => 'fa-solid'],
					],
				],

				'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) }}} {{{ tab_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_fancy_tabs',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'tabs_item_cutom_width',
			[
				'label' => esc_html__('Icon Area Width(%)', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs .bdt-custom-width.bdt-width-1-2\@s' => 'width: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Icon Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label'   => esc_html__('Icon Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => [
					'small'    => esc_html__('Small', 'bdthemes-element-pack'),
					'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
					'large'    => esc_html__('Large', 'bdthemes-element-pack'),
					'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'fancy_tabs_event',
			[
				'label'   => __('Select Event', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mouseover',
				'options' => [
					'click'     => __('Click', 'bdthemes-element-pack'),
					'mouseover' => __('Hover', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'fancy_tabs_position',
			[
				'label'   => __('Select Tabs Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __('Left', 'bdthemes-element-pack'),
					'right' => __('Right', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'tabs_content_height_show',
			[
				'label' => __('Content Fixed Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'tabs_content_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-height-fixed .bdt-ep-fancy-tabs-content' => 'max-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-height-fixed' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'tabs_content_height_show' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'tabs_content_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'   => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_content',
			[
				'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__('Show Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'fancy_tab_active_item',
			[
				'label'       => __('Active Item', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'default'	  => 1,
				'description' => 'Type your item number.',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_fancy_tabs_style',
			[
				'label' => __('Fancy Tabs', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_content_heading',
			[
				'label'      => __('Content', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'tabs_content_padding',
			[
				'label'      => __('Content Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tabs_item_heading',
			[
				'label'      => __('Item', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tabs_item_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'glassmorphism_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

			]
		);

		$this->add_control(
			'glassmorphism_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'glassmorphism_effect' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-item',
			]
		);

		$this->add_responsive_control(
			'tabs_item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'tabs_item_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-tabs-item',
			]
		);

		$this->add_control(
			'tabs_item_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'tabs_item_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'tabs_item_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tabs_item_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'tabs_item_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_item_hover',
			[
				'label' => __('hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover',
			]
		);

		$this->add_control(
			'tabs_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_item_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tabs_item_active_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-item.active',
			]
		);

		$this->add_control(
			'tabs_item_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item.active'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tabs_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tabs_item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-item.active',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon_box',
			[
				'label'      => __('Icon/Image', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('icon_colors');

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-icon',
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-tabs-icon'
			]
		);

		$this->add_control(
			'icon_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'icon_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'icon_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon'     => 'border-radius: {{VALUE}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon img' => 'border-radius: {{VALUE}}; overflow: hidden;'
				],
				'condition' => [
					'icon_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-icon'
			]
		);

		$this->add_control(
			'rotate',
			[
				'label'   => __('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon i, {{WRAPPER}} .bdt-ep-fancy-tabs-icon svg, {{WRAPPER}} .bdt-ep-fancy-tabs-icon img'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_background_rotate',
			[
				'label'   => __('Background Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_icon_heading',
			[
				'label'     => __('Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => __('Image Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_fullwidth',
			[
				'label' => __('Image Fullwidth', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-icon' => 'width: 100%;box-sizing: border-box;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs img',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-fancy-tabs img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'fancy_tabs_event' => 'click',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'icon_hover_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon'
			]
		);

		$this->add_control(
			'icon_hover_rotate',
			[
				'label'   => __('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon i, {{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon svg, {{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon img'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_hover_background_rotate',
			[
				'label'   => __('Background Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);


		$this->add_control(
			'image_icon_hover_heading',
			[
				'label'     => __('Image Effect', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters_hover',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon img',
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item:hover .bdt-ep-fancy-tabs-icon img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item.active .bdt-ep-fancy-tabs-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-tabs-item.active .bdt-ep-fancy-tabs-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-title' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'label' => __('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_content' => ['yes'],
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-text' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-tabs-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-tabs-button a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'border_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-button a',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-tabs-button a:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-tabs-button a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function activeItem($active_item, $totalItem) {
		$active_item = (int) $active_item;
		return $active_item = ($active_item <= 0 || $active_item > $totalItem ? 1 : $active_item);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['fancy_tabs_event']) {
			$fancyTabsEvent = $settings['fancy_tabs_event'];
		} else {
			$fancyTabsEvent = false;
		}

		$this->add_render_attribute(
			[
				'tabs' => [
					'id' => 'bdt-ep-fancy-tabs-' . $this->get_id(),
					'class' => 'bdt-ep-fancy-tabs',
					'data-settings' => [
						wp_json_encode(array_filter([
							'tabs_id' => 'bdt-ep-fancy-tabs-' . $this->get_id(),
							'mouse_event' => $fancyTabsEvent,
						]))
					]
				]
			]
		);

?>
		<div <?php echo $this->get_render_attribute_string('tabs'); ?>>
			<div class="bdt-grid bdt-flex bdt-flex-middle">

				<?php if ('left' == $settings['fancy_tabs_position']) : ?>
					<div class="bdt-width-1-1 bdt-width-1-2@s bdt-flex-center bdt-custom-width">
						<?php $this->tab_items(); ?>
					</div>
					<div class="bdt-width-1-1 bdt-width-expand@s">
						<?php $this->tabs_content(); ?>
					</div>
				<?php endif; ?>

				<?php if ('right' == $settings['fancy_tabs_position']) : ?>
					<div class="bdt-width-1-1 bdt-width-expand@s">
						<?php $this->tabs_content(); ?>
					</div>
					<div class="bdt-width-1-1 bdt-width-1-2@s bdt-flex-center bdt-custom-width">
						<?php $this->tab_items(); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	<?php
	}

	public function tabs_content() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		if ($settings['tabs_content_height_show'] == 'yes') {
			$this->add_render_attribute('tabs-content-wrapper', 'class', 'bdt-ep-fancy-tabs-height-fixed');
		}
		$this->add_render_attribute('tabs-content-wrapper', 'class', 'bdt-ep-fancy-tabs-content-wrap');

	?>
		<div <?php echo $this->get_render_attribute_string('tabs-content-wrapper'); ?>>
			<?php foreach ($settings['tabs'] as $index => $item) :
				$tab_count = $index + 1;
				$tab_id    = 'bdt-tab-' . $tab_count . esc_attr($id);

				$active_item = $this->activeItem($settings['fancy_tab_active_item'], count($settings['tabs']));

				if ($tab_id    == 'bdt-tab-' . $active_item . esc_attr($id)) {
					$this->add_render_attribute('tabs-content', 'class', 'bdt-ep-fancy-tabs-content active', true);
				} else {
					$this->add_render_attribute('tabs-content', 'class', 'bdt-ep-fancy-tabs-content', true);
				}

				$this->add_render_attribute('fancy_title_tags', 'class', 'bdt-ep-fancy-tabs-title', true);

			?>

				<div id="<?php echo esc_attr($tab_id); ?>" <?php echo ($this->get_render_attribute_string('tabs-content')); ?>>

					<?php if ($item['tab_sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
						<div class="bdt-ep-fancy-tabs-sub-title">
							<?php echo wp_kses($item['tab_sub_title'], element_pack_allow_tags('title')); ?>
						</div>
					<?php endif; ?>

					<?php if ($item['tab_title'] && ('yes' == $settings['show_title'])) : ?>
						<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('fancy_title_tags'); ?>>
							<?php echo wp_kses($item['tab_title'], element_pack_allow_tags('title')); ?>
						</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
					<?php endif; ?>

					<?php if ($item['tab_content'] && ('yes' == $settings['show_content'])) : ?>
						<div class="bdt-ep-fancy-tabs-text">
							<?php echo $this->parse_text_editor($item['tab_content']); ?>
						</div>
					<?php endif; ?>

					<?php if ($item['tabs_button'] && ('yes' == $settings['show_button'])) : ?>
						<div class="bdt-ep-fancy-tabs-button">
							<?php if ('' !== $item['button_link']['url']) : ?>
								<?php
								if ($item['button_link']['is_external']) {
									$this->add_render_attribute('link_key', 'target', '_blank');
								}

								if ($item['button_link']['nofollow']) {
									$this->add_render_attribute('link_key', 'rel', 'nofollow');
								}
								?>
								<a <?php echo $this->get_render_attribute_string('link_key'); ?> href="<?php echo esc_url($item['button_link']['url']); ?>">
								<?php endif; ?>
								<?php echo wp_kses_post($item['tabs_button']); ?>
								<?php if ('' !== $item['button_link']['url']) : ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	<?php
	}

	public function tab_items() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$desktop_cols = $settings['columns'];
		$tablet_cols = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : '2';
		$mobile_cols = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : '1';

		$this->add_render_attribute('tab-settings', 'data-bdt-ep-fancy-tabs-items', 'connect: #bdt-tab-content-' .  esc_attr($id) . ';');

	?>
		<div <?php echo ($this->get_render_attribute_string('tab-settings')); ?>>
			<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?> bdt-child-width-1-<?php echo esc_attr($mobile_cols); ?> bdt-child-width-1-<?php echo esc_attr($tablet_cols); ?>@s bdt-child-width-1-<?php echo esc_attr($desktop_cols); ?>@l" data-bdt-grid>

				<?php foreach ($settings['tabs'] as $index => $item) :

					$tab_count = $index + 1;
					$tab_id    = 'bdt-tab-' . $tab_count . esc_attr($id);
					$active_item = $this->activeItem($settings['fancy_tab_active_item'], count($settings['tabs']));
					if ($tab_id    == 'bdt-tab-' . $active_item . esc_attr($id)) {
						$this->add_render_attribute('tabs-item', 'class', 'bdt-ep-fancy-tabs-item active', true);
					} else {
						$this->add_render_attribute('tabs-item', 'class', 'bdt-ep-fancy-tabs-item', true);
					}

					$has_icon  = !empty($item['selected_icon']);
					$has_image = !empty($item['image']['url']);

				?>
					<div>
						<div <?php echo ($this->get_render_attribute_string('tabs-item')); ?> data-id="<?php echo esc_attr($tab_id); ?>">
							<a class="bdt-ep-fancy-tabs-icon-box" href="javascript:void(0);" data-tab-index="<?php echo esc_attr($index); ?>">

								<?php if ($has_icon or $has_image) : ?>
									<span class="bdt-ep-fancy-tabs-icon">
										<?php if ($has_icon and 'icon' == $item['icon_type']) { ?>

											<?php Icons_Manager::render_icon($item['selected_icon'], ['aria-hidden' => 'true']); ?>

										<?php } elseif ($has_image and 'image' == $item['icon_type']) {
											if ($item['image']['id']) {
												print(wp_get_attachment_image(
													$item['image']['id'],
													'medium',
													false,
													[
														'alt'   => esc_html($item['tab_title'])
													]
												));
											} else {
												printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['tab_title']));
											}
										}
										?>
									</span>
								<?php endif; ?>

							</a>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
<?php
	}
}
