<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Box Office installer and updater.
 *
 * @since 1.1.0
 */
class WC_Box_Office_Updater {

	/**
	 * List of update callbacks from version-to-version.
	 *
	 * @var array
	 */
	protected $_updates = array(
		'1.1.0' => 'wcbo_update_110',
	);

	/**
	 * Install the plugin. Called by handler of register_activation_hook.
	 */
	public function install() {
		// In case activated without WooCommerce activated.
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$this->install_my_ticket_page();
		$this->install_default_settings();
		$this->update_version();

		// During plugin activation endpoint needs to be registered here before
		// flushing the rewrite.
		add_rewrite_endpoint( 'my-tickets', EP_ROOT | EP_PAGES );

		flush_rewrite_rules();
	}

	/**
	 * Install my ticket page.
	 *
	 * @return void
	 */
	public function install_my_ticket_page() {
		if ( ! function_exists( 'wc_create_page' ) ) {
			require_once( WC()->plugin_path() . '/includes/admin/wc-admin-functions.php' );
		}

		wc_create_page(
			esc_sql( _x( 'my-ticket', 'Page slug', 'woocommerce-box-office' ) ),
			'box_office_my_ticket_page_id',
			_x( 'My Ticket', 'Page title', 'woocommerce-box-office' ),
			'[my_ticket]'
		);
	}

	/**
	 * Install default settings.
	 *
	 * @return void
	 */
	public function install_default_settings() {
		add_option( 'box_office_enable_ticket_printing', 'no' );
		add_option( 'box_office_enable_ticket_emails', 'no' );
		add_option( 'box_office_enable_logging', 'no' );
	}

	/**
	 * Update version on DB. Called by version check routine that's called when
	 * plugin is loaded.
	 *
	 * @see WC_Account_Funds::version_check
	 *
	 * @param string $version Version to store in DB
	 *
	 * @return void
	 */
	public function update_version( $version = null ) {
		if ( is_null( $version ) ) {
			$version = WCBO()->_version;
		}
		delete_option( 'woocommerce_box_office_version' );
		add_option( 'woocommerce_box_office_version', $version );
	}

	/**
	 * Check for update based on current plugin's version versus installed
	 * version. Perform update routine if version mismatches.
	 *
	 * @param string $current_version Plugin's version
	 *
	 * @return void
	 */
	public function update_check( $current_version ) {
		$installed_version = get_option( 'woocommerce_box_office_version' );
		if ( $current_version !== $installed_version ) {
			$this->_update( $installed_version );
		}
	}

	/**
	 * Perform update.
	 *
	 * @param string $installed_version Installed version
	 *
	 * @return void
	 */
	protected function _update( $installed_version ) {
		require_once( WCBO()->dir . 'includes/wcbo-update-functions.php' );

		foreach ( $this->_updates as $version => $callback ) {
			if ( version_compare( $installed_version, $version, '<' ) && is_callable( $callback ) ) {
				call_user_func( $callback );
				$this->update_version( $version );
			}
		}
	}
}

