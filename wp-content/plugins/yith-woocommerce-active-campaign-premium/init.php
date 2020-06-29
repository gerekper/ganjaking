<?php
/**
 * Plugin Name: YITH Active Campaign for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-active-campaign/
 * Description: <code><strong>YITH Active Campaign for WooCommerce</strong></code> allows you to manage and create forms to register to Active Campaign helping you to outline users through tags and groups in a dynamic way. You will have a perfect system to send emails with a percentage of conversion higher than the average. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 2.0.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-active-campaign
 * Domain Path: /languages/
 * WC requires at least: 3.8.0
 * WC tested up to: 4.1
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.21
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCAC' ) ) {
	define( 'YITH_WCAC', true );
}

if ( ! defined( 'YITH_WCAC_VERSION' ) ) {
	define( 'YITH_WCAC_VERSION', '2.0.0' );
}

if ( ! defined( 'YITH_WCAC_DB_VERSION' ) ) {
	define( 'YITH_WCAC_DB_VERSION', '2.0.0' );
}

if ( ! defined( 'YITH_WCAC_PREMIUM' ) ) {
	define( 'YITH_WCAC_PREMIUM', true );
}

if ( ! defined( 'YITH_WCAC_URL' ) ) {
	define( 'YITH_WCAC_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAC_DIR' ) ) {
	define( 'YITH_WCAC_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAC_INC' ) ) {
	define( 'YITH_WCAC_INC', YITH_WCAC_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCAC_INIT' ) ) {
	define( 'YITH_WCAC_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAC_PREMIUM_INIT' ) ) {
	define( 'YITH_WCAC_PREMIUM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCAC_SLUG' ) ) {
	define( 'YITH_WCAC_SLUG', 'yith-active-campaign-for-woocommerce' );
}

if ( ! defined( 'YITH_WCAC_SECRET_KEY' ) ) {
	define( 'YITH_WCAC_SECRET_KEY', '12345' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCAC_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCAC_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCAC_DIR );

if ( ! function_exists( 'yith_active_campaign_constructor' ) ) {
	function yith_active_campaign_constructor() {
		load_plugin_textdomain( 'yith-woocommerce-active-campaign', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// temporarily disable Guzzle
		// require_once( YITH_WCAC_DIR . 'vendor/autoload.php' );

		require_once( YITH_WCAC_INC . 'functions.yith-wcac.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-api.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-deep-data.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-deep-data-register.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-carts-waiting-queue.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-background-process.php' );
		require_once( YITH_WCAC_INC . 'class.yith-wcac-widget.php' );

		// Let's start the game
		YITH_WCAC();
		YITH_WCAC_Deep_Data();

		if ( is_admin() ) {
			require_once( YITH_WCAC_INC . 'class.yith-wcac-admin.php' );

			YITH_WCAC_Admin();
		}
	}
}
add_action( 'yith_wcac_init', 'yith_active_campaign_constructor' );

if ( ! function_exists( 'yith_active_campaign_install' ) ) {
	function yith_active_campaign_install() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! function_exists( 'yit_deactive_free_version' ) ) {
			require_once 'plugin-fw/yit-deactive-plugin.php';
		}
		yit_deactive_free_version( 'YITH_WCAC_FREE_INIT', plugin_basename( __FILE__ ) );

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcac_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_wcac_init' );
		}
	}
}
add_action( 'plugins_loaded', 'yith_active_campaign_install', 11 );

if ( ! function_exists( 'yith_wcac_install_woocommerce_admin_notice' ) ) {
	function yith_wcac_install_woocommerce_admin_notice() {
		?>
        <div class="error">
            <p><?php echo sprintf( __( '%s is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-active-campaign' ), 'YITH Active Campaign for WooCommerce' ); ?></p>
        </div>
		<?php
	}
}

if ( ! function_exists( 'yith_wcac_install_free_admin_notice' ) ) {
	function yith_wcac_install_free_admin_notice() {
		?>
        <div class="error">
            <p><?php echo sprintf( __( 'You can\'t activate the free version of %s while you are using the premium one.', 'yith-woocommerce-active-campaign' ), 'YITH Active Campaign for WooCommerce' ); ?></p>
        </div>
		<?php
	}
}