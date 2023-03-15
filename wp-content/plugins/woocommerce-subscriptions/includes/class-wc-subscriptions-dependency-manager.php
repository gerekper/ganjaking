<?php
/**
 * WC_Subscriptions_Dependency_Manager class
 *
 * @package WooCommerce Subscriptions
 * @since 5.0.0
 */

defined( 'ABSPATH' ) || exit;

class WC_Subscriptions_Dependency_Manager {

	/**
	 * The minimum supported WooCommerce version.
	 *
	 * @var string
	 */
	private $minimum_supported_wc_version;

	/**
	 * Constructor.
	 */
	public function __construct( $minimum_supported_wc_version ) {
		$this->minimum_supported_wc_version = $minimum_supported_wc_version;
	}

	/**
	 * Checks if the required dependencies are met.
	 *
	 * @since 5.0.0
	 * @return bool True if the required dependencies are met. Otherwise, false.
	 */
	public function has_valid_dependencies() {
		return $this->is_woocommerce_active() && $this->is_woocommerce_version_supported();
	}

	/**
	 * Determines if the WooCommerce plugin is active.
	 *
	 * @since 5.0.0
	 * @return bool True if the plugin is active, false otherwise.
	 */
	public function is_woocommerce_active() {
		if ( class_exists( 'WooCommerce' ) ) {
			return true;
		}

		// Load plugin.php if it's not already loaded.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Check if WC is installed at woocommerce/woocommerce.php.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		}

		$wc_active = false;

		// Check if WC is installed at any directory. eg xyz/woocommerce.php.
		foreach ( get_plugins() as $plugin_slug => $plugin_data ) {
			if ( $this->is_plugin_slug( 'woocommerce', $plugin_slug ) && is_plugin_active( $plugin_slug ) ) {
				$wc_active = true;
				break;
			}
		}

		return $wc_active;
	}

	/**
	 * Determines if the WooCommerce version is supported by Subscriptions.
	 *
	 * The minimum supported WooCommerce version is defined in the WC_Subscriptions::$wc_minimum_supported_version property.
	 *
	 * @return bool true if the WooCommerce version is supported, false otherwise.
	 */
	public function is_woocommerce_version_supported() {
		$wc_version = 0;

		if ( defined( 'WC_VERSION' ) ) {
			$wc_version = WC_VERSION;
		} elseif ( function_exists( 'WC' ) ) {
			$wc_version = WC()->version;
		} else {
			foreach ( get_plugins() as $plugin_slug => $plugin_data ) {
				// We need to check that this is the active plugin, because get_plugins() returns all plugins (including inactive ones).
				if ( $this->is_plugin_slug( 'woocommerce', $plugin_slug ) && is_plugin_active( $plugin_slug ) ) {
					$wc_version = $plugin_data['Version'];
					break;
				}
			}
		}

		if ( empty( $wc_version ) ) {
			return false;
		}

		return version_compare( $wc_version, $this->minimum_supported_wc_version, '>=' );
	}

	/**
	 * Checks if a given plugin slug is for a specific plugin.
	 *
	 * Helpful when the plugin is installed in a directory other than the plugin name.
	 * eg works for situations like:
	 * - woocommerce/woocommerce.php
	 * - woocommerce-trunk/woocommerce.php
	 * - woocommerce-7.1.0/woocommerce.php
	 *
	 * @param string $plugin_name The plugin name. eg 'woocommerce'.
	 * @param string $plugin_slug The plugin slug. eg 'woocommerce/woocommerce.php'.
	 *
	 * @return bool True if the plugin slug is for the given plugin name. Otherwise, false.
	 */
	private function is_plugin_slug( $plugin_name, $plugin_slug ) {
		return "/$plugin_name.php" === substr( $plugin_slug, -strlen( "/$plugin_name.php" ) );
	}

	/**
	 * Displays an admin notice if the required dependencies are not met.
	 *
	 * @since 5.0.0
	 */
	public function display_dependency_admin_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$admin_notice_content = '';

		if ( ! $this->is_woocommerce_active() ) {
			$install_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => 'woocommerce',
					),
					admin_url( 'update.php' )
				),
				'install-plugin_woocommerce'
			);

			// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin
			$admin_notice_content = sprintf( esc_html__( '%1$sWooCommerce Subscriptions is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for WooCommerce Subscriptions to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'woocommerce-subscriptions' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
		} elseif ( ! $this->is_woocommerce_version_supported() ) {
			// translators: 1$-2$: opening and closing <strong> tags, 3$: minimum supported WooCommerce version, 4$-5$: opening and closing link tags, leads to plugin admin
			$admin_notice_content = sprintf( esc_html__( '%1$sWooCommerce Subscriptions is inactive.%2$s This version of Subscriptions requires WooCommerce %3$s or newer. Please %4$supdate WooCommerce to version %3$s or newer &raquo;%5$s', 'woocommerce-subscriptions' ), '<strong>', '</strong>', $this->minimum_supported_wc_version, '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '</a>' );
		}

		if ( $admin_notice_content ) {
			echo '<div class="error">';
			echo '<p>' . wp_kses_post( $admin_notice_content ) . '</p>';
			echo '</div>';
		}
	}
}
