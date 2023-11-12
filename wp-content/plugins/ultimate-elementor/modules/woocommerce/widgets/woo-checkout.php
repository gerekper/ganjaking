<?php
/**
 * UAEL WooCommerce Checkout.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Modules\Woocommerce\Templates\Woo_Checkout_Template;


if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Checkout.
 */
class Woo_Checkout extends Common_Widget {
	use Woo_Checkout_Template;

	/**
	 * Retrieve Widget name.
	 *
	 * @return string Widget name.
	 * @since 1.31.0
	 * @access public
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Woo_Checkout' );
	}

	/**
	 * Retrieve Widget title.
	 *
	 * @return string Widget title.
	 * @since 1.31.0
	 * @access public
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Checkout' );
	}

	/**
	 * Retrieve Widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.31.0
	 * @access public
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Checkout' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @return string Widget keywords.
	 * @since 1.31.0
	 * @access public
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Checkout' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @return array scripts.
	 * @since 1.31.0
	 * @access public
	 */
	public function get_script_depends() {
		return array( 'uael-woocommerce' );
	}

	/**
	 * Register Woo_Checkout controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_content_general_controls();
		$this->register_content_labels_controls();
		$this->register_content_login_controls();
		$this->register_content_coupon_controls();

		$this->register_style_section_controls();
		$this->register_style_headings_controls();
		$this->register_style_inputs_controls();
		$this->register_styles_multistep_tabs_controls();
		$this->register_style_order_controls();
		$this->register_style_payment_controls();
		$this->register_style_buttons_controls();
		$this->register_error_style_controls();

		$this->register_helpful_information();
	}

	/**
	 * Register Woo_Checkout general controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_content_general_controls() {

		$admin_link = admin_url();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => array(
					'1' => __( 'One Column', 'uael' ),
					'2' => __( 'Two Columns', 'uael' ),
					'3' => __( 'Multistep', 'uael' ),
				),
			)
		);

		$this->add_control(
			'layout_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: %s admin link */
				'raw'             => sprintf( __( 'Note: On responsive devices it will be stacked.', 'uael' ) ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => array(
					'layout!' => '3',
				),
			)
		);

		$this->add_control(
			'enable_back_to_cart_btn',
			array(
				'label'        => __( 'Cart Link', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_shop_link',
			array(
				'label'        => esc_html__( 'Shop Link', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'additional_info_box',
			array(
				'label'        => __( 'Additional Information Box', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout labels controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_content_labels_controls() {
		$this->start_controls_section(
			'section_labels',
			array(
				'label' => __( 'Checkout Labels', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'labels_billing_section',
			array(
				'label'   => __( 'Billing Text', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Billing Details', 'uael' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'labels_shipping_section',
			array(
				'label'   => __( 'Shipping Text', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Ship to a different address?', 'uael' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'labels_order_section',
			array(
				'label'   => __( 'Order Text', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Order Review', 'uael' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'labels_payment_section',
			array(
				'label'   => __( 'Payment Text', 'uael' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Payment Method', 'uael' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'labels_back_to_cart',
			array(
				'label'     => __( 'Cart Button Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Back To Cart', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'enable_back_to_cart_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'shop_link_text',
			array(
				'label'     => __( 'Shop Link Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => __( 'Continue Shopping?', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'enable_shop_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'labels_previous_btn',
			array(
				'label'     => __( 'Previous Button Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Previous', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'labels_next_btn',
			array(
				'label'     => __( 'Next Button Text', 'uael' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'uael' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout login controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_content_login_controls() {
		$this->start_controls_section(
			'section_login',
			array(
				'label' => __( 'Login Section', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'login_title',
			array(
				'label'       => __( 'Title', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Returning customer?', 'uael' ),
				'placeholder' => __( 'Type your title here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'login_toggle_text',
			array(
				'label'       => __( 'Link Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here to login', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'login_form_text',
			array(
				'label'       => __( 'Form Text', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'row'         => 5,
				'default'     => __( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout coupon controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_content_coupon_controls() {
		$this->start_controls_section(
			'section_coupon',
			array(
				'label' => __( 'Coupon Section', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'coupon_switcher',
			array(
				'label'     => __( 'Enable', 'uael' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'uael' ),
				'label_on'  => __( 'Show', 'uael' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'coupon_title',
			array(
				'label'       => __( 'Title', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Have a coupon?', 'uael' ),
				'placeholder' => __( 'Type your title here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'coupon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'coupon_toggle_text',
			array(
				'label'       => __( 'Link Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here to enter your code', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'coupon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'coupon_form_text',
			array(
				'label'       => __( 'Form Text', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'row'         => 5,
				'default'     => __( 'If you have a coupon code, please apply it below.', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'coupon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'coupon_field_placeholder',
			array(
				'label'       => __( 'Field Placeholder', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Coupon code', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'coupon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'coupon_button_text',
			array(
				'label'       => __( 'Button Text', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Apply Coupon', 'uael' ),
				'placeholder' => __( 'Type your text here', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'coupon_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles section controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_section_controls() {

		$this->start_controls_section(
			'section_styles',
			array(
				'label' => __( 'Sections', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-billing-form h3,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-shipping-form h3,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .woocommerce-info,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login p:not(.form-row),
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .woocommerce-info,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon p:not(.form-row),
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment label,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row label,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox),
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout #customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox),
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment label,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment label a.about_paypal,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-terms-and-conditions-wrapper,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'section_background',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-billing-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-shipping-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment, {{WRAPPER}} .uael-woo-checkout #customer_details .col-2 .woocommerce-additional-fields:only-child, {{WRAPPER}} .uael_multistep_container .woocommerce-additional-fields' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sections_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login p:not(.form-row), {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon p:not(.form-row), {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row label, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row label, {{WRAPPER}} .uael-woo-checkout .customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox), {{WRAPPER}} .uael-woo-checkout #customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox), {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details select,
					{{WRAPPER}} .uael-woo-checkout .customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout #customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout #customer_details select,
					{{WRAPPER}} .uael-woo-checkout #customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection--single, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review ul.uael-order-review-table .product-name, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review ul.uael-order-review-table .product-total, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment label, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment #payment .payment_box, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .form-row.place-order, {{WRAPPER}} .uael-woo-checkout #uael-tabs, {{WRAPPER}} .uael-woo-checkout .uael-buttons',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_border',
				'label'    => __( 'Border', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-billing-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-shipping-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment, {{WRAPPER}} .uael-woo-checkout #customer_details .col-2 .woocommerce-additional-fields:only-child, {{WRAPPER}} .uael_multistep_container .woocommerce-additional-fields',
			)
		);

		$this->add_responsive_control(
			'section_border_radius',
			array(
				'label'              => __( 'Rounded Corners', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'default'            => array(
					'top'    => '2',
					'bottom' => '2',
					'left'   => '2',
					'right'  => '2',
					'unit'   => 'px',
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-billing-form,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-shipping-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment, {{WRAPPER}} .uael-woo-checkout #customer_details .col-2 .woocommerce-additional-fields:only-child, {{WRAPPER}} .uael_multistep_container .woocommerce-additional-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'default'            => array(
					'top'      => '0',
					'right'    => '24',
					'bottom'   => '24',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login-msg, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-billing-form,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-shipping-form, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment, {{WRAPPER}} .uael-woo-checkout #customer_details .col-2 .woocommerce-additional-fields:only-child, {{WRAPPER}} .uael_multistep_container .woocommerce-additional-fields, {{WRAPPER}} .uael_multistep_container .uael-buttons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'toggle_heading',
			array(
				'label'     => __( 'Login and Coupon Toggle', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'toggle_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .woocommerce-info, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .woocommerce-info' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F4F4F4',
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .woocommerce-info, .uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .woocommerce-info' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Woo_Checkout styles headings controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_headings_controls() {

		$this->start_controls_section(
			'section_section_headings_styles',
			array(
				'label' => __( 'Headings', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_headings_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout .customer_details h3, .uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout #customer_details h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'section_headings_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout .customer_details h3, .uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout #customer_details h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_headings_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout .customer_details h3, .uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .woocommerce-checkout #customer_details h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3',
			)
		);

		$this->add_control(
			'section_headings_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'    => '0',
					'bottom' => '18',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .customer_details h3,
					{{WRAPPER}} .uael-woo-checkout #customer_details h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'section_headings_separator',
			array(
				'label'     => __( 'Separator', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'section_headings_show_separator',
			array(
				'label'        => __( 'Hide Separator', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-woo-checkout__show-separator-',
			)
		);

		$this->add_control(
			'section_headings_separator_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E7E7E7',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .customer_details h3,
					{{WRAPPER}} .uael-woo-checkout #customer_details h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3' => 'border-bottom-color: {{VALUE}};',
				),
				'condition' => array(
					'section_headings_show_separator!' => 'yes',
				),
			)
		);

		$this->add_control(
			'section_headings_separator_width',
			array(
				'label'      => __( 'Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .customer_details h3,
					{{WRAPPER}} .uael-woo-checkout #customer_details h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'section_headings_show_separator!' => 'yes',
				),
			)
		);

		$this->add_control(
			'section_headings_separator_spacing',
			array(
				'label'      => __( 'Top & Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '15',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce-checkout .customer_details h3,
					{{WRAPPER}} .uael-woo-checkout .woocommerce-checkout #customer_details h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .uael-checkout-section-payment-title h3,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-checkout-section-order-title h3' => 'padding-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'section_headings_show_separator!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles input fields controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_inputs_controls() {

		$this->start_controls_section(
			'input_fields_styles',
			array(
				'label' => __( 'Input Fields', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'labels_heading',
			array(
				'label' => __( 'Labels', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'labels_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row label,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row label,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox),
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout #customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'labels_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row label, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login p:not(.form-row), {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon p:not(.form-row), {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row label, {{WRAPPER}} .uael-woo-checkout .customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox), {{WRAPPER}} .uael-woo-checkout #customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox)',
			)
		);

		$this->add_responsive_control(
			'labels_spacing',
			array(
				'label'              => __( 'Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row label, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row label, {{WRAPPER}} .uael-woo-checkout .customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox), {{WRAPPER}} .uael-woo-checkout #customer_details label:not(.woocommerce-form__label.woocommerce-form__label-for-checkbox.checkbox)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'fields_heading',
			array(
				'label'     => __( 'Fields', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'inputs_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details select,
					{{WRAPPER}} .uael-woo-checkout .customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout #customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout #customer_details select,
					{{WRAPPER}} .uael-woo-checkout #customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection--single,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection__rendered' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inputs_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details select,
					{{WRAPPER}} .uael-woo-checkout .customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout #customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout #customer_details select,
					{{WRAPPER}} .uael-woo-checkout #customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection--single' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'inputs_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details select,
					{{WRAPPER}} .uael-woo-checkout .customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout #customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout #customer_details select,
					{{WRAPPER}} .uael-woo-checkout #customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection--single',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'inputs_border',
				'label'          => __( 'Border', 'uael' ),
				'placeholder'    => '1px',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'color'  => array(
						'default' => '#E7E7E7',
					),
				),
				'separator'      => 'before',
				'selector'       => '{{WRAPPER}} .uael-woo-checkout form .input-text, {{WRAPPER}} .uael-woo-checkout form select, {{WRAPPER}} .uael-woo-checkout form .woocommerce-input-wrapper .select2-selection--single',
			)
		);

		$this->add_control(
			'active_border_color',
			array(
				'label'     => __( 'Border Active Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout form .input-text:focus, {{WRAPPER}} .uael-woo-checkout form select:focus, {{WRAPPER}} .uael-woo-checkout form .woocommerce-input-wrapper .select2-selection--single:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'inputs_border_radius',
			array(
				'label'              => __( 'Border Radius', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout form .input-text, {{WRAPPER}} .uael-woo-checkout form select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'inputs_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login .form-row input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout .customer_details select,
					{{WRAPPER}} .uael-woo-checkout .customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout #customer_details input.input-text,
					{{WRAPPER}} .uael-woo-checkout #customer_details select,
					{{WRAPPER}} .uael-woo-checkout #customer_details textarea,
					{{WRAPPER}} .uael-woo-checkout .select2-container .select2-selection--single' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inputs_bottom_spacing',
			array(
				'label'              => __( 'Bottom Spacing', 'uael' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .customer_details .form-row, {{WRAPPER}} .uael-woo-checkout #customer_details .form-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'inputs_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .uael-woo-checkout .woocommerce form .input-text, {{WRAPPER}} .uael-woo-checkout .woocommerce form  select',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles buttons controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_buttons_controls() {

		$this->start_controls_section(
			'button_styles',
			array(
				'label' => __( 'Buttons', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_alignment',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'full',
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
					'full'   => array(
						'title' => __( 'Full Width', 'uael' ),
						'icon'  => 'fa fa-align-justify',
					),
				),

				'prefix_class' => 'uael-login-apply-order-button-',
			)
		);

		$this->add_control(
			'multistep_button',
			array(
				'label'     => __( 'Next/Previous Button', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'multistep_step_buttons_text_align',
			array(
				'label'        => __( 'Alignment', 'uael' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'      => 'right',
				'toggle'       => true,
				'prefix_class' => 'uael-multistep-step-buttons-',
				'condition'    => array(
					'layout' => '3',
				),
			)
		);

		$this->start_controls_tabs(
			'button_styles_tabs'
		);

		$this->start_controls_tab(
			'button_styles_tab_normal',
			array(
				'label' => __( 'Normal', 'uael' ),
			)
		);

		$this->add_control(
			'button_text_color_normal',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"],
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"],
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-woo-checkout a.showlogin, {{WRAPPER}} .uael-woo-checkout a.showcoupon, {{WRAPPER}} .uael-woo-checkout a.about_paypal, {{WRAPPER}} .uael-woo-checkout .woocommerce-terms-and-conditions-wrapper a.woocommerce-privacy-policy-link,
					{{WRAPPER}} .uael-woo-checkout .woocommerce-info:before' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_styles_tab_hover',
			array(
				'label' => __( 'Hover', 'uael' ),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"]:hover,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"]:hover,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order:hover,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev:hover,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"]:hover,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"]:hover,
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order:hover,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev:hover,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-woo-checkout a.showlogin:hover,
					{{WRAPPER}} .uael-woo-checkout a.showcoupon:hover,
					{{WRAPPER}} .uael-woo-checkout a.about_paypal:hover,
					{{WRAPPER}} .uael-woo-checkout .woocommerce-terms-and-conditions-wrapper a.woocommerce-privacy-policy-link:hover,
					{{WRAPPER}} .uael-woo-checkout .woocommerce-info:hover:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'separator' => 'before',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'label'     => __( 'Typography', 'uael' ),
				'selector'  => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'label'    => __( 'Border', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"], {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next',
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label'              => __( 'Border Radius', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce-checkout #place_order,
					{{WRAPPER}} .uael-woo-checkout .checkout_coupon.woocommerce-form-coupon .form-row-last button, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'default'            => array(
					'top'      => '12',
					'right'    => '12',
					'bottom'   => '12',
					'left'     => '12',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-login button[name="login"],
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-coupon button[name="apply_coupon"],
					{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment button#place_order,
					{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-prev, {{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-buttons .button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Woo_Checkout styles multistep tabs controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_styles_multistep_tabs_controls() {
		$this->start_controls_section(
			'section_multistep_tabs_styles',
			array(
				'label'     => __( 'Tabs', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'multistep_style',
			array(
				'label'     => __( 'Progress Bar Style', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'Default', 'uael' ),
					'icons'   => __( 'Tabs with Icon', 'uael' ),
					'dot'     => __( 'Dot Indicator', 'uael' ),
					'counter' => __( 'Steps Counter', 'uael' ),
				),
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'login_step_icon',
			array(
				'label'     => __( 'Login Step Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-user-lock',
					'library' => 'solid',
				),
				'condition' => array(
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'billing_step_icon',
			array(
				'label'     => __( 'Billing Step Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-file-invoice',
					'library' => 'solid',
				),
				'condition' => array(
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'shipping_step_icon',
			array(
				'label'     => __( 'Shipping Step Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-location-arrow',
					'library' => 'solid',
				),
				'condition' => array(
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'payment_step_icon',
			array(
				'label'     => __( 'Payment Step Icon', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-rupee-sign',
					'library' => 'solid',
				),
				'condition' => array(
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'tab_alignment',
			array(
				'label'     => __( 'Tab Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'align-center',
				'options'   => array(
					'align-left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'align-center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'align-right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'default', 'icons' ),
				),
			)
		);

		$this->start_controls_tabs(
			'section_multistep_tab_tabs'
		);

		$this->start_controls_tab(
			'section_multistep_tab_tab_active',
			array(
				'label' => __( 'Active', 'uael' ),
			)
		);

		$this->add_control(
			'section_multistep_tabs_color_active',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab.uael-tab-after a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'section_multistep_icon_color_active',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-icon li.uael-tab.uael-tab-after a > span' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'section_multistep_counter_color_active',
			array(
				'label'     => __( 'Counter Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-dot li.uael-tab.uael-tab-after > *:before,
					{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-counter li.uael-tab.uael-tab-after > *:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => 'counter',
				),
			)
		);

		$this->add_control(
			'section_multistep_dot_color_active',
			array(
				'label'     => __( 'Indicator Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-dot li.uael-tab.uael-tab-after > *:before,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-counter li.uael-tab.uael-tab-after > *:before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'dot', 'counter' ),
				),
			)
		);

		$this->add_control(
			'section_multistep_tabs_bg_color_active',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab.uael-tab-after a.active' => 'background-color: {{VALUE}};',
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab.uael-tab-after a.active:after' => 'border-left-color: {{VALUE}};',
					'.rtl {{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab.uael-tab-after a.active:after' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'default', 'icons' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'section_multistep_tab_tab_inactive',
			array(
				'label' => __( 'Inactive', 'uael' ),
			)
		);

		$this->add_control(
			'section_multistep_tabs_color_inactive',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => '3',
				),
			)
		);

		$this->add_control(
			'section_multistep_icon_color_inactive',
			array(
				'label'     => __( 'Icon Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab a > span' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => 'icons',
				),
			)
		);

		$this->add_control(
			'section_multistep_counter_color_inactive',
			array(
				'label'     => __( 'Counter Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-dot li.uael-tab > *:before,
					{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-counter li.uael-tab > *:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => 'counter',
				),
			)
		);

		$this->add_control(
			'section_multistep_dot_color_inactive',
			array(
				'label'     => __( 'Indicator Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d5d5d5',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-dot li:not(.uael-tab-after) > *:before,
					{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-counter li:not(.uael-tab-after) > *:before' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'dot', 'counter' ),
				),
			)
		);

		$this->add_control(
			'section_multistep_tabs_bg_color_inactive',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab a:not(.active)' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab a:not(.active):after' => 'border-left-color: {{VALUE}};',
					'.rtl {{WRAPPER}} .uael-woo-checkout ul.uael-tabs li.uael-tab a:not(.active):after' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'default', 'icons' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'section_multistep_line_color',
			array(
				'label'     => __( 'Line Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d5d5d5',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-dot li:not(:last-child)::after,
					{{WRAPPER}} .uael-woo-checkout ul.uael-tabs.uael-step-counter li:not(:last-child)::after' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'layout'          => '3',
					'multistep_style' => array( 'dot', 'counter' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'section_tabs_padding',
			array(
				'label'              => __( 'Padding', 'uael' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .uael-woo-checkout .uael_multistep_container .uael-tabs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'layout'          => '3',
					'multistep_style' => array( 'default', 'icons' ),
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles order controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_order_controls() {
		$this->start_controls_section(
			'section_order_review_styles',
			array(
				'label' => __( 'Order Review', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'order_review_text_color',
			array(
				'label'     => __( 'Text Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer,
					.uael-woocommerce-checkout {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'order_review_product_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review ul.uael-order-review-table .product-name, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review ul.uael-order-review-table .product-total, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer',
			)
		);

		$this->add_control(
			'order_review_product',
			array(
				'label'     => __( 'Product', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'order_review_product_img_style',
			array(
				'label'      => __( 'Rounded Corners', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .product-thumbnail img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'order_review_cart_link',
			array(
				'label'      => __( 'Cart/Shop Link', 'uael' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'enable_back_to_cart_btn',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'enable_shop_link',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'order_review_cart_link_color_normal',
			array(
				'label'      => __( 'Color', 'uael' ),
				'type'       => Controls_Manager::COLOR,
				'global'     => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .back-to-shop .back-to-shop-link, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .uae-shop-link .uae-back-to-shop-link' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'enable_back_to_cart_btn',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'enable_shop_link',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'order_review_cart_link_color_hover',
			array(
				'label'      => __( 'Hover Color', 'uael' ),
				'type'       => Controls_Manager::COLOR,
				'global'     => array(
					'default' => Global_Colors::COLOR_ACCENT,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .back-to-shop .back-to-shop-link:hover,{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .uae-shop-link .uae-back-to-shop-link:hover' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'enable_back_to_cart_btn',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'enable_shop_link',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'order_review_separator',
			array(
				'label'     => __( 'Separator', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'order_review_hide_separator',
			array(
				'label'        => __( 'Hide Separator', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-woo-checkout__order-product-separator-',
			)
		);

		$this->add_control(
			'order_review_separator_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#D4D4D4',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .footer-content .order-total' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array(
					'order_review_hide_separator!' => 'yes',
				),
			)
		);

		$this->add_control(
			'order_review_separator_width',
			array(
				'label'      => __( 'Thickness', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .footer-content .order-total' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'order_review_hide_separator!' => 'yes',
				),
			)
		);

		$this->add_control(
			'order_review_separator_spacing',
			array(
				'label'      => __( 'Top & Bottom Spacing', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => '12',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer, {{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-order-review .uael-order-review-table-footer .footer-content .order-total' => 'padding-top: {{SIZE}}{{UNIT}}; margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'order_review_hide_separator!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles payment controls.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_style_payment_controls() {
		$this->start_controls_section(
			'section_payment_styles',
			array(
				'label' => __( 'Payment Method', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_payment_method_label_heading',
			array(
				'label' => __( 'Labels', 'uael' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'payment_method_label_text_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .wc_payment_methods label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'payment_method_label_typography',
				'label'    => __( 'Typography', 'uael' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment .wc_payment_methods label',
			)
		);

		$this->add_control(
			'section_payment_method_message_heading',
			array(
				'label'     => __( 'Description', 'uael' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'payment_method_description_text_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment #payment .payment_box' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'payment_method_description_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment #payment .payment_box' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment #payment .payment_box:before' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'payment_method_description_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'label'    => __( 'Typography', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-woo-checkout .uael-woo-checkout-payment #payment .payment_box',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo_Checkout styles for error messages and error fields.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_error_style_controls() {
		$this->start_controls_section(
			'section_error_styles',
			array(
				'label' => __( 'Field Validation & Error Messages', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fields_error_text',
			array(
				'label'       => __( 'Field Validation', 'uael' ),
				'type'        => Controls_Manager::HEADING,
				'label_block' => true,
			)
		);

		$this->add_control(
			'label_error_color',
			array(
				'label'     => __( 'Label Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row.woocommerce-invalid label' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'field_error_border_color',
			array(
				'label'     => __( 'Field Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .select2-container--default.field-required .select2-selection--single,
						{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row input.input-text.field-required,
						{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row textarea.input-text.field-required,
						{{WRAPPER}} .uael-woo-checkout .woocommerce #order_review .input-text.field-required
						{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row.woocommerce-invalid .select2-container,
						{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row.woocommerce-invalid input.input-text,
						{{WRAPPER}} .uael-woo-checkout .woocommerce form .form-row.woocommerce-invalid select' => 'border-color: {{VALUE}};',

				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'fields_error_section',
			array(
				'label'       => __( 'Error Messages', 'uael' ),
				'type'        => Controls_Manager::HEADING,
				'label_block' => true,
			)
		);

		$this->add_control(
			'text_error_color',
			array(
				'label'     => __( 'Error Message Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-NoticeGroup .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-notices-wrapper .woocommerce-error' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'error_bg_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-NoticeGroup .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-notices-wrapper .woocommerce-error' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'error_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-NoticeGroup .woocommerce-error,
						{{WRAPPER}} .uael-woo-checkout .woocommerce .woocommerce-notices-wrapper .woocommerce-error' => 'border-color: {{VALUE}};',

				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		$help_link_1 = UAEL_DOMAIN . 'docs/woo-checkout-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		$help_link_2 = UAEL_DOMAIN . 'docs/faqs-woo-checkout-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s General FAQs » %2$s', 'uael' ), '<a href=' . $help_link_2 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Refresh button.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Render Woo_Checkout output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.31.0
	 * @access protected
	 */
	protected function render() {
		if ( ! class_exists( 'woocommerce' ) || null === WC()->cart ) {
			return;
		}

		$settings = $this->get_settings();

		$page_id             = get_the_id();
		$is_editor           = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$wc_checkout_page_id = intval( get_option( 'woocommerce_checkout_page_id' ) );

		$this->add_render_attribute(
			'container',
			'class',
			array(
				'uael-woo-checkout',
				'uael-woo-checkout-layout-' . $settings['layout'],
			)
		);
		$this->add_render_attribute(
			'container',
			array(
				'data-page-id' => $page_id,
			)
		);
		$this->uael_set_woo_checkout_settings( $settings );
		$this->uael_woo_checkout_add_actions();
		global $wp;

		if ( $is_editor && $page_id !== $wc_checkout_page_id ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning">
				<span class="elementor-alert-title"><?php esc_html_e( 'Woo - Checkout - ID ', 'uael' ); ?><?php echo esc_attr( $page_id ); ?></span>
				<span class="elementor-alert-description"><?php esc_html_e( 'Before editing checkout widget, please set this page as your Checkout page in WooCommerce settings.', 'uael' ); ?><br>
					<?php esc_attr_e( 'Navigate to WooCommerce -> Settings -> Advanced Tab -> Page setup -> Checkout page.', 'uael' ); ?>
				</span>
			</div>
			<?php
			return;
		} elseif ( ! $is_editor && $page_id !== $wc_checkout_page_id ) {
			return;
		}

		if ( $is_editor && ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ) {
			?>
			<div class="uael-builder-msg elementor-alert elementor-alert-warning uael-builder-no-cart">
				<span class="elementor-alert-description"><?php esc_html_e( 'There are no products in the cart. To display Checkout form at frontend add some product in the cart', 'uael' ); ?>
				</span>
			</div>
			<?php
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
			<div class="woocommerce">
				<style>
					.woocommerce .blockUI.blockOverlay:before {
						background-image: url('<?php echo WC_ABSPATH . 'assets/images/icons/loader.svg'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>') center center !important;
					}
				</style>
				<?php

				// Backwards compatibility with old pay and thanks link arguments.
				if ( isset( $_GET['order'] ) && isset( $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wc_deprecated_argument( __CLASS__ . '->' . __FUNCTION__, '2.1', '"order" is no longer used to pass an order ID. Use the order-pay or order-received endpoint instead.' );

					$order_id = absint( $_GET['order'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$order    = wc_get_order( $order_id );

					if ( $order && $order->has_status( 'pending' ) ) {
						$wp->query_vars['order-pay'] = absint( $_GET['order'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					} else {
						$wp->query_vars['order-received'] = absint( $_GET['order'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					}
				}

				if ( ! empty( $wp->query_vars['order-pay'] ) ) {

					self::uael_pay_order( $wp->query_vars['order-pay'] );

				} elseif ( isset( $wp->query_vars['order-received'] ) ) {

					self::uael_received_order( $wp->query_vars['order-received'] );

				} else {
					self::uael_checkout();
				}

				?>
			</div>
		</div>
		<?php
	}
}
