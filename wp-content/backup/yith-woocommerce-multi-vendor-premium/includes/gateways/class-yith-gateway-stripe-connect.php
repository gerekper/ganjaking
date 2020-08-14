<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendors_Gateway_Stripe_Connect' ) ) {
	/**
	 * YITH Gateway Stripe Connect
	 *
	 * Define methods and properties for class that manages payments via Stripe Connect
	 *
	 * @package   YITH_Marketplace
	 * @author    Your Inspiration <info@yourinspiration.it>
	 * @license   GPL-2.0+
	 * @link      http://yourinspirationstore.it
	 * @copyright 2014 Your Inspiration
	 */
	class YITH_Vendors_Gateway_Stripe_Connect extends YITH_Vendors_Gateway {

		/**
		 * @var YITH_Stripe_Connect_Frontend instance
		 */
		public $YITH_Stripe_Connect_Frontend = null;

		/**
		 * @var string gateway slug
		 */
		protected $_id = 'stripe-connect';

		/**
		 * @var string gateway name
		 */
		protected $_method_title = 'Stripe Connect';

		/**
		 * YITH_Vendors_Gateway_Stripe_Connect constructor.
		 *
		 * @param $gateway
         *
		 */
		public function __construct( $gateway ) {
			$this->set_is_external( true );
			$this->set_is_available_on_checkout( true );

			$current_user_can_manage_woocommerce  = current_user_can( 'manage_woocommerce' );

			$is_external_args = array(
				'check_method'   => 'function_exists',
				'check_for'      => 'YITH_Stripe_Connect',
				'plugin_url'     => '//yithemes.com/themes/plugins/yith-woocommerce-stripe-connect/',
				'plugin_name'    => 'YITH Stripe Connect for WooCommerce',
				'min_version'    => '1.0.4',
				'plugin_version' => defined( 'YITH_WCSC_VERSION' ) ? YITH_WCSC_VERSION : 0
			);

			$this->set_external_args( $is_external_args );

			parent::__construct( $gateway );

			if( $this->is_external_plugin_enabled() ){
				/* === Admin Panel === */
				add_filter( 'yith_wcmv_panel_gateways_options', 'YITH_Vendors_Gateway_Stripe_Connect::add_section_options' );
				add_filter( 'yith_wcmv_panel_sections', 'YITH_Vendors_Gateway_Stripe_Connect::add_section' );
				add_filter( 'yith_wcsc_prepared_commission_args', 'YITH_Vendors_Gateway_Stripe_Connect::prepared_commission_args', 10, 2 );

				/* === Enqueue Scripts === */
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

				if ( $current_user_can_manage_woocommerce && ! $this->is_enabled() ) {
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_connect_enable_gateway_for_vendors_message' ) );
					add_action( 'wp_ajax_enable_gateway_for_vendors', array( $this, 'save_enable_gateway_done' ) );
				}

				if( $this->is_enabled() ){
					add_action( 'admin_notices', array( $this, 'print_connect_vendor_to_stripe_message' ) );
					add_action( 'wp_ajax_enable_gateway_for_vendors', array( $this, 'save_connect_to_stripe_done' ) );
                }
			}

			if ( $this->is_enabled() ) {

			    $is_vendor_profile_page = is_admin() && ! is_ajax() && ! empty( $_GET['page'] ) && YITH_Vendors()->admin->vendor_panel_page == $_GET['page'] && ! empty( $_GET['tab'] ) && 'vendor-payments' == $_GET['tab'];

				if ( apply_filters( "yith_wcmv_is_vendor_profile_page_stripe_connect", $is_vendor_profile_page ) ) {
					/* === Vendor Admin Panel === */
					add_action( 'yith_wcmv_vendor_panel_payments', array( $this, 'stripe_connect_account_page' ) );
					add_filter( 'yith_wcsc_connect_account_template_args', array( $this, 'stripe_connect_account_template_args' ) );
					add_filter( 'yith_wcsc_account_page_script_data', array( $this, 'stripe_connect_account_template_args' ) );
				}

				if ( $current_user_can_manage_woocommerce ) {
					/* === Admin Message if the gateway is enabled for vendors === */
					add_action( 'admin_notices', array( $this, 'print_wc_stripe_connect_redirect_uri_message' ) );
					add_action( 'wp_ajax_redirect_uri_done_for_vendors', array( $this, 'save_redirect_uri_done' ) );

					/* === Commissions Table === */
					// Bulk Actions
					add_filter( 'yith_wcmv_commissions_bulk_actions', 'YITH_Vendors_Gateway_Stripe_Connect::commissions_bulk_actions' );
				}

				if ( current_user_can( YITH_Vendors()->get_role_name() ) && ! empty( YITH_Stripe_Connect()->admin ) ) {
					remove_action( 'admin_notices', array(
						YITH_Stripe_Connect()->admin,
						'print_wc_stripe_connect_uri_webhook_message'
					) );
				}

				/* === Checkout Payment === */
                if( $this->is_enabled_for_checkout() ){
                    add_action( 'yith_wcsc_payment_complete', array( $this, 'create_vendors_transfer' ), 20, 2 );
                }
			}

			//Prevent generate commissions for suborders
			add_filter( 'yith_wcsc_process_order_commissions', array( $this, 'process_order_commissions' ), 10, 2 );
			add_filter( 'yith_wcsc_process_product_commissions', array( $this, 'process_product_commissions' ), 10, 4 );
		}

		/**
         * Filter the standard commission args to allow Stripe Connect To manage Shipping FEE
         *
		 * @param $args prepared commission args
         * @return array commission args
		 */
		public static function prepared_commission_args( $args, $commission ){
            $integration_note = isset( $commission['integration_item'] ) ? maybe_unserialize( $commission['integration_item'] ) : '';

            if( ! empty( $integration_note['plugin_integration'] ) && YITH_WPV_SLUG == $integration_note['plugin_integration'] ){
                $commission_id = ! empty( $integration_note['vendor_commission_id'] ) ? $integration_note['vendor_commission_id'] : 0;
                $commission = YITH_Commission( $commission_id );
                if( $commission->exists() ){
	                $shipping_fee = _x( 'Shipping fee', '[admin]: commission type', 'yith-woocommerce-product-vendors' );
                    $args['product_info'] = empty( $args['product_info'] ) ? sprintf(  "%s %s", YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ), $shipping_fee ) : $args['product_info'];
                }
            }

            return $args;
        }

		/**
		 * Add Pay Bulk Actions
		 *
		 * @param $actions array Bulk actions for commissions table
		 *
		 * @return array allowed bulk actions
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function commissions_bulk_actions( $actions ) {
			$actions = array_merge( array( 'stripe-connect' => sprintf( "%s %s", _x( 'Pay with', "[Button Label]: Pay with PayPal", 'yith-woocommerce-product-vendors' ), 'Stripe Connect' ) ), $actions );
			return $actions;
		}

		/**
		 * Retrieve the Stripe Connect options array from this plugin.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return array option array
		 */
		public static function get_options_array() {
			$p_text          = _x( "When creating charges on your platform and separately creating a transfer, the platform can earn money by allocating less of the charge amount to the destination Stripe account, as in the above code example. Assuming that represents a delivery service transaction, with a charge to the customer of $100, a transfer of $20 to the delivery person, and a transfer of $70 to the restaurant:", '[Admin]: Option description', 'yith-woocommerce-product-vendors' );
			$li_1            = _x( "1. The charge amount less the Stripe fees is added to the platform account’s pending balance", '[Admin]: Option description', 'yith-woocommerce-product-vendors' );
			$li_2            = _x( "2. When the platform’s available balance is sufficient (at least $90), the transfers can be made, reducing the platform’s available balance by the specified amounts and increasing both connected account’s available balances by that same amount", '[Admin]: Option description', 'yith-woocommerce-product-vendors' );
			$li_3            = _x( "3. The platform retains an additional $6.80 ($100.00 - $70.00 - $20.00 - $3.20, assuming standard U.S. Stripe fees).", '[Admin]: Option description', 'yith-woocommerce-product-vendors' );
			$img_path        = YITH_WPV_ASSETS_URL . 'images/stripe-charges-transfers.png';
			$doc_url         = '//stripe.com/docs/connect/charges-transfers#collecting-fees';
			$source          = sprintf( "%s: <a href='%s' target='_blank'>%s</a>", _x( 'Source', '[part of] Source Stripe Documentation', 'yith-woocommerce-product-vendors' ), $doc_url, __( 'Stripe Documentation', 'yith-woocommerce-product-vendors' ) );
			$collecting_fees = sprintf( "<div id='stripe-connect-description-wrapper'><p>%s<br/><ul id='stripe_collect_fees'><li>%s</li><li>%s</li><li>%s</li></ul><img class='stripe-img' src='%s'><small>%s</small></p></div>", $p_text, $li_1, $li_2, $li_3, $img_path, $source );

			return apply_filters( 'yith_wcmv_stripe-connect_gateways_options', array(
					'stripe-connect' => array(

						'stripe_connect_options_start' => array(
							'type' => 'sectionstart',
						),

						'stripe_connect_title' => array(
							'title' => __( 'Stripe Connect', 'yith-woocommerce-product-vendors' ),
							'type'  => 'title',
							'desc'  => __( 'Configure here your gateways in order to process the payment of commissions.', 'yith-woocommerce-product-vendors' ),
						),

						'stripe_connect_enable_service' => array(
							'id'      => 'yith_wcmv_enable_stripe-connect_gateway',
							'type'    => 'checkbox',
							'title'   => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
							'desc'    => __( 'Enable Stripe Connect gateway', 'yith-woocommerce-product-vendors' ),
							'default' => 'no'
						),

						'stripe_connect_enable_commissions_log' => array(
							'id'      => 'yith_wcmv_enable_stripe-connect_commissions_log',
							'type'    => 'checkbox',
							'title'   => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
							'desc'    => sprintf( "%s <a href='%s'>%s</a>", __( "Add vendors' commissions to", 'yith-woocommerce-product-vendors' ), add_query_arg( array( 'page' => 'yith_wcsc_panel', 'tab' => 'commissions' ), admin_url( 'admin.php' ) ), __( 'Stripe Connect Commission Report', 'yith-woocommerce-product-vendors' ) ),
							'default' => 'no'
						),

						'stripe_connect_options_end' => array(
							'type' => 'sectionend',
						),

						'stripe_connect_checkout_options_start' => array(
							'type' => 'sectionstart',
						),

						'stripe_connect_checkout_title' => array(
							'title' => __( 'Send Money to vendor on payment completed', 'yith-woocommerce-product-vendors' ),
							'type'  => 'title',
						),

						'stripe_connect_checkout_description' => array(
							'title' => __( 'Send commissions to vendors on payment complete', 'yith-woocommerce-product-vendors' ),
							'type' => 'yith-field',
							'yith-type' => 'html',
                            'html' => $collecting_fees
						),

						'stripe_connect_checkout_options_end' => array(
							'type' => 'sectionend',
						),
					)
				)
			);
		}

		/**
		 * Add Stripe Connect Section
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array Stripe Connect option array
		 */
		public static function add_section( $sections ) {
			$sections['gateways']['stripe-connect'] = __( 'Stripe Connect', 'yith-woocommerce-product-vendors' );

			return $sections;
		}

		/**
		 * Add  Stripe Connect options array from this plugin.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array Stripe Connect option array
		 */
		public static function add_section_options( $options ) {
			return array_merge( $options, self::get_options_array() );
		}

		/**
		 * Add "Connect to Stripe" button in vnedor panel
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return void
		 */
		public function stripe_connect_account_page() {
		    if( defined( 'YITH_WCSC_PREMIUM' ) ){
			    if ( ! class_exists( 'YITH_Stripe_Connect_Frontend' ) ) {
				    require_once( YITH_WCSC_PATH . 'includes/class.yith-stripe-connect-frontend.php' );
			    }

			    $this->YITH_Stripe_Connect_Frontend = new YITH_Stripe_Connect_Frontend();

			    $option_description = _x( 'In order to use Stripe Connect service you need to link your Stripe account with website application.', '[Admin]: Option description', 'yith-woocommerce-product-vendors' );
			    $option_description = apply_filters( 'yith_wcmv_stripe_connect_option_description', $option_description );
			    ob_start();
			    printf( "<h3>%s</h3>", $this->get_method_title() );
			    printf( ' <div class="form-field">' );
			    $this->YITH_Stripe_Connect_Frontend->stripe_connect_account_page();
			    printf( '<p class="description">%s</p>', $option_description );
			    printf( '</div>' );
			    echo ob_get_clean();
            }
		}

		/**
		 * Filter the account connection template args
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return array template arguments
		 */
		public function stripe_connect_account_template_args( $args ) {
			$OAuth_link         = add_query_arg( array( 'redirect_uri' => $this->get_redirection_uri( true ) ), $args['OAuth_link'] );
			$args['OAuth_link'] = $OAuth_link;

			$vendor = yith_get_vendor( 'current', 'user' );

			if( $vendor->is_valid() && $vendor->has_limited_access()){
			    $args['count_commissions'] = 0;
            }

			return $args;
		}

		/**
		 * Vendor panel redirection uri
		 *
		 * @param $urlencode bool True if you want the url encoded, false otherwise
		 *
		 * @since 2.6.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return string redirect uri
		 */
		public function get_redirection_uri( $urlencode = false ) {
			$vendor_admin_panel = add_query_arg( array(
				'page' => YITH_Vendors()->admin->vendor_panel_page,
				'tab'  => 'vendor-payments'
			), admin_url( 'admin.php' ) );

			return $urlencode ? urlencode( $vendor_admin_panel ) : $vendor_admin_panel;
		}

		/**
		 * Add redirect URI message for vendors
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return void
		 */
		public function print_wc_stripe_connect_redirect_uri_message() {
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$section      = isset( $_GET['section'] ) ? $_GET['section'] : '';

			if ( 'yes' != get_option( 'yith_wcmv_redirected_uri_for_vendors', 'no' ) && ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section || 'yith_wpv_panel' == $current_page ) ) {
				?>
                <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_redirect_uri_for_vendors"
                     data-action="redirect_uri_done_for_vendors">
                    <p><?php echo sprintf( __( '<b>YITH Stripe Connect for WooCommerce (Multi Vendor Integration) -</b> Define the following <b>Redirect URI</b> %s in your <b>Redirect URIs</b> section at the following path <a href="%s" target="_blank">Stripe Dashboard > Connect > Settings</a>.', 'yith-stripe-connect-for-woocommerce' ), '<code>' . $this->get_redirection_uri() . '</code>', 'https://dashboard.stripe.com/account/applications/settings' ); ?></p>
                    <p>
                        <a class="button-primary"> <?php echo __( 'Done', 'yith-woocommerce-product-vendors' ); ?> </a>
                    </p>

                </div>
				<?php
			}
		}

		/**
		 * Add Enable Stripe Connect For Vendors message
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return void
		 */
		public function print_connect_vendor_to_stripe_message() {
			if( $this->is_enabled() ){
				$vendor = yith_get_vendor( 'current', 'user' );
				$stripe_connect_id =  get_user_meta( $vendor->get_owner(), 'stripe_user_id', true );
				if( empty( $stripe_connect_id ) && $vendor->is_valid() && $vendor->has_limited_access() && $vendor->get_owner() == get_current_user_id() ){
					$stripe_connect_vendor_uri = add_query_arg( array(
						'page'    => 'yith_vendor_settings',
						'tab'     => 'vendor-payments',
					), admin_url( 'admin.php' ) )
					?>
                    <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_connect_to_stripe_gateway_for_vendors"
                         data-action="enable_gateway_for_vendors">
                        <p><?php echo sprintf( __( '<b>Stripe Connect Enabled - </b>You can use your Stripe account to receive the commissions. Please go to the <a href="%s" target="_blank">Vendor Profile> Payments</a> section and click on <b>Connect from Stripe</b>. If you don\'t have a Stripe account you can create a new one after click on connect button. Thanks', 'yith-stripe-connect-for-woocommerce' ), $stripe_connect_vendor_uri ); ?></p>
                    </div>
					<?php
				}
			}
        }

		/**
		 * Add Enable Stripe Connect For Vendors message
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return void
		 */
		public function print_wc_stripe_connect_enable_gateway_for_vendors_message() {
			$current_page                         = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$section                              = isset( $_GET['section'] ) ? $_GET['section'] : '';

			if ( ! $this->is_enabled() && ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section || 'yith_wpv_panel' == $current_page ) ) {
				$stripe_connect_uri = add_query_arg( array(
					'page'    => 'yith_wpv_panel',
					'tab'     => 'gateways',
					'section' => 'stripe-connect'
				), admin_url( 'admin.php' ) )
				?>
                <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_enable_gateway_for_vendors"
                     data-action="enable_gateway_for_vendors">
                    <p><?php echo sprintf( __( '<b>YITH Stripe Connect for WooCommerce (Multi Vendor Integration) - </b>Please, enable the <b>Multi Vendor Integration</b> for Stripe Connect in <a href="%s" target="_blank">YITH Plugins > Multi Vendor > Gateways ></a> <b>Stripe Connect</b> section.', 'yith-stripe-connect-for-woocommerce' ), $stripe_connect_uri ); ?></p>
                    <p>
                        <a class="button-primary"> <?php echo __( 'Done', 'yith-woocommerce-product-vendors' ); ?> </a>
                    </p>

                </div>
				<?php
			}
		}

		/**
		 * Save redirect uri option
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @since 2.6.0
		 */
		public function save_redirect_uri_done() {
			$value = update_option( 'yith_wcmv_redirected_uri_for_vendors', 'yes' );
			wp_send_json_success( $value );
		}

		/**
		 * Save redirect uri option
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @since 2.6.0
		 */
		public function save_enable_gateway_done() {
			$value = update_option( 'yith_wcmv_enable_gateway_for_vendors', 'yes' );
			wp_send_json_success( $value );
		}

		/**
		 * Enqueue Scripts
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 * @since 2.6.0
		 */
		public function enqueue_scripts() {
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
			$section      = isset( $_GET['section'] ) ? $_GET['section'] : '';
			if ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section || 'yith_wpv_panel' == $current_page ) {
				wp_enqueue_script( 'yith-wcsc-admin' );
			}
		}

		/* === PAYMENT METHODS === */

		/**
		 * Add args for specific gateway
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 2.6.0
		 * @return array filtered args
		 */
		public function get_pay_data_extra_args( $pay_data ) {
			/* === Add Stripe Connect Args === */
            $vendor_ids = array_keys( $pay_data );

			foreach ( $vendor_ids as $vendor_id ) {
				$owner_id                                 = $pay_data[ $vendor_id ]['user_id'];
				$stripe_user_id                           = get_user_meta( $owner_id, 'stripe_user_id', true );
				$pay_data[ $vendor_id ]['stripe_user_id'] = $stripe_user_id;
				$pay_data[ $vendor_id ]['gateway_id']     = $this->get_id();
			}

			return $pay_data;
		}

		/**
		 * Pay method, used to process payment requests
		 *
		 * @param $pay_data  array  Array of parameters for the single requests
		 *
         * [30] => Array
		 *       (
		 *       [user_id] => 5
		 *       [amount] => Array( [EUR] => 9.0000 )
         *
		 * [commission_ids] => Array
		 *           [EUR] => Array(
         *                [0] => 154
         *                [1] => 178
         *                [2] => 193
         *                [3] => 18
		 *           )
         *
		 * [payment_date] => 2018-05-25 08:02:56
		 * [payment_date_gmt] => 2018-05-25 08:02:56
		 * [stripe_user_id] => acct_8237382hton32t
		 * )
         *
		 * @return array An array holding the status of the operation; it should have at least a boolean status, a verbose status and an array of messages
		 *
		 * @since 1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function pay( $pay_data ) {
			$result = array(
				'status'   => false,
				'messages' => ''
			);

			$not_connected_message = _x( "The vendor hasn't connected the profile to Stripe.", '[Admin Error Message]', 'yith-woocommerce-product-vendors' );

		    if( empty( $pay_data ) ){
			    $message = _x( 'Missing required parameters for Stripe Connect', '[Admin]: error message', 'yith-woocommerce-product-vendors' );
			    $result  = array(
				    'status'   => false,
				    'messages' => urlencode( $message )
			    );

		        return $result;
            }

            $commission_log_enabled_for_vendors = 'yes' == get_option( 'yith_wcmv_enable_stripe-connect_commissions_log', 'no' );

		    /* === Gateway Requirements === */
			$api_handler               = YITH_Stripe_Connect_API_Handler::instance();
			$stripe_connect_gateway    = YITH_Stripe_Connect_Gateway::instance();
			$stripe_connect_commission = $commission_log_enabled_for_vendors ? YITH_Stripe_Connect_Commissions::instance() : null;

			/* === Build Payments Data For API === */
            $transfer = null;

			foreach ( $pay_data as $vendor_id => $vendor_pay_data ) {
				$stripe_connect_id = $pay_data[ $vendor_id ]['stripe_user_id'];
				$amounts           = $pay_data[ $vendor_id ]['amount'];
				foreach( $amounts as $currency => $amount ){
					$user_id = $pay_data[ $vendor_id ]['user_id'];

					$args    = array(
						'amount'      => yith_wcsc_get_amount( $amount, $currency ),
						'currency'    => $currency,
						'destination' => $stripe_connect_id,
						//'description' => sprintf( __( 'Commission %d - Order %d - %s', 'yith-stripe-connect-for-woocommerce' ), $commission['ID'], $commission['order_id'], $item->get_name() )
					);

					$args = apply_filters( 'yith_wcmv_stripe_connect_transfer_args', $args );

					if( ! empty( $pay_data[ $vendor_id ]['transfer_group'] ) ){
						$args['transfer_group'] = $pay_data[ $vendor_id ]['transfer_group'];
					}

					if( ! empty( $pay_data[ $vendor_id ]['source_transaction'] ) ){
						$args['source_transaction'] = $pay_data[ $vendor_id ]['source_transaction'];
					}

					$commission_ids = $pay_data[ $vendor_id ]['commission_ids'][ $currency ];

					$log_args = array(
						'payment' => array(
							'vendor_id'        => $vendor_id,
							'user_id'          => $user_id,
							'amount'           => $amount,
							'currency'         => $currency,
							'status'           => 'processing',
							'payment_date'     => $pay_data[ $vendor_id ]['payment_date'],
							'payment_date_gmt' => $pay_data[ $vendor_id ]['payment_date_gmt'],
							'gateway_id'       => $pay_data[ $vendor_id ]['gateway_id']
						),

                        'commission_ids' => $commission_ids
					);

					//Create entry in Payments table
                    $payment_id = YITH_Vendors()->payments->add_payment( $log_args );

                    if ( ! empty( $stripe_connect_id ) ) {
                        //The vendor have a valid Stripe account
						$transfer = $api_handler->create_transfer( $args );

	                    if ( isset( $transfer['error_transfer'] ) ) {
		                    // Display messages on order note and log file
                            $message = sprintf( '%s', $transfer['error_transfer'] );
		                    $stripe_connect_gateway->log( 'info', sprintf( 'Payment ID: %s', $payment_id ) );
		                    $stripe_connect_gateway->log( 'info', sprintf( 'Destination ID: %s', $stripe_connect_id ) );
		                    $stripe_connect_gateway->log( 'info', sprintf( 'User ID: %s', $user_id ) );
                            $stripe_connect_gateway->log( 'error', sprintf( 'Stripe Error: %s', $message ) );
		                    YITH_Vendors()->payments->add_note( $payment_id, $message );

		                    $result = array(
			                    'status'   => false,
			                    'messages' => urlencode( $message )
		                    );
	                    }

	                    elseif( $transfer instanceof \Stripe\Transfer ) {
                            //Lucky Day For Vendors! Money transfer complete
		                    $message = __( 'Payment correctly issued to the gateway', 'yith-woocommerce-product-vendors' );
		                    YITH_Vendors()->payments->add_note( $payment_id, urldecode( $message ) );
		                    $stripe_connect_gateway->log( 'info', sprintf( 'Stripe Success: %s', $message ) );

		                    $result = array();
		                    $result[ $currency ] = array(
			                    'status'   => true,
			                    'messages' => urlencode( $message )
		                    );
                        }
					}

                    else {
                        //Not Connected to Stripe
	                    $vendor  = yith_get_vendor( $vendor_id, 'vendor' );
	                    $message = sprintf( '%s (%s: <a href="%s">#%s - %s</a>)',
		                    $not_connected_message,
		                    YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ),
		                    $vendor->get_url( 'admin' ),
		                    $vendor_id,
                            $vendor->name
	                    );

	                    YITH_Vendors()->payments->add_note( $payment_id, urldecode( $message ) );

	                    $message = sprintf( __( 'Payment failed: %s', 'yith-woocommerce-product-vendors' ), $not_connected_message );
	                    $this->add_note_commissions( $commission_ids, $message );

	                    $stripe_connect_gateway->log( 'info', sprintf( 'Payment ID: %s', $payment_id ) );
	                    $stripe_connect_gateway->log( 'info', sprintf( 'Destination ID: %s', $stripe_connect_id ) );
	                    $stripe_connect_gateway->log( 'info', sprintf( 'User ID: %s', $user_id ) );
	                    $stripe_connect_gateway->log( 'error', sprintf( 'Stripe Error: %s', $message ) );

	                    $result = array(
		                    'status'   => false,
		                    'messages' => urlencode( $message )
	                    );
                    }

                    $payment_status = false;

                    if( isset( $result[ $currency ]['status'] ) ){
	                    $payment_status = true === $result[ $currency ]['status'] ? 'paid' : 'failed';
                    }

                    elseif( $result['status'] ) {
	                    $payment_status = true === $result['status'] ? 'paid' : 'failed';
                    }

                    else {
	                    $payment_status = 'failed';
                    }


					YITH_Vendors()->payments->update_payment_status( $payment_id, $payment_status );

                    foreach ( $commission_ids as $commission_id ){
                        $commission = YITH_Commission( $commission_id );
                        if( $commission->exists() ){
                            $order    = $commission->get_order();
                            $currency = $order->get_currency();

	                        if( 'paid' == $payment_status ){
		                        $commission->update_status( $payment_status, '', true );
		                        $this->set_payment_post_meta( $commission );
		                        $gateway_payment_message = sprintf( "%s. %s %s", $result[ $currency ]['messages'], _x( 'Paid via', '[Note]: Payed By Gateway', 'yith-woocommerce-product-vendors' ), $this->get_method_title() );
		                        $commission->add_note( urldecode( $gateway_payment_message ) );
                            }

                            if ( $commission_log_enabled_for_vendors ) {
	                            $item    = $commission->get_item();
	                            $product = $commission->get_product();
	                            $vendor  = $commission->get_vendor();
	                            $item_id = $item instanceof WC_Order_Item ? $item->get_id() : 0;
                                $notes = array();
	                            if( $transfer instanceof \Stripe\Transfer ){
	                                $extra_info = array(
		                                'generated_by' => array(
			                                'label' => __( 'Generated by', 'yith-woocommerce-product-vendors' ),
			                                'note'  => 'YITH WooCommerce Multi Vendor',
		                                ),

		                                'vendor_information' => array(
			                                'label' => __( 'Vendor', 'yith-woocommerce-product-vendors' ),
			                                'note'  => sprintf( '<a href="%s" target="_blank">%s</a>', $vendor->get_url( 'admin' ), $vendor->name )
		                                ),

		                                'commission_type' => array(
			                                'label' => __( 'Commission type', 'yith-woocommerce-product-vendors' ),
			                                'note'  => ucfirst( $commission->type )
		                                ),

		                                'commission_url' => array(
			                                'label' => __( 'Commission ID', 'yith-woocommerce-product-vendors' ),
			                                'note'  => sprintf( '<a href="%s" target="_blank">#%s</a>', $commission->get_view_url( 'admin' ), $vendor->id )
		                                ),
	                                );
;
		                            $notes = array(
			                            'transfer_id'         => $transfer->id,
			                            'destination_payment' => $transfer->destination,
			                            'extra_info'          => apply_filters( 'yith_wcmv_extra_info_for_stripe_connect_commission', $extra_info )
                                    );
	                            }

	                            $integration_item = array(
		                            'plugin_integration'      => YITH_WPV_SLUG,
		                            'payment_id'              => $payment_id,
		                            'vendor_commission_id'    => $commission->id
	                            );

	                            $sc_commission = array(
		                            'user_id'           => $user_id,
		                            'order_id'          => $order->get_id(),
		                            'order_item_id'     => $item_id,
		                            'product_id'        => $product instanceof WC_Product ? $product->get_id() : 0,
		                            'commission'        => $commission->get_amount(),
		                            'commission_status' => 'paid' == $payment_status ? 'sc_transfer_success' : 'sc_transfer_error',
		                            'commission_type'   => 'percentage',
		                            'commission_rate'   => ( $commission->get_rate() * 100 ),
		                            'payment_retarded'  => 0,
		                            'purchased_date'    => $commission->get_date( 'mysql' ),
		                            'note'              => maybe_serialize( $notes ),
		                            'integration_item'  => maybe_serialize( $integration_item )
	                            );

                                $stripe_connect_commission->insert( $sc_commission );
                            }
                        }
                    }
				}
			}

			return $result;
		}

		/**
		 * Handle the single commission from commission list
		 */
		public function handle_single_commission_pay() {
			$message = $text = '';
			if ( current_user_can( 'manage_woocommerce' ) && wp_verify_nonce( $_GET['_wpnonce'], 'yith-vendors-pay-commission' ) && isset( $_GET['commission_id'] ) ) {
				$commission_id = absint( $_GET['commission_id'] );
				$result        = $this->pay_commission( $commission_id );
				$message       = $result['status'] ? 'pay-process' : 'pay-failed';
				$text          = $result['messages'];
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( array(
				'message' => $message,
				'text'    => urlencode( $text )
			), wp_get_referer() ) ) );
			exit();
		}

		/**
		 * Handle the massive commission from commission list
		 */
		public function handle_massive_commissions_pay( $vendor, $commission_ids, $action ) {
			$message = $text = '';
			if ( current_user_can( 'manage_woocommerce' ) && ! empty( $commission_ids ) ) {
				$result  = $this->pay_massive_commissions( $commission_ids, $action );
				$message = $result['status'] ? 'pay-process' : 'pay-failed';
				$text    = $result['messages'];
			}

			elseif( empty( $commission_ids ) ){
				$text    = __( 'Please, select at least one commission', 'yith-woocommerce-product-vendors' );
				$message = 'pay-failed';
            }

			wp_safe_redirect( esc_url_raw( add_query_arg( array(
				'message' => $message,
				'text'    => urlencode( $text )
			), wp_get_referer() ) ) );
			exit();
		}

		/**
         * Create transfers for vendor's commissions
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @return void
         */
		public function create_vendors_transfer( $order_id, $charge_id ){
			$commission_ids = array();
			$order          = wc_get_order( $order_id );

		    //Check if the current order use Stripe Connect Gateway
		    if( $order instanceof WC_Order && 'yith-stripe-connect' == $order->get_payment_method() ){
			    $suborder_ids = YITH_Orders::get_suborder( $order_id );
			    foreach( $suborder_ids as $suborder_id ){
				    $commission_ids = array_merge( $commission_ids, YITH_Commissions()->get_commissions( array( 'order_id' => $suborder_id, 'status' => 'all' ) ) );
                }

			    $extra_args    = array( 'source_transaction' => $charge_id, 'transfer_group' => $order_id );
			    $pay_data_args = array( 'commission_ids' => $commission_ids, 'extra_args' => $extra_args );
			    $pay_data      = $this->get_pay_data( $pay_data_args );

			    $this->pay( $pay_data );
            }
        }

		/**
		 * @param $commission_ids
		 * @param $message
		 */
        public function add_note_commissions( $commission_ids, $message ){
	        //Add Note to commissions
	        foreach( $commission_ids as $commission_id ){
		        $commission = YITH_Commission( $commission_id );
		        if( $commission->exists() ){
			        $commission->add_note( $message );
		        }
	        }
        }

        /**
         * Check if current order is a vendor suborder and skip it to commissions creation process
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 3.0.1
         * @return bool TRUE if Stripe Connect can process this order, false otherwise
         */
        public function process_order_commissions( $process, $order_id ){
            //Check if this is a suborder or not
	        $parent_order_id = get_post_field( 'post_parent', $order_id );

            if( ! empty( $parent_order_id ) ){
                //is a suborder. Check if this is a vendor suborder
                $parent_order = wc_get_order( $parent_order_id );
                if( 'yith_wcmv_vendor_suborder' == $parent_order->get_created_via() ){
                    $process = false;
                }
            }

            return $process;
        }

		/**
		 * Check if current product is from vendor or not
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 3.0.1
		 * @return bool TRUE if Stripe Connect can process this order, false otherwise
		 */
		public function process_product_commissions( $process, $product_id, $order_item, $order_id ) {
			if ( function_exists( 'YITH_Stripe_Connect_Commissions' ) && 'yes' == YITH_Stripe_Connect_Commissions()->stripe_connect_gateway->get_option( 'vendor-product-commissions', 'yes' ) ) {
				$product   = wc_get_product( $product_id );
				$parent_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
				$vendor    = yith_get_vendor( $parent_id, 'product' );
				$process   = $vendor->is_valid() ? false : $process;
			}

			return $process;
		}
	}
}