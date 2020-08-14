<?php
/**
 * Frontend Premium class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC_Frontend_Premium extends YITH_WOCC_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_Frontend_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_credict_card = '';

		/**
		 * User meta name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_options_user_meta = 'yith-wocc-user-options';

		/**
		 * Action for load one-click checkout form
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_action_add_one_click = 'yith_wocc_load';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_Frontend_Premium
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			// enqueue style and scripts premium
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_premium' ), 15 );

			add_filter( 'yith_wocc_customer_can', array( $this, 'customer_can_premium' ), 10, 1 );

			// ajax activation
			add_action( 'wp_ajax_' . $this->_action_add_one_click, array( $this, 'load_in_ajax' ) );
			add_action( 'wp_ajax_nopriv_' . $this->_action_add_one_click, array( $this, 'load_in_ajax' ) );

			// add button in loop
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_loop' ), 15 );

			// add attr to template
			add_action( 'yith_wocc_after_one_click_button', array( $this, 'add_shipping_address_select' ), 10, 1 );

			// filter one click url
            add_filter( 'yith_wocc_one_click_url', array( $this, 'guest_redirect_url' ), 10, 1 );
			add_filter( 'yith_wocc_one_click_url_args', array( $this, 'premium_url_args' ), 10, 1 );

			add_filter( 'yith_wocc_filter_shipping_address', array( $this, 'chosen_shipping_address' ), 10, 1 );
			add_filter( 'yith_wocc_redirect_after_create_order', array( $this, 'filter_redirect_url' ), 10, 2 );

			// load modal template
			add_action( 'wp_footer', array( $this, 'modal_address' ) );

			add_action( 'yith_wooc_handler_before_redirect', array( $this, 'stripe_payment' ), 10, 1 );

			// shortcode for print button
			add_shortcode( 'yith_wocc_button', array( $this, 'shortcode_button' ) );
		}

		/**
		 * Enqueue premium scripts and style
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts_premium() {

			wp_localize_script( 'yith-wocc-script', 'yith_wocc', array(
				'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'action_load'           => $this->_action_add_one_click,
				'nonce_load'            => wp_create_nonce( $this->_action_add_one_click ),
				'select_placeholder'    => apply_filters( 'yith_wocc_select_placeholder_text', __( 'Select an address...', 'yith-woocommerce-one-click-checkout' ) )
			));

			if( yith_wocc_enabled_shipping() ){

				// select2
				$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
				wp_enqueue_script( 'select2' );
				wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );

				// woocommerce scripts for edit address
				wp_enqueue_script( 'wc-country-select' );
				wp_enqueue_script( 'wc-address-i18n' );

				// scroll plugin
				wp_enqueue_style( 'nanoscroller-plugin', YITH_WOCC_ASSETS_URL . '/css/perfect-scrollbar.css' );
				wp_enqueue_script( 'nanoscroller-plugin', YITH_WOCC_ASSETS_URL . '/js/perfect-scrollbar.min.js', array('jquery'), $this->version, true );
			}
		}

		/**
		 * Check if user can use one-click checkout after first order
		 *
		 * @since 1.0.0
		 * @param $value
		 * @return bool
		 * @author Francesco Licandro
		 */
		public function customer_can_premium( $value ) {

		    // check for guest
		    if( $this->guest_can() ) {
		        return true;
            }
			// Check if option is enabled
			if( get_option( 'yith-wocc-after-first-order' ) == 'yes' ) {
				return '1' === get_user_meta( $this->_user_id, 'paying_customer', true );
			}
			// check if user have disabled one-click checkout from my-account
			$user_meta = get_user_meta( $this->_user_id, $this->_options_user_meta, true );
			if( ! isset( $user_meta['activate'] ) || $user_meta['activate'] === '0' ) {
				return false;
			}

			return $value;

		}

        /**
         * Check if guest can use one click checkout features
         *
         * @since 1.3.6
         * @author Francesco Licandro
         * @return boolean
         */
        protected function guest_can(){
            return ! $this->_user_id && get_option( 'yith-wocc-activate-for-guest', 'no' ) == 'yes';
        }

		/**
		 * Add one click button
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_button() {
            /**
             * @type $product WC_Product
             */
			global $product;

			if( ! $this->product_can() || ! $this->customer_can() ) {
				return;
			}

			$meta_user = get_user_meta( $this->_user_id, 'yith_wocc_skip_activation_link', true );
			$action = ( get_option( 'yith-wocc-activate-with-link' ) == 'yes' && ! $meta_user ) ? 'print_link' : 'print_button';

			if( $product->is_type( 'variable' ) ) {
				add_action( 'woocommerce_after_single_variation', array( $this, $action ) );
			}
			else {
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, $action ) );
			}
		}

		/**
		 * Check if passed product or global can have one-click button
		 *
		 * @since 1.1.1
		 * @author Francesco Licandro
		 * @param object $product
		 * @return boolean
		 */
		public function product_can( $product = null ){
			if( is_null( $product ) ) {
				global $product;
			}

			$can = ! yith_wocc_product_is_excluded( $product );
			// if not excluded continue checking
			$can && $can = ! empty( $product ) && ( $product->is_purchasable() && $product->is_in_stock() && ! $product->is_type( 'external' ) );



			return apply_filters( 'yith_wocc_product_can', $can, $product );
		}

		/**
		 * Add one click button in loop
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_button_loop() {
            /**
             * @type $product WC_Product
             */
			global $product;

			if( get_option( 'yith-wocc-activate-in-loop' ) != 'yes'
				|| ! $product->is_type( 'simple' ) || ! $this->product_can() || ! $this->customer_can() ) {
				return;
			}

			$meta_user = get_user_meta( $this->_user_id, 'yith_wocc_skip_activation_link' );

			if( get_option( 'yith-wocc-activate-with-link' ) == 'yes' && ! $meta_user  ) {
				$this->print_link( true );
			}
			else {

				$this->print_button( array( 'is_loop' => true ) );
			}
		}

		/**
		 * Print link to activate one-click checkout
		 *
		 * @access public
		 * @since 1.0.0
		 * @param boolean $is_loop
		 * @author Francesco Licandro
		 */
		public function print_link( $is_loop = false ){
            /**
             * @type $product WC_Product
             */
			global $product;

			$text = get_option( 'yith-wocc-link-label', '' );

			$html = '<div class="clear"></div><div class="yith-wocc-wrapper">';
			$html .= '<a href="#" class="yith-wocc-activate" data-product_id="' . $product->get_id() . '" data-is_loop="' . $is_loop .'">' . $text . '</a>';
			$html .= '</div>';

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Load one-click form in ajax
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function load_in_ajax() {

			if( ( ! isset( $_REQUEST['action'] ) && $_REQUEST['action'] != $this->_action_add_one_click )
				|| ( ! isset( $_REQUEST['_nonce' ] ) && ! wp_verify_nonce( $_REQUEST['_nonce'], $this->_action_add_one_click ) )
				|| ! isset( $_REQUEST['product_id'] )
			) {
				die();
			}

			add_user_meta( $this->_user_id, 'yith_wocc_skip_activation_link', 1 );

			ob_start();

			global $product;

			$product = wc_get_product( intval( $_REQUEST['product_id'] ) );

			if( isset( $_REQUEST['is_loop'] ) && $_REQUEST['is_loop'] ){
				$args = array( 'is_loop' => true );
			}
			else {
				$args = array();
			}

			$this->print_button( $args );

			$res = ob_get_clean();

			echo $res; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			die();
		}

		/**
		 * Get billing and shipping info address
		 *
		 * @access public
		 * @since 1.0.0
		 * @return array
		 * @author Francesco Licandro
		 */
		public function get_formatted_address() {

			// standard types
			$types = array( 'billing', 'shipping' );
			$address = array();

			foreach( $types as $type ) {

				$fields = array(
					'first_name' => get_user_meta( $this->_user_id, $type . '_first_name', true ),
					'last_name'  => get_user_meta( $this->_user_id, $type . '_last_name', true ),
					'company'    => get_user_meta( $this->_user_id, $type . '_company', true ),
					'address_1'  => get_user_meta( $this->_user_id, $type . '_address_1', true ),
					'address_2'  => get_user_meta( $this->_user_id, $type . '_address_2', true ),
					'city'       => get_user_meta( $this->_user_id, $type . '_city', true ),
					'state'      => get_user_meta( $this->_user_id, $type . '_state', true ),
					'postcode'   => get_user_meta( $this->_user_id, $type . '_postcode', true ),
					'country'    => get_user_meta( $this->_user_id, $type . '_country', true )
				);

				// remove empty
				$fields = array_filter( $fields );

				if( ! empty( $fields ) ) {
					$formatted = WC()->countries->get_formatted_address( $fields );
					$address[$type] = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $formatted ) );
				}
			}

			// add custom also
			$custom = $this->get_formatted_custom_address();
			if( ! empty( $custom ) )
				$address = array_merge( $address, $custom );


			return apply_filters( 'yith_wocc_get_formatted_address', $address );
		}

		/**
		 * Get custom address info
		 *
		 * @access public
		 * @since 1.0.0
		 * @param bool $inline
		 * @return array
		 * @author Francesco Licandro
		 */
		public function get_formatted_custom_address( $inline = true ) {

			// custom types
			$custom_address = yith_wocc_get_custom_address( $this->_user_id );
			$address = array();

			if( ! $custom_address ){
				return $address;
			}

			foreach( $custom_address as $key => $value ) {

				// remove empty
				$fields = array_filter( $value );

				if( ! empty( $fields ) ) {
					$formatted = WC()->countries->get_formatted_address( $fields );
					$address[ $key ] = $inline ? esc_html( preg_replace( '#<br\s*/?>#i', ', ', $formatted ) ) : $formatted;
				}
			}

			return $address;
		}

		/**
		 * Add shipping address select to one-click form
		 *
		 * @since 1.0.0
		 * @param object $product \WC Product
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function add_shipping_address_select( $product = null ) {
			if( is_null( $product ) || ! $this->_user_id || ! $product || ! $product->needs_shipping() || ! yith_wocc_enabled_shipping() ) {
				return;
			}

			echo $this->shipping_address_select_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get shipping address select html
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $selected
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function shipping_address_select_html( $selected = null ) {

			$address = $this->get_formatted_address();
			$html    = '';

			// set default from option
			is_null( $selected ) && $selected = get_option( 'yith-wocc-default-shipping-addr', '' );

			if ( ! empty( $address ) ) {
				ob_start();
				?>
				<div class="yith-wocc-select-address-container">
					<label for="_yith_wocc_select_address">
						<?php esc_html_e( 'Ship to', 'yith-woocommerce-one-click-checkout' ); ?>
						<select class="yith-wocc-select-address" name="_yith_wocc_select_address">
							<option value=""></option>
							<?php foreach ( $address as $key => $value ) : ?>
								<option value="<?php echo esc_html( $key ) ?>" <?php selected( $selected, $key ) ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
							<?php if( ! wp_is_mobile() ) : ?>
								<option value="add-new"><?php echo esc_html__( 'Add new shipping address', 'yith-woocommerce-one-click-checkout' ) ?></option>
							<?php endif; ?>
						</select>
					</label>
				</div>
				<?php

				$html = ob_get_clean();
			}

			return apply_filters( 'yith_wocc_address_select_html', $html, $address );
		}

		/**
		 * Add premium args to one-click url
		 *
		 * @since 1.0.0
		 * @param mixed $args
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function premium_url_args( $args ) {

			if( isset( $_REQUEST['_yith_wocc_select_address'] ) && $_REQUEST['_yith_wocc_select_address'] !== '' ) {
				$args['_ywocc_address'] = $_REQUEST['_yith_wocc_select_address'];
			}

			return $args;
		}

		/**
		 * Filter shipping address based on user select
		 *
		 * @since 1.0.0
		 * @param mixed $default
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function chosen_shipping_address( $default ) {

			if( ! isset( $_GET['_ywocc_address'] ) ){
				return $default;
			}

			$key = $_GET['_ywocc_address'];

			switch( $key ) {
				case 'billing':
					$address = $this->get_user_billing_address( $this->_user_id );
					break;
				case 'shipping':
					$address = $this->get_user_shipping_address( $this->_user_id );
					break;
				default:

					// custom types
					$custom_types = yith_wocc_get_custom_address( $this->_user_id );

					// check if custom type exist
					if( $custom_types && isset( $custom_types[$key] ) ) {
						$address = array_filter( $custom_types[$key] );
					}
					// else
					else {
						$address = $default;
					}
					break;
			}

			return $address;
		}

		/**
		 * Add modal window for add custom address directly from select
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function modal_address() {

			if( ! yith_wocc_enabled_shipping() ){
				return;
			}

			// get form html
			$form = YITH_WOCC_User_Account()->get_address_form_html();

			wc_get_template( 'yith-wocc-modal-address.php', array( 'content' => $form ), '', YITH_WOCC_DIR . 'templates/' );
		}

		/**
		 * Redirect to thank you page
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $url
		 * @param $order
		 * @return string
		 * @author Francesco Licandro
		 */
		public function filter_redirect_url( $url, $order ) {
            /**
             * @type $order WC_Order
             */
			if( ! $order )
				return $url;

			$page = get_option( 'yith-wocc-redirection-url' );

			// if order doesn't need payment exclude pay url
			if( ! $order->has_status( 'pending' ) && $page == 'pay' ) {
				$page = false;
			}

			switch( $page ) {
				case 'pay' :
					$url = $order->get_checkout_payment_url();
					break;
				case 'thankyou':
					$url = $order->get_checkout_order_received_url();
					break;
				case 'custom':
					$id = get_option( 'yith-wocc-custom-link', '' );
					$url = $id ? get_permalink( $id ) : $url;
					break;
				default :
					break;
			}

			return $url;
		}

		/**
		 * Stripe payment compatibility
		 *
		 * @access public
		 * @since 1.0.0
		 * @param object|bool $order
		 * @author Francesco Licandro
		 */
		public function stripe_payment( $order ) {

			// return if creating order failed or stripe is not installed
			if( ! $order || ! yith_wocc_is_stripe_enabled( $this->_user_id, $this->_options_user_meta ) ){
				return;
			}

			// get main class
			$object_stripe = YITH_WCStripe()->get_gateway();
			// get stripe customer
			$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $this->_user_id );

			// return if customer not exists or have not set a default card
			if( ! $customer || ! isset( $customer['default_source'] ) ) {
				return;
			}

			$this->_credict_card = $customer['default_source'];

			// set payment method for order
			$order->set_payment_method( $object_stripe );

			// filter card
			add_filter( 'yith_stripe_selected_card', array( $this, 'filter_credit_card' ), 10, 1 );

			$result = $object_stripe->process_payment( $order->id );

			if( isset( $result['result'] ) && $result['result'] == 'success' ) {

				if( 'hosted' != $object_stripe->mode ) {
					// clear all prev wc notices
					wc_clear_notices();
					// then add new
					$message = apply_filters('yith_wocc_success_msg_order_payed', __('Thank you, your order is now complete and it has already been charged correctly.', 'yith-woocommerce-one-click-checkout'));
					wc_add_notice($message, 'success');
				}

				wp_safe_redirect( $result['redirect'] );
				exit;
			}
		}

		/**
		 * Filter credit card number and pass default value
		 *
		 * @param $std
		 * @return mixed
		 */
		public function filter_credit_card( $std ) {
			return $this->_credict_card;
		}
		
		/**
		 * Shortcode for print one click button
		 * 
		 * @since 1.0.5
		 * @author Francesco Licandro
		 * @param array $atts
		 * @return string
		 */
		public function shortcode_button( $atts ){
			extract( shortcode_atts( array(
				'product'   => '',
				'label'     => get_option( 'yith-wocc-button-label', '' ),
			), $atts ) );

			if( ! empty( $product ) ){
                // get product
                $product = wc_get_product( $product );
            } else {
			    global $product; // user the global one
            }

			if( empty( $product ) ) {
			    return '';
            }

			ob_start();
			$this->print_button( array(
				'label'     => $label,
				'product'   => $product,
				'is_loop'   => true, // force to print link
				'divider'   => false
			));

			return ob_get_clean();
		}

		/**
         * Filter redirect url after one click action only for guest customer
         *
         * @since 1.3.6
         * @author Francesco Licandro
         * @param string $url
         * @return string
         */
		public function guest_redirect_url( $url ) {
		    if( $this->guest_can() ) {
		        return wc_get_checkout_url();
            }

            return $url;
        }
	}
}

/**
 * Unique access to instance of YITH_WOCC_Frontend_Premium class
 *
 * @return \YITH_WOCC_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WOCC_Frontend_Premium(){
	return YITH_WOCC_Frontend_Premium::get_instance();
}