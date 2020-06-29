<?php

/**
 * Class FUE_Addon_Woocommerce_Reports
 */
class FUE_Addon_Woocommerce_Reports {

	public function __construct() {
		$this->register_hooks();
	}

	private function register_hooks() {
		// Reports
		add_filter( 'fue_report_email_trigger', array($this, 'report_email_trigger'), 10, 2 );

		add_filter( 'fue_report_email_address', array($this, 'report_email_address'), 10, 2 );
		add_filter( 'fue_report_order_str', array($this, 'report_order_str'), 10, 2 );

		add_action( 'fue_reports_reset', array($this, 'reset_reports') );
	}

	public function report_email_trigger( $trigger, $email_row ) {
		$meta = '';

		if ( isset($email_row->meta) ) {
			$email_meta = maybe_unserialize( $email_row->meta );

			if ( $email_row->interval_type == 'order_total_above' && isset($email_meta['order_total_above']) ) {
				$meta = wc_price($email_meta['order_total_above']);
			} elseif ( $email_row->interval_type == 'order_total_below' && isset($email_meta['order_total_below']) ) {
				$meta = wc_price( $email_meta['order_total_below'] );
			}
		}

		return $trigger .' '. $meta;
	}

	public function report_email_address( $email, $report ) {

		if ( $report->order_id && $report->order_id != 0 ) {
			$order = WC_FUE_Compatibility::wc_get_order($report->order_id);
			$email = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
		}

		return $email;
	}

	/**
	 * Reset coupon logs
	 * @param array $data
	 */
	public function reset_reports( $data ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( $data['type'] == 'coupons' && $data['coupons_action'] == 'trash' ) {
			$coupon_ids_str = implode(',', array_map( 'absint', $data['coupon_id'] ) );

			/*foreach ( $data['coupon_id'] as $coupon_id ) {
				$wpdb->update( $wpdb->prefix . 'followup_coupons', array('usage_count' => 0), array('id' => $coupon_id) );
			}*/

			$wpdb->query("DELETE FROM {$wpdb->prefix}followup_coupon_logs WHERE id IN ($coupon_ids_str)");
		}
	}

	public static function report_order_str( $str, $report ) {
		if ( $report->order_id != 0 ) {
			$order      = WC_FUE_Compatibility::wc_get_order( $report->order_id );
			$str  = '<a href="'. get_admin_url() .'post.php?post='. $report->order_id .'&action=edit">View Order</a>';
		}

		return $str;
	}

}
