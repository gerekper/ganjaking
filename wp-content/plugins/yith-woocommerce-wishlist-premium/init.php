<?php
/**
 * Plugin Name: YITH WooCommerce Wishlist Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-wishlist/
 * Description: <code><strong>YITH WooCommerce Wishlist</strong></code> gives your users the possibility to create, fill, manage and share their wishlists allowing you to analyze their interests and needs to improve your marketing strategies. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 3.26.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-wishlist
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! defined( 'YITH_WCWL' ) ) {
	define( 'YITH_WCWL', true );
}

if ( ! defined( 'YITH_WCWL_URL' ) ) {
	define( 'YITH_WCWL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_DIR' ) ) {
	define( 'YITH_WCWL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_INC' ) ) {
	define( 'YITH_WCWL_INC', YITH_WCWL_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCWL_INIT' ) ) {
	define( 'YITH_WCWL_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWL_SLUG' ) ) {
	define( 'YITH_WCWL_SLUG', 'yith-woocommerce-wishlist' );
}

if ( ! defined( 'YITH_WCWL_SECRET_KEY' ) ) {
	define( 'YITH_WCWL_SECRET_KEY', 'ky18RdyseqSoSPgdungS' );
}

if ( ! defined( 'YITH_WCWL_PREMIUM' ) ) {
	define( 'YITH_WCWL_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCWL_PREMIUM_INIT' ) ) {
	define( 'YITH_WCWL_PREMIUM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! function_exists( 'yith_wcwl_install_plugin_fw' ) ) {
	/**
	 * Install plugin-fw when needed
	 *
	 * @since 3.9.0
	 */
	function yith_wcwl_install_plugin_fw() {
		if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCWL_DIR . 'plugin-fw/init.php' ) ) {
			require_once YITH_WCWL_DIR . 'plugin-fw/init.php';
		}
		yit_maybe_plugin_fw_loader( YITH_WCWL_DIR );
	}
}

if ( ! function_exists( 'yith_wcwl_register_activation' ) ) {
	/**
	 * Performs required action on activation hook
	 *
	 * @since 3.9.0
	 */
	function yith_wcwl_register_activation() {
		if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
			require_once 'plugin-fw/yit-plugin-registration-hook.php';
		}
		register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

		if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
			include_once 'plugin-upgrade/functions-yith-licence.php';
		}
		register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );
	}
}

if ( ! function_exists( 'yith_wishlist_constructor' ) ) {
	/**
	 * Bootstrap function; loads all required dependencies and start the process
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function yith_wishlist_constructor() {

		load_plugin_textdomain( 'yith-woocommerce-wishlist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		// Load required classes and functions.
		require_once YITH_WCWL_INC . 'data-stores/class-yith-wcwl-wishlist-data-store.php';
		require_once YITH_WCWL_INC . 'data-stores/class-yith-wcwl-wishlist-item-data-store.php';
		require_once YITH_WCWL_INC . 'tables/class-yith-wcwl-popular-table.php';
		require_once YITH_WCWL_INC . 'tables/class-yith-wcwl-popular-table-premium.php';
		require_once YITH_WCWL_INC . 'tables/class-yith-wcwl-users-popular-table.php';
		require_once YITH_WCWL_INC . 'tables/class-yith-wcwl-users-popular-table-premium.php';
		require_once YITH_WCWL_INC . 'functions-yith-wcwl.php';
		require_once YITH_WCWL_INC . 'legacy/functions-yith-wcwl-legacy.php';
		require_once YITH_WCWL_INC . 'legacy/class-yith-wcwl-deprecated-hooks.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-exception.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-form-handler.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-form-handler-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-form-handler-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-ajax-handler.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-ajax-handler-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-ajax-handler-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-session.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-cron.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-cron-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-cron-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-wishlist.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-wishlist-item.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-wishlist-factory.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-frontend.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-frontend-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-frontend-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-install.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-shortcode.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-shortcode-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-shortcode-premium.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-emails.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-emails-extended.php';
		require_once YITH_WCWL_INC . 'class-yith-wcwl-emails-premium.php';

		// load widget classes.
		require_once YITH_WCWL_INC . 'widgets/class-yith-wcwl-widget.php';
		require_once YITH_WCWL_INC . 'widgets/class-yith-wcwl-items-widget.php';

		// load admin classes.
		if ( is_admin() ) {
			require_once YITH_WCWL_INC . 'class-yith-wcwl-admin.php';
			require_once YITH_WCWL_INC . 'class-yith-wcwl-admin-extended.php';
			require_once YITH_WCWL_INC . 'class-yith-wcwl-admin-premium.php';
		}

		// Let's start the game!

		/**
		 * $yith_wcwl global was deprecated since 3.0.0
		 *
		 * @deprecated
		 */
		global $yith_wcwl;

		$yith_wcwl = YITH_WCWL_Premium();
	}
}

if ( ! function_exists( 'yith_wishlist_install' ) ) {
	/**
	 * Performs pre-flight checks, and gives green light for plugin bootstrap
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function yith_wishlist_install() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcwl_install_woocommerce_admin_notice' );
		} else {
			if ( ! function_exists( 'yith_deactivate_plugins' ) ) {
				require_once 'plugin-fw/yit-deactive-plugin.php';
			}

			yith_deactivate_plugins( array( 'YITH_WCWL_FREE_INIT', 'YITH_WCWL_EXTENDED_INIT' ) );

			/**
			 * DO_ACTION: yith_wcwl_init
			 *
			 * Allows the plugin initialization.
			 */
			do_action( 'yith_wcwl_init' );
		}
	}
}

if ( ! function_exists( 'yith_wcwl_deactivate_lower_tier_notice' ) ) {
	/**
	 * Print an admin notice if trying to activate this version when an higher tier is already enabled
	 *
	 * @return void
	 * @use    admin_notices hooks
	 * @since  1.0
	 */
	function yith_wcwl_deactivate_lower_tier_notice() {
		?>
		<div class="notice">
			<p><?php esc_html_e( 'YITH WooCommerce Wishlist was deactivated as you\'re running an higher tier version of the same plugin.', 'yith-woocommerce-wishlist' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_wcwl_install_woocommerce_admin_notice' ) ) {
	/**
	 * Shows admin notice when plugin is activated without WooCommerce
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function yith_wcwl_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php echo esc_html( 'YITH WooCommerce Wishlist ' . __( 'is enabled but not effective. It requires WooCommerce to work.', 'yith-woocommerce-wishlist' ) ); ?></p>
		</div>
		<?php
	}
}

yith_wcwl_register_activation();
yith_wcwl_install_plugin_fw();

add_action( 'plugins_loaded', 'yith_wishlist_install', 11 );
add_action( 'yith_wcwl_init', 'yith_wishlist_constructor' );
