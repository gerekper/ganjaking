<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Help_Scout_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Help Scout', 'woocommerce-help-scout' ) );

		$this->add_exporter( 'woocommerce-help-scout-customer-data', __( 'WooCommerce Help Scout Customer Data', 'woocommerce-help-scout' ), array( $this, 'customer_data_exporter' ) );

		$this->add_eraser( 'woocommerce-help-scout-customer-data', __( 'WooCommerce Help Scout Customer Data', 'woocommerce-help-scout' ), array( $this, 'customer_data_eraser' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-help-scout' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-help-scout' ) );
	}

	/**
	 * Finds and exports customer data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_exporter( $email_address, $page ) {
		$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$data_to_export = array();

		if ( $user instanceof WP_User ) {
			$data_to_export[] = array(
				'group_id'    => 'woocommerce_customer',
				'group_label' => __( 'Customer Data', 'woocommerce-help-scout' ),
				'item_id'     => 'user',
				'data'        => array(
					array(
						'name'  => __( 'Help Scout customer id', 'woocommerce-help-scout' ),
						'value' => get_user_meta( $user->ID, '_help_scout_customer_id', true ),
					),
				),
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Finds and erases customer data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_eraser( $email_address, $page ) {
		$page = (int) $page;
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$customer_id = get_user_meta( $user->ID, '_help_scout_customer_id', true );

		$items_removed  = false;
		$messages       = array( __( 'Help Scout Personal Data erased.', 'woocommerce-help-scout' ) );

		if ( ! empty( $customer_id ) ) {
			$items_removed = true;
			delete_user_meta( $user->ID, '_help_scout_customer_id' );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => true,
		);
	}
}

new WC_Help_Scout_Privacy();
