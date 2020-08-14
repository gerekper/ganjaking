<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YITH_Redeem_Funds_Payouts_Gateway {

	protected static $_instance;

	public function __construct() {

		add_action( 'yith_paypal_payout_item_before_change_status', array( $this, 'check_payout_status' ), 20, 3 );
	}

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * redeem vendor fuds with PayPal Payout
	 * vendor_data is an array of array ( 'user_id', 'amount', 'paypal_email', 'currency' );
	 * @param array $vendor_data
	 * @return array
	 * @throws Exception
	 *
	 */
	public function redeem(  $vendor_data  ) {

		$sender_items = array();

		foreach ( $vendor_data as $data ){
			$fund_redeem  = apply_filters( 'yith_fund_redeem_base_currency', $data['amount'] );
			$sender_items[] = array(
				'recipient_type' => 'EMAIL',
				'receiver'       => $data['paypal_email'],
				'note'           => _x( 'Redeem Funds','Is the subject email that is send by PayPal', 'yith-woocommerce-account-funds' ),
				'amount'         => array(
					'value'    => $fund_redeem,
					'currency' => $data['currency']
				),
				'sender_item_id' => 'redeem_funds_vendor_' . $data['user_id'],
			);
		}

		$sender_batch_id = 'redeem_funds_' . uniqid();
		$payouts_args    = array(
			'sender_batch_id' => $sender_batch_id,
			'order_id'        => '',
			'items'           => $sender_items,
			'payout_mode'     => 'redeem_funds'

		);

		YITH_PayOuts_Service()->register_payouts( $payouts_args );
		$batch = 	YITH_PayOuts_Service()->PayOuts( array(
			'sender_batch_id' => $sender_batch_id,
			'sender_items'    => $sender_items
		) );


		foreach ( $vendor_data as $data ) {
			$fund_redeem  = apply_filters( 'yith_fund_redeem_base_currency', $data['amount'] );
			$customer = new YITH_YWF_Customer( $data['user_id'] );

			$fund_log_args = array(
				'type_operation' => 'redeem_vendor_funds',
				'description'    => sprintf( __( 'Redeem %s funds with PayPal Payouts', 'yith-woocommerce-account-funds' ), wc_price( - $data['amount'] ) ),
			);

			$customer->add_funds_with_log( - $fund_redeem, $fund_log_args );
		}


		do_action('yith_funds_after_redeem_funds', $vendor_data );
		return array();
	}


	public function check_payout_status( $payout_item_id, $transaction_status, $resource ) {

		$sender_item_id  = isset( $resource['payout_item']['sender_item_id'] ) ? $resource['payout_item']['sender_item_id'] : '';
		$sender_item_id  = str_replace( 'redeem_funds_vendor_', '', $sender_item_id );
		$sender_batch_id = $resource['sender_batch_id'];
		$transaction_id  = isset( $resource['transaction_id'] ) ? $resource['transaction_id'] : '';
		$amount          = '';


		if ( $sender_item_id ) {

			$args        = array(
				'fields'          => array( 'amount', 'currency', 'transaction_status' ),
				'sender_batch_id' => $sender_batch_id,
				'sender_item_id'  => $resource['payout_item']['sender_item_id'],
			);
			$payout_item = YITH_Payout_Items::get_payout_items( $args );
			$old_status  = $payout_item['transaction_status'];
			$amount      = $payout_item['amount'];
			$currency    = $payout_item['currency'];

			switch ( $transaction_status ) {
				case 'success' :
				case 'pending':

					if ( ! in_array( $old_status, array(
						'success',
						'pending',
						'unprocessed',
						'onhold',
						'unclaimed'
					) ) ) {
						$customer = new YITH_YWF_Customer( $sender_item_id );

						$fund_log_args = array(
							'type_operation' => 'redeem_vendor_funds',
							'description'    => sprintf( __( 'Redeem %s funds with PayPal Payouts', 'yith-woocommerce-account-funds' ), wc_price( - $amount, array( 'currency' => $currency ) ) ),
						);

						$customer->add_funds_with_log( - $amount, $fund_log_args );
					}
					break;

				case 'refunded':
				case 'failed':
				case 'returned':
				case 'blocked':
					$customer = new YITH_YWF_Customer( $sender_item_id );

					$fund_log_args = array(
						'type_operation' => 'restore_vendor_funds',
						'description'    => sprintf( __( 'Restored %s funds because payout %s is %s', 'yith-woocommerce-account-funds' ), wc_price( $amount, array( 'currency' => $currency ) ), $transaction_id, $transaction_status ),
					);

					$customer->add_funds_with_log( $amount, $fund_log_args );
					break;

			}
		}
	}

}

if ( ! function_exists( 'YITH_Redeem_Funds_with_Payouts' ) ) {

	function YITH_Redeem_Funds_with_Payouts() {
		return YITH_Redeem_Funds_Payouts_Gateway::get_instance();
	}
}