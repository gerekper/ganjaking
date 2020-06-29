<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Velocities extends WC_AF_Rule {

	private $is_enabled  = false;
    private $rule_weight = 0;

	/**
	 * The constructor
	 */
	public function __construct() {
		$this->is_enabled  =  get_option('wc_af_attempt_count_check');
		$this->rule_weight = get_option('wc_settings_anti_fraud_order_attempt_weight');
		$this->time_stamp  = get_option('wc_settings_anti_fraud_attempt_time_span');
		$this->max_orders = get_option('wc_settings_anti_fraud_max_order_attempt_time_span');
		parent::__construct( 'velocities', 'IP address ordered multiple orders in the last '.$this->time_stamp.' hours.', $this->rule_weight );
	}

	/**
	 * Add the date range condition to the SQL
	 *
	 * @param string $where
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function date_range( $where = '' ) {
		$where .= " AND ( `post_date` >= '" . $this->start_datetime_string . "' AND `post_date` <= '" . $this->end_datetime_string . "' )";

		return $where;
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 *
	 * @param WC_Order $order
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_risk( WC_Order $order ) {
		global $wpdb;

		// Default risk is false
		$risk = false;

		$pre_wc_30  = version_compare( WC_VERSION, '3.0', '<' );
		$order_id   = $pre_wc_30 ? $order->id : $order->get_id();
		$order_date = $pre_wc_30 ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
		$order_ip   = $pre_wc_30 ? get_post_meta( $order_id, '_customer_ip_address', true ) : $order->get_customer_ip_address();

		if ( empty( $order_ip ) ) {
			return false;
		}

		// Calculate the new datetime
		$dt = new DateTime( $order_date );
		$dt->modify( '-'.$this->time_stamp.' hours' );

		// Set the start and send datetime strings
		$this->start_datetime_string = $dt->format( 'Y-m-d H:i:s' );
		$this->end_datetime_string   = $order_date;

		// For WC 3.0, `date_before` and `date_after` will take care of this filter.
		if ( $pre_wc_30 ) {
			// Add date range filter
			add_filter( 'posts_where', array( $this, 'date_range' ) );
		}

		// Get the Same IP Orders
		$velocities_orders = wc_get_orders(
			array(
				'exclude'             => array( $order_id ),
				'customer_ip_address' => $order_ip,
				'type'                => wc_get_order_types( 'order-count' ),
				'date_after'          => $this->start_datetime_string,
				'date_before'         => $this->end_datetime_string,
			)
		);
		
		if ( $pre_wc_30 ) {
			// Remove date range filter
			remove_filter( 'posts_where', array( $this, 'date_range' ) );
		}

		// Check if there are orders with same IP
		if ( count( $velocities_orders ) >= $this->max_orders ) {
			$risk = true;
		}

		return $risk;
	}
	//Enable rule check
	public function is_enabled(){
		if('yes' == $this->is_enabled){
			return true;
		}
		return false;
	}
}
