<?php
/**
 * Init global
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'YITH_WCBK' ) ) {
	define( 'YITH_WCBK', true );
}

if ( ! defined( 'YITH_WCBK_URL' ) ) {
	define( 'YITH_WCBK_URL', plugin_dir_url( YITH_WCBK_FILE ) );
}

if ( ! defined( 'YITH_WCBK_DIR' ) ) {
	define( 'YITH_WCBK_DIR', plugin_dir_path( YITH_WCBK_FILE ) );
}

if ( ! defined( 'YITH_WCBK_DOMPDF_DIR' ) ) {
	define( 'YITH_WCBK_DOMPDF_DIR', YITH_WCBK_DIR . 'lib/dompdf/' );
}

if ( ! defined( 'YITH_WCBK_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCBK_TEMPLATE_PATH', YITH_WCBK_DIR . 'templates/' );
}

if ( ! defined( 'YITH_WCBK_VIEWS_PATH' ) ) {
	define( 'YITH_WCBK_VIEWS_PATH', YITH_WCBK_DIR . 'views/' );
}

if ( ! defined( 'YITH_WCBK_ASSETS_URL' ) ) {
	define( 'YITH_WCBK_ASSETS_URL', YITH_WCBK_URL . 'assets' );
}

if ( ! defined( 'YITH_WCBK_ASSETS_PATH' ) ) {
	define( 'YITH_WCBK_ASSETS_PATH', YITH_WCBK_DIR . 'assets' );
}

if ( ! defined( 'YITH_WCBK_LANGUAGES_PATH' ) ) {
	define( 'YITH_WCBK_LANGUAGES_PATH', YITH_WCBK_DIR . 'languages/' );
}

if ( ! defined( 'YITH_WCBK_INCLUDES_PATH' ) ) {
	define( 'YITH_WCBK_INCLUDES_PATH', YITH_WCBK_DIR . 'includes' );
}

if ( ! defined( 'YITH_WCBK_REST_API_PATH' ) ) {
	define( 'YITH_WCBK_REST_API_PATH', YITH_WCBK_INCLUDES_PATH . '/rest-api' );
}

if ( ! defined( 'YITH_WCBK_MODULES_PATH' ) ) {
	define( 'YITH_WCBK_MODULES_PATH', YITH_WCBK_DIR . 'modules/' );
}

if ( ! defined( 'YITH_WCBK_MODULES_URL' ) ) {
	define( 'YITH_WCBK_MODULES_URL', YITH_WCBK_URL . 'modules/' );
}

if ( ! defined( 'YITH_WCBK_SLUG' ) ) {
	define( 'YITH_WCBK_SLUG', 'yith-woocommerce-booking' );
}

if ( ! defined( 'YITH_WCBK_PREMIUM_LANDING_URL' ) ) {
	define( 'YITH_WCBK_PREMIUM_LANDING_URL', 'https://yithemes.com/themes/plugins/yith-woocommerce-booking' );
}

if ( ! defined( 'YITH_WCBK_SECRET_KEY' ) ) {
	define( 'YITH_WCBK_SECRET_KEY', 'pJaiF0sH1JraDv721O9m' );
}

if ( ! defined( 'YITH_WCBK_PLUGIN_NAME' ) ) {
	define( 'YITH_WCBK_PLUGIN_NAME', 'YITH Booking and Appointment for WooCommerce' );
}

/**
 * Print admin notice if WooCommerce is not enabled
 */
function yith_wcbk_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p>
			<?php
			// translators: %s is the plugin name.
			echo esc_html( sprintf( __( '%s is enabled but not effective. It requires WooCommerce in order to work.', 'yith-booking-for-woocommerce' ), YITH_WCBK_PLUGIN_NAME ) );
			?>
		</p>
	</div>
	<?php
}

/**
 * Plugin init
 */
