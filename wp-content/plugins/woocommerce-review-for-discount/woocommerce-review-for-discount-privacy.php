<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class SFN_ReviewDiscount_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Review for Discount', 'wc_review_discount' ) );

		$this->add_exporter( 'wc_review_discount', __( 'WooCommerce Review for Discount Data', 'wc_review_discount' ), array( $this, 'discount_exporter' ) );

		$this->add_eraser( 'wc_review_discount', __( 'WooCommerce Review for Discount Data', 'wc_review_discount' ), array( $this, 'discount_eraser' ) );
	}

	/**
	 * Returns a list sent coupons.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array
	 */
	protected function get_sent_coupons( $email_address, $page ) {
		global $wpdb;

		$limit = 10;

		$coupons = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}wrd_sent_coupons WHERE author_email = %s LIMIT %d, %d",
			$email_address,
			( $page - 1 ) * $limit,
			$limit
		) );

		return $coupons;
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'wc_review_discount' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-review-for-discount' ) );
	}

	/**
	 * Handle exporting data for Sent Coupons.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function discount_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();
		$sent_coupons   = $this->get_sent_coupons( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $sent_coupons ) ) {
			foreach ( $sent_coupons as $sent_coupon ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_review_for_discount',
					'group_label' => __( 'Review for Discount', 'wc_review_discount' ),
					'item_id'     => 'review-for-discount-' . $sent_coupon->comment_id . '-' . $sent_coupon->discount_id . '-' . $sent_coupon->coupon_id,
					'data'        => array(
						array(
							'name'  => __( 'Review for Discount number', 'wc_review_discount' ),
							'value' => $sent_coupon->comment_id . '-' . $sent_coupon->discount_id . '-' . $sent_coupon->coupon_id,
						),
						array(
							'name'  => __( 'Review for Discount comment', 'wc_review_discount' ),
							'value' => get_comment_text( $sent_coupon->comment_id ),
						),
						array(
							'name'  => __( 'Review for Discount coupon', 'wc_review_discount' ),
							'value' => get_the_title( $sent_coupon->coupon_id ),
						),
					),
				);
			}

			$done = 10 > count( $sent_coupons );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases sent coupons data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function discount_eraser( $email_address, $page ) {
		global $wpdb;

		$sent_coupons = $this->get_sent_coupons( $email_address, 1 );
		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( $sent_coupons as $sent_coupon ) {
			$wpdb->query(
				$wpdb->prepare( "DELETE FROM {$wpdb->prefix}wrd_sent_coupons WHERE comment_id = %d AND discount_id = %d AND coupon_id = %d",
				$sent_coupon->comment_id, $sent_coupon->discount_id, $sent_coupon->coupon_id
			) );

			$items_removed = true;
		}

		if ( $items_removed ) {
			$messages[] = __( 'PayFast Subscriptions Data Erased.', 'wc_review_discount' );
		}

		// Tell core if we have more rows to work on still
		$done = count( $sent_coupons ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}
}

new SFN_ReviewDiscount_Privacy();
