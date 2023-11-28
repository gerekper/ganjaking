<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Elementor\Group_Control_Text_Shadow;
use Essential_Addons_Elementor\Controls\EAEL_Background;

use Essential_Addons_Elementor\Traits\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Thank You Widget
 */
class Woo_Thank_You extends Widget_Base {
	use Helper;

	function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$widgets    = get_post_meta( get_the_ID(), '_elementor_controls_usage', true );
		$widget_key = 'eael-woo-thank-you';

		if ( ! $widgets ) {
			$widget_key = 'woo-thank-you';
			$widgets    = get_post_meta( get_the_ID(), '_eael_widget_elements', true );
		}

		if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' ) && isset( $widgets[ $widget_key ] ) ) {

			add_filter( 'wc_get_template', function ( $template, $template_name ) {

				if ( $template_name === 'checkout/thankyou.php' ) {
					return EAEL_PLUGIN_PATH . 'index.php';
				}

				return $template;
			}, 10, 2 );
		}
	}

	/**
	 * Retrieve thank you widget name.
	 */
	public function get_name() {
		return 'eael-woo-thank-you';
	}

	/**
	 * Retrieve thank you widget title.
	 */
	public function get_title() {
		return __( 'Woo Thank You', 'essential-addons-elementor' );
	}

	/**
	 * Retrieve the list of categories the divider widget belongs to.
	 */
	public function get_categories() {
		return [ 'essential-addons-elementor', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [
			'ea checkout',
			'thankyou',
			'thank you',
			'cart',
			'woo thank you',
			'woo thankyou',
			'woocommerce',
			'ea',
			'essential addons',
			'ea woo thank you',
			'ea woo thankyou',
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-woo-thank-you';
	}

	/**
	 * Retrieve divider widget icon.
	 */
	public function get_icon() {
		return 'eaicon-thank-you';
	}

	/**
	 * Register divider widget controls.
	 */
	protected function register_controls() {
		if( !class_exists( 'woocommerce' ) ) {
			$this->start_controls_section(
				'eael_global_warning',
				[
					'label' => __('Warning!', 'essential-addons-for-elementor-lite'),
				]
			);

			$this->add_control(
				'eael_global_warning_text',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __('<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.',
						'essential-addons-for-elementor-lite'),
					'content_classes' => 'eael-warning',
				]
			);

			$this->end_controls_section();
			return;
		}

		$this->start_controls_section(
			'eael_woo_thankyou_general_section',
			[
				'label' => esc_html__( 'General', 'essential-addons-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'eael_thankyou_layout',
			[
				'label'   => esc_html__( 'Layout', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'preset-1',
				'options' => $this->get_template_list_for_dropdown( true ),
			]
		);

		$this->add_control(
			'eael_show_thankyou_message',
			[
				'label'        => esc_html__( 'Thank You Message', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_thankyou_hello_text',
			[
				'label'     => esc_html__( 'Hello', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Hello', 'essential-addons-elementor' ),
                'ai'        => [
                        'active' => false
                ],
				'condition' => [
					'eael_thankyou_layout'       => [ 'preset-2' ],
					'eael_show_thankyou_message' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_thankyou_customer_name_type',
			[
				'label'     => esc_html__( 'Name', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'first',
				'options'   => [
					'first' => esc_html__( 'First Name', 'essential-addons-elementor' ),
					'last'  => esc_html__( 'Last Name', 'essential-addons-elementor' ),
					'full'  => esc_html__( 'Full Name', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_thankyou_layout'       => [ 'preset-2' ],
					'eael_show_thankyou_message' => 'yes',
				]
			]
		);


		$this->add_control(
			'eael_show_thankyou_text',
			[
				'label'        => esc_html__( 'Thank You Text', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_thankyou_layout'       => [ 'preset-3' ],
					'eael_show_thankyou_message' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_thankyou_text',
			[
				'label'     => esc_html__( 'Thank You Text', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Thank you !', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_thankyou_layout'       => [ 'preset-3' ],
					'eael_show_thankyou_message' => 'yes',
					'eael_show_thankyou_text'    => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_thankyou_message_icon',
			[
				'label'     => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_thankyou_layout'       => [ 'preset-1', 'preset-3' ],
					'eael_show_thankyou_message' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_thankyou_message',
			[
				'label'       => esc_html__( 'Message', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__( 'Thank you. Your order has been received.', 'essential-addons-elementor' ),
				'placeholder' => esc_html__( 'Type your message here', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_show_thankyou_message' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview',
			[
				'label'        => esc_html__( 'Order Overview', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_show_order_details',
			[
				'label'        => esc_html__( 'Order Details', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_show_order_summary',
			[
				'label'        => esc_html__( 'Order Summary', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_show_order_billing',
			[
				'label'        => esc_html__( 'Billing Address', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_show_order_shipping',
			[
				'label'        => esc_html__( 'Shipping Address', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->end_controls_section();

		//Order overview content controll
		$this->start_controls_section(
			'eael_woo_thankyou_order_overview_section',
			[
				'label'     => esc_html__( 'Order Overview', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'eael_show_order_overview' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_order_overview_item_alignment',
			[
				'label' => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-overview ul li' => 'justify-content: {{VALUE}};',
				],
				'condition'    => [
					'eael_thankyou_layout!'    => 'preset-3',
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_section_title',
			[
				'label'        => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_thankyou_layout'    => 'preset-3',
					'eael_show_thankyou_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_order_overview_section_title',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Order Overview', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_section_title' => 'yes',
					'eael_thankyou_layout'                   => 'preset-3',
					'eael_show_thankyou_text'                => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_number',
			[
				'label'        => esc_html__( 'Order Number', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_order_overview_number_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Order number:', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_number' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_date',
			[
				'label'        => esc_html__( 'Date', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_order_overview_date_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Date:', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_order_overview_date_format',
			[
				'label'     => esc_html__( 'Date Format', 'essential-addons-for-elementor-lite' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'F j, Y',
				'options'   => [
					'F j, Y'    => date( 'F j, Y' ),                   // January 1, 2022
					'Y-m-d'     => date( 'Y-m-d' ),
					"d-m-Y"     => date( "d-m-y" ),
					"m-d-Y"     => date( "m-d-y" ),
					'm/d/Y'     => date( 'm/d/Y' ),                    // 01/01/2022
					'd/m/Y'     => date( 'd/m/Y' ),                    // 01/01/2022
					'Y/m/d'     => date( 'Y/m/d' ),                    // 2022/01/01
					'M j, Y'    => date( 'M j, Y' ),                   // Jan 1, 2022
					'jS F Y'    => date( 'jS F Y' ),                   // 1st January 2022
					'D, M j, Y' => date( 'D, M j, Y' ),                // Sat, Jan 1, 2022
					'l, F j, Y' => date( 'l, F j, Y' ),                // Saturday, January 1, 2022
					'j F, Y'    => date( 'j F, Y' ),                   // 1 January, 2022
					'l, j F, Y' => date( 'l, j F, Y' ),                // Saturday, 1 January, 2022
					'D, d M Y'  => date( 'D, d M Y' ),                 // Sat, 01 Jan 2022
					'l, d-M-Y'  => date( 'l, d-M-Y' ),                 // Saturday, 01-Jan-2022
				],
				'condition' => [
					'eael_show_order_overview_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_email',
			[
				'label'        => esc_html__( 'Email', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_order_overview_email_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Email:', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_email' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_total',
			[
				'label'        => esc_html__( 'Total', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_order_overview_total_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Total:', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_total' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_overview_payment_method',
			[
				'label'        => esc_html__( 'Payment Method', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before'
			]
		);

		$this->add_control(
			'eael_order_overview_payment_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Payment Method:', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_overview_payment_method' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		//Order Details content controll
		$this->start_controls_section(
			'eael_woo_thankyou_order_details_section',
			[
				'label'     => esc_html__( 'Order Details', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'eael_show_order_details' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_details_title',
			[
				'label'        => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'eael_thankyou_layout!' => 'preset-3'
				]
			]
		);

		$this->add_control(
			'eael_order_details_title',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Order Details', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_details_title' => 'yes',
					'eael_thankyou_layout!'         => 'preset-3'
				]
			]
		);

		$this->add_control(
			'eael_show_order_table_heading',
			[
				'label'        => esc_html__( 'Table Heading', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_order_table_product_label',
			[
				'label'     => esc_html__( 'Product Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Product', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_table_heading' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_order_table_total_label',
			[
				'label'     => esc_html__( 'Total Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Total', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_table_heading' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_items_image',
			[
				'label'        => esc_html__( 'Image', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_show_order_items_name',
			[
				'label'        => esc_html__( 'Name', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes'
			]
		);

		$this->add_control(
			'eael_show_order_items_qty',
			[
				'label'        => esc_html__( 'Quantity', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes'
			]
		);

		$this->add_control(
			'eael_order_table_qty_label',
			[
				'label'     => esc_html__( 'Quantity Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Quantity', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'separator' => 'after',
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_thankyou_layout'          => 'preset-2',
					'eael_show_order_items_qty'     => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_items_meta',
			[
				'label'        => esc_html__( 'Meta Data', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_order_table_meta_label',
			[
				'label'     => esc_html__( 'Meta Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Variation', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_thankyou_layout'          => 'preset-2',
					'eael_show_order_items_meta'    => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_order_items_meta_label',
			[
				'label'        => esc_html__( 'Meta Data Label', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_show_order_items_meta' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_show_order_items_price',
			[
				'label'        => esc_html__( 'Product Price', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_control(
			'eael_order_table_item_price_label',
			[
				'label'     => esc_html__( 'Price Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Price', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_thankyou_layout'          => 'preset-2',
					'eael_show_order_items_price'   => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_order_table_item_align',
			[
				'label'     => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-details' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .wc-item-meta li'                  => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-price'      => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-qty'        => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-total'   => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table thead tr th'                            => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-order-items-table thead tr th.eael-thankyou-order-totals' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->end_controls_section();

		//Order billing content controll
		$this->start_controls_section(
			'eael_woo_thankyou_billing_section',
			[
				'label'     => esc_html__( 'Billing Address', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'eael_show_order_billing' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_billing_title',
			[
				'label'        => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_order_billing_title',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Billing Address', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_billing_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_show_billing_cell_no',
			[
				'label'        => esc_html__( 'Mobile No', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_show_billing_cell_label_type',
			[
				'label'     => esc_html__( 'Label Type', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'text' => [
						'title' => esc_html__( 'Text', 'essential-addons-elementor' ),
						'icon'  => 'eicon-animation-text',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'icon'  => ' eicon-alert',
					],
				],
				'default'   => 'icon',
				'condition' => [
					'eael_show_billing_cell_no' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_show_billing_cell_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Mobile', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_billing_cell_no'         => 'yes',
					'eael_show_billing_cell_label_type' => 'text'
				]
			]
		);

		$this->add_control(
			'eael_show_billing_cell_label_icon',
			[
				'label'     => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-phone',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_show_billing_cell_no'         => 'yes',
					'eael_show_billing_cell_label_type' => 'icon'
				]
			]
		);

		$this->add_control(
			'eael_show_billing_email',
			[
				'label'        => esc_html__( 'Email', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_show_billing_email_label_type',
			[
				'label'     => esc_html__( 'Label Type', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'text' => [
						'title' => esc_html__( 'Text', 'essential-addons-elementor' ),
						'icon'  => 'eicon-animation-text',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'icon'  => 'eicon-alert',
					],
				],
				'default'   => 'icon',
				'condition' => [
					'eael_show_billing_email' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_show_billing_email_label',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Email', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_billing_email'            => 'yes',
					'eael_show_billing_email_label_type' => 'text'
				]
			]
		);

		$this->add_control(
			'eael_show_billing_email_label_icon',
			[
				'label'     => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-envelope',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_show_billing_email'            => 'yes',
					'eael_show_billing_email_label_type' => 'icon'
				]
			]
		);

		$this->end_controls_section();

		//Order Shipping content controll
		$this->start_controls_section(
			'eael_woo_thankyou_shipping_section',
			[
				'label'     => esc_html__( 'Shipping Address', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'eael_show_order_shipping' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_show_shipping_title',
			[
				'label'        => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'essential-addons-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'eael_order_shipping_title',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Shipping Address', 'essential-addons-elementor' ),
				'ai'        => [
					'active' => false
				],
				'condition' => [
					'eael_show_shipping_title' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		//Order message styling
		$this->start_controls_section(
			'eael_woo_thankyou_message_styling',
			[
				'label'     => esc_html__( 'Thank You Message', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_thankyou_message' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_icon_section',
			[
				'label'     => esc_html__( 'Icon', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_icon_size',
			[
				'label'      => esc_html__( 'Size', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message-icon .eael-thankyou-icon' => 'font-size: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-thankyou-message-icon svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_icon_area',
			[
				'label'      => esc_html__( 'Area', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message-icon' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_icon_position',
			[
				'label'        => esc_html__( 'Position', 'essential-addons-elementor' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'essential-addons-elementor' ),
				'label_on'     => esc_html__( 'Custom', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_thankyou_layout' => 'preset-1'
				]
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'eael_woo_thankyou_message_icon_position_x',
			[
				'label'      => esc_html__( 'Horizontal', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => - 1000,
						'max' => 2000,
					],
					'%'  => [
						'min' => - 100,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_woo_thankyou_message_icon_position' => 'yes',
					'eael_thankyou_layout'                    => 'preset-1'
				]
			]
		);

		$this->add_responsive_control(
			'eael_woo_thankyou_message_icon_position_y',
			[
				'label'      => esc_html__( 'Vertical', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => - 1000,
						'max' => 2000,
					],
					'%'  => [
						'min' => - 100,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_woo_thankyou_message_icon_position' => 'yes',
					'eael_thankyou_layout'                    => 'preset-1'
				]
			]
		);

		$this->end_popover();

		$this->add_control(
			'eael_woo_thankyou_message_icon_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#6345EA',
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-message-icon .eael-thankyou-icon' => 'color: {{VALUE}};fill: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'      => 'eael_woo_thankyou_message_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .eael-thankyou-message-icon',
				'condition' => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_message_icon_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-message-icon',
				'separator' => 'before',
				'condition' => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_thankyou_layout!' => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_hello_text_Heading',
			[
				'label'     => esc_html__( 'Hello Text', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_hello_text_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-hello',
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_hello_text_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-hello' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_text_Heading',
			[
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_thankyou_layout'    => 'preset-3',
					'eael_show_thankyou_text' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_text_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-message-text-area .eael-thankyou-text',
				'condition' => [
					'eael_thankyou_layout'    => 'preset-3',
					'eael_show_thankyou_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_text_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-message-text-area .eael-thankyou-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_thankyou_layout'    => 'preset-3',
					'eael_show_thankyou_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_Heading',
			[
				'label'     => esc_html__( 'Message', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_message_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-message-text',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-message-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			EAEL_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_message_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-message',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_message_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-message',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_message_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Order Overview styling
		$this->start_controls_section(
			'eael_woo_thankyou_overview_styling',
			[
				'label'     => esc_html__( 'Order Overview', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_order_overview' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_title_styling',
			[
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'eael_thankyou_layout' => 'preset-3',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_overview_title_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-overview .eael-order-overview-title',
				'condition' => [
					'eael_thankyou_layout' => 'preset-3',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-overview .eael-order-overview-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_thankyou_layout' => 'preset-3',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_section_styling',
			[
				'label'     => esc_html__( 'Container', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_overview_section_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-order-overview',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_overview_section_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-overview',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_section_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_section_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_section_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_item_styling',
			[
				'label'     => esc_html__( 'Items', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_overview_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-order-overview ul li',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_overview_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-overview ul li',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-overview ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_labels_styling',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_overview_labels_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-order-overview .woocommerce-order-overview-label',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_labels_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-overview .woocommerce-order-overview-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_overview_values_styling',
			[
				'label'     => esc_html__( 'Values', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_overview_vbalues_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-order-overview .woocommerce-order-overview-value',
				'fields'
			]
		);
		$this->add_control(
			'eael_woo_thankyou_overview_values_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-overview .woocommerce-order-overview-value' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		//Order details section styling
		$this->start_controls_section(
			'eael_woo_thankyou_details_styling',
			[
				'label'     => esc_html__( 'Order Details', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_order_details' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_section_title_styling',
			[
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'eael_show_order_details_title' => 'yes',
					'eael_show_order_details'       => 'yes',
					'eael_thankyou_layout!'         => 'preset-3',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_section_title',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-details .woocommerce-order-details__title',
				'condition' => [
					'eael_show_order_details_title' => 'yes',
					'eael_show_order_details'       => 'yes',
					'eael_thankyou_layout!'         => 'preset-3',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_section_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-container .woocommerce-order-details__title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_details_title' => 'yes',
					'eael_show_order_details'       => 'yes',
					'eael_thankyou_layout!'         => 'preset-3',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_section_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-details .woocommerce-order-details__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_details_title' => 'yes',
					'eael_show_order_details'       => 'yes',
					'eael_thankyou_layout!'         => 'preset-3',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_table_styling',
			[
				'label'     => esc_html__( 'Table', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'eael_show_order_details_title' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'eael_woo_thankyou_details_table_border',
				'selector' => '{{WRAPPER}} .eael-thankyou-order-items .eael-thankyou-order-items-table',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_table_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-items .eael-thankyou-order-items-table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_table_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-items .eael-thankyou-order-items-table' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_details_table_heading_styling',
			[
				'label'     => esc_html__( 'Table Heading', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_heading_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table thead th',
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_heading_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table thead th' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_heading_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table thead',
				'condition' => [
					'eael_show_order_table_heading' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_heading_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table thead tr::after',
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_heading_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-items-table thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_table_heading' => 'yes',
					'eael_show_order_details'       => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_background_styling',
			[
				'label'     => esc_html__( 'Item Details', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_details' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_table_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody',
				'condition' => [
					'eael_show_order_details' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_body_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_details' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_body_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody tr::after',
				'condition' => [
					'eael_thankyou_layout' => 'preset-2',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_image_styling',
			[
				'label'     => esc_html__( 'Product Image', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_items_image' => 'yes',
					'eael_show_order_details'     => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_image_width',
			[
				'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-item-details .eael-thankyou-product-image' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_items_image' => 'yes',
					'eael_show_order_details'     => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_image_height',
			[
				'label'      => esc_html__( 'Height', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-item-details .eael-thankyou-product-image' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_items_image' => 'yes',
					'eael_show_order_details'     => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'eael_woo_thankyou_details_item_image_border',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-item-details .eael-thankyou-product-image',
				'condition' => [
					'eael_show_order_items_image' => 'yes',
					'eael_show_order_details'     => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-order-item-details .eael-thankyou-product-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_order_items_image' => 'yes',
					'eael_show_order_details'     => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_name_styling',
			[
				'label'     => esc_html__( 'Product Name', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_name_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-name',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_name_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-name a' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_variation_styling',
			[
				'label'     => esc_html__( 'Product Variation', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_variation_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-meta ul li',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_variation_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-meta ul li'      => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-meta ul li span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-meta ul li p'    => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_price_styling',
			[
				'label'     => esc_html__( 'Product Price', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_price_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-price .woocommerce-Price-amount.amount',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_price_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-price'                                  => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-price .woocommerce-Price-amount.amount' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);
		$this->add_control(
			'eael_woo_thankyou_details_item_qty_styling',
			[
				'label'     => esc_html__( 'Product Quantity', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_qty_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-qty',
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_qty_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-qty' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_items_name' => 'yes',
					'eael_show_order_details'    => 'yes',
					'eael_thankyou_layout'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_details_item_meta_styling',
			[
				'label'     => esc_html__( 'Product Meta', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_details'     => 'yes',
					'eael_show_order_items_meta!' => '',
					'eael_show_order_items_qty!'  => '',
					'eael_thankyou_layout!'       => 'preset-2'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_meta_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-qty,
				               {{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-meta ul li .wc-item-meta-label,
				               {{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-meta ul li p',
				'condition' => [
					'eael_show_order_details'     => 'yes',
					'eael_show_order_items_meta!' => '',
					'eael_show_order_items_qty!'  => '',
					'eael_thankyou_layout!'       => 'preset-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_meta_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-qty'                            => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-meta ul li .wc-item-meta-label' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-meta ul li p'                   => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-product-summary .eael-thankyou-product-meta ul li'                     => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_details'     => 'yes',
					'eael_show_order_items_meta!' => '',
					'eael_show_order_items_qty!'  => '',
					'eael_thankyou_layout!'       => 'preset-2'
				]
			]
		);


		$this->add_control(
			'eael_woo_thankyou_details_item_total_price_styling',
			[
				'label'     => esc_html__( 'Product Total Price', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_order_details' => 'yes',
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_item_total_price_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-total .woocommerce-Price-amount.amount,
				               {{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-total .woocommerce-Price-amount.amount .woocommerce-Price-currencySymbol',
				'condition' => [
					'eael_show_order_details' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_item_total_price_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-total .woocommerce-Price-amount.amount'                                   => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-thankyou-order-items-table tbody .eael-thankyou-order-item-total .woocommerce-Price-amount.amount .woocommerce-Price-currencySymbol' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_order_details' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		//Billing section styling
		$this->start_controls_section(
			'eael_woo_thankyou_billing_section_styling',
			[
				'label'     => esc_html__( 'Billing Address', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_order_billing' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_billing_section_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'eael_woo_thankyou_billing_section_border',
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_title',
			[
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_billing_title' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_billing_section_title_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-billing .eael-thankyou-billing-title',
				'condition' => [
					'eael_show_billing_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-billing .eael-thankyou-billing-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_billing_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing .eael-thankyou-billing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_billing_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_details',
			[
				'label'     => esc_html__( 'Content', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_billing_section_content_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing-address,
				                {{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-phone,
				                {{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-email',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_content_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-billing-address'           => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-phone'                     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-email'                     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-phone .eael-thankyou-icon' => 'color: {{VALUE}};fill: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-email .eael-thankyou-icon' => 'color: {{VALUE}};fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_billing_section_content_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-phone .eael-thankyou-icon' => 'font-size:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-billing .eael-thankyou-email .eael-thankyou-icon' => 'font-size:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		//Billing section styling
		$this->start_controls_section(
			'eael_woo_thankyou_shipping_section_styling',
			[
				'label'     => esc_html__( 'Shipping Address', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_order_shipping' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_shipping_section_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'eael_woo_thankyou_shipping_section_border',
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_title',
			[
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_show_shipping_title' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_thankyou_shipping_section_title_typography',
				'selector'  => '{{WRAPPER}} .eael-thankyou-shipping .eael-thankyou-shipping-title',
				'condition' => [
					'eael_show_shipping_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-shipping .eael-thankyou-shipping-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'eael_show_shipping_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-shipping .eael-thankyou-shipping-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_show_shipping_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_details',
			[
				'label'     => esc_html__( 'Content', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_shipping_section_content_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-shipping-address,
				                {{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-phone,
				                {{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-email',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_shipping_section_content_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-shipping-address'          => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-phone'                     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-email'                     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-phone .eael-thankyou-icon' => 'color: {{VALUE}};fill: {{VALUE}};',
					'{{WRAPPER}} .eael-thankyou-billing-shipping .eael-thankyou-shipping .eael-thankyou-email .eael-thankyou-icon' => 'color: {{VALUE}};fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		//order summary styling
		$this->start_controls_section(
			'eael_woo_thankyou_summary_styling',
			[
				'label'     => esc_html__( 'Order Summary', 'essential-addons-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_show_order_summary' => 'yes'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_thankyou_summary_background',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .eael-thankyou-wrapper:not(.preset-3) .eael-thankyou-order-summary-table,
				{{WRAPPER}} .eael-thankyou-wrapper.preset-3 .eael-thankyou-order-summary',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_width',
			[
				'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'  => [
					'eael_thankyou_layout' => 'preset-3'
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'eael_woo_thankyou_summary_border',
				'selector' => '{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_padding',
			[
				'label'      => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_margin',
			[
				'label'      => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_label_styling',
			[
				'label'     => esc_html__( 'Label', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_summary_label_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table tr th',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_label_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table tr th' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_value_styling',
			[
				'label'     => esc_html__( 'Value', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_thankyou_summary_value_typography',
				'selector' => '{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table tr td',
			]
		);

		$this->add_control(
			'eael_woo_thankyou_summary_value_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-thankyou-wrapper .eael-thankyou-order-summary-table tr td' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render thankyou widget output on the frontend.
	 */
	protected function render() {

		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		global $wp;
		$is_edit_mode = apply_filters( 'eael_woo_thankyou_force_view', \Elementor\Plugin::$instance->editor->is_edit_mode() );
		$order        = false;
		$settings     = $this->get_settings_for_display();
		$security_key = empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] );
		$is_key_valid = $is_edit_mode;

		//static values
		$message = $payment_msg = $number_label = $date_label = $email_label = $total_label = $payment_label = $table_title = $product_label = $total_label = $subtotal_label = $shipping_label = $payment_method_label = $total_label = $billing_title = $shipping_title = '';

		//order data
		$order_id = $order_date = $email = $total = $payment_method = $products = $subtotal = $billing_name = $billing_address = $billing_phone = $shipping_name = $shipping_address = $shipping_phone = '';

		if ( $is_edit_mode ) {
			$order = wc_get_orders( [ 'numberposts' => 1 ] );
			$order = ! empty( $order ) ? $order[0] : false;
			if ( ! $order ) {
				_e( 'To view the widget, you must first place an order.', 'essential-addons-elementor' );

				return;
			}
		}

		if ( isset( $wp->query_vars['order-received'] ) ) {
			$order        = wc_get_order( $wp->query_vars['order-received'] );
			$is_key_valid = is_object( $order ) && $order->key_is_valid( $security_key );
			$is_edit_mode = false;
		}

		if ( ! $is_edit_mode && ( ! is_wc_endpoint_url( 'order-received' ) || ! $order || ! $is_key_valid ) ) {
			return;
		}

		$this->add_render_attribute( 'container', 'class', [
			'eael-thankyou-wrapper',
			esc_attr( $settings['eael_thankyou_layout'] )
		] );
		?>
        <div <?php $this->print_render_attribute_string( 'container' ); ?> >
			<?php
			$template = $this->get_template( $settings['eael_thankyou_layout'] );
			if ( file_exists( $template ) ):
				include( $template );
			else:
				_e( '<p class="eael-no-posts-found">No layout found!</p>', 'essential-addons-elementor' );
			endif; ?>
        </div>
		<?php
	}
}