function yith_wcbk_init() {
	load_plugin_textdomain( 'yith-booking-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once YITH_WCBK_DIR . 'includes/functions.yith-wcbk.php';
	require_once YITH_WCBK_DIR . 'includes/traits/trait-yith-wcbk-singleton-trait.php';
	require_once YITH_WCBK_DIR . 'includes/traits/trait-yith-wcbk-multiple-singleton-trait.php';
	require_once YITH_WCBK_DIR . 'includes/traits/trait-yith-wcbk-extensible-singleton-trait.php';

	require_once YITH_WCBK_DIR . 'includes/abstracts/abstract-yith-wcbk-data-extension.php';
	require_once YITH_WCBK_DIR . 'includes/abstracts/abstract-yith-wcbk-product-data-extension.php';
	require_once YITH_WCBK_DIR . 'includes/abstracts/abstract-yith-wcbk-booking-data-extension.php';

	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-shortcodes.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-emails.php';
	require_once YITH_WCBK_DIR . 'includes/abstract-yith-wcbk-db.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-language.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-notes.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-printer.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-cart.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-orders.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-frontend.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-frontend-action-handler.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-admin.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-admin-notices.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-product-post-type-admin.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-date-helper.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-ajax.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-settings.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-cache.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-logger.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-theme.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-endpoints.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-post-types.php';

	require_once YITH_WCBK_DIR . 'includes/background-process/class-yith-wcbk-background-processes.php';

	// Data.
	require_once YITH_WCBK_DIR . 'includes/data/abstract-yith-wcbk-data.php';
	require_once YITH_WCBK_DIR . 'includes/data/abstract-yith-wcbk-availability-handler.php';
	require_once YITH_WCBK_DIR . 'includes/data/abstract-yith-wcbk-simple-object.php';
	require_once YITH_WCBK_DIR . 'includes/data/abstract-yith-wcbk-booking.php';

	require_once YITH_WCBK_DIR . 'includes/data/class-wc-product-booking.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-availability-rule-legacy.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-availability-rule.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-availability.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-price-rule.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-booking-data-query.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-product-availability-handler.php';

	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-booking.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-global-availability-rule.php';
	require_once YITH_WCBK_DIR . 'includes/data/class-yith-wcbk-global-price-rule.php';

	// Data stores.
	require_once YITH_WCBK_DIR . 'includes/data-stores/abstract-yith-wcbk-custom-table-data-store.php';
	require_once YITH_WCBK_DIR . 'includes/data-stores/class-yith-wcbk-product-booking-data-store-cpt.php';
	require_once YITH_WCBK_DIR . 'includes/data-stores/class-yith-wcbk-booking-data-store.php';
	require_once YITH_WCBK_DIR . 'includes/data-stores/class-yith-wcbk-global-availability-rule-data-store.php';
	require_once YITH_WCBK_DIR . 'includes/data-stores/class-yith-wcbk-global-price-rule-data-store.php';

	// Assets.
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-assets.php';

	// Integrations.
	require_once YITH_WCBK_DIR . 'includes/integrations/class-yith-wcbk-integrations.php';

	// Tools.
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-tools.php';

	// Utils.
	require_once YITH_WCBK_DIR . 'includes/utils/class-yith-wcbk-exporter.php';
	require_once YITH_WCBK_DIR . 'includes/utils/class-yith-wcbk-wp-compatibility.php';

	// Booking classes.
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-booking-helper.php';

	require_once YITH_WCBK_DIR . 'includes/admin/class-yith-wcbk-booking-calendar.php';

	// Widgets.
	require_once YITH_WCBK_DIR . 'includes/widgets/class-yith-wcbk-product-form-widget.php';

	// Builders.
	require_once YITH_WCBK_DIR . 'includes/builders/class-yith-wcbk-builders.php';

	// Install.
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-install.php';

	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-modules.php';
	require_once YITH_WCBK_DIR . 'includes/class-yith-wcbk-module.php';

	require_once YITH_WCBK_DIR . 'includes/legacy/class-yith-wcbk-legacy-elements.php';

	// REST API.
	require_once YITH_WCBK_DIR . 'includes/rest-api/class-yith-wcbk-rest-server.php';

	// Deprecated.
	require_once YITH_WCBK_DIR . 'includes/legacy/deprecated/class-yith-wcbk-search-form-helper.php';
	require_once YITH_WCBK_DIR . 'includes/legacy/deprecated/class-yith-wcbk-extra-cost-helper.php';
	require_once YITH_WCBK_DIR . 'includes/legacy/deprecated/class-yith-wcbk-notifier.php';

	// Let's start the game!
	yith_wcbk();

	do_action( 'yith_wcbk_loaded' );
}

add_action( 'yith_wcbk_init', 'yith_wcbk_init' );

// Load plugin-fw file to deactivate plugins.
if ( ! function_exists( 'yith_deactivate_plugins' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/yit-deactive-plugin.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/yit-deactive-plugin.php';
}

/**
 * Install
 */
function yith_wcbk_install() {
	if ( function_exists( 'yith_deactivate_plugins' ) ) {
		if ( defined( 'YITH_WCBK_EXTENDED' ) && defined( 'YITH_WCBK_PREMIUM' ) ) {
			yith_deactivate_plugins( 'YITH_WCBK_EXTENDED_INIT' );
		}
	}
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcbk_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcbk_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcbk_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
