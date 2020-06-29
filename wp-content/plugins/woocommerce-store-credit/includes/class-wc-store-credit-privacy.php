<?php
/**
 * Privacy/GDPR related functionality.
 *
 * @package WC_Store_Credit
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Privacy class.
 */
class WC_Store_Credit_Privacy extends WC_Abstract_Privacy {

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::__construct( __( 'Store Credit', 'woocommerce-store-credit' ) );

		$this->add_exporter( 'woocommerce-store-credit-coupon-data', __( 'WooCommerce Store Credit Coupon Data', 'woocommerce-store-credit' ), array( $this, 'coupon_data_exporter' ) );
		$this->add_eraser( 'woocommerce-store-credit-coupon-data', __( 'WooCommerce Store Credit Coupon Data', 'woocommerce-store-credit' ), array( $this, 'coupon_data_eraser' ) );
	}

	/**
	 * Gets the store credit coupons associated to the specified email.
	 *
	 * @since 3.0.0
	 *
	 * @param string $email_address E-mail address.
	 * @param int    $page          Pagination of data.
	 * @return array
	 */
	protected function get_coupons( $email_address, $page ) {
		$limit  = 10;
		$offset = ( $page - 1 ) * $limit;

		$coupons = wc_store_credit_get_customer_coupons( $email_address, 'all' );

		return array_slice( $coupons, $offset, $limit );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 * @since 3.0.0
	 */
	public function get_privacy_message() {
		/* translators: %s marketplace privacy URL */
		$privacy_message = _x(
			'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>',
			'privacy message',
			'woocommerce-store-credit'
		);

		return wpautop( sprintf( $privacy_message, esc_url( 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-store-credit' ) ) );
	}

	/**
	 * Handles exporting data for store credit coupons.
	 *
	 * @since 3.0.0
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page          Pagination of data.
	 * @return array An array of personal data in name value pairs
	 */
	public function coupon_data_exporter( $email_address, $page ) {
		$coupons        = $this->get_coupons( $email_address, (int) $page );
		$done           = true;
		$data_to_export = array();

		if ( ! empty( $coupons ) ) {
			foreach ( $coupons as $coupon ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_coupons',
					'group_label' => _x( 'Coupons', 'data exporter: group label', 'woocommerce-store-credit' ),
					'item_id'     => 'coupon-' . $coupon->get_id(),
					'data'        => $this->get_coupon_personal_data( $coupon, $email_address ),
				);
			}

			$done = 10 > count( $coupons );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases store credit coupons data by email address.
	 *
	 * @sinc 3.0.0
	 *
	 * @param string $email_address Email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function coupon_data_eraser( $email_address, $page ) {
		$coupons         = $this->get_coupons( $email_address, (int) $page );
		$erasure_enabled = wc_string_to_bool( get_option( 'woocommerce_erasure_request_removes_order_data', 'no' ) );
		$response        = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		if ( ! empty( $coupons ) ) {
			foreach ( $coupons as $coupon ) {
				if ( apply_filters( 'wc_store_credit_privacy_erase_coupon_personal_data', $erasure_enabled, $coupon ) ) {
					$this->remove_coupon_personal_data( $coupon, $email_address );

					/* Translators: %s Coupon code. */
					$response['messages'][]    = sprintf( __( 'Removed personal data from coupon %s.', 'woocommerce-store-credit' ), $coupon->get_code() );
					$response['items_removed'] = true;
				} else {
					/* Translators: %s Coupon code. */
					$response['messages'][]     = sprintf( __( 'Personal data within coupon %s has been retained.', 'woocommerce-store-credit' ), $coupon->get_code() );
					$response['items_retained'] = true;
				}
			}

			$response['done'] = 10 > count( $coupons );
		}

		return $response;
	}

	/**
	 * Gets the personal data (key/value pairs) for a store credit coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $email  Email address.
	 * @return array
	 */
	protected function get_coupon_personal_data( $coupon, $email ) {
		$data = array(
			array(
				'name'  => _x( 'Coupon code', 'data exporter: data label', 'woocommerce-store-credit' ),
				'value' => $coupon->get_code(),
			),
			array(
				'name'  => _x( 'Coupon amount', 'data exporter: data label', 'woocommerce-store-credit' ),
				'value' => $coupon->get_amount(),
			),
			array(
				'name'  => _x( 'Allowed emails', 'data exporter: data label', 'woocommerce-store-credit' ),
				'value' => $email,
			),
		);

		/**
		 * Filters the personal data for a store credit coupon.
		 *
		 * @since 3.0.0
		 *
		 * @param array     $data   An array with the personal data in name value pairs.
		 * @param WC_Coupon $coupon Coupon object.
		 * @param string    $email  Email address.
		 */
		return apply_filters( 'wc_store_credit_privacy_export_coupon_personal_data', $data, $coupon, $email );
	}

	/**
	 * Removes the personal data from the store credit coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $email  Email address.
	 */
	protected function remove_coupon_personal_data( $coupon, $email ) {
		$allowed_emails = $coupon->get_email_restrictions( 'edit' );
		$allowed_emails = array_diff( $allowed_emails, array( $email ) );

		// Remove the coupon if it doesn't have more allowed emails.
		if ( empty( $allowed_emails ) ) {
			wp_delete_post( $coupon->get_id(), true );
		} else {
			$coupon->set_email_restrictions( $allowed_emails );
			$coupon->save();
		}
	}
}

new WC_Store_Credit_Privacy();
