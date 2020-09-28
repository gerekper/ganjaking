<?php

/**
 * @since 1.7.0
 * @todo This controller is not currently used.  Will be implemented if users request re-sending notification functionality.
 */
class WC_Wishlists_Admin_Controller {
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Wishlists_Admin_Controller();
		}
	}

	private function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_handle_request' ) );
		add_action( 'admin_menu', array( $this, 'on_admin_menu' ) );
	}

	public function maybe_handle_request() {
		if ( isset( $_REQUEST['wc-wishlist-admin-action'] ) && WC_Wishlists_Plugin::verify_nonce( $_REQUEST['wc-wishlist-admin-action'] ) ) {

			$action = $_REQUEST['wc-wishlist-admin-action'];

			switch ( $action ) {
				case 'send-notifications':
					$this->handle_send_notifications();
					break;
			}

		}


	}

	public function on_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wishlist', __( 'Notifications', 'wc_wishlist' ), __( 'Notifications', 'wc_wishlist' ), 'manage_woocommerce', 'wc-wishlist-admin-notifications', array(
			$this,
			'do_notifications_page'
		) );
	}

	public function do_notifications_page() {

		include 'views/admin-notifications.php';

	}


	protected function handle_send_notifications() {
		$result = false;

		$cron = new WC_Wishlists_Cron();
		$cron->send_price_changes();

	}

}

