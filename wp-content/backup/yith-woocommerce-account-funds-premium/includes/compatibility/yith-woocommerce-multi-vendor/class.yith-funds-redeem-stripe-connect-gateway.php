<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YITH_Redeem_Funds_Stripe_Connect {

	protected static $_instance;

	public function __construct() {


	}

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * redeem vendor fuds with Stripe Connect
	 * vendor_data is an array of array ( 'user_id', 'amount', 'stripe_user_id', 'currency' );
	 *
	 * @param array $vendor_data
	 *
	 * @throws Exception
	 *
	 */
	public function redeem( $vendor_data ) {

		/* === Gateway Requirements === */
		$api_handler            = YITH_Stripe_Connect_API_Handler::instance();
		$stripe_connect_gateway = YITH_Stripe_Connect_Gateway::instance();
		$results                = array();
		$success_vendors        = array();
		foreach ( $vendor_data as $vendor ) {

			$amount   = yith_wcsc_get_amount( $vendor['amount'] );
			$currency = strtolower( $vendor['currency'] );
			$args     = array(
				'amount'         => $amount,
				'currency'       => $currency,
				'destination'    => $vendor['stripe_user_id'],
				'description'    => _x( 'Redeem Funds','Is the description of Stripe transfer', 'yith-woocommerce-account-funds' ),
				'transfer_group' => 'redeem_funds_vendor_' . $vendor['user_id']
			);

			$balance = $api_handler->get_balance();

			$available_balance = false;
			if ( $balance instanceof \Stripe\Balance ) {

				foreach ( $balance->available as $currency_balance ) {

					if ( ( $vendor['currency'] == $currency_balance['currency'] ) && ( $amount <= $currency_balance['amount'] ) ) {
						$available_balance = true;
						break;
					}
				}
				$stripe_connect_commission = YITH_Stripe_Connect_Commissions::instance();
				$integration_item          = array(
					'plugin_integration' => YITH_FUNDS_SLUG,
					'redeem_funds'       => true
				);
				$sc_commission             = array(
					'user_id'          => $vendor['user_id'],
					'order_id'         => '',
					'order_item_id'    => '',
					'product_id'       => '',
					'commission'       => $vendor['amount'],
					'commission_type'  => 'fixed',
					'payment_retarded' => 0,
					'purchased_date'   => current_time( 'mysql' ),
					'integration_item' => maybe_serialize( $integration_item )
				);
				if ( $available_balance ) {

					$transfer = $api_handler->create_transfer( $args );
					$stripe_connect_gateway->log( 'info', sprintf( 'Payment ID: %s', 'redeem_funds_vendor_' . $vendor['user_id'] ) );
					$stripe_connect_gateway->log( 'info', sprintf( 'Destination ID: %s', $vendor['stripe_user_id'] ) );
					$stripe_connect_gateway->log( 'info', sprintf( 'User ID: %s', $vendor['user_id'] ) );
					if ( isset( $transfer['error_transfer'] ) ) {
						$message = sprintf( '%s', $transfer['error_transfer'] );
						$stripe_connect_gateway->log( 'error', sprintf( 'Stripe Error: %s', $message ) );
						$results[]               = $message;
						$sc_commission['status'] = 'sc_transfer_error';
						$notes                   = array(
							'transfer_id'         => '',
							'destination_payment' => '',
							'error_transfer'          => $message
						);

					} elseif ( $transfer instanceof \Stripe\Transfer ) {

						$fund_redeem             = apply_filters( 'yith_fund_redeem_base_currency', $vendor['amount'] );
						$customer                = new YITH_YWF_Customer( $vendor['user_id'] );
						$notes                   = array(
							'transfer_id'         => $transfer->id,
							'destination_payment' => $transfer->destination,
							'extra_info'          => __( 'Redeem vendor funds', 'yith-woocommerce-account-funds' )
						);
						$fund_log_args           = array(
							'type_operation' => 'redeem_vendor_funds',
							'description'    => sprintf( __( 'Redeem %s funds with Stripe Connect', 'yith-woocommerce-account-funds' ), wc_price( - $vendor['amount'] ) ),
						);
						$sc_commission['status'] = 'sc_transfer_success';
						$customer->add_funds_with_log( - $fund_redeem, $fund_log_args );
						$stripe_connect_gateway->log( 'info', sprintf( 'Stripe Success: %s', $fund_log_args['description'] ) );
						$success_vendors[] = $vendor;
					}


				} else {
					$message = __( 'Impossible redeem funds for now, please contact the site administrator for more details', 'yith-woocommerce-account-funds' );
					$stripe_connect_gateway->log( 'error', sprintf( 'Stripe Error: %s', __( 'You have insufficient funds in your Stripe account :', 'yith-woocommerce-account-funds' ) ) );
					$stripe_connect_gateway->log( 'error', sprintf( 'Destination ID: %s', $vendor['stripe_user_id'] ) );
					$stripe_connect_gateway->log( 'error', sprintf( 'User ID: %s', $vendor['user_id'] ) );
					$results[] = $message;
					$notes     = array(
						'transfer_id'         => '',
						'destination_payment' => '',
						'error_transfer'          => $message
					);
				}

				$sc_commission['note']   = maybe_serialize( $notes );
				$sc_commission['status'] = 'sc_transfer_error';

				$stripe_connect_commission->insert( $sc_commission );
			} else {
				$message   = __( 'Impossible redeem funds for now, please contact the site administrator for more details', 'yith-woocommerce-account-funds' );
				$results[] = $message;
			}


		}

		do_action( 'yith_funds_after_redeem_funds', $success_vendors );

		return $results;

	}
}

if ( ! function_exists( 'YITH_Redeem_Funds_with_Stripe_Connect' ) ) {

	function YITH_Redeem_Funds_with_Stripe_Connect() {
		return YITH_Redeem_Funds_Stripe_Connect::get_instance();
	}
}