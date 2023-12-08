<?php

namespace ElementPack\Modules\EddCart\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;


if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class EDD_Cart extends Module_Base {

	public function get_name() {
		return 'bdt-edd-cart';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Cart', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-cart bdt-new';
	}
	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['edd', 'easy', 'digital', 'downlaod', 'cart', 'checkout'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-edd-cart'];
		}
	}

	protected function register_controls() {
		$this->register_form_controls_layout();
		$this->register_controls_cart_header();
		$this->register_controls_cart_body();
		$this->register_controls_cart_total();
		$this->register_form_submit_button();
	}

	protected function register_controls_cart_header() {
		$this->start_controls_section(
			'section_controls_cart_header_style',
			[
				'label' => __('Cart Header', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_controls_cart_header_style');

		$this->start_controls_tab(
			'tab_controls_cart_header_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'checkout_header_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_header_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items',
			]
		);
		$this->add_responsive_control(
			'header_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_checkout_header_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'checkout_header_hover_color',
			[
				'label' => __('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_header_hover_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart-number-of-items:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_controls_cart_body() {
		$this->start_controls_section(
			'section_style_body',
			[
				'label' => __('Cart Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_body_style');

		$this->start_controls_tab(
			'tab_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'normal_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'normal_action_btn_color',
			[
				'label'     => __('Action Button Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item span.edd-action-btn-remove-icon' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'normal_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_cell_border_style',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item',
			]
		);
		$this->add_responsive_control(
			'cell_padding',
			[
				'label'      => __('Cell Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'    => 5,
					'bottom' => 5,
					'left'   => 10,
					'right'  => 10,
					'unit'   => 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'checkout_cart_items_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item span',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'row_hover_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item:hover span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'row_action_btn_hover_color',
			[
				'label'     => __('Action Button Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item:hover a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'row_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd-cart-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	protected function register_controls_cart_total() {
		$this->start_controls_section(
			'checkout_section_cart_total',
			[
				'label' => __('Cart Total', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'checkout_cart_total_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_total' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'checkout_cart_total_background',
				'label'     => __('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_total',
			]
		);
		$this->add_responsive_control(
			'checkout_cart_total_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_total'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'checkout_cart_total_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_total',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'checkout_cart_total_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_total',
			]
		);
		$this->end_controls_section();
	}

	protected function register_checkout_profile() {
	}

	protected function register_form_controls_layout() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'cart_action_button_type',
			[
				'label'      => __('Action Button Type', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'icon'   => __('Icon', 'bdthemes-element-pack'),
					'text'   => __('Text', 'bdthemes-element-pack'),
				],
				'default'    => 'icon',
				'dynamic'    => ['active' => true],
			]
		);
		$this->add_control(
			'cart_action_button_text',
			[
				'label'       => __('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default' => __('Remove', 'bdthemes-element-pack'),
				'condition' => [
					'cart_action_button_type' => 'text'
				]
			]
		);
		$this->add_control(
			'cart_action_button_icon',
			[
				'label'         => __('Select Icon', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::ICONS,
				'default'       => [
					'value'     => 'eicon-close',
					'library'   => 'solid',
				],
				'condition' => [
					'cart_action_button_type' => 'icon'
				]
			]
		);
		$this->end_controls_section();
	}

	protected function register_form_submit_button() {
		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label' => esc_html__('Form Submit Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a',
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
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a::before',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'label'    => esc_html__('Box Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-edd-cart .edd-cart .edd_checkout a:hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	public function render() {
		$settings = $this->get_settings_for_display();
		$cart_items = edd_get_cart_contents();
		$cart_quantity = edd_get_cart_quantity();
		$display = $cart_quantity > 0 ? '' : ' style="display:none;"';
?>
		<div class="bdt-edd-cart">
			<p class="edd-cart-number-of-items" <?php echo $display; ?>><?php _e('Number of items in cart', 'bdthemes-element-pack'); ?>: <span class="edd-cart-quantity"><?php echo $cart_quantity; ?></span></p>
			<div class="edd-cart">
				<?php if ($cart_items) : ?>
					<?php foreach ($cart_items as $key => $item) : ?>
						<div class="edd-cart-item">
							<div class="edd-cart-title-wrap">
								<span class="edd-cart-item-title"><?php echo get_the_title($item['id']); ?></span>
							</div>
							&nbsp;<?php echo edd_item_quantities_enabled() ? '<span class="edd-cart-item-quantity">' . $item['quantity'] . '&nbsp;@&nbsp;</span>' : ''; ?>
							<span class="edd-cart-item-price">
								<?php edd_price($item['id']); ?></span>&nbsp;
							<div class="edd-remove-wrap">
								<a href="<?php echo esc_url(edd_remove_item_url($key)); ?>" data-nonce="<?php echo wp_create_nonce('edd-remove-cart-widget-item'); ?>" data-cart-item="<?php echo esc_attr($key); ?>" data-download-id="<?php echo esc_attr($item['id']); ?>" data-action="edd_remove_from_cart" class="edd-remove-from-cart">
									<?php if (($settings['cart_action_button_type'] === 'text')) {
										echo '<span class="edd-action-btn-remove-text">' . esc_html($settings['cart_action_button_text']) . '</span>';
									} else { ?>
										<span class="edd-action-btn-remove-icon">
											<?php Icons_Manager::render_icon($settings['cart_action_button_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
										</span>
									<?php
									} ?>

								</a>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if (edd_use_taxes()) : ?>
						<div class="cart_item edd-cart-meta edd_subtotal"><?php echo __('Subtotal:', 'bdthemes-element-pack') . " <span class='subtotal'>" . edd_currency_filter(edd_format_amount(edd_get_cart_subtotal())); ?></span></div>
						<div class="cart_item edd-cart-meta edd_cart_tax"><?php _e('Estimated Tax:', 'bdthemes-element-pack'); ?> <span class="cart-tax"><?php echo edd_currency_filter(edd_format_amount(edd_get_cart_tax())); ?></span></div>
					<?php endif; ?>
					<div class="cart_item edd-cart-meta edd_total"><?php _e('Total:', 'bdthemes-element-pack'); ?> <span class="cart-total"><?php echo edd_currency_filter(edd_format_amount(edd_get_cart_total())); ?></span></div>
					<div class="cart_item edd_checkout">
						<a href="<?php echo edd_get_checkout_uri(); ?>">
							<span>
								<?php _e('Checkout', 'bdthemes-element-pack'); ?>
							</span>
						</a>
					</div>

				<?php else : ?>
					<div class="cart_item empty"><?php echo edd_empty_cart_message(); ?></div>
				<?php endif; ?>
			</div>
		</div>
<?php
	}
}
