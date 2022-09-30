<?php
/**
 * Plugin Name: YITH WooCommerce Membership Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-membership
 * Description: <code><strong>YITH WooCommerce Membership</strong></code> allows creating dedicated areas on your website/store where you can manage reserved access to your contents depending on what you want to show. Excellent to create online courses, study plans, paid areas, etc. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.15.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-membership
 * Domain Path: /languages/
 * WC requires at least: 6.5.0
 * WC tested up to: 6.7.x
 *
 * @author  yithemes
 * @package YITH WooCommerce Membership Premium
 * @version 1.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Free version deactivation if installed __________________

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCMBS_FREE_INIT', plugin_basename( __FILE__ ) );

function yith_wcmbs_pr_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Membership Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-membership' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

if ( ! defined( 'YITH_WCMBS_VERSION' ) ) {
	define( 'YITH_WCMBS_VERSION', '1.15.0' );
}

if ( ! defined( 'YITH_WCMBS_PREMIUM' ) ) {
	define( 'YITH_WCMBS_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCMBS_INIT' ) ) {
	define( 'YITH_WCMBS_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMBS' ) ) {
	define( 'YITH_WCMBS', true );
}

if ( ! defined( 'YITH_WCMBS_FILE' ) ) {
	define( 'YITH_WCMBS_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCMBS_URL' ) ) {
	define( 'YITH_WCMBS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMBS_DIR' ) ) {
	define( 'YITH_WCMBS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMBS_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCMBS_TEMPLATE_PATH', YITH_WCMBS_DIR . 'templates/premium' );
}

if ( ! defined( 'YITH_WCMBS_VIEWS_PATH' ) ) {
	define( 'YITH_WCMBS_VIEWS_PATH', YITH_WCMBS_DIR . 'views/' );
}

if ( ! defined( 'YITH_WCMBS_ASSETS_URL' ) ) {
	define( 'YITH_WCMBS_ASSETS_URL', YITH_WCMBS_URL . 'assets' );
}

if ( ! defined( 'YITH_WCMBS_ASSETS_PATH' ) ) {
	define( 'YITH_WCMBS_ASSETS_PATH', YITH_WCMBS_DIR . 'assets' );
}

if ( ! defined( 'YITH_WCMBS_INCLUDES_PATH' ) ) {
	define( 'YITH_WCMBS_INCLUDES_PATH', YITH_WCMBS_DIR . 'includes' );
}

if ( ! defined( 'YITH_WCMBS_LANGUAGES_PATH' ) ) {
	define( 'YITH_WCMBS_LANGUAGES_PATH', YITH_WCMBS_DIR . 'languages/' );
}

if ( ! defined( 'YITH_WCMBS_SLUG' ) ) {
	define( 'YITH_WCMBS_SLUG', 'yith-woocommerce-membership' );
}

if ( ! defined( 'YITH_WCMBS_SECRET_KEY' ) ) {
	define( 'YITH_WCMBS_SECRET_KEY', 'Ms2yxy33VWudPm2enaJ4' );
}

if ( ! defined( 'YITH_WCMBS_DEBUG' ) ) {
	define( 'YITH_WCMBS_DEBUG', false );
}


function yith_wcmbs_pr_init() {

	load_plugin_textdomain( 'yith-woocommerce-membership', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/objects/abstract.yith-wcmbs-cpt-object.php';
	require_once 'includes/objects/class.yith-wcmbs-plan.php';

	// Load required classes and functions
	require_once 'includes/admin/class.yith-wcmbs-admin-profile.php';
	require_once 'includes/admin/class.yith-wcmbs-admin-profile-premium.php';
	require_once 'includes/functions.yith-wcmbs.php';
	require_once 'includes/class.yith-wcmbs-cron.php';
	require_once 'includes/class.yith-wcmbs-membership.php';
	require_once 'includes/class.yith-wcmbs-membership-helper.php';
	require_once 'includes/class.yith-wcmbs-activity.php';
	require_once 'includes/class.yith-wcmbs-products-manager.php';
	require_once 'includes/class.yith-wcmbs-reports.php';
	require_once 'includes/reports/class.yith-wcmbs-download-reports-ajax-table.php';
	require_once 'includes/reports/class.yith-wcmbs-download-reports-by-user-table.php';
	require_once 'includes/reports/class.yith-wcmbs-download-reports-details-by-user-table.php';
	require_once 'includes/class.yith-wcmbs-members.php';
	require_once 'includes/class.yith-wcmbs-members-premium.php';
	require_once 'includes/class.yith-wcmbs-member.php';
	require_once 'includes/class.yith-wcmbs-member-premium.php';
	require_once 'includes/class.yith-wcmbs-manager.php';
	require_once 'includes/class.yith-wcmbs-manager-premium.php';
	require_once 'includes/class.yith-wcmbs-message.php';
	require_once 'includes/class.yith-wcmbs-messages-manager-admin.php';
	require_once 'includes/class.yith-wcmbs-messages-manager-frontend.php';
	require_once 'includes/class.yith-wcmbs-messages-widget.php';
	require_once 'includes/class.yith-wcmbs-notifier.php';
	require_once 'includes/class.yith-wcmbs-shortcodes.php';
	require_once 'includes/class.yith-wcmbs-protected-media.php';
	require_once 'includes/shipping/class.wc-shipping-membership-free-shipping.php';
	require_once 'includes/shipping/class.wc-shipping-membership-flat-rate.php';
	require_once 'includes/compatibility/class.yith-wcmbs-compatibility.php';
	require_once 'includes/compatibility/class.yith-wcmbs-wp-compatibility.php';
	require_once 'includes/class.yith-wcmbs-advanced-administration.php';
	require_once 'includes/class.yith-wcmbs-admin-assets.php';
	require_once 'includes/class.yith-wcmbs-admin-meta-boxes.php';
	require_once 'includes/class.yith-wcmbs-protected-links.php';
	require_once 'includes/class.yith-wcmbs-orders.php';
	require_once 'includes/class.yith-wcmbs-orders-premium.php';
	require_once 'includes/class.yith-wcmbs-ajax.php';

	require_once 'includes/class.yith-wcmbs-frontend.php';
	require_once 'includes/class.yith-wcmbs-frontend-premium.php';
	require_once 'includes/class.yith-wcmbs-admin.php';
	require_once 'includes/class.yith-wcmbs-admin-premium.php';
	require_once 'includes/class.yith-wcmbs.php';
	require_once 'includes/class.yith-wcmbs-settings.php';
	require_once 'includes/class.yith-wcmbs-install.php';

	// Builders.
	require_once 'includes/builders/class.yith-wcmbs-builders.php';

	// Let's start the game!
	YITH_WCMBS();

}

add_action( 'yith_wcmbs_pr_init', 'yith_wcmbs_pr_init' );


function yith_wcmbs_pr_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcmbs_pr_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcmbs_pr_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcmbs_pr_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );

/**
 * Activation Hooks
 * see also YITH_WCMBS_Install::install
 */
require_once 'includes/class.yith-wcmbs-downloads-report.php';
require_once 'includes/class.yith-wcmbs-legacy-elements.php';
require_once 'includes/class.yith-wcmbs-endpoints.php';
require_once 'includes/class.yith-wcmbs-post-types.php';

register_activation_hook( YITH_WCMBS_FILE, 'YITH_WCMBS_Legacy_Elements::check_for_legacy_elements' );
register_activation_hook( YITH_WCMBS_FILE, 'YITH_WCMBS_Endpoints::install' );
register_activation_hook( YITH_WCMBS_FILE, 'YITH_WCMBS_Post_Types::install' );
register_activation_hook( YITH_WCMBS_FILE, 'YITH_WCMBS_Post_Types::add_capabilities' );


/**
 * compatibility with dynamic pricing
 */
require_once( 'includes/compatibility/class.yith-wcmbs-dynamic-pricing-compatibility.php' );
add_filter( 'yit_ywdpd_pricing_rules_options', 'YITH_WCMBS_Dynamic_Pricing_Compatibility::add_membership_in_pricing_rules_options' );