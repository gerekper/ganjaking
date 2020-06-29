<?php

/**
 * Class FUE_Addon_Warranty
 */
class FUE_Addon_Warranty {

	/**
	 * class constructor
	 */
	public function __construct() {
		if ( self::is_installed() ) {
			add_filter( 'fue_wc_storewide_triggers', array($this, 'add_triggers') );
			add_filter( 'fue_wc_product_triggers', array($this, 'add_triggers') );

			add_action( 'wc_warranty_status_updated', array($this, 'status_updated'), 10, 2 );
		}
	}

	/**
	 * Check if plugin in active
	 * @return bool
	 */
	public static function is_installed() {
		return class_exists('WC_Warranty');
	}

	/**
	 * Add warranty trigger to storewide and product emails
	 *
	 * @param array $triggers
	 *
	 * @return array
	 */
	public function add_triggers( $triggers ) {
		$triggers['warranty_status'] = __('after warranty status changes', 'wc_followup_emails');
		return $triggers;
	}

	/**
	 * Queue emails after an RMA's status changes
	 *
	 * @param int       $request_id
	 * @param string    $status
	 */
	public function status_updated( $request_id, $status ) {
		$order_id   = get_post_meta( $request_id, '_order_id', true );
		$triggers   = array('warranty_status');

		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'warranty_status'
				)
			)
		) );

		foreach ( $emails as $email ) {
			$interval   = (int)$email->interval_num;

			$insert = array(
				'send_on'       => $email->get_send_timestamp(),
				'email_id'      => $email->id,
				'user_id'       => 0,
				'order_id'      => $order_id,
				'is_cart'       => 0
			);

			if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				// Tell FUE that an email order has been created
				// to stop it from sending storewide emails
				if (! defined('FUE_ORDER_CREATED'))
					define('FUE_ORDER_CREATED', true);
			}

		}

	}

}

$GLOBALS['fue_warranty'] = new FUE_Addon_Warranty();
