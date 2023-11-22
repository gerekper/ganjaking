<?php

namespace ElementPack\Modules\EddMiniCart\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;


if (!defined('ABSPATH')) {
	exit;
}

class Edd_Mini_Cart extends Module_Base {

	public function get_name() {
		return 'bdt-edd-mini-cart';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Mini Cart', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-mini-cart bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['EDD', 'mini', 'cart', 'easy', 'digital', 'downlaod'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-edd-mini-cart'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/PWxNP2zLqDg';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_mini_cart',
			[
				'label' => esc_html__('Mini Cart', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'cart-light'    => __('Cart', 'bdthemes-element-pack') . ' ' . __('Light', 'bdthemes-element-pack'),
					'cart-medium'   => __('Cart', 'bdthemes-element-pack') . ' ' . __('Medium', 'bdthemes-element-pack'),
					'cart-solid'    => __('Cart', 'bdthemes-element-pack') . ' ' . __('Solid', 'bdthemes-element-pack'),
					'basket-light'  => __('Basket', 'bdthemes-element-pack') . ' ' . __('Light', 'bdthemes-element-pack'),
					'basket-medium' => __('Basket', 'bdthemes-element-pack') . ' ' . __('Medium', 'bdthemes-element-pack'),
					'basket-solid'  => __('Basket', 'bdthemes-element-pack') . ' ' . __('Solid', 'bdthemes-element-pack'),
					'bag-light'     => __('Bag', 'bdthemes-element-pack') . ' ' . __('Light', 'bdthemes-element-pack'),
					'bag-medium'    => __('Bag', 'bdthemes-element-pack') . ' ' . __('Medium', 'bdthemes-element-pack'),
					'bag-solid'     => __('Bag', 'bdthemes-element-pack') . ' ' . __('Solid', 'bdthemes-element-pack'),
				],
				'default' => 'cart-medium',
				'prefix_class' => 'edd-cart-icon--',
			]
		);

