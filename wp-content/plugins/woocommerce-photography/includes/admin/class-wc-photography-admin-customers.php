<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Admin Customers.
 *
 * @package  WC_Photography/Admin/Customers
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Admin_Customers {

	/**
	 * Initialize the admin customers actions.
	 */
	public function __construct() {
		// Customer fields.
		add_action( 'user_new_form', array( $this, 'customer_fields' ) );
		add_action( 'show_user_profile', array( $this, 'customer_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'customer_fields' ) );

		// Save customer fields.
		add_action( 'user_register', array( $this, 'save_customer_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_customer_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_customer_fields' ) );
	}

	/**
	 * Customer fields.
	 *
	 * @return string
	 */
	public function customer_fields( $user ) {
		$collections = array();

		if ( is_object( $user ) ) {
			$_collections = get_user_meta( $user->ID, '_wc_photography_collections', true );
			$_collections = is_array( $_collections ) ? $_collections : array();

			foreach ( $_collections as $collection_id ) {
				$collection = get_term( $collection_id, 'images_collections' );

				if ( ! is_object( $collection ) ) {
					continue;
				}

				$collections[ $collection_id ] = html_entity_decode( $collection->name );
			}
		}

		include_once( 'views/html-customer-profile-fields.php' );
	}

	/**
	 * Save customer fields.
	 *
	 * @param  int $user_id
	 *
	 * @return void
	 */
	public function save_customer_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$collections = array();

		if ( isset( $_POST['collections'] ) ) {

			if ( is_array( $_POST['collections'] ) ) {
				$collections = array_map( 'absint', $_POST['collections'] );
			} elseif ( '' != $_POST['collections'] ) {
				$collections = explode( ',', $_POST['collections'] );
				$collections = array_filter( array_map( 'absint', $collections ) );
			}

			// Test for new collections and send the notification.
			$old_collections = get_user_meta( $user_id, '_wc_photography_collections', true );
			$old_collections = is_array( $old_collections ) ? $old_collections : array();
			$new_collections = array_filter( array_diff( $collections, $old_collections ) );

			if ( $new_collections ) {
				$this->trigger_new_collection_notification( $user_id, $new_collections );
			}
		}

		update_user_meta( $user_id, '_wc_photography_collections', $collections );
	}

	/**
	 * Trigger new collection email notification.
	 *
	 * @param  int   $user_id
	 * @param  array $collections
	 *
	 * @return void
	 */
	protected function trigger_new_collection_notification( $user_id, $collections ) {
		$mailer       = WC()->mailer();
		$notification = $mailer->emails['WC_Email_Photography_New_Collection'];
		$notification->trigger( $user_id, $collections );
	}
}

new WC_Photography_Admin_Customers();
