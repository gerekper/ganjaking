<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Frontend' ) ) {
	/**
	 * @class   YITH_YWGC_Frontend
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

            add_action( 'init', array(
                $this,
                'frontend_init'
            ) );

			/**
			 * Enqueue frontend scripts
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ) );

			/**
			 * Enqueue frontend styles
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_style' ) );

			/**
			 * If the product being added to the cart should be used as a gift card, avoid adding it
			 * on cart and use a gift card instead.
			 */
			add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'set_product_type_before_add_to_cart' ), 10, 2 );

			/**
			 * Show the gift card product frontend template
			 */
			add_action( 'woocommerce_single_product_summary', array( $this, 'show_gift_card_product_template' ), 60 );

			/** show element on gift card product template */
			add_action( 'yith_gift_cards_template_after_gift_card_form', array( $this, 'show_gift_card_add_to_cart_button' ), 20 );

			add_action( 'yith_ywgc_show_gift_card_amount_selection', array( $this, 'show_amount_selection' ) );

			/** compatibility with product addons */
			add_filter( 'yith_wapo_product_type_list' , array( $this , 'wapo_product_type_list' ) );

		}

        /**
         * initiate the frontend
         *
         * @since 2.0.2
         */
        public function frontend_init() {

            if ( get_option( 'ywgc_gift_card_form_on_cart', 'yes' ) == 'yes' ){

                $get_option_ywgc_gift_card_form_on_cart_place = get_option ( 'ywgc_gift_card_form_on_cart_place', 'woocommerce_before_cart' );
                $ywgc_cart_hook = apply_filters( 'ywgc_gift_card_code_form_cart_hook', ( empty( $get_option_ywgc_gift_card_form_on_cart_place ) ? 'woocommerce_before_cart' : get_option( 'ywgc_gift_card_form_on_cart_place' ) ) );
                /**
                 * Show the gift card section for entering the discount code in the cart page
                 */

                add_action( $ywgc_cart_hook, array(
                    $this,
                    'show_field_for_gift_code'
                ) );

            }

            if ( get_option( 'ywgc_gift_card_form_on_checkout', 'yes' ) == 'yes' ){

                $get_option_ywgc_gift_card_form_on_checkout_place = get_option ( 'ywgc_gift_card_form_on_checkout_place', 'woocommerce_before_checkout_form' );
                $ywgc_checkout_hook = apply_filters( 'ywgc_gift_card_code_form_checkout_hook', empty( $get_option_ywgc_gift_card_form_on_checkout_place ) ? 'woocommerce_before_checkout_form' : get_option( 'ywgc_gift_card_form_on_checkout_place' ) ) ;
                /**
                 * Show the gift card section for entering the discount code in the cart page
                 */
                add_action( $ywgc_checkout_hook, array(
                    $this,
                    'show_field_for_gift_code'
                ) );

            }

        }

		public function wapo_product_type_list( $allows_type ) {
			$allows_type =  array_merge( $allows_type, array('gift-card') );
			return $allows_type;
		}

		public function show_amount_selection( $product ) {

			wc_get_template( 'single-product/add-to-cart/gift-card-amount-selection.php',
				array(
					'product' => $product,
					'amounts' => $product->get_amounts_to_be_shown(),
				),
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
		}


		/**
		 * When a product is chosen as a starting point for creating a gift card, as in "give it as a present" function on
		 * product page, the product that will really go in the cart if a gift card, not the product that is
		 * currently shown.
		 */
		public function set_product_type_before_add_to_cart( $product_type, $adding_to_cart ) {
			//  If a hidden input with name "ywgc-as-present" will be in POST vars array, so the real
			//  product to add to the cart is a gift card.
			if ( ! isset( $_POST["ywgc-as-present"] ) ) {
				return $product_type;
			}

			return YWGC_GIFT_CARD_PRODUCT_TYPE;
		}

		/**
		 * Output the add to cart button for variations.
		 */
		public function show_gift_card_add_to_cart_button() {
			global $product;
			if ( 'gift-card' == $product->get_type() ) {

				// Load the template
				wc_get_template( 'single-product/add-to-cart/gift-card-add-to-cart.php',
					'',
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Show the gift card product frontend template
		 */
		public function show_gift_card_product_template() {
			global $product;
			if ( 'gift-card' == $product->get_type() ) {

				// Load the template
				wc_get_template( 'single-product/add-to-cart/gift-card.php',
					'',
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_script() {

			if ( is_product() || is_cart() || is_checkout() || apply_filters( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {

				$frontend_deps = array(
					'jquery',
					'woocommerce',
				);

				if ( is_cart() ) {
					$frontend_deps[] = 'wc-cart';
				}
				//  register and enqueue ajax calls related script file
				wp_register_script( "ywgc-frontend-script",
					YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-frontend.js' ),
					$frontend_deps,
					YITH_YWGC_VERSION,
					true );

				wp_localize_script( 'ywgc-frontend-script',
					'ywgc_data',
					array(
						'loader'        => apply_filters( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'      => admin_url( 'admin-ajax.php' ),
						'wc_ajax_url'   => WC_AJAX::get_endpoint( "%%endpoint%%" ),
						'notice_target' => apply_filters( 'yith_ywgc_gift_card_notice_target', 'div.woocommerce' ),


					) );

				wp_enqueue_script( "ywgc-frontend-script" );
			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_style() {

			if ( is_product() || is_cart() || is_checkout() || apply_filters( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				wp_enqueue_style( 'ywgc-frontend',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-frontend.css',
					array(),
					YITH_YWGC_VERSION );
			}
		}


		/**
		 * Show gift card field
		 */
		public function show_field_for_gift_code() {

			wc_get_template( 'checkout/form-gift-cards.php',
				array(),
				'',
				YITH_YWGC_TEMPLATES_DIR );
		}
	}
}
