<?php

/**
 *
 * @link              https://wpdeveloper.net
 * @since             1.0.0
 * @package           Betterdocs_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       BetterDocs Pro
 * Plugin URI:        https:/betterdocs.co
 * Description:       A better documentation and knowledgebase plugin for WordPress
 * Version:           1.3.3
 * Author:            WPDeveloper
 * Author URI:        https://wpdeveloper.net
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       betterdocs-pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'BETTERDOCS_PRO_VERSION', '1.3.3' );
//define( 'BETTERDOCS_PRO_PUBLIC_URL', plugins_url( '/', __FILE__ ) );
define( 'BETTERDOCS_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'BETTERDOCS_PRO_PUBLIC_URL', BETTERDOCS_PRO_URL . 'public/' );
define( 'BETTERDOCS_PRO_ADMIN_URL', BETTERDOCS_PRO_URL . 'admin/' );
define( 'BETTERDOCS_PRO_FILE', __FILE__ );

define( 'BETTERDOCS_PRO_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BETTERDOCS_PRO_ADMIN_DIR_PATH', BETTERDOCS_PRO_ROOT_DIR_PATH . 'admin/' );
define( 'BETTERDOCS_PRO_PUBLIC_PATH', BETTERDOCS_PRO_ROOT_DIR_PATH . 'public/' );

// Licensing
define( 'BETTERDOCS_PRO_STORE_URL', 'https://wpdeveloper.net/' );
define( 'BETTERDOCS_PRO_SL_ITEM_ID', 342422 );
define( 'BETTERDOCS_PRO_SL_ITEM_SLUG', 'betterdocs-pro' );
define( 'BETTERDOCS_PRO_SL_ITEM_NAME', 'BetterDocs Pro' );
define( 'BETTERDOCS_FREE_PLUGIN', BETTERDOCS_PRO_ADMIN_DIR_PATH . 'library/betterdocs.zip' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-betterdocs-pro-activator.php
 */
function activate_betterdocs_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-betterdocs-pro-activator.php';
	Betterdocs_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-betterdocs-pro-deactivator.php
 */
function deactivate_betterdocs_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-betterdocs-pro-deactivator.php';
	Betterdocs_Pro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_betterdocs_pro' );
register_deactivation_hook( __FILE__, 'deactivate_betterdocs_pro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-betterdocs-pro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_betterdocs_pro() {

	$plugin = new Betterdocs_Pro();
	$plugin->run();

}
// run_betterdocs_pro();
add_action( 'betterdocs_init', 'run_betterdocs_pro' );

// Install Core plugin
include_once BETTERDOCS_PRO_ADMIN_DIR_PATH . '/includes/class-betterdocs-core-installer.php';
new BetterDocsPro_Install_Core('');

/**
 * Admin Notices
 */
function betterdocs_install_core_notice() {

	$has_installed = get_plugins();
	$button_text = isset( $has_installed['betterdocs/betterdocs.php'] ) ? __( 'Activate Now!', 'betterdocs-pro' ) : __( 'Install Now!', 'betterdocs-pro' );

	if( ! class_exists( 'BetterDocs' ) ) :
	?>
		<div class="error notice is-dismissible">
			<p><?php sprintf( '<strong>%1$s</strong> %2$s <strong>%3$s</strong> %4$s', __( 'BetterDocs Pro', 'betterdocs' ), __( 'requires', 'betterdocs' ), __( 'BetterDocs', 'betterdocs' ), __( 'core plugin to be installed. Please get the plugin now!', 'betterdocs' ) ) ?> <button id="betterdocs-install-core" class="button button-primary"><?php echo $button_text; ?></button></p>
		</div>
	<?php
	endif;
}
add_action( 'admin_notices', 'betterdocs_install_core_notice' );

/**
 * Plugin Licensing
 *
 * @since v1.0.0
 */
function betterdocs_plugin_licensing() {

	// Requiring Licensing Class
	require_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'includes/licensing/class-betterdocs-licensing.php';
	if ( is_admin() ) {
		// Setup the settings page and validation
		$licensing = new BetterDocs_Licensing(
			BETTERDOCS_PRO_SL_ITEM_SLUG,
			BETTERDOCS_PRO_SL_ITEM_NAME,
			'betterdocs-pro'
		);
	}

}
// add_action( 'betterdocs_init', 'betterdocs_plugin_licensing' );
betterdocs_plugin_licensing();

/**
 * Handles Updates
 *
 * @since 1.0.0
 */
function betterdocs_plugin_updater() {

	// Requiring the Updater class
	require_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'includes/licensing/class-betterdocs-updater.php';

	// Disable SSL verification
	add_filter( 'edd_sl_api_request_verify_ssl', '__return_false' );

	// Setup the updater
	$license = get_option( BETTERDOCS_PRO_SL_ITEM_SLUG . '-license-key' );
	$updater = new BetterDocs_Plugin_Updater( BETTERDOCS_PRO_STORE_URL, __FILE__, array(
			'version'      => BETTERDOCS_PRO_VERSION,
			'license'      => $license,
			'item_id'      => BETTERDOCS_PRO_SL_ITEM_ID,
			'author'       => 'WPDeveloper',
		)
	);
}
add_action( 'admin_init', 'betterdocs_plugin_updater' );

 /**
 *  Load customizer conditional controler js file.
 *
 * @since 1.0.2
 */

function betterdocs_customizer_condition_pro() {
	wp_enqueue_script( 'betterdocs-customize-condition-pro', 
		BETTERDOCS_PRO_ADMIN_URL . 'js/customizer-condition.js',
		array(), 
		true 
	);
}
add_action( 'customize_controls_enqueue_scripts', 'betterdocs_customizer_condition_pro' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0.2
 */
function betterdocs_customize_preview_js_pro() {
	wp_enqueue_script( 'betterdocs-customizer-pro', 
		BETTERDOCS_PRO_ADMIN_URL . 'js/customizer.js', 
		array( 'customize-preview' ), 
		'', 
		true 
	);
}
add_action( 'customize_preview_init', 'betterdocs_customize_preview_js_pro', 99 );