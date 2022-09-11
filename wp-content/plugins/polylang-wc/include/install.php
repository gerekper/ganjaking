<?php
/**
 * @package Polylang-WC
 */

/**
 * Activation / de-activation class compatible with multisite.
 * Based on PLL_Install_Base.
 *
 * @since 0.1
 */
class PLLWC_Install {
	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	protected $plugin_basename;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param string $plugin_basename Plugin basename.
	 */
	public function __construct( $plugin_basename ) {
		$this->plugin_basename = $plugin_basename;

		// Manages plugin activation and deactivation.
		register_activation_hook( $plugin_basename, array( $this, 'activate' ) );
		register_deactivation_hook( $plugin_basename, array( $this, 'deactivate' ) );

		// Blog creation on multisite.
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ), 5 ); // Before WP attempts to send mails which can break on some PHP versions.
	}

	/**
	 * Allows to detect the plugin deactivation.
	 *
	 * @since 0.1
	 *
	 * @return bool True if the plugin is currently beeing deactivated.
	 */
	public function is_deactivation() {
		return isset( $_GET['action'], $_GET['plugin'] ) && 'deactivate' === $_GET['action'] && $this->plugin_basename === $_GET['plugin'];  // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Activation or deactivation for all sites.
	 *
	 * @since 0.1
	 *
	 * @param string $what        Either 'activate' or 'deactivate'.
	 * @param bool   $networkwide True if the plugin is network activated, false otherwise.
	 * @return void
	 */
	protected function do_for_all_blogs( $what, $networkwide ) {
		if ( is_multisite() && $networkwide ) { // Network install.
			global $wpdb;

			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				'activate' === $what ? $this->_activate() : $this->_deactivate();
			}
			restore_current_blog();
		} else { // Single site.
			'activate' === $what ? $this->_activate() : $this->_deactivate();
		}
	}

	/**
	 * Plugin activation, multisite compatible.
	 *
	 * @since 0.1
	 *
	 * @param bool $networkwide True if the plugin is network activated, false otherwise.
	 * @return void
	 */
	public function activate( $networkwide ) {
		$this->do_for_all_blogs( 'activate', $networkwide );
	}

	/**
	 * Plugin activation on a single site.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function _activate() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Plugin deactivation, multisite compatible.
	 *
	 * @since 0.1
	 *
	 * @param bool $networkwide True if the plugin is network activated, false otherwise.
	 * @return void
	 */
	public function deactivate( $networkwide ) {
		$this->do_for_all_blogs( 'deactivate', $networkwide );
	}

	/**
	 * Plugin deactivation on a single site.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function _deactivate() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Site creation on multisite.
	 *
	 * @since 0.9.4
	 *
	 * @param int $blog_id Blog ID.
	 * @return void
	 */
	public function wpmu_new_blog( $blog_id ) {
		switch_to_blog( $blog_id );
		$this->_activate();
		restore_current_blog();
	}
}