		$this->add_responsive_control(
			'mini_cart_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
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
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => 'left',
			]
		);

		$this->add_control(
			'mini_cart_icon_indent',
			[
				'label'   => esc_html__('Icon Size(px)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 16,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);
		$this->add_control(
			'mini_cart_icon_badge',
			[
				'label'   => esc_html__('Badge Size(px)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 16,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'mini_cart_icon_badge_font',
			[
				'label'   => esc_html__('Badge font Size(px)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-badge' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'custom_widget_cart_title',
			[
				'label'   => esc_html__('Cart Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'default' => esc_html__('Shopping Cart', 'bdthemes-element-pack'),
				'separator' => 'after',
			]
		);

		$this->add_control(
			'custom_content_before_switcher',
			[
				'label' => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_content_after_switcher',
			[
				'label' => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'offcanvas_overlay',
			[
				'label'        => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'offcanvas_animations',
			[
				'label'     => esc_html__('Animations', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => [
					'slide'  => esc_html__('Slide', 'bdthemes-element-pack'),
					'push'   => esc_html__('Push', 'bdthemes-element-pack'),
					'reveal' => esc_html__('Reveal', 'bdthemes-element-pack'),
					'none'   => esc_html__('None', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'offcanvas_flip',
			[
				'label'        => esc_html__('Flip', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'right',
			]
		);

		$this->add_control(
			'offcanvas_close_button',
			[
				'label'   => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'offcanvas_bg_close',
			[
				'label'   => esc_html__('Close on Click Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'offcanvas_esc_close',
			[
				'label'   => esc_html__('Close on Press ESC', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'offcanvas_width',
			[
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range'      => [
					'px' => [
						'min' => 240,
						'max' => 1200,
					],
					'vw' => [
						'min' => 10,
						'max' => 100,
					]
				],
				'selectors' => [
					'body:not(.bdt-offcanvas-flip) #bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'width: {{SIZE}}{{UNIT}};left: -{{SIZE}}{{UNIT}};',
					'body:not(.bdt-offcanvas-flip) #bdt-offcanvas-{{ID}}.bdt-offcanvas.bdt-open>.bdt-offcanvas-bar' => 'left: 0;',
					'.bdt-offcanvas-flip #bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'width: {{SIZE}}{{UNIT}};right: -{{SIZE}}{{UNIT}};',
					'.bdt-offcanvas-flip #bdt-offcanvas-{{ID}}.bdt-offcanvas.bdt-open>.bdt-offcanvas-bar' => 'right: 0;',
				],
				'condition' => [
					'offcanvas_animations!' => ['push', 'reveal'],
				]
			]
		);


		$this->add_responsive_control(
			'offcanvas_height',
			[
				'label'      => esc_html__('Height', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range'      => [
					'px' => [
						'min' => 600,
						'max' => 1200,
					],
					'vh' => [
						'min' => 80,
						'max' => 100,
					]
				],
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_custom_before',
			[
				'label'     => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'condition' => [
					'custom_content_before_switcher' => 'yes',
				]
			]
		);

		$this->add_control(
			'custom_content_before',
			[
				'label'   => esc_html__('Custom Content Before', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => ['active' => true],
				'default' => esc_html__('This is your custom content for before of your offcanvas.', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_content_custom_after',
			[
				'label'     => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'condition' => [
					'custom_content_after_switcher' => 'yes',
				]
			]
		);


		$this->add_control(
			'custom_content_after',
			[
				'label'   => esc_html__('Custom Content After', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => ['active' => true],
				'default' => esc_html__('This is your custom content for after of your offcanvas.', 'bdthemes-element-pack'),
			]
		);

		$this->end_controls_section();

		//Style

		$this->start_controls_section(
			'section_style_mini_cart_content',
			[
				'label' => esc_html__('Mini Cart', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'mini_cart_icon_style',
			[
				'label' 	=> __('Cart Icon', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'mini_cart_icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-icon i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_badge_style',
			[
				'label' 	=> __('Cart Badge', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'mini_cart_badge_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mini_cart_badge_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-mini-cart-wrapper .bdt-mini-cart-button-icon .bdt-cart-badge' => 'background: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas_content',
			[
				'label' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_style_offcanvas_content');

		$this->start_controls_tab(
			'tab_style_product_cart',
			[
				'label' => esc_html__('Product List', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'product_cart_main_title_color',
			[
				'label'     => esc_html__('Cart Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-widget-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'product_cart_main_title_border_color',
			[
				'label'     => esc_html__('Cart Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-widget-title' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_cart_main_title_typography',
				'selector' => '{{WRAPPER}} .bdt-offcanvas .bdt-widget-title',
			]
		);

		$this->add_control(
			'prodct_total_item_msg',
			[
				'label'     => __('Total Item/ Empty Cart', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'product_total_items_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .cart_item .edd_empty_cart, {{WRAPPER}} .bdt-offcanvas .edd-cart-number-of-items' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'product_cart_style',
			[
				'label' 	=> __('Product Cart', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_cart_title_color',
			[
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .edd-cart-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_cart_title_hover_color',
			[
				'label'     => esc_html__('Title Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .edd-cart-item-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_cart_title_typography',
				'selector' => '{{WRAPPER}} .bdt-offcanvas .bdt-offcanvas-bar .edd-cart-item-title',
			]
		);

		$this->add_control(
			'product_cart_item_border_color',
			[
				'label'     => esc_html__('Item Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .edd_shopping_cart_content .edd-cart .edd-cart-item' => 'border-bottom-color: {{VALUE}};',
				],
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'product_cart_quantity_price_style',
			[
				'label' 	=> __('Price', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_cart_quantity_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd-cart-item-price' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_cart_price_typography',
				'selector' => '{{WRAPPER}} .bdt-offcanvas .edd-cart-item-price',
			]
		);

		$this->add_control(
			'product_cart_remove_button_style',
			[
				'label' 	=> __('Product Remove Button', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pc_remove_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd-remove-from-cart:before, {{WRAPPER}} .bdt-offcanvas .edd-remove-from-cart:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pc_remove_button_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd-remove-from-cart:hover:before, {{WRAPPER}} .bdt-offcanvas .edd-remove-from-cart:hover:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_cart_subtotal_style',
			[
				'label' 	=> __('Total', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'product_cart_total_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'product_cart_total_bg_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total' => 'background: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'product_cart_total_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'product_cart_total_margin',
			[
				'label'                 => __('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'product_cart_total_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total, {{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total',
			]
		);
		$this->add_responsive_control(
			'product_cart_total_radius',
			[
				'label'                 => __('Border Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'product_cart_total_typography',
				'selector' => '{{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-checkout-btn .cart_item.edd-cart-meta.edd_total, {{WRAPPER}} .bdt-offcanvas .edd_shopping_cart_content .edd-cart .edd_total',
			]
		);

		$this->add_control(
			'product_cart_checkout_button_style',
			[
				'label' 	=> __('Checkout Button', 'bdthemes-element-pack'),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pc_checkout_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pc_checkout_button_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pc_checkout_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pc_checkout_background_hover_color',
			[
				'label'     => esc_html__('Hover Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'pc_checkout_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-offcanvas .edd_checkout a',
			]
		);

		$this->add_control(
			'pc_checkout_hover_border_color',
			[
				'label'     => esc_html__('Hover Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'pc_checkout_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pc_checkout_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pc_checkout_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-offcanvas .edd_checkout a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pc_checkout_shadow',
				'selector' => '{{WRAPPER}} .bdt-offcanvas .edd_checkout a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'pc_checkout_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-offcanvas .edd_checkout a',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_style_offcanvas_after_before',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'custom_content_before_switcher',
							'value'    => 'yes',
						],
						[
							'name'  => 'custom_content_after_switcher',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'offcanvas_content_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-custom-content-before.widget, {{WRAPPER}} .bdt-offcanvas-custom-content-after.widget' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_color',
			[
				'label'     => esc_html__('Link Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-custom-content-before.widget, {{WRAPPER}} .bdt-offcanvas-custom-content-after.widget'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-offcanvas-custom-content-before.widget *, {{WRAPPER}} .bdt-offcanvas-custom-content-after.widget *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_hover_color',
			[
				'label'     => esc_html__('Link Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-offcanvas-custom-content-before.widget:hover, {{WRAPPER}} .bdt-offcanvas-custom-content-after.widget:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'offcanvas_content_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-offcanvas-custom-content-before.widget, {{WRAPPER}} .bdt-offcanvas-custom-content-after.widget',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_style_offcanvas_content',
			[
				'label' => esc_html__('Offcanvas', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'offcanvas_content_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'offcanvas_content_shadow',
				'selector'  => '#bdt-offcanvas-{{ID}}.bdt-offcanvas > div',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'offcanvas_content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'offcanvas_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close_button',
			[
				'label'     => esc_html__('Offcanvas Close Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'offcanvas_close_button' => 'yes'
				]
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_bg',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'close_button_shadow',
				'selector'  => '#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'close_button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'close_button_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'close_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_bg',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'close_button_border_border!' => '',
				],
				'selectors' => [
					'#bdt-offcanvas-{{ID}}.bdt-offcanvas .bdt-offcanvas-close:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render_button() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-offcanvas-' . $this->get_id();

		$this->add_render_attribute('button', 'class', ['bdt-offcanvas-button', 'bdt-mini-cart-button']);
		if (!empty($settings['size'])) {
			$this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
		}
		$this->add_render_attribute('button', 'data-bdt-toggle', 'target: #' . esc_attr($id));
		$this->add_render_attribute('button', 'href', '#');
?>

		<div class="bdt-mini-cart-wrapper">
			<a <?php echo $this->get_render_attribute_string('button'); ?>>
				<span class="cart bdt-mini-cart-button-icon" tabindex="0">
					<span class="bdt-cart-icon">
						<i class="ep-icon-cart" area-hidden="true"></i>
					</span>
					<span class="count edd-cart-quantity bdt-cart-badge"><?php echo edd_get_cart_quantity(); ?></span>
				</span>
			</a>
		</div>


	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-offcanvas-' . $this->get_id();


		$this->add_render_attribute('offcanvas', 'class', 'bdt-offcanvas');
		$this->add_render_attribute('offcanvas', 'id', $id);
		$this->add_render_attribute(
			[
				'offcanvas' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'id'      =>  $id,
						]))
					]
				]
			]
		);

		$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'mode: ' . $settings['offcanvas_animations'] . ';');

		if ($settings['offcanvas_overlay']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'overlay: true;');
		}

		if ('right' == $settings['offcanvas_flip']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'flip: true;');
		}

		if ('yes' !== $settings['offcanvas_bg_close']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'bg-close: false;');
		}

		if ('yes' !== $settings['offcanvas_esc_close']) {
			$this->add_render_attribute('offcanvas', 'data-bdt-offcanvas', 'esc-close: false;');
		}

	?>

		<?php $this->render_button(); ?>

		<div <?php echo $this->get_render_attribute_string('offcanvas'); ?>>
			<div class="bdt-offcanvas-bar bdt-text-left">
				<?php if ($settings['offcanvas_close_button'] === 'yes') : ?>
					<button class="bdt-offcanvas-close" type="button"><i class="ep-icon-close" aria-hidden="true"></i></button>
				<?php endif; ?>
				<div class="bdt-widget-title">
					<?php echo wp_kses_post($settings['custom_widget_cart_title']); ?>
				</div>

				<?php if ($settings['custom_content_before_switcher'] === 'yes' and !empty($settings['custom_content_before'])) : ?>
					<div class="bdt-offcanvas-custom-content-before widget">
						<?php echo wp_kses_post($settings['custom_content_before']); ?>
					</div>
				<?php endif; ?>
				<div class="product-holder widget_edd_cart_widget">
					<?php $this->edd_cart_items_content(); ?>
				</div>
				<?php if ($settings['custom_content_after_switcher'] === 'yes' and !empty($settings['custom_content_after'])) : ?>
					<div class="bdt-offcanvas-custom-content-after widget">
						<?php echo wp_kses_post($settings['custom_content_after']); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

	<?php
	}

	public function edd_cart_items_content() {
		$cart_items    = edd_get_cart_contents();
		$cart_quantity = edd_get_cart_quantity();
		$display       = $cart_quantity > 0 ? '' : ' style="display:none;"';
	?>
		<div class="edd_shopping_cart_content">
			<p class="edd-cart-number-of-items" <?php echo $display; ?>><?php _e('Number of items in cart', 'easy-digital-downloads'); ?>: <span class="edd-cart-quantity"><?php echo esc_html($cart_quantity); ?></span></p>
			<ul class="edd-cart">
				<?php if ($cart_items) : ?>
					<?php foreach ($cart_items as $key => $item) : ?>
						<?php echo edd_get_cart_item_template($key, $item, false); ?>
					<?php endforeach; ?>
			</ul>
			<ul class="edd-checkout-btn">
				<?php edd_get_template_part('widget', 'cart-checkout'); ?>
			</ul>
		<?php else : ?>
			<?php edd_get_template_part('widget', 'cart-empty'); ?>
		<?php endif; ?>
		</div>
<?php
	}
}
