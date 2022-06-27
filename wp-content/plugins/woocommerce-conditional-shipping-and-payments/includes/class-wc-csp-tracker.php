<?php
/**
 * WCS_CSP_Tracker class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracking module.
 *
 * @class    WCS_CSP_Tracker
 * @version  1.14.0
 */
class WCS_CSP_Tracker {

	/**
	 * Initialize the Tracker.
	 */
	public static function init() {
		if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			add_filter( 'woocommerce_tracker_data', array( __CLASS__, 'add_tracking_data' ), 10 );
		}
	}

	/**
	 * Adds CSP data to the WC tracked data.
	 *
	 * @param  array  $data
	 * @return array
	 */
	public static function add_tracking_data( $data ) {

		$integration_data = self::get_integration_data();
		$restriction_data = self::get_global_restriction_data();
		$product_data     = self::get_product_data();

		$data[ 'extensions' ][ 'wc_csp' ][ 'integrations' ] = $integration_data;
		$data[ 'extensions' ][ 'wc_csp' ][ 'restrictions' ] = $restriction_data[ 'restrictions' ];
		$data[ 'extensions' ][ 'wc_csp' ][ 'conditions' ]   = $restriction_data[ 'conditions' ];
		$data[ 'extensions' ][ 'wc_csp' ][ 'products' ]     = $product_data;

		return $data;
	}

	/**
	 * Gets integration data.
	 *
	 * @return array
	 */
	private static function get_integration_data() {

		$integrations = array(
			'subscriptions'   => 'no',
			'memberships'     => 'no',
			'giftcards'       => 'no',
			'blocks'          => 'no',
			'amazon_payments' => 'no',
			'klarna_checkout' => 'no',
			'klarna_payments' => 'no',
			'currency'        => 'no',
			'paypal_ppec'     => 'no',
			'stripe'          => 'no',
		);

		foreach ( $integrations as $integration_key => &$is_integration_enabled ) {
			$is_integration_enabled = WC_CSP_Compatibility::is_module_loaded( $integration_key ) ? 'yes' : 'no';
		}

		return $integrations;
	}

	/**
	 * Gets global restriction insights.
	 *
	 * @return array
	 */
	private static function get_global_restriction_data() {

		$global_restrictions      = WC_CSP()->restrictions->get_restrictions();
		$global_restrictions_data = array(
			'restrictions' => array(),
			'conditions'   => array()
		);

		foreach ( $global_restrictions as $restriction_id => $restriction ) {

			$show_excluded_count         = 0;
			$show_excluded_notices_count = 0;
			$custom_notices_count        = 0;

			$rules = $restriction->get_global_restriction_data( 'edit' );

			if ( ! empty( $rules ) && is_array( $rules ) ) {

				foreach ( $rules as $rule_data ) {

					if ( ! empty( $rule_data[ 'show_excluded' ] ) && 'yes' === $rule_data[ 'show_excluded' ] ) {
						$show_excluded_count++;
						if ( ! empty( $rule_data[ 'show_excluded_notices' ] ) && 'yes' === $rule_data[ 'show_excluded_notices' ] ) {
							$show_excluded_notices_count++;
						}
					}

					if ( ! empty( $rule_data[ 'message' ] ) ) {
						$custom_notices_count++;
					}

					if ( ! empty( $rule_data[ 'conditions' ] ) ) {

						foreach ( $rule_data[ 'conditions' ] as $condition_data ) {

							if ( ! empty( $condition_data[ 'condition_id' ] ) ) {

								$condition_id = $condition_data[ 'condition_id' ];

								if ( ! isset( $global_restrictions_data[ 'conditions' ][ $condition_id . '_count' ] ) ) {
									$global_restrictions_data[ 'conditions' ][ $condition_id . '_count' ] = 1;
								} else {
									$global_restrictions_data[ 'conditions' ][ $condition_id . '_count' ]++;
								}
							}
						}
					}
				}
			}

			$global_restrictions_data[ 'restrictions' ][ $restriction_id ] = array(
				'count'                       => is_array( $rules ) ? count( $rules ) : 0,
				'show_excluded_count'         => $show_excluded_count,
				'show_excluded_notices_count' => $show_excluded_notices_count,
				'custom_notices_count'        => $custom_notices_count,
			);
		}

		return $global_restrictions_data;
	}

	/**
	 * Gets product data.
	 *
	 * @return array
	 */
	private static function get_product_data() {

		global $wpdb;

		return array(
			'products_count'                   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->posts}` WHERE `post_type` = 'product' AND `post_status` NOT IN ( 'trash', 'auto-draft' )" ),
			'products_with_restrictions_count' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->posts}` AS posts INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_wccsp_restrictions' WHERE posts.post_type = 'product' AND posts.post_status NOT IN ( 'trash', 'auto-draft' )" ),
		);
	}
}

WCS_CSP_Tracker::init();
