<?php
/**
 * Price Menu
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || die();

class Price_Menu extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title () {
		return __( 'Price Menu', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon () {
		return 'hm hm-menu-price';
	}

	public function get_keywords () {
		return [ 'price-menu', 'price', 'pricing', 'menu' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls () {
		$this->__price_menu_content_controls();
		$this->__settings_content_controls();
	}

	protected function __price_menu_content_controls () {

		$this->start_controls_section(
			'_section_price_menu',
			[
				'label' => __( 'Price Menu', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Title', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'icon' => [
						'title' => __( 'Icon', 'happy-addons-pro' ),
						'icon' => 'eicon-star',
					],
					'text' => [
						'title' => __( 'Text', 'happy-addons-pro' ),
						'icon' => 'eicon-text',
					],
				],
				'default' => 'icon',
			]
		);

		$repeater->add_control(
			'badge_icon',
			[
				'label' => __( 'Badge Icon', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'condition' => [
					'badge' => 'icon',
				]
			]
		);

		$repeater->add_control(
			'badge_icon_title',
			[
				'label' => __( 'Icon Hover Title', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'badge' => 'icon',
					'badge_icon[value]!' => '',
				]
			]
		);

		$repeater->add_control(
			'badge_text',
			[
				'label' => __( 'Badge Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'badge' => 'text',
				]
			]
		);

		$repeater->add_control(
			'desc',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'label_block' => true,
				'description' => ha_get_allowed_html_desc( 'intermediate' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Culpa enim esse excepturi nemo nesciunt officia officiis optio.', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'price',
			[
				'label' => __( 'Price', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '$199',
			]
		);

		$repeater->add_control(
			'old_price',
			[
				'label' => __( 'Old Price', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '$250',
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'happy-addons-pro' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => 'https://www.example.com',
			]
		);

		$this->add_control(
			'price_list',
			[
				'label' => __( 'Price Menu', 'happy-addons-pro' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Gold Coffee', 'happy-addons-pro' ),
						'desc' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Culpa enim esse excepturi nemo nesciunt officia officiis optio.', 'happy-addons-pro' ),
						'price' => '$199',
						'old_price' => '$250',
					],
					[
						'title' => __( 'Cold Coffee', 'happy-addons-pro' ),
						'desc' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Culpa enim esse excepturi nemo nesciunt officia officiis optio.', 'happy-addons-pro' ),
						'price' => '$150',
						'old_price' => '$200',
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls () {

		$this->start_controls_section(
			'_section_price_menu_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'label' => __( 'Image Size', 'happy-addons-pro' ),
				'default' => 'thumbnail',
			]
		);

		$this->add_control(
			'image_position',
			[
				'label' => __( 'Image Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'selectors_dictionary' => [
					'left' => '0',
					'top' => 'unset',
					'right' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-item .ha-price-menu-image' => 'order: {{VALUE}};',
				],
				'prefix_class' => 'ha-price-menu-image-align-',
			]
		);

		$this->add_control(
			'price_position',
			[
				'label' => __( 'Price Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'beside_title' => [
						'title' => __( 'Beside Title', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'beside_title',
			]
		);

		$this->add_control(
			'price_content_align',
			[
				'label' => __( 'Content Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'prefix_class' => 'ha-price-menu-content-align-',
			]
		);

		$this->add_control(
			'title_price_separator',
			[
				'label' => __( 'Title Price Separator', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'item_counter',
			[
				'label' => __( 'Item Counter', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls () {
		$this->__menu_box_style_controls();
		$this->__title_style_controls();
		$this->__price_style_controls();
		$this->__image_style_controls();
		$this->__desc_style_controls();
	}

	protected function __menu_box_style_controls () {

		$this->start_controls_section(
			'_section_price_menu_item_style',
			[
				'label' => __( 'Menu Box', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'price_menu_margin_btm',
			[
				'label' => __( 'Item Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-item:last-child' => 'margin-bottom: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'price_menu_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-item',
			]
		);

		$this->add_responsive_control(
			'price_menu_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __title_style_controls () {

		$this->start_controls_section(
			'_section_price_menu_title_style',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-title',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hvr_color',
			[
				'label' => __( 'Hover Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-title:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'price_menu_title_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-title-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'title_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-header',
			]
		);

		$this->add_control(
			'badge_heading',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'badge_space',
			[
				'label' => __( 'Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'exclude' => [
					'letter_spacing'
				],
				'default' => [
					'font_family' => [ '' ],
					'font_size' => [ '' ],
					'font_weight' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon, {{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text',
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon, {{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon, {{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-badge-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'counter_heading',
			[
				'label' => __( 'Counter', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'item_counter' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'counter_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-price-menu.ha-price-menu-counter .ha-price-menu-title::before',
				'condition' => [
					'item_counter' => 'yes',
				],
				'exclude' => [
					'letter_spacing',
					'text_transform',
				],
			]
		);

		$this->add_control(
			'counter_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu.ha-price-menu-counter .ha-price-menu-title::before' => 'color: {{VALUE}}',
				],
				'condition' => [
					'item_counter' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'counter_space',
			[
				'label' => __( 'Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu.ha-price-menu-counter .ha-price-menu-content' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'item_counter' => 'yes',
				]
			]
		);

		$this->add_control(
			'title_price_separator_heading',
			[
				'label' => __( 'Separator', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'title_price_separator' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_price_separator_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-title-separator' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'title_price_separator' => 'yes',
				]
			]
		);

		$this->add_control(
			'title_price_separator_style',
			[
				'label' => __( 'Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'solid' => __( 'Solid', 'happy-addons-pro' ),
				],
				'default' => 'dashed',
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-title-separator' => 'border-bottom-style: {{VALUE}}',
				],
				'condition' => [
					'title_price_separator' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'title_price_separator_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-title-separator' => 'border-bottom-width: {{VALUE}}px;',
				],
				'condition' => [
					'title_price_separator' => 'yes',
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __price_style_controls () {

		$this->start_controls_section(
			'_section_price_menu_price_style',
			[
				'label' => __( 'Price', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_heading',
			[
				'label' => __( 'Price', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-price',
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'old_price_heading',
			[
				'label' => __( 'Old Price', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'old_price_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-old-price',
			]
		);

		$this->add_control(
			'old_price_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-old-price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __image_style_controls () {

		$this->start_controls_section(
			'_section_price_menu_image_style',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_box_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-price-menu-image-align-top .ha-price-menu .ha-price-menu-image' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-price-menu-image-align-left .ha-price-menu .ha-price-menu-image' => 'max-width: {{SIZE}}{{UNIT}}; flex-basis: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-price-menu-image-align-right .ha-price-menu .ha-price-menu-image' => 'max-width: {{SIZE}}{{UNIT}}; flex-basis: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-price-menu-image-align-left .ha-price-menu .ha-price-menu-content' => 'max-width: calc(100% - {{SIZE}}{{UNIT}}); flex-basis: calc(100% - {{SIZE}}{{UNIT}});',
					'{{WRAPPER}}.ha-price-menu-image-align-right .ha-price-menu .ha-price-menu-content' => 'max-width: calc(100% - {{SIZE}}{{UNIT}}); flex-basis: calc(100% - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'image_box_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_space',
			[
				'label' => __( 'Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-price-menu-image-align-top .ha-price-menu .ha-price-menu-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-price-menu-image-align-left .ha-price-menu .ha-price-menu-image' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-price-menu-image-align-right .ha-price-menu .ha-price-menu-image' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-image img',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-image img',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __desc_style_controls () {

		$this->start_controls_section(
			'_section_price_menu_desc_style',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-price-menu .ha-price-menu-desc p',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-desc p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'desc_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-price-menu .ha-price-menu-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function render () {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'price-menu', 'class', 'ha-price-menu' );
		if ( 'yes' === $settings['item_counter'] ) {
			$this->add_render_attribute( 'price-menu', 'class', 'ha-price-menu-counter' );
		}
		$this->add_render_attribute( 'item', 'class', 'ha-price-menu-item' );
		?>
		<div <?php $this->print_render_attribute_string( 'price-menu' ); ?>>
			<?php foreach ( $settings['price_list'] as $key => $item ) : ?>
				<div <?php $this->print_render_attribute_string( 'item' ); ?>>
					<?php
					if ( $item['image']['url'] ):
						$image = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'image_size', $settings );
						?>
						<div class="ha-price-menu-image">
							<img src="<?php echo esc_url( $image ); ?>">
						</div>
					<?php endif; ?>
					<div class="ha-price-menu-content">
						<div class="ha-price-menu-header">
							<!-- title tag Start -->
							<<?php echo ha_escape_tags( $settings['title_tag'], 'h4' ) . ' class="ha-price-menu-title"';?>>
								<?php
								$repeater_key = 'item-link' . $key;
								if ( ! empty( $item['link']['url'] ) ) {
									$this->add_link_attributes( $repeater_key, $item['link'] );
								}
								?>
								<?php if ( ! empty( $item['link']['url'] ) ) {
									echo '<a ' . $this->get_render_attribute_string( $repeater_key ) . '>';
								} ?>
								<span class="ha-price-menu-title-text"><?php echo esc_html( $item['title'] ); ?></span>
								<?php
								if ( 'icon' === $item['badge'] && $item['badge_icon']['value'] ) {
									$icon = sprintf( '<i class="%1$s" aria-hidden="true" title="%2$s"></i>', esc_attr( $item['badge_icon']['value'] ), esc_attr( $item['badge_icon_title'] ) );
									echo sprintf( '<span class="ha-price-menu-badge-icon">%s</span>', $icon );
								} elseif ( 'text' == $item['badge'] && $item['badge_text'] ) {
									echo sprintf( '<span class="ha-price-menu-badge-text">%s</span>', esc_html( $item['badge_text'] ) );
								}
								?>

								<?php if ( ! empty( $item['link']['url'] ) ) {
									echo '</a>';
								} ?>
							</<?php echo ha_escape_tags( $settings['title_tag'], 'h4' ); ?>>
							<!-- title tag End -->
							<?php if ( 'yes' === $settings['title_price_separator'] ) : ?>
								<span class="ha-price-title-separator"></span>
							<?php endif; ?>

							<?php if ( 'beside_title' === $settings['price_position'] && $item['price'] ) : ?>
								<div class="ha-price-menu-price-wrap">
									<?php if ( $item['old_price'] ) : ?>
										<span class="ha-price-menu-old-price"><?php echo esc_html( $item['old_price'] ); ?></span>
									<?php endif; ?>
									<span class="ha-price-menu-price"><?php echo esc_html( $item['price'] ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( $item['desc'] ) : ?>
							<div class="ha-price-menu-desc">
								<p><?php echo ha_kses_intermediate( $item['desc'] ); ?></p>
							</div>
						<?php endif; ?>
						<?php if ( 'bottom' === $settings['price_position'] && $item['price'] ) : ?>
							<div class="ha-price-menu-price-wrap">
								<?php if ( $item['old_price'] ) : ?>
									<span class="ha-price-menu-old-price"><?php echo esc_html( $item['old_price'] ); ?></span>
								<?php endif; ?>
								<span class="ha-price-menu-price"><?php echo esc_html( $item['price'] ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
