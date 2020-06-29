<?php
/**
 * Updater for 1.0.2.
 *
 * @package WC_Shipping_Aramex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update routine for 1.0.2 where shipping zone support is initially supported.
 *
 * @since 1.6.5
 */
class WC_Shipment_Tracking_Updater_1_6_5 extends WC_Shipment_Tracking_Updater {
	/**
	 * {@inheritdoc}
	 */
	public function update() {
		$this->log_debug( 'Performing Shipment Tracking update routine for 1.6.5.' );

		$order_ids = $this->get_order_ids_with_legacy_meta();

		$this->log_debug( sprintf( 'Found %s orders to update.', count( $order_ids ) ) );

		foreach ( $order_ids as $order_id ) {
			$this->convert_old_meta_in_order( $order_id );
		}

		return true;
	}

	/**
	 * Convert old meta in a given order ID to new meta structure.
	 *
	 * @param int $order_id Order ID.
	 */
	private function convert_old_meta_in_order( $order_id ) {
		$this->log_debug( sprintf( 'Updating legacy meta in order #%s.', $order_id ) );

		$tracking_provider        = get_post_meta( $order_id, '_tracking_provider', true );
		$custom_tracking_provider = get_post_meta( $order_id, '_custom_tracking_provider', true );
		$tracking_number          = get_post_meta( $order_id, '_tracking_number', true );
		$custom_tracking_link     = get_post_meta( $order_id, '_custom_tracking_link', true );
		$date_shipped             = get_post_meta( $order_id, '_date_shipped', true );

		if ( empty( $tracking_provider ) && ! empty( $custom_tracking_provider ) ) {
			$tracking_provider = $custom_tracking_provider;
		}

		wc_st_add_tracking_number(
			$order_id,
			$tracking_number,
			$tracking_provider,
			$date_shipped,
			$custom_tracking_link
		);

		delete_post_meta( $order_id, '_tracking_number' );
		delete_post_meta( $order_id, '_tracking_provider' );
		delete_post_meta( $order_id, '_custom_tracking_provider' );
		delete_post_meta( $order_id, '_custom_tracking_link' );
		delete_post_meta( $order_id, '_date_shipped' );
	}

	/**
	 * Get order IDs with legacy meta.
	 *
	 * @return array Order IDs.
	 */
	private function get_order_ids_with_legacy_meta() {
		global $wpdb;

		return $wpdb->get_col(
			"
			SELECT DISTINCT( m.post_id ) FROM {$wpdb->postmeta} m
			LEFT JOIN {$wpdb->posts} p ON m.post_id = p.ID
			WHERE
				m.meta_key = '_tracking_number' AND
				m.meta_value != '' AND
				p.post_type = 'shop_order'
			ORDER BY p.ID DESC;
			"
		);
	}
}
