<?php
/**
 * Class for handling the admin user views.
 *
 * @package WC_Account_Funds/Admin
 * @since   2.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Admin_Users class.
 */
class WC_Account_Funds_Admin_Users {

	/**
	 * Constructor.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		// Users table list.
		add_filter( 'manage_users_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_users_custom_column', array( $this, 'get_column_content' ), 10, 3 );

		// Edit user.
		add_action( 'show_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'user_meta_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta_fields' ) );
	}

	/**
	 * Adds custom columns to the users' table.
	 *
	 * @since 2.7.0
	 *
	 * @param array $columns Table columns.
	 * @return array
	 */
	public function add_columns( $columns ) {
		if ( wc_account_funds_current_user_can( 'view_user_funds' ) ) {
			$columns['account_funds'] = __( 'Account Funds', 'woocommerce-account-funds' );
		}

		return $columns;
	}

	/**
	 * Gets the content for the custom column of the users' table.
	 *
	 * @since 2.7.0
	 *
	 * @param string $content Column content.
	 * @param string $column  Column name.
	 * @param int    $user_id User ID.
	 * @return string
	 */
	public function get_column_content( $content, $column, $user_id ) {
		if ( 'account_funds' !== $column ) {
			return $content;
		}

		$funds = WC_Account_Funds_Manager::get_user_funds( $user_id );

		return wc_price( $funds );
	}

	/**
	 * Shows custom fields on the edit user pages.
	 *
	 * @since 2.7.0
	 *
	 * @param WP_User $user User object.
	 */
	public function user_meta_fields( $user ) {
		if ( ! wc_account_funds_current_user_can( 'edit_user_funds', $user->ID ) ) {
			return;
		}

		$funds = WC_Account_Funds_Manager::get_user_funds( $user->ID );

		include_once 'views/html-admin-user-meta-fields.php';
	}

	/**
	 * Shows the custom fields on the edit user pages.
	 *
	 * @since 2.7.0
	 *
	 * @param int $user_id User ID.
	 */
	public function save_user_meta_fields( $user_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( ! isset( $_POST['account_funds'] ) || ! wc_account_funds_current_user_can( 'edit_user_funds', $user_id ) ) {
			return;
		}

		$new_funds     = floatval( wc_clean( wp_unslash( $_POST['account_funds'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$current_funds = WC_Account_Funds_Manager::get_user_funds( $user_id );

		$funds_updated = WC_Account_Funds_Manager::set_user_funds( $user_id, $new_funds );

		// Send email to customer.
		if ( $funds_updated && $current_funds < $new_funds ) {
			$wc_emails = WC_Emails::instance();
			$email     = $wc_emails->emails['WC_Account_Funds_Email_Account_Funds_Increase'];

			$email->trigger( $user_id, $current_funds, $new_funds );
		}
	}
}

return new WC_Account_Funds_Admin_Users();
