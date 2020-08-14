<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendors_Gateway_Account_Funds' ) ) {
	/**
	 * YITH Gateway Account Funds
	 *
	 * Define methods and properties for class that manages payments via Account Funds

	 */
	class YITH_Vendors_Gateway_Account_Funds extends YITH_Vendors_Gateway {

		/**
		 * @var string gateway slug
		 */
		protected $_id = 'account-funds';

		/**
		 * @var string gateway name
		 */
		protected $_method_title = 'Account Funds';

		public function __construct( $gateway  ) {
			$this->set_is_external( true );
			$this->set_is_available_on_checkout( true );

			$current_user_can_manage_woocommerce  = current_user_can( 'manage_woocommerce' );

			$is_external_args = array(
				'check_method'   => 'class_exists',
				'check_for'      => 'YITH_YWF_Customer',
				'plugin_url'     => '//yithemes.com/themes/plugins/yith-woocommerce-account-funds/',
				'plugin_name'    => 'YITH WooCommerce Account Funds',
				'min_version'    => '1.3.0',
				'plugin_version' => defined( 'YITH_FUNDS_VERSION' ) ? YITH_FUNDS_VERSION : 0
			);

			$this->set_external_args( $is_external_args );

			parent::__construct( $gateway );


			if( $this->is_external_plugin_enabled() ) {
				/* === Admin Panel === */
				add_filter( 'yith_wcmv_panel_gateways_options', 'YITH_Vendors_Gateway_Account_Funds::add_section_options' );
				add_filter( 'yith_wcmv_panel_sections', 'YITH_Vendors_Gateway_Account_Funds::add_section' );

				if( $this->is_enabled() ){
					if ( $current_user_can_manage_woocommerce ) {
						add_filter( 'yith_wcmv_commissions_bulk_actions', 'YITH_Vendors_Gateway_Account_Funds::commissions_bulk_actions' );
					}

					/* === Checkout Payment === */
					if ( $this->is_enabled_for_checkout() ) {

						add_action( 'woocommerce_order_status_changed', array( $this, 'process_credit' ), 30, 3 );
					}
				}
			}
		}

		/**
		 * Add Account Funds Section
		 *
		 * @author YITH
		 *
		 * @return array Stripe Connect option array
		 */
		public static function add_section( $sections ) {
			$sections['gateways']['account-funds'] = __( 'Account Funds', 'yith-woocommerce-product-vendors' );

			return $sections;
		}

		/**
		 * Add  Account Funds options array from this plugin.
		 *
		 * @author YITH
		 *
		 * @return array Stripe Connect option array
		 */
		public static function add_section_options( $options ) {
			return array_merge( $options, self::get_options_array() );
		}

		/**
		 * add account funds gateway options
		 * @author Salvatore Strano
		 * @return array
		 */
		public static function get_options_array() {

			$options = array(
				'account-funds' => array(
					'account_funds_options_start' => array(
						'type' => 'sectionstart',
					),

					'account_funds_title' => array(
						'title' => __( 'Account Funds', 'yith-woocommerce-product-vendors' ),
						'type'  => 'title',
						'desc'  => __( 'Configure your gateways here so you can process the payment of vendor commissions.', 'yith-woocommerce-product-vendors' ),
					),

					'account_funds_enable_service' => array(
						'id'      => 'yith_wcmv_enable_account-funds_gateway',
						'type'    => 'checkbox',
						'title'   => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
						'desc'    => __( 'Enable Account Funds gateway', 'yith-woocommerce-product-vendors' ),
						'default' => 'no'
					),

					'account_funds_options_end' => array(
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
			$actions = array_merge( array( 'account-funds' => sprintf( "%s %s", _x( 'Pay with', "[Button Label]: Pay with your Account Funds", 'yith-woocommerce-product-vendors' ), 'Account Funds' ) ), $actions );

			return $actions;
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
		 * Process the commission payment
		 * @author YITH
		 * @param array $payment_detail
		 *
		 * @return array
		 */
		public function pay( $payment_detail ) {

			$result = array(
				'status'   => true,
				'messages' => ''
			);
			foreach ( $payment_detail as $vendor_id => $pay_data ) {
				$user_id = $payment_detail[ $vendor_id ]['user_id'];
				$customer = new YITH_YWF_Customer( $user_id );
				$amounts           = $payment_detail[ $vendor_id ]['amount'];
				foreach( $amounts as $currency => $amount ){

					$commission_ids = $payment_detail[ $vendor_id ]['commission_ids'][ $currency ];
					$order    = YITH_Commission( $commission_ids[0] )->get_order();
					$amount  = round( $amount, 2 );
					$log_args = array(
						'payment' => array(
							'vendor_id'        => $vendor_id,
							'user_id'          => $user_id,
							'amount'           => $amount,
							'currency'         => $currency,
							'status'           => 'processing',
							'payment_date'     => $payment_detail[ $vendor_id ]['payment_date'],
							'payment_date_gmt' => $payment_detail[ $vendor_id ]['payment_date_gmt'],
							'gateway_id'       => $this->get_id()
						),

						'commission_ids' => $commission_ids
					);

					//Create entry in Payments table
					$payment_id = YITH_Vendors()->payments->add_payment( $log_args );

					//Add funds in the vendor's account

					$funds_to_add = apply_filters( 'yith_admin_deposit_funds', $amount, $order->get_id() );
					$old_fund = $customer->get_funds();
					$customer->add_funds( $funds_to_add );
					$new_fund = $customer->get_funds();

					if( ( $old_fund+$funds_to_add ) === $new_fund ){
						$status = 'paid';
						$message = __( 'Payment correctly issued through the selected gateway', 'yith-woocommerce-product-vendors' );
					}else{
						$status = 'failed';
						$message = '';
						$customer->set_funds($old_fund);
					}

					YITH_Vendors()->payments->update_payment_status( $payment_id, $status );
					$commissions_message = '';

					foreach ( $commission_ids as $commission_id ) {
						$commission = YITH_Commission( $commission_id );
						if ( $commission->exists() ) {
							$order    = $commission->get_order();

							if ( 'paid' == $status ) {
								$commission->update_status( $status, '', true );

								$this->set_payment_post_meta( $commission );
								$gateway_payment_message = sprintf( "%s. %s %s", $message, _x( 'Paid via', '[Note]: Paid through gateway X', 'yith-woocommerce-product-vendors' ), $this->get_method_title() );
								$commission->add_note( urldecode( $gateway_payment_message ) );

								$commissions_message .= sprintf('#%s,', $commission_id );
							}
						}
					}

					if( 'paid' == $status  ){

						$fund_log_args = array(
							'user_id' => $user_id,
							'fund_user' => $funds_to_add,
							'type_operation' => 'commission',
							'description' => sprintf( __('Added %s funds for these commissions %s', 'yith-woocommerce-account-funds'), wc_price( $amount, array('currency' => $currency ) ),  $commissions_message  ),
							'order_id' => $order->get_id()
						);

						do_action( 'ywf_add_user_log', $fund_log_args );
					}


				}
			}
			return $result;
		}

		/**
		 * @param int $order_id
		 * @param string $old_status
		 * @param string $new_status
		 * @return  boolean|array
		 */
		public function process_credit( $order_id, $old_status, $new_status ){

			if( wp_get_post_parent_id( $order_id ) == 0 ){
				return false;
			}

			$order = wc_get_order( $order_id );
			if ( !$order->has_status( array( 'completed','processing' ) ) ) {
				return false;
			}

			$args           = array(
				'order_id' => $order_id,
				'status'   => 'all',
				'fields'   => 'ids'
			);
			$commission_ids = YITH_Commissions()->get_commissions( $args );
			$pay_data = $this->get_pay_data( array( 'commission_ids' => $commission_ids ) );

			return $this->pay( $pay_data );
		}
	}
}