<?php

namespace ElementPack\Modules\WcCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use ElementPack\Utils;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

use ElementPack\Traits\Global_Widget_Controls;
use WP_Query;

use ElementPack\Traits\Global_Swiper_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Carousel extends Module_Base {

	use Group_Control_Query;
	use Global_Widget_Controls;
	use Global_Swiper_Controls;
	private $_query = null;

	public function get_name() {
		return 'bdt-wc-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__('WC - Carousel', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-wc-carousel';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['carousel', 'woocommerce', 'wc', 'product'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-wc-carousel', 'ep-font'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-woocommerce', 'ep-scripts'];
		} else {
			return ['ep-woocommerce'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/5lxli5E9pc4';
	}

	public function get_query() {
		return $this->_query;
	}

	public function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'   => __('Text Align', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
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
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'image',
				'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude' => ['custom'],
				'default' => 'medium',
			]
		);

		$this->add_control(
			'show_badge',
			[
				'label'   => esc_html__('Show Badge', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
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
			'show_rating',
			[
				'label'   => esc_html__('Rating', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'   => esc_html__('Price', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_cart',
			[
				'label'   => esc_html__('Add to Cart', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_cart_always',
			[
				'label'   => esc_html__('Show Cart always', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_cart' => 'yes',
				]
			]
		);

		$this->add_control(
			'show_quick_view',
			[
				'label'   => esc_html__('Quick View', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'match_height',
			[
				'label' => __('Item Match Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack') . BDTEP_NC,
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->register_query_builder_controls();
		$this->register_wc_query_additional('8');
		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		// Style
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item, {{WRAPPER}} .bdt-wc-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'desc_padding',
			[
				'label'      => esc_html__('Description Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_hover_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-item:hover',
			]
		);

		$this->add_responsive_control(
			'item_shadow_padding',
			[
				'label'       => __('Match Padding', 'bdthemes-element-pack'),
				'description' => __('You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default' => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_quick_view',
			[
				'label'     => esc_html__('Quick View', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quick_view' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_quick_view_style');

		$this->start_controls_tab(
			'tab_quick_view_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quick_view_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'quick_view_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'quick_view_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quick_view_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'quick_view_typography',
				'selector'  => '{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a i',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_quick_view_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a:hover i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'quick_view_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-quick-view a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-badge' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-carousel .bdt-badge',
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-badge',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-carousel-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label'     => esc_html__('Price', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'old_price_heading',
			[
				'label' => esc_html__('Old Price', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'old_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'old_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price del' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'old_price_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price del',
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'label'     => esc_html__('Sale Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price, {{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price .price, {{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price ins .amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sale_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price, {{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price ins' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price, {{WRAPPER}} .bdt-wc-carousel .bdt-products-skin-price ins',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .star-rating:before' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .star-rating span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .star-rating span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Add to Cart Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cart' => 'yes',
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
			'button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-add-to-cart a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-add-to-cart a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'button_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_control(
		// 	'overlay_animation',
		// 	[
		// 		'label'     => esc_html__( 'Overlay Animation', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::SELECT,
		// 		'default'   => 'fade',
		// 		'options'   => element_pack_transition_options(),
		// 		'separator' => 'before',
		// 	]
		// );

		// $this->add_control(
		// 	'overlay_background',
		// 	[
		// 		'label'  => esc_html__( 'Overlay Color', 'bdthemes-element-pack' ),
		// 		'type'   => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-wc-carousel .bdt-overlay-default' => 'background: {{VALUE}};',
		// 		],
		// 		'separator' => 'after',
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a',
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
			'button_hover_background',
			[
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-add-to-cart a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-item-skin-hidie .bdt-products-skin-add-to-cart a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-carousel .bdt-wc-add-to-cart a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls('swiper-carousel');

		$this->end_controls_section();
	}

	public function render_image() {
		$settings = $this->get_settings_for_display();
?>
		<div class="bdt-wc-carousel-image bdt-background-cover">
			<a href="<?php the_permalink(); ?>" title="<?php echo get_the_title(); ?>">
				<img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']); ?>" alt="<?php echo get_the_title(); ?>">
			</a>

			<?php
			if ($settings['show_cart']) {
				$show_cart_always = ($settings['show_cart_always']) == 'yes' ? 'active' : '';

				echo '<div class="bdt-wc-add-to-cart ' . $show_cart_always . '">';
				woocommerce_template_loop_add_to_cart();
				echo '</div>';
			}
			?>
		</div>
		<?php
	}

	public function render_quick_view($product_id) {
		$settings = $this->get_settings_for_display();
		if ($settings['show_quick_view']) : ?>
			<div class="bdt-quick-view">
				<?php wp_nonce_field('ajax-ep-wc-product-nonce', 'bdt-wc-product-modal-sc'); ?>
				<input type="hidden" class="bdt_modal_spinner_message" value="<?php echo __('Please wait...', 'bdthemes-element-pack'); ?>" />
				<a href="javascript:void(0)" data-id="<?php echo absint($product_id); ?>" data-bdt-tooltip="title: <?php echo __('Quick View', 'bdthemes-element-pack'); ?>; pos: left;">
					<i class="ep-icon-eye"></i>
				</a>
			</div>
		<?php endif;
	}

	public function render_header($skin = 'default') {
		$settings = $this->get_settings_for_display();

		//Global Function
		$this->render_swiper_header_attribute('wc-carousel');

		$this->add_render_attribute('wc-carousel-wrapper', 'class', 'swiper-wrapper');

		$this->add_render_attribute('carousel', 'class', 'bdt-wc-carousel');
		$this->add_render_attribute('carousel', 'class', 'bdt-wc-carousel-skin-' . $skin);

		if ('yes' == $settings['match_height']) {
			$this->add_render_attribute('carousel', 'data-bdt-height-match', 'target: > div > div > div > .bdt-wc-carousel-item-inner');
		}

		?>
		<div <?php echo $this->get_render_attribute_string('carousel'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper'); ?>>
				<div <?php echo $this->get_render_attribute_string('wc-carousel-wrapper'); ?>>
					<?php
				}

				public function render_query($posts_per_page) {
					$settings = $this->get_settings_for_display();
					$default = $this->getGroupControlQueryArgs();
					$args    = [];

					if ($posts_per_page) {
						$args['posts_per_page'] = $posts_per_page;
						// $args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
					}

					$args['post_type'] = 'product';

					$product_visibility_term_ids = wc_get_product_visibility_term_ids();
					if ('yes' == $settings['product_hide_free']) {
						$args['meta_query'][] = [
							'key'     => '_price',
							'value'   => 0,
							'compare' => '>',
							'type'    => 'DECIMAL',
						];
					}

					if ('yes' == $settings['product_hide_out_stock']) {
						$args['tax_query'][] = [
							[
								'taxonomy' => 'product_visibility',
								'field'    => 'term_taxonomy_id',
								'terms'    => $product_visibility_term_ids['outofstock'],
								'operator' => 'NOT IN',
							],
						];
					}

					switch ($settings['product_show_product_type']) {
						case 'featured':
							$args['tax_query'][] = [
								'taxonomy' => 'product_visibility',
								'field'    => 'term_taxonomy_id',
								'terms'    => $product_visibility_term_ids['featured'],
							];
							break;
						case 'onsale':
							$product_ids_on_sale    = wc_get_product_ids_on_sale();
							$product_ids_on_sale[]  = 0;
							$args['post__in'] = $product_ids_on_sale;
							break;
					}
					switch ($settings['posts_orderby']) {
						case 'price':
							$args['meta_key'] = '_price'; // WPCS: slow query ok.
							$args['orderby']  = 'meta_value_num';
							break;
						case 'sales':
							$args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
							$args['orderby']  = 'meta_value_num';
							break;
						default:
							$args['orderby'] = $settings['posts_orderby'];
					}
					$args              = array_merge($default, $args);
					$wp_query          = new WP_Query($args);

					return $wp_query;
				}

				public function render_loop_item() {
					$settings = $this->get_settings_for_display();

					$text_align = $settings['text_align'] ?: 'left';

					// TODO need to delete after v6.5
					if (isset($settings['posts']) and $settings['posts_per_page'] == 6) {
						$limit = $settings['posts'];
					} else {
						$limit = $settings['posts_per_page'];
					}

					$wp_query = $this->render_query($limit);

					if ($wp_query->have_posts()) {

						$this->add_render_attribute('wc-carousel-item', 'class', ['bdt-wc-carousel-item', 'swiper-slide', 'bdt-transition-toggle']);

						$this->add_render_attribute('bdt-wc-carousel-title', 'class', 'bdt-wc-carousel-title');

						while ($wp_query->have_posts()) : $wp_query->the_post();
							global $post, $product;

					?>
							<div <?php echo $this->get_render_attribute_string('wc-carousel-item'); ?>>
								<div class="bdt-wc-carousel-item-inner">
									<?php if ($settings['show_badge'] and !$product->is_in_stock()) : ?>
										<div class="bdt-badge bdt-position-top-left bdt-position-small">
											<?php echo apply_filters('woocommerce_product_is_in_stock', '<span class="bdt-onsale">' . esc_html__('Out of Stock!', 'woocommerce') . '</span>', $post, $product); ?>
										</div>
									<?php elseif ($settings['show_badge'] and $product->is_on_sale()) : ?>
										<div class="bdt-badge bdt-position-top-left bdt-position-small">
											<?php echo apply_filters('woocommerce_sale_flash', '<span class="bdt-onsale">' . esc_html__('Sale!', 'woocommerce') . '</span>', $post, $product); ?>
										</div>
									<?php endif; ?>

									<?php $this->render_image(); ?>

									<?php $this->render_quick_view($product->get_id()) ?>

									<div class="bdt-wc-carousel-desc bdt-padding bdt-position-relative bdt-text-<?php echo esc_attr($text_align); ?>">
										<div class="bdt-wc-carousel-desc-inner">
											<?php if ($settings['show_title']) : ?>
												<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-wc-carousel-title'); ?>>
													<?php the_title(); ?>
												</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
											<?php endif; ?>

											<?php if ($settings['show_price'] or $settings['show_rating']) : ?>
												<div class="bdt-wc-carousel-price-wrapper bdt-flex-middle bdt-flex-<?php echo esc_attr($text_align); ?> bdt-grid bdt-grid-small" data-bdt-grid>
													<?php if ($settings['show_price']) : ?>
														<div class="bdt-products-skin-price"><?php woocommerce_template_single_price(); ?></div>
													<?php endif; ?>

													<?php if ($settings['show_rating']) : ?>
														<div class="bdt-wc-rating bdt-flex-right">
															<?php woocommerce_template_loop_rating(); ?>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										</div>

									</div>
								</div>
							</div>
			<?php
						endwhile;

						wp_reset_postdata();
					} else {
						echo '<div class="bdt-alert-warning" data-bdt-alert>Ops! There is no product<div>';
					}
				}

				public function render() {
					$this->render_header();
					$this->render_loop_item();
					$this->render_footer();
				}
			}
