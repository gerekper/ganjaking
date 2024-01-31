<?php
/**
 * Plugin Name: Essential Addons for Elementor - Pro
 * Description: Supercharge your Elementor page building experience with Essential Addons PRO. Get your hands on exclusive elements such as Instagram Feed, Protected Content, Smart Post List, and many more.
 * Plugin URI: https://essential-addons.com/elementor/
 * Author: WPDeveloper
 * Version: 5.8.7
 * Author URI: https://www.wpdeveloper.com
 * Text Domain: essential-addons-elementor
 * Domain Path: /languages
 *
 * WC tested up to: 8.4.0
 * Elementor tested up to: 3.19
 * Elementor Pro tested up to: 3.19
 */
update_option( 'essential-addons-elementor-license-status', 'valid' );
update_option( 'essential-addons-elementor-license-key', 'activated' );
set_transient( 'essential-addons-elementor-license_data', [ 'license' => 'valid' ] );
if (!defined('WPINC')) {
    exit;
}

/**
 * Defining plugin constants.
 *
 * @since 3.0.0
 */
define('EAEL_PRO_PLUGIN_FILE', __FILE__);
define('EAEL_PRO_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('EAEL_PRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('EAEL_PRO_PLUGIN_URL', plugins_url('/', __FILE__));
define('EAEL_PRO_PLUGIN_VERSION', '5.8.7');
define('EAEL_STORE_URL', 'https://api.wpdeveloper.com/');
define('EAEL_SL_ITEM_ID', 4372);
define('EAEL_SL_ITEM_SLUG', 'essential-addons-elementor');
define('EAEL_SL_ITEM_NAME', 'Essential Addons for Elementor');

/**
 * Including autoloader.
 *
 * @since 3.0.0
 */
require_once EAEL_PRO_PLUGIN_PATH . 'autoload.php';

/**
 * Run plugin before lite version
 *
 * @since 3.0.0
 */
add_action('eael/before_init', function () {
    // compatibility with lite
    if (version_compare(EAEL_PLUGIN_VERSION, '4.6.3', '<=')) {
        return;
    }

    /**
     * Including plugin config.
     *
     * @since 3.0.0
     */
    $GLOBALS['eael_pro_config'] = require_once EAEL_PRO_PLUGIN_PATH . 'config.php';

    \Essential_Addons_Elementor\Pro\Classes\Bootstrap::instance();
});

/**
 * Plugin updater
 *
 * @since v3.0.4
 */
add_action('plugins_loaded', function () {
    $migration = new \Essential_Addons_Elementor\Pro\Classes\Migration;
    $migration->plugin_updater();
});

/**
 * Plugin migrator
 *
 * @since v3.0.0
 */
add_action('wp_loaded', function () {
    $migration = new \Essential_Addons_Elementor\Pro\Classes\Migration;
    $migration->migrator();
});

/**
 * Activation hook
 *
 * @since v3.0.0
 */
register_activation_hook(__FILE__, function () {
    $migration = new \Essential_Addons_Elementor\Pro\Classes\Migration;
    $migration->plugin_activation_hook();
});

/**
 * Deactivation hook
 *
 * @since v3.0.0
 */
register_deactivation_hook(__FILE__, function () {
    $migration = new \Essential_Addons_Elementor\Pro\Classes\Migration;
    $migration->plugin_deactivation_hook();
});

/**
 * Upgrade hook
 *
 * @since v3.0.0
 */
add_action('upgrader_process_complete', function ($upgrader_object, $options) {
    $migration = new \Essential_Addons_Elementor\Pro\Classes\Migration;
    $migration->plugin_upgrade_hook($upgrader_object, $options);
}, 10, 2);

/**
 * Admin Notices
 *
 * @since v3.0.0
 */
add_action('admin_notices', function () {
    $notice = new \Essential_Addons_Elementor\Pro\Classes\Notice;
    $notice->failed_to_load();
});

/**
 * WooCommerce HPOS Support
 * 
 * @since v5.4.13
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );