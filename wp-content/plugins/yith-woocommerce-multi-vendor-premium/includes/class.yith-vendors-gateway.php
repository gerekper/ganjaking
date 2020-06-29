<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendors_Gateway' ) ) {
	/**
	 * YITH Gateway
	 *
	 * Define methods and properties for class that manages admin payments
	 *
	 * @class      YITH_Vendors_Gateway
	 * @package    Yithemes
	 * @since      Version 2.0.0
	 * @author     Your Inspiration Themes
	 */
	class YITH_Vendors_Gateway {

		/**
		 * @var bool check if this gateway is available on checkout or not
		 */
		protected $_is_available_on_checkout = false;

		/**
		 * @var bool check if this gateway is available now
		 */
		protected $_is_coming_soon = false;

		/**
		 * @var bool check if this gateway required an external plugin to works
		 */
		protected $_is_external = false;

		/**
		 * @var array args for external gateays
		 */
		protected $_external_args = array();

		/**
		 * @var string gateway slug
		 */
		protected $_id = 'gateway-id';

		/**
		 * @var string gateway name
		 */
		protected $_method_title = 'Gateway';

		/**
		 * @var string default gateway id
		 */
		protected static $_default_gateway_id = 'manual-payments';

		/**
		 * Array of instances of the class, one for each available gateway
		 *
		 * @var mixed Array of instances of the class
		 *
		 * @since 1.0
		 */
		static public $instances = array();

		/**
		 * Name of the class of the actual used gateway
		 *
		 * @var string Gateway class name
		 *
		 * @since 1.0
		 */
		public $gateway;

		/**
		 * Constructor Method
		 *
		 * @return \YITH_Vendors_Gateway
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function __construct( $gateway = null ) {
			$this->gateway = $gateway;
			if ( $this->get_is_external() ) {
				add_filter( "yith_wcmv_displayed_{$this->get_id()}_id", array( $this, 'add_is_external_required_message' ) );
				add_filter( "yith_wcmv_{$this->get_id()}_options_admin_url", array( $this, 'change_admin_url' ) );
			}

			if ( $this->is_enabled() ) {
				//Add Pay Button
				add_action( 'yith_wcmv_before_user_actions', array( $this, 'add_button' ), 10, 1 );

				//Pay commission(s) in admin area
				add_action( "admin_action_pay_commission_{$this->get_id()}", array( $this, 'handle_single_commission_pay' ) );
				add_action( "admin_action_pay_commissions_{$this->get_id()}", array( $this, 'handle_massive_commissions_pay' ), 10, 3 );

				/* === Get Pay Data Filter === */
				add_filter( "yith_wcmv_get_pay_data_args_for_{$this->get_id()}", array( $this, 'get_pay_data_extra_args' ) );
			}
		}

		/* === STATIC INITIALIZATION === */

		/**
		 * Returns instance of the class, created specifically for the slug passed as parameter
		 * Each gateway slug will generate at most one YITH_Vendors_Gateway instance
		 *
		 * @param $gateway string Gateway slug
		 *
		 * @static
		 * @return \YITH_Vendors_Gateway Unique instance of the class for the passed gateway slug
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		static public function get_instance( $gateway ) {

			if ( empty( $gateway ) ) {
				//Load the default one
				$gateway = self::$_default_gateway_id;
			}

			if ( isset( self::$instances[ $gateway ] ) ) {
				return self::$instances[ $gateway ];
			} else {
				if ( file_exists( YITH_WPV_PATH . 'includes/gateways/class-yith-gateway-' . $gateway . '.php' ) ) {
					require_once( YITH_WPV_PATH . 'includes/gateways/class-yith-gateway-' . $gateway . '.php' );
				}

				$class = YITH_Vendors_Gateways::get_gateway_class_from_slug( $gateway );

				if ( class_exists( $class ) ) {
					self::$instances[ $gateway ] = new $class( $gateway );

					return self::$instances[ $gateway ];
				}

				return false;
			}
		}

		/* === DYNAMIC INITIALIZATION === */

		/**
		 * Sends payment requests to gateway specific method
		 *
		 * @param $payment_detail mixed  Array used to identify payment to execute; it will be passed to gateway method, so can be anything
		 *
		 * @return array An array holding the status of the operation; it should have at least a boolean status, a verbose status and an array of messages
		 * [
		 *     status => bool (status of the operation)
		 *     verbose_status => string (E.G.: for PayPal one between PAYMENT_STATUS_OK and PAYMENT_STATUS_FAIL)
		 *     messages => string|array (one or more message describing operation status)
		 * ]
		 * If payment can be executed, method will return pay method result value
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function pay( $payment_detail ) {
			return array();
		}

		/**
		 * Get the gateway options
		 *
		 * @return array
		 */
		public function get_gateway_options() {
			return array();
		}

		/**
		 * Get the data for pay() method
		 *
		 * @args Array argument to retreive payment data
		 * @return array
		 */
		public function get_pay_data( $args = array() ) {
			$commission_ids = $pay_data = $extra_args = array();

			if( ! empty( $args['commission_ids'] ) ){
				$commission_ids = $args['commission_ids'];
			}

			elseif( ! empty( $args['commission_id'] ) ){
				$commission_ids = $args['commission_id'];
			}

			elseif ( ! empty( $_REQUEST['commissions'] ) ) {
				$commission_ids = $_REQUEST['commissions'];
			}

			elseif ( ! empty( $_REQUEST['commission_id'] ) ) {
				$commission_ids = $_REQUEST['commission_id'];
			}

			$pay_data = $this->build_args_to_register_vendor_payments( $commission_ids );

			if( ! empty( $args['extra_args'] ) ){
				$extra_args = $args['extra_args'];
			}

			elseif( ! empty( $_REQUEST['extra_args'] ) ){
				$extra_args = $_REQUEST['extra_args'];
			}

			$vendor_ids = array_keys( $pay_data );
			foreach ( $vendor_ids as $vendor_id ){
				$pay_data[ $vendor_id ] = array_merge( $pay_data[ $vendor_id ], $extra_args );
			}

			return apply_filters( "yith_wcmv_get_pay_data_args_for_{$this->get_id()}", $pay_data );
		}

		/**
		 * Get the data from get_pay_data() method
		 *
		 * @args Array argument to pay
		 * @return array
		 */
		public function get_pay_data_extra_args( $pay_data ) {
			return $pay_data;
		}

		/**
		 * Check if the current gateway is enabled or not
		 *
		 * @return bool TRUE if enabled, FALSE otherwise
		 */
		public function is_enabled() {
			$gateway_slug = $this->get_id();
			$enabled      = 'yes' == get_option( "yith_wcmv_enable_{$gateway_slug}_gateway", 'no' );

			if ( $enabled && $this->get_is_external() ) {
				$enabled = $this->is_external_plugin_enabled();
			}

			return $enabled;
		}

		/**
		 * Get Class Slug
		 *
		 * @return string
		 */
		public function get_id() {
			return $this->_id;
		}

		/**
		 * Get Class Name
		 *
		 * @return string
		 */
		public function get_method_title() {
			return $this->_method_title;
		}

		/**
		 * Get is_coming_soon attribute
		 *
		 * @return string
		 */
		public function get_is_coming_soon() {
			return $this->_is_coming_soon;
		}

		/**
		 * set is_coming_soon attribute
		 *
		 * @return void
		 */
		public function set_is_coming_soon( $is_coming_soon ) {
			$this->_is_coming_soon = $is_coming_soon;
		}

		/**
		 * Get is_external attribute
		 *
		 * @return string
		 */
		public function get_is_external() {
			return $this->_is_external;
		}

		/**
		 * set is_external attribute
		 *
		 * @return void
		 */
		public function set_is_external( $is_extenal ) {
			$this->_is_external = $is_extenal;
		}

		/**
		 * Set is_external attribute
		 *
		 * @return array
		 */
		public function get_external_args() {
			return $this->_external_args;
		}

		/**
		 * Get is_available_on_checkout attribute
		 *
		 * @return string
		 */
		public function get_is_available_on_checkout() {
			return $this->_is_available_on_checkout;
		}

		/**
		 * set is_available_on_checkout attribute
		 *
		 * @return void
		 */
		public function set_is_available_on_checkout( $args ) {
			$this->_is_available_on_checkout = $args;
		}

		/**
		 * set is_extenal attribute
		 *
		 * @return void
		 */
		public function set_external_args( $args ) {
			$this->_external_args = $args;
		}

		/**
		 * check for external plugin
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return bool TRUE if the external plugin is required, false otherwise
		 */
		public function is_external_plugin_enabled() {
			$external_args = $this->get_external_args();
			extract( $external_args );

			$check = $check_method( $check_for );

			if ( isset( $min_version ) && isset( $plugin_version ) ) {
				$is_enabled          = $check;
				$is_required_version = $plugin_version >= $min_version;
				$check               = $is_required_version && $is_enabled;
			}

			return $check;
		}

		/**
		 * Add external plugin required message
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return bool TRUE if the external plugin is required, false otherwise
		 */
		public function add_is_external_required_message( $gateway_id ) {
			if ( ! $this->is_external_plugin_enabled() ) {
				$min_version_message = '';
				$external_args = $this->get_external_args();
				extract( $external_args );

				if( isset( $min_version ) ){
					$min_version_message = sprintf( '(%s %s)', $min_version, _x( 'or greater', "[Admin] Required version x.x.x or greater", 'yith-woocommerce-product-vendors' ) );
				}


				$gateway_id = sprintf( '<a href="%s" class="yith-wcmv-gateway-required-external" target="_blank">%s %s %s</a>', $plugin_url, _x( 'Required', '[Admin]: Part of Required xxx plugin', 'yith-woocommerce-product-vendors' ), $plugin_name, $min_version_message );

			}

			return $gateway_id;
		}

		/**
		 * Change admin url
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return string admin option url if plugin is enabled, landing page url otherwise
		 *
		 */
		public function change_admin_url( $admin_url ) {
			if ( ! $this->is_external_plugin_enabled() ) {
				$external_args = $this->get_external_args();
				extract( $external_args );
				$admin_url = $plugin_url;
			}

			return $admin_url;
		}

		/**
		 * Check if this gateway is enabled for checkout
		 */
		public function is_enabled_for_checkout(){
			return $this->get_id() == get_option( 'yith_wcmv_checkout_gateway', 'no' );
		}

		/**
		 * Handle the single commission from commission list
		 */
		public function handle_single_commission_pay() {
			//Silence is golden
		}

		/**
		 * Handle the massive commission from commission list
		 */
		public function handle_massive_commissions_pay( $vendor, $commission_ids, $action ) {
			//Silence is golden
		}

		/**
		 * Pay single commission
		 *
		 * @param $commission_id
		 *
		 * @return array
		 */
		public function pay_commission( $commission_id ) {
			$commission = YITH_Commission( $commission_id );
			$order = $commission->get_order();
			$currency = $order->get_currency();

			if ( ! $commission->exists() ) {
				return;
			}

			$data = $this->get_pay_data( array( 'commissions' => $commission_id, 'type' => 'massive_payment' ) );

			// process payment
			$result = $this->pay( $data );

			//Check for Multi Currency Message
			if( isset( $result[ $currency ] ) ){
				$result = $result[ $currency ];
			}

			// set as processing, because gateway like paypal will set as paid as soon as the transaction is completed
			if ( $result['status'] ) {
				$commission->update_status( 'processing' );
			}

			return $result;
		}

		/**
		 * Pay massive commission
		 *
		 * @param $commission_ids
		 * @param $action
		 * @param $transaction_status string processing by default
		 *
		 * @return array
		 */
		public function pay_massive_commissions( $commission_ids, $action, $transaction_status = 'processing' ) {
			if ( empty( $commission_ids ) ) {
				return;
			}

			if ( ! is_array( $commission_ids ) ) {
				$commission_ids = explode( ',', $commission_ids );
			}

			$args = array(
				'type'        => 'massive_payment',
				'commissions' => $commission_ids,
			);

			$data = $this->get_pay_data( $args );

			// process payment
			$commission_ids = array();
			$status = $message = '';
			$result         = $this->pay( $data );


			//Error messages isn't related to currency
			if( isset( $result['status'] ) && isset( $result['messages'] ) ){
				$status  = $result['status'];
				$message = $result['messages'];
			}

			else {
				foreach ( $data as $vendor_id => $vendor_data ){
					if( isset( $vendor_data['commission_ids'] ) ){
						$commission_ids = $vendor_data['commission_ids'];
						foreach ( $commission_ids as $currency => $commission_ids_by_currency ) {
							foreach( $commission_ids_by_currency as $commission_id ){
								$commission = YITH_Commission( $commission_id );
								if( $commission->exists() ){
									$order    = $commission->get_order();
									$currency = $order->get_currency();

									if ( $result[$currency]['status'] ) {
										$status = $result[$currency]['status'];
										$commission->update_status( $transaction_status );
									}

									// save the error in the note
									else {
										$status  = $result[ $currency ]['status'];
										$message = $result[ $currency ]['messages'];
									}
								}
							}
						}
					}

				}
			}

			return array( 'status' => $status, 'messages' => $message );
		}

		/**
		 * Show Pay Button for MassPay service
		 *
		 * @param $commission YITH_Commission the commission to pay
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_button( $commission ) {
			if ( $commission instanceof YITH_Commission && $commission->has_status( 'unpaid' ) && apply_filters( "yith_wcmv_show_pay_button_for_{$this->get_id()}", true ) ) {
				$args        = array(
					'action'        => "pay_commission_{$this->get_id()}",
					'commission_id' => $commission->id,
					'gateway_id'    => $this->get_id()
				);
				$gateway_uri = esc_url( wp_nonce_url( add_query_arg( $args, admin_url( 'admin.php' ) ), 'yith-vendors-pay-commission' ) );
				$gateway_uri = apply_filters( "yith_wcmv_commissions_list_table_{$this->get_id()}_button_url", $gateway_uri, $commission );
				printf( '<a class="button tips pay" href="%1$s" data-tip="%2$s %3$s" data-gateway="%3$s">%3$s</a>', $gateway_uri, _x( 'Pay with', "[Button Label]: Pay with PayPal", 'yith-woocommerce-product-vendors' ), $this->get_method_title() );
			}
		}

		/**
		 * This function return  the needs args for  function register_vendor_payments
		 * @author Salvatore Strano
		 *
		 * @param array $commissions
		 *
		 * @return array
		 */
		public function build_args_to_register_vendor_payments( $commissions ) {

			if ( ! is_array( $commissions ) ) {
				$commissions = array( $commissions );
			}

			$args = array();


			foreach ( $commissions as $commission_id ) {
				$commission = YITH_Commission( $commission_id );

				if( $commission->exists() && 'paid' != $commission->get_status() ){
					$vendor     = $commission->get_vendor();
					$vendor_id  = $vendor->id;
					$order      = $commission->get_order();
					$currency   = $order->get_currency();
					$user_id    = $vendor->get_owner();

					if ( ! isset( $args[ $vendor_id ] ) ) {
						$args[ $vendor_id ]['user_id']          = $user_id;
						$args[ $vendor_id ]['payment_date']     = current_time( 'mysql' );
						$args[ $vendor_id ]['payment_date_gmt'] = current_time( 'mysql', 1 );
					}

					if ( ! isset( $args[ $vendor_id ]['amount'][ $currency ] ) ) {
						$args[ $vendor_id ]['amount'][ $currency ] = $commission->get_amount_to_pay();
					}

					else {
						$args[ $vendor_id ]['amount'][ $currency ] += $commission->get_amount_to_pay();
					}

					if ( ! isset( $args[ $vendor_id ]['commission_ids'][ $currency ] ) ) {
						$args[ $vendor_id ]['commission_ids'][ $currency ] = array( $commission_id );
					}

					else {

						$args[ $vendor_id ]['commission_ids'][ $currency ][] = $commission_id;
					}
				}
			}

			return $args;
		}

		/**
		 * Add post meta after payment successful
		 *
		 * @since 2.6.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function set_payment_post_meta( $commission ){
			if( $commission instanceof YITH_Commission ){
				/** @var WC_Order $order */
				/** @var YITH_Commission $commission */
				$order = $commission->get_order();
				
				$order_meta_values = array(
					"_commission_{$commission->id}_paid_by_gateway" => 'yes',
					"_commission_{$commission->id}_paid_by" => $this->get_id()
				);

				foreach ( $order_meta_values as $meta_key => $meta_value ){
					$order->add_meta_data( $meta_key, $meta_value, true );
				}

				$order->save_meta_data();
			}
		}
	}
}

/**
 * Get the single instance of YITH_Vendors_Gateway_Panel class
 *
 *
 * @return \YITH_Vendors_Gateway Single instance of the class
 * @since  1.0
 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
 */
function YITH_Vendors_Gateway( $gateway ) {
	return YITH_Vendors_Gateway::get_instance( $gateway );
}