<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendors_Gateway_Paypal_Payouts' ) ) {
	/**
	 * YITH Gateway PayPal Payouts
	 *
	 * Define methods and properties for class that manages payments via PayPal Payouts
	 *
	 * @package   YITH_Marketplace
	 * @author    Your Inspiration <info@yourinspiration.it>
	 * @license   GPL-2.0+
	 * @link      http://yourinspirationstore.it
	 * @copyright 2014 Your Inspiration
	 */
	class YITH_Vendors_Gateway_Paypal_Payouts extends YITH_Vendors_Gateway {

		protected $YITH_PayOuts_Service;
		/**
		 * @var string gateway slug
		 */
		protected $_id = 'paypal-payouts';

		/**
		 * @var string gateway name
		 */
		protected $_method_title = 'PayPal Payouts';

		/**
		 * YITH_Vendors_Gateway_PayPal_Payouts constructor.
		 *
		 * @param $gateway
		 *
		 */
		public function __construct( $gateway ) {
			$this->set_is_external( true );
			$this->set_is_available_on_checkout( true );

			$current_user_can_manage_woocommerce = current_user_can( 'manage_woocommerce' );

			$is_external_args = array(
				'check_method'   => 'function_exists',
				'check_for'      => 'YITH_PayPal_Payouts',
				'plugin_url'     => '//yithemes.com/themes/plugins/yith-paypal-payouts-for-woocommerce/',
				'plugin_name'    => 'YITH PayPal Payouts for WooCommerce',
				'min_version'    => '1.0.0',
				'plugin_version' => defined( 'YITH_PAYOUTS_VERSION' ) ? YITH_PAYOUTS_VERSION : 0
			);

			$this->set_external_args( $is_external_args );

			parent::__construct( $gateway );

			if ( $this->is_external_plugin_enabled() ) {
				/* === Admin Panel === */
				add_filter( 'yith_wcmv_panel_gateways_options', 'YITH_Vendors_Gateway_Paypal_Payouts::add_section_options' );
				add_filter( 'yith_wcmv_panel_sections', 'YITH_Vendors_Gateway_Paypal_Payouts::add_section' );
			}

			if ( function_exists( 'YITH_PayPal_Payouts' ) ) {
				/**
				 * Load Payouts core classes
				 */
				YITH_PayPal_Payouts()->load_payouts_classes();

				if ( YITH_PayOuts_Service()->check_service_configuration() ) {

					if ( $this->is_enabled() ) {

						if ( $current_user_can_manage_woocommerce ) {

							// Bulk Actions
							add_filter( 'yith_wcmv_commissions_bulk_actions', 'YITH_Vendors_Gateway_PayPal_Payouts::commissions_bulk_actions' );
						}

						add_action( 'yith_paypal_payout_batch_change_status', array(
							$this,
							'change_commissions_status'
						), 10, 3 );

						add_action( 'yith_wcmv_vendor_panel_payments', array(
							$this,
							'add_vendor_panel_payments_options'
						), 10, 1 );
					}
					/* === Checkout Payment === */
					if ( $this->is_enabled_for_checkout() ) {

						add_action( 'woocommerce_order_status_changed', array( $this, 'process_credit' ), 30, 3 );
					}

					$exclude_vendor_product = get_option( 'yith_payouts_exclude_vendor_commission', 'yes' );

					if ( 'yes' === $exclude_vendor_product ) {

						add_filter( 'yith_payouts_include_item', array(
							$this,
							'remove_vendor_product_from_calculation'
						), 10, 2 );
					}

					add_filter( 'yith_payout_receiver_email', array( $this, 'return_paypal_email' ), 10, 2 );

				}
			}
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
		 * add payouts gateway options
		 * @author Salvatore Strano
		 * @return array
		 */
		public static function get_options_array() {

			$options = array(
				'paypal-payouts' => array(
					'paypal_payouts_options_start' => array(
						'type' => 'sectionstart',
					),

					'paypal_payouts_title' => array(
						'title' => __( 'PayPal Payouts Connect', 'yith-woocommerce-product-vendors' ),
						'type'  => 'title',
						'desc'  => __( 'Configure here your gateways in order to process the payment of commissions.', 'yith-woocommerce-product-vendors' ),
					),

					'paypal_payouts_enable_service' => array(
						'id'      => 'yith_wcmv_enable_paypal-payouts_gateway',
						'type'    => 'checkbox',
						'title'   => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
						'desc'    => __( 'Enable PayPal Payouts gateway', 'yith-woocommerce-product-vendors' ),
						'default' => 'no'
					),
					'payment_minimum_withdrawals'   => array(
						'id'                => 'yith_wcmv_paypal_payment_minimum_withdrawals',
						'type'              => 'number',
						'title'             => __( 'Minimum Withdrawal', 'yith-woocommerce-product-vendors' ) . ' ' . get_woocommerce_currency_symbol(),
						'desc'              => __( "Set the minimum value for commission withdrawals. This setting will update all vendors' accounts that still have a threshold lower than the one set.", 'yith-woocommerce-product-vendors' ),
						'custom_attributes' => array(
							'min' => 0
						),
						'default'           => 1
					),

					'paypal_payouts_options_end' => array(
						'type' => 'sectionend',
					),
				)
			);

			return $options;
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
			$actions = array_merge( array( 'paypal-payouts' => sprintf( "%s %s", _x( 'Pay with', "[Button Label]: Pay with PayPal", 'yith-woocommerce-product-vendors' ), 'PayPal Payouts' ) ), $actions );

			return $actions;
		}

		/**
		 * Add Stripe Connect Section
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @return array Stripe Connect option array
		 */
		public static function add_section( $sections ) {
			$sections['gateways']['paypal-payouts'] = __( 'PayPal Payouts', 'yith-woocommerce-product-vendors' );

			return $sections;
		}



		/**
		 *
		 * /**
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
		 * [paypal_email] => paypal_email
		 * )
		 *
		 *
		 * @return array
		 */
		public function pay( $payment_data ) {

			$result = array(
				'status'   => true,
				'messages' => ''
			);

			foreach ( $payment_data as $vendor_id => $pay_data ) {

				$amounts      = $pay_data['amount'];
				$paypal_email = $pay_data['paypal_email'];

				foreach ( $amounts as $currency => $amount ) {
					$sender_items   = array();
					$commission_ids = $pay_data['commission_ids'][ $currency ];

					$order    = YITH_Commission( $commission_ids[0] )->get_order();
					$order_id = $order->get_id();
					$log_args = array(
						'payment'        => array(
							'user_id'          => $pay_data['user_id'],
							'vendor_id'        => $vendor_id,
							'amount'           => $amount,
							'currency'         => $currency,
							'status'           => 'processing',
							'payment_date'     => $payment_data[ $vendor_id ]['payment_date'],
							'payment_date_gmt' => $payment_data[ $vendor_id ]['payment_date_gmt'],
							'gateway_id'       => $payment_data[ $vendor_id ]['gateway_id']
						),
						'commission_ids' => $commission_ids
					);

					//Create entry in Payments table
					$payment_id = YITH_Vendors()->payments->add_payment( $log_args );

					if ( $paypal_email == '' ) {

						continue;
					}


					$sender_items[] = array(
						'recipient_type' => 'EMAIL',
						'receiver'       => $paypal_email,
						'note'           => 'Thank you',
						'amount'         => array(
							'value'    => $amount,
							'currency' => $currency
						)
					);
					$payouts_args   = array(
						'sender_batch_id' => 'commission_' . $payment_id,
						'order_id'        => $order_id,
						'items'           => $sender_items,
						'payout_mode'     => 'commission'

					);

					YITH_PayOuts_Service()->register_payouts( $payouts_args );
					$payouts = YITH_PayOuts_Service()->PayOuts( array(
						'sender_batch_id' => 'commission_' . $payment_id,
						'sender_items'    => $sender_items
					) );


					foreach ( $commission_ids as $commission_id ) {
						$commission   = YITH_Commission( $commission_id );
						$commission->update_status('processing' );
                    }

				}
			}

			return $result;
		}


		/**
		 * register commission payout in Payout list
		 *
		 * @param $order_id
		 * @param $sender_batch_id
		 */
		public function register_commission_payout( $order_id, $sender_batch_id ) {

			$args = array(
				'order_id'        => $order_id,
				'payout_mode'     => 'commission',
				'sender_batch_id' => $sender_batch_id
			);

			YITH_Payout()->add( $args );

			$order = wc_get_order( $order_id );

			if( $order instanceof WC_Order ){
			    $order->add_meta_data( 'yith_payouts_sender_commission_id', $sender_batch_id, true);
			    $order->save_meta_data();
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

			$vendor_ids = array_keys( $pay_data );

			foreach ( $vendor_ids as $vendor_id ) {
				$vendor                                 = yith_get_vendor( $vendor_id );
				$pay_data[ $vendor_id ]['paypal_email'] = $vendor->paypal_email;
				$pay_data[ $vendor_id ]['gateway_id']   = $this->get_id();
			}

			return $pay_data;
		}

		/* === PAYMENT METHODS === */

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
			} elseif ( empty( $commission_ids ) ) {
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
		 * @param $payout_batch_id
		 * @param $status
		 * @param $batch_header
		 */
		public function change_commissions_status( $payout_batch_id, $status, $batch_header ) {

			$payment_id = $batch_header['sender_batch_header']['sender_batch_id'];

			//the sender batch id for this payment_id
			$payment_id = str_replace( 'commission_', '', $payment_id );

			if ( $payment_id > 0 ) {

				$commission_ids = YITH_Vendors()->payments->get_commissions_by_payment_id( $payment_id );

				$commission_status = 'processing';
				foreach ( $commission_ids as $commission_id ) {
					$commission   = YITH_Commission( $commission_id );
					$gateway_name = YITH_Vendors_Gateway( $this->gateway )->get_method_title();
					if ( 'success' === strtolower( $status ) ) {
						$commission_status = 'paid';
						$commission->update_status( 'paid', sprintf( __( 'Commission paid via %s ( batch ID: %s)', 'yith-woocommerce-product-vendors' ), $gateway_name, $payout_batch_id ) );
						$this->set_payment_post_meta( $commission );
					} elseif ( 'denied' == strtolower( $status ) ) {
						$commission_status = 'failed';
						$commission->update_status( 'paid', sprintf( __( 'Payment %s', 'yith-woocommerce-product-vendors' ), $gateway_name, $status ) );

					} else {
						$commission_status = 'processing';
					}
				}

				YITH_Vendors()->payments->update_payment_status( $payment_id, $commission_status );

			}
		}

		/**
		 * Process commissions at checkout
		 *
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 */
		public function process_credit( $order_id, $old_status, $new_status ) {

			//check if this is the parent order
			if ( wp_get_post_parent_id( $order_id ) == 0 ) {
				return false;
			}
			if ( 'completed' != $new_status ) {
				return false;
			}

			if ( 'manual' == get_option( 'yith_wcmv_paypal_payment_method' ) ) {
				return false;
			}

			$args           = array(
				'order_id' => $order_id,
				'status'   => 'all',
				'fields'   => 'ids'
			);
			$commission_ids = $this->get_commissions( $args );

			$pay_data = $this->get_pay_data( array( 'commission_ids' => $commission_ids ) );


			return $this->pay( $pay_data );
		}

		/**
		 * @param array $args ( order_id, status, fields )
		 *
		 * @return array
		 */
		public function get_commissions( $args ) {

			$commission_ids =  YITH_Commissions()->get_commissions( $args );

			if ( $this->is_threshold_enabled() ) {

			    $commission_threshold_ids = array();

			    $vendor_ids = array();
			    foreach( $commission_ids as $commission_id ){
			        $commission = YITH_Commission( $commission_id );

			        $vendor = $commission->get_vendor();

			        if( !in_array( $vendor->id, $vendor_ids ) ) {
				        $commission_threshold_ids = array_merge( $commission_threshold_ids, $vendor->get_unpaid_commissions_if_out_threshold() );
                        $vendor_ids[] = $vendor->id;
			        }
                }

                $commission_threshold_ids = array_unique( $commission_threshold_ids );
			    $commission_ids = $commission_threshold_ids ;


			}

			return $commission_ids;

		}

		/**
         * check if the threshold option is enabled
         * @author Salvatore Strano
		 * @return bool
		 */
		public function is_threshold_enabled() {

			$min = get_option( 'yith_wcmv_paypal_payment_minimum_withdrawals', 0 );

			return $min > 0;
		}

		/**
		 * remove vendor products from compute global payouts
		 * @author Salvatore Strano
		 * @since 1.0.0
		 *
		 * @param bool $include_product
		 * @param $item
		 *
		 * @return bool
		 */
		public function remove_vendor_product_from_calculation( $include_product, $item ) {

			$product_id = $item['product_id'];

			$vendor = yith_get_vendor( $product_id, 'product' );

			if ( $vendor->is_valid() ) {

				$include_product = false;
			}

			return $include_product;
		}

		/**
		 * @param $email
		 * @param $user_id
		 */
		public function return_paypal_email( $email, $user_id ) {

			$vendor = yith_get_vendor( $user_id, 'user' );

			if ( '' === $email ) {
				if ( $vendor->is_valid() ) {
					$email = $vendor->paypal_email;
				}

			}

			return $email;
		}


		public function add_vendor_panel_payments_options( $args = array() ) {

			$currency_symbol = get_woocommerce_currency_symbol();

			$step   = 'any';
			$min    = get_option( 'yith_wcmv_paypal_payment_minimum_withdrawals', 0 );
			$vendor = ! empty( $args['vendor'] ) && $args['vendor'] instanceof YITH_Vendor ? $args['vendor'] : yith_get_vendor( 'current', 'user' );

			if ( $min > 0 ) {
				/**
				 * @todo create a template and merge this feature with the MassPay gateway
				 */
				ob_start(); ?>
                <div class="form-field">
                    <label class="yith_vendor_payment_threshold"
                           for="yith_vendor_payment_threshold"><?php _e( 'Threshold', 'yith-woocommerce-product-vendors' ); ?></label>
                    <input type="number" class="payment-threshold-field" name="yith_vendor_data[threshold]"
                           id="yith_vendor_payment_threshold" value="<?php echo $vendor->threshold ?>"
                           min="<?php echo $min ?>"
                           step="<?php echo $step ?>" style="max-width:70px;"/>
					<?php echo $currency_symbol ?>
                    <br/>
                    <span class="description"><?php printf( '%s (%s: <strong>%s</strong>).',
							__( "Minimum vendor's earnings before vendor commissions can be paid", 'yith-woocommerce-product-vendors' ),
							__( 'Minimum threshold allowed by site administrator is', 'yith-woocommerce-product-vendors' ),
							wc_price( get_option( 'yith_wcmv_paypal_payment_minimum_withdrawals' ) )
						); ?></span>
                </div>
				<?php echo ob_get_clean();
			}
		}
	}
}