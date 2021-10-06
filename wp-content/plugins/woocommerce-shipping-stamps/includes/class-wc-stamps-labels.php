<?php
/**
 * Label class.
 *
 * @package WC_Stamps_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper to abstract labels CRUD.
 */
class WC_Stamps_Labels {

	/**
	 * Create a label.
	 *
	 * @param WC_Order $order        Order object.
	 * @param object   $label_result Label result creation from API.
	 *
	 * @return int|WP_Error
	 */
	public static function create_label( $order, $label_result ) {
		$label                  = array();
		$label['post_type']     = 'wc_stamps_label';
		$label['post_status']   = 'publish';
		$label['ping_status']   = 'closed';
		$label['post_author']   = 1;
		$label['post_password'] = uniqid( 'label_' );
		$label['post_parent']   = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
		$label['post_title']    = isset( $label_result->TrackingNumber ) ? $label_result->TrackingNumber : '';
		$label['post_content']  = isset( $label_result->URL ) ? $label_result->URL : '';
		$label_id               = wp_insert_post( $label, true );

		if ( is_wp_error( $label_id ) ) {
			return $label_id;
		}

		// Store rate as meta.
		foreach ( (array) $label_result->Rate as $key => $value ) {
			update_post_meta( $label_id, $key, $value );
		}

		update_post_meta( $label_id, 'StampsTxID', $label_result->StampsTxID );

		return $label_id;
	}

	/**
	 * Delete a label.
	 *
	 * @param int $label_id Label ID.
	 *
	 * @return bool
	 */
	public static function delete_label( $label_id ) {
		if ( 'wc_stamps_label' === get_post_type( $label_id ) ) {
			wp_delete_post( $label_id, true );
			return true;
		}
		return false;
	}

	/**
	 * Get labels attached to an order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array List of labels.
	 */
	public static function get_order_labels( $order_id ) {
		$label_ids = get_posts( array(
			'posts_per_page' => -1,
			'post_type'      => 'wc_stamps_label',
			'fields'         => 'ids',
			'post_parent'    => $order_id,
		) );
		$labels = array();
		foreach ( $label_ids as $id ) {
			$labels[] = new WC_Stamps_Label( $id );
		}
		return $labels;
	}

}
