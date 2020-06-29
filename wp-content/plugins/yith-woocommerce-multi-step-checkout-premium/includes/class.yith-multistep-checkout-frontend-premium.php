<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Multistep_Checkout_Frontend_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Andrea Grillo <andrea.grillo@yithemes.com>
 *
 */

if ( ! class_exists( 'YITH_Multistep_Checkout_Frontend_Premium' ) ) {
    /**
     * Class YITH_Multistep_Checkout_Frontend_Premium
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    class YITH_Multistep_Checkout_Frontend_Premium extends YITH_Multistep_Checkout_Frontend {

        /**
         * Construct
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         */
        public function __construct() {
            /* === Timeline Customizzation === */
            add_filter( 'yith_wcms_timeline_labels', array( $this, 'timeline_labels' ) );
            add_filter( 'yith_wcms_timeline_display', array( $this, 'timeline_display' ) );
            add_action( 'wp_head', array( $this, 'timeline_style' ) );

            /* === Order Received Customizzation === */
            add_filter( 'body_class', array( $this, 'body_class' ) );
            add_action( 'wp_head', array( $this, 'thankyou_style' ) );

            /* === Checkout Customizzation === */
            add_filter( 'wc_get_template', array( $this, 'get_template' ), 10, 5 );
            add_filter( 'the_title', array( $this, 'remove_endpoint_title' ), 30 );

            /* === Enqueue Scripts === */
            add_filter( 'yith_wcms_main_script', array( $this, 'premium_script' ) );
            add_action( 'yith_wcms_enqueue_scripts', array( $this, 'premium_enqueue_scripts' ) );

            /* WooCommerce Multiple Shipping Support */
            if( class_exists('WC_Ship_Multiple') ){
                global $wcms;
                if( $wcms ){
                    remove_action( 'woocommerce_before_checkout_form', array( $wcms->checkout, 'before_checkout_form' ) );
                    add_action( 'woocommerce_checkout_shipping', array( $wcms->checkout, 'before_checkout_form' ) );
                }
            }

	        /* YITH Multiple Shipping Addresses for WooCommerce */
	        if( class_exists('YITH_Multiple_Addresses_Shipping') ){
		        $YITH_Multiple_Addresses_Shipping_Frontend = YITH_Multiple_Addresses_Shipping::instance()->frontend;
		        if( ! empty( $YITH_Multiple_Addresses_Shipping_Frontend ) ){
			        remove_action( 'woocommerce_before_checkout_form', array( $YITH_Multiple_Addresses_Shipping_Frontend, 'manage_addresses_cb' ) );
			        remove_action( 'woocommerce_checkout_before_customer_details', array( $YITH_Multiple_Addresses_Shipping_Frontend, 'manage_addresses_content' ) );
			        add_action( 'woocommerce_checkout_shipping', array( $YITH_Multiple_Addresses_Shipping_Frontend, 'manage_addresses_cb' ) );
			        add_action( 'woocommerce_checkout_shipping', array( $YITH_Multiple_Addresses_Shipping_Frontend, 'manage_addresses_content' ) );
		        }
	        }

	        /* YITH WooCommerce Delivery Date Premium Support */
            if( class_exists( 'YITH_Delivery_Date_Shipping_Manager' ) ){
                $shipping_manager = YITH_Delivery_Date_Shipping_Manager();
                if( false !== $shipping_manager ){
	                remove_action( 'woocommerce_checkout_shipping', array( $shipping_manager, 'print_delivery_from' ), 20 );
	                remove_action( 'woocommerce_after_order_notes', array( $shipping_manager, 'print_delivery_from' ), 20 );
	                add_action( 'yith_woocommerce_checkout_order_review', array( $shipping_manager, 'print_delivery_from' ), 30 );
                }
            }

            if( 'yes' == get_option( 'yith_wcms_show_amount_on_payments', 'no' ) ){
                add_action( 'woocommerce_review_order_before_submit', array( $this, 'cart_totals_order_total_html' ) );
                add_action( 'woocommerce_review_order_before_submit', 'wc_cart_totals_order_total_html' );
            }

            /* WooCommerce Amazon Pay Gateway Support  */
            if( class_exists( 'WC_Amazon_Payments_Advanced' ) ){
            	add_action( 'woocommerce_checkout_init', array( $this, 'wc_amazon_payments_support' ), 15 );
            }

	        if ( function_exists( 'YITH_WC_Points_Rewards' ) ) {
	        	add_action( 'template_redirect', array( $this, 'yith_points_and_rewards_support' ), 40 );
	        }

            parent::__construct();

            if( 'yes' == get_option( 'yith_wcms_timeline_use_my_account_in_login_step', 'no' ) ){
	            remove_action( 'yith_wcms_checkout_login_form', 'yith_wcms_login_form', 10, 1 );
	            add_action( 'yith_wcms_checkout_login_form', 'yith_wcms_my_account_login_form' );
            }
        }

        /**
         * Change Timeline and Button Label
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         */
        public function timeline_labels( $labels ) {
            return array(
                'login'         => get_option( 'yith_wcms_timeline_options_login', $labels['login'] ),
                'skip_login'    => get_option( 'yith_wcms_timeline_options_skip_login', $labels['skip_login'] ),
                'billing'       => get_option( 'yith_wcms_timeline_options_billing', $labels['billing'] ),
                'shipping'      => get_option( 'yith_wcms_timeline_options_shipping', $labels['shipping'] ),
                'order'         => get_option( 'yith_wcms_timeline_options_order', $labels['order'] ),
                'payment'       => get_option( 'yith_wcms_timeline_options_payment', $labels['payment'] ),
                'next'          => get_option( 'yith_wcms_timeline_options_next', $labels['next'] ),
                'prev'          => get_option( 'yith_wcms_timeline_options_prev', $labels['prev'] ),
                'back_to_cart'  => get_option( 'yith_wcms_timeline_options_back_to_cart', $labels['back_to_cart'] ),
            );
        }

        /**
         * Change Timeline display
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         */
        public function timeline_display( $display ) {
            return get_option( 'yith_wcms_timeline_display', 'horizontal' );
        }

        /**
         * Add a body class(es)
         *
         * @param $classes The classes array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         * @return array
         */
        public function body_class( $classes ) {
            if ( ( is_order_received_page() || is_view_order_page() || is_page( 'my-account' ) ) && 'plugin' == get_option( 'yith_wcms_thankyou_style' ) ) {
                $classes[] = 'yith-wcms-pro-myaccount';
            }

            $is_checkout = is_checkout();

            if( $is_checkout ){

                if( 'yes' == get_option( 'yith_wcms_show_amount_on_payments', 'no' ) ){
                    $classes[] = 'yith_wcms_show_amount_on_payments';
                }

                if( $terms_page_id = wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ){
                    $classes[] = 'yith_wcms_wc_checkout_show_terms';
                }
            }

            return $classes;
        }

        /**
         * Add a body class(es)
         *
         * @param $located
         * @param $template_name
         * @param $args
         * @param $template_path
         * @param $default_path
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0
         * @return array
         */
        public function get_template( $located, $template_name, $args, $template_path, $default_path ) {
            if ( 'plugin' == get_option( 'yith_wcms_thankyou_style' ) && 'checkout/thankyou.php' == $template_name ) {
                $located = YITH_WCMS_WC_TEMPLATE_PATH . 'checkout/thankyou.php';
            }
            return $located;
        }

        /**
         * Add a body class(es)
         *
         * @param $title The page title
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0
         * @return   array
         */
        public function remove_endpoint_title( $title ) {
            return 'plugin' == get_option( 'yith_wcms_thankyou_style' ) && 'order-received' == WC()->query->get_current_endpoint() && $title == WC()->query->get_endpoint_title( 'order-received' ) ? __return_empty_string() : $title;
        }

        /**
         * Add thankyou style
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0
         * @return   array
         */
        public function thankyou_style(){
            $is_enable_customizzation  = ( is_order_received_page() || is_view_order_page() || is_page( 'my-account' ) ) && 'plugin' == get_option( 'yith_wcms_thankyou_style' );
            if( ! $is_enable_customizzation ){
                return false;
            }

            ob_start();
            yith_wcms_get_template( 'thankyou-style.php', array(), 'style' );
            echo ob_get_clean();
        }

        /**
         * Add timeline style
         *
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @since    1.0
         * @return   array
         */
        public function timeline_style(){
            $timeline_template = get_option( 'yith_wcms_timeline_template' );
            if( ! is_checkout() || 'text' == $timeline_template ){
                return false;
            }

            ob_start();
            yith_wcms_get_template( "timeline-{$timeline_template}.php", array(), 'style' );
            echo ob_get_clean();
        }


        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @return void
         */
        public function premium_enqueue_scripts() {
            /* === Style === */
            wp_register_style( 'yith-wcms-checkout-responsive', YITH_WCMS_ASSETS_URL . 'css/responsive.css', array( 'yith-wcms-checkout' ), YITH_WCMS_VERSION );

            //Registered js-cookie
            if( ! wp_script_is( 'js-cookie', 'registered' ) ){
                $js_cookie = function_exists( 'yit_load_js_file' ) ? yit_load_js_file( 'js.cookie.js' ) : str_replace( '.js', '.min.js', 'js.cookie.js' );
                wp_register_script( 'js-cookie', YITH_WCMS_ASSETS_URL . 'third-party/js/js-cookie/' . $js_cookie, array(), '2.1.3', true );
            }

            /* === Localize Script === */
            $dom = apply_filters( 'yith_wcms_frontend_dom_object', array(
                    'login'                     => '#checkout_login',
                    'billing'                   => '#customer_billing_details',
                    'shipping'                  => '#customer_shipping_details',
                    'order'                     => '#order_info',
                    'payment'                   => '#order_checkout_payment',
                    'form_actions'              => '#form_actions',
                    'coupon'                    => '#checkout_coupon',
                    'checkout_timeline'         => '#checkout_timeline',
                    'checkout_form'             => 'form.woocommerce-checkout',
                    'active_timeline'           => '.timeline.active',
                    'button_next'               => '.button.next',
                    'button_prev'               => '.button.prev',
                    'button_back_to_cart'       => '#yith-wcms-back-to-cart-button',
                    'shipping_check'            => '#ship-to-different-address-checkbox',
                    'create_account'            => '#createaccount',
                    'create_account_wrapper'    => '.create-account',
                    'account_password'          => '#account_password',
                    'wc_invalid_required'       => '.woocommerce-invalid-required-field',
                    'timeline_id_prefix'        => '#timeline-',
                    'required_fields_check'     => '.input-text, select, input:radio',
                    'select2_fields'            => array( 'billing_country', 'shipping_country', 'billing_state', 'shipping_state' ),
                    'day_of_birth'              => '#ywces_birthday',
                    'email'                     => '#billing_email_field',
                    'wc_checkout_addons'        => '#wc_checkout_add_ons',
                    'ship-to-different-address' => '#ship-to-different-address',
                    'additional_fields'         => '.woocommerce-shipping-fields',
                    'scroll_top_anchor'         => get_option( 'yith_wcms_scroll_top_anchor', '#checkout_timeline' )
                )
            );

            $validate_checkout_event = array( 'input', 'validate', 'change', 'focusout' ); ;
			$remove_shipping_step = get_option( 'yith_wcms_timeline_remove_shipping_step', 'no' );

	        $to_localize = array(
		        'dom'                             => $dom,
		        'live_fields_validation'          => get_option( 'yith_wcms_enable_ajax_validator', 'no' ),
		        'disabled_prev_button'            => get_option( 'yith_wcms_nav_disabled_prev_button', 'no' ),
		        'disabled_back_to_cart_button'    => get_option( 'yith_wcms_nav_disabled_back_to_cart_button', 'no' ),
		        'wc_shipping_multiple'            => class_exists( 'WC_Ship_Multiple' ),
		        'is_old_wc'                       => version_compare( WC()->version, '2.5', '<' ),
		        'checkout_login_reminder_enabled' => 'yes' == get_option( 'woocommerce_enable_checkout_login_reminder', 'yes' ) ? true : false,
		        'is_order_received_endpoint'      => is_wc_endpoint_url( 'order-received' ),
		        'transition_duration'             => get_option( 'yith_wcms_timeline_fade_duration', 200 ),
		        'skip_login_label'                => get_option( 'yith_wcms_timeline_options_skip_login', _x( 'Skip Login', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' ) ),
		        'next_label'                      => get_option( 'yith_wcms_timeline_options_next' ),
		        'use_cookie'                      => apply_filters( 'yith_wcms_use_cookie', true ),
		        'is_scroll_top_enabled'           => get_option( 'yith_wcms_scroll_top_enabled', 'no' ),
		        'is_coupon_email_system_enabled'  => defined( 'YWCES_PREMIUM' ),
		        'is_delivery_date_enabled'        => defined( 'YITH_DELIVERY_DATE_PREMIUM' ),
		        'is_wc_checkout_addons_enabled'   => class_exists( 'WC_Checkout_Add_Ons' ),
		        'wp_gdpr'                         => array(
			        'is_enabled'              => class_exists( 'GDPR' ),
			        'add_consent_on_checkout' => get_option( 'gdpr_add_consent_checkboxes_checkout', false ),
			        'consents'                => $consents = get_option( 'gdpr_consent_types', array() ),
			        'consents_number'         => count( $consents )
		        ),
		        'skip_shipping_method'            => apply_filters( 'yith_wcms_skip_shipping_method', false ),
		        'skip_payment_method'             => apply_filters( 'yith_wcms_skip_payment_method', false ),
		        'remove_shipping_step'            => $remove_shipping_step,
		        //Validate checkout event documented in wp-content/plugins/woocommerce/assets/js/frontend/checkout.js:35
		        'validate_checkout_event'         => apply_filters( 'yith_wcms_validate_checkout_event', $validate_checkout_event ),
		        'steps_timeline'                  => $this->get_steps_timeline()
	        );

            wp_localize_script( 'yith-wcms-step', 'yith_wcms', $to_localize );

            if( is_checkout() ){
                if( apply_filters( 'yith_wcms_use_cookie', true ) ) {
                    wp_enqueue_script( 'js-cookie' );
                }
                wp_enqueue_style( 'yith-wcms-checkout-responsive' );
            }
        }

        /**
         * Order total amount in last checkout step (payment tab)
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.3.13
         * @return void
         */
        public function cart_totals_order_total_html(){
            $text = get_option( 'yith_wcms_show_amount_on_payments_text', __( 'Order total amount', 'yith-woocommerce-multi-step-checkout' ) );
            printf( '<span class="order-total-in-payment"><strong>%s</strong> </span>', $text );
        }

        /**
         * Premium Script File
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since  1.0
         *
         * @param $js_file The premium js filename
         *
         * @return string The new filename
         */
        public function premium_script( $js_file ){
            return 'multistep-premium.js';
        }

	    /**
	     * Support for WooCommerce Amazon Payments Plugin
	     *
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @since  1.5.1
	     *
	     * @return void
	     */
	    public function wc_amazon_payments_support() {
		    global $wc_amazon_payments_advanced;
		    if( ! empty( $wc_amazon_payments_advanced ) && $wc_amazon_payments_advanced instanceof WC_Amazon_Payments_Advanced ){
			    remove_action( 'woocommerce_before_checkout_form', array( $wc_amazon_payments_advanced, 'checkout_message' ), 5 );
			    add_action( 'yith_woocommerce_checkout_payment', array( $wc_amazon_payments_advanced, 'checkout_message' ), 5 );
		    }
	    }

	    /**
	     * Support for YITH WooCommerce Points and Rewards Premium Plugin
	     *
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @since  1.6.3
	     *
	     * @return void
	     */
	    public function yith_points_and_rewards_support(){
		    if ( ! empty( YITH_WC_Points_Rewards_Frontend() ) ) {
		    	if( YITH_WC_Points_Rewards()->get_option( 'enabled_rewards_cart_message' ) == 'yes' && YITH_WC_Points_Rewards()->is_user_enabled( 'redeem' ) ){
				    remove_action( 'woocommerce_before_checkout_form', array( YITH_WC_Points_Rewards_Frontend(), 'print_rewards_message_in_cart' ) );
				    add_action( 'yith_woocommerce_checkout_coupon', array( YITH_WC_Points_Rewards_Frontend(), 'print_rewards_message_in_cart' ), 5 );
			    }

			    if( 'vertical' == get_option( 'yith_wcms_timeline_display' ) && YITH_WC_Points_Rewards()->get_option( 'enabled_checkout_message' ) == 'yes' ){
				    remove_action( 'woocommerce_before_checkout_form', array( YITH_WC_Points_Rewards_Frontend(), 'print_messages_in_cart' ) );
				    add_action( 'yith_woocommerce_show_wc_notices', array( YITH_WC_Points_Rewards_Frontend(), 'print_messages_in_cart' ), 15 );
				    add_filter( 'yith_par_messages_class', 'YITH_Multistep_Checkout_Frontend_Premium::add_vertical_timeline_html_class_for_yith_points_and_rewards' );
			    }
		    }
	    }

	    /**
	     * Add specific class for YITH WooCommerce Points and Rewards Premium Plugin
	     * if vertical timeline is enabled
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @since  1.6.3
	     *
	     * @return array classes
	     */
	    public static function add_vertical_timeline_html_class_for_yith_points_and_rewards( $classes ){
		     $classes[] = 'yith-wcms-vertical-timeline';
		     return $classes;
	    }

	    /**
	     * get steps and timeline orders
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @since  2.0.0
	     *
	     * @return array steps
	     */
	    public function get_steps_timeline(){
		    $remove_shipping_step = get_option( 'yith_wcms_timeline_remove_shipping_step', 'no' );

		    $steps_timeline = array(
			    'login' => array(
				    'prev' => false,
				    'next' => 'billing',
			    ),
			    'billing' => array(
				    'prev' => 'login',
				    'next' => 'shipping',
			    ),
			    'shipping' => array(
				    'prev' => 'billing',
				    'next' => 'order'
			    ),
			    'order' => array(
				    'prev' => 'shipping',
				    'next' => 'payment'
			    ),
			    'payment' => array(
				    'next' => false,
				    'prev' => 'order'
			    )
		    );

		    /**
		     * If Shipping step isn't available
		     * I need to change next and prev steps for
		     * billing and order
		     */
		    if ( 'yes' == $remove_shipping_step ) {
			    $steps_timeline['billing']['next'] = 'order';
			    $steps_timeline['order']['prev']   = 'billing';
		    }

		    /**
		     * If billing is the first step I need to set
		     * the prev option to false.
		     *
		     * Billing is the first step if:
		     * 1. Current user is logged in
		     * 2. Login box disabled
		     */
		    $woocommerce_enable_checkout_login_reminder = get_option( 'woocommerce_enable_checkout_login_reminder', 'no' );

		    if( is_user_logged_in() || 'no' === $woocommerce_enable_checkout_login_reminder ){
			    $steps_timeline['billing']['prev'] = false;
		    }

		    return $steps_timeline;
	    }
    }
}