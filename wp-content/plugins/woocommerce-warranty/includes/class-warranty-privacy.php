<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class Warranty_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Warranty', 'wc_warranty' ) );

		$this->add_exporter( 'woocommerce-warranty-data', __( 'WooCommerce Warranty Data', 'wc_warranty' ), array( $this, 'warranty_data_exporter' ) );

		$this->add_eraser( 'woocommerce-warranty-data', __( 'WooCommerce Warranty Data', 'wc_warranty' ), array( $this, 'warranty_data_eraser' ) );
	}

	/**
	 * Returns a list of warranties.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_warranties( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$warranty_query = array(
			'post_type'  => 'warranty_request',
			'meta_key'   => '_email',
			'meta_value' => $email_address,
			'limit'      => 10,
			'page'       => $page,
		);

		return get_posts( $warranty_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'wc_warranty' ), 'https://docs.woocommerce.com/privacy/?woocommerce-warranty' ) );
	}

	/**
	 * Handle exporting data for warranties.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function warranty_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();
		$warranties     = $this->get_warranties( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $warranties ) ) {
			foreach ( $warranties as $warranty ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_warranties',
					'group_label' => __( 'Warranties', 'wc_warranty' ),
					'item_id'     => 'warranty-' . $warranty->ID,
					'data'        => array(
						array(
							'name'  => __( 'Email', 'wc_warranty' ),
							'value' => get_post_meta( $warranty->ID, '_email', true ),
						),
						array(
							'name'  => __( 'First name', 'wc_warranty' ),
							'value' => get_post_meta( $warranty->ID, '_first_name', true ),
						),
						array(
							'name'  => __( 'Last name', 'wc_warranty' ),
							'value' => get_post_meta( $warranty->ID, '_last_name', true ),
						),
						array(
							'name'  => __( 'RMA code', 'wc_warranty' ),
							'value' => get_post_meta( $warranty->ID, '_code', true ),
						),
					),
				);
			}

			$done = 10 > count( $warranties );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases warranty data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function warranty_data_eraser( $email_address, $page ) {
		$warranties = $this->get_warranties( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $warranties as $warranty ) {
			list( $removed, $retained, $msgs ) = $this->maybe_handle_warranty( $warranty );
			$items_removed  |= $removed;
			$items_retained |= $retained;
			$messages        = array_merge( $messages, $msgs );
		}

		// Tell core if we have more warranties to work on still
		$done = count( $warranties ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to warranties
	 *
	 * @param WP_Post $warranty
	 * @return array
	 */
	protected function maybe_handle_warranty( $warranty ) {
		$warranty_id = $warranty->ID;

		$order_id      = get_post_meta( $warranty_id, '_order_id', true );
		$email         = get_post_meta( $warranty_id, '_email', true );
		$first_name    = get_post_meta( $warranty_id, '_first_name', true );
		$last_name     = get_post_meta( $warranty_id, '_last_name', true );

		if ( empty( $order_id ) && empty( $email ) && empty( $first_name ) && empty( $last_name ) ) {
			return array( false, false, array() );
		}

		delete_post_meta( $warranty_id, '_order_id' );
		delete_post_meta( $warranty_id, '_email' );
		delete_post_meta( $warranty_id, '_first_name' );
		delete_post_meta( $warranty_id, '_last_name' );

		return array( true, false, array( __( 'Warranty Order Data Erased.', 'wc_warranty' ) ) );
	}
}

new Warranty_Privacy();
