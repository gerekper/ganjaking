<?php
/*
Plugin Name: Admin Columns Pro
Version: 5.7.3
Description: Customize columns on the administration screens for post(types), users and other content. Filter and sort content, and edit posts directly from the posts overview. All via an intuitive, easy-to-use drag-and-drop interface.
Author: AdminColumns.com
Author URI: https://www.admincolumns.com
Plugin URI: https://www.admincolumns.com
Requires PHP: 5.6.20
Text Domain: codepress-admin-columns
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

define( 'ACP_FILE', __FILE__ );
define( 'ACP_VERSION', '5.7.3' );

/**
 * Deactivate Admin Columns
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

deactivate_plugins( 'codepress-admin-columns/codepress-admin-columns.php' );

/**
 * Load integrated Admin Columns
 */
add_action( 'plugins_loaded', function () {
	require_once 'admin-columns/codepress-admin-columns.php';
} );

/**
 * Load Admin Columns Pro
 */
add_action( 'after_setup_theme', function () {
	$dependencies = new AC\Dependencies( plugin_basename( ACP_FILE ), ACP_VERSION );
	$dependencies->requires_php( '5.6.20' );

	if ( $dependencies->has_missing() ) {
		return;
	}

	require_once __DIR__ . '/api.php';

	$class_map = __DIR__ . '/config/autoload-classmap.php';

	if ( is_readable( $class_map ) ) {
		AC\Autoloader::instance()->register_class_map( require $class_map );
	} else {
		AC\Autoloader::instance()->register_prefix( 'ACP', __DIR__ . '/classes' );
	}

	// Backward compatible underscore loader
	AC\Autoloader\Underscore::instance()
	                        ->add_alias( 'ACP\AdminColumnsPro', 'ACP' )
	                        ->add_alias( 'ACP\Editing\Editable', 'ACP_Column_EditingInterface' )
	                        ->add_alias( 'ACP\Export\Exportable', 'ACP_Export_Column' )
	                        ->add_alias( 'ACP\Sorting\Sortable', 'ACP_Column_SortingInterface' )
	                        ->add_alias( 'ACP\Filtering\Filterable', 'ACP_Column_FilteringInterface' );

	/**
	 * For loading external resources, e.g. column settings.
	 * Can be called from plugins and themes.
	 */
	do_action( 'acp/ready', ACP() );
}, 5 );

/**
 * Deactivate incompatible integrations
 */
add_action( 'after_setup_theme', function () {
	// Minimum required version. False is incompatible.
	$versions = [
		'ac-addon-acf/ac-addon-acf.php'                         => '2.8',
		'ac-addon-buddypress/ac-addon-buddypress.php'           => '1.7',
		'ac-addon-events-calendar/ac-addon-events-calendar.php' => '1.7',
		'ac-addon-gravityforms/ac-addon-gravityforms.php'       => '1.2',
		'ac-addon-jetengine/ac-addon-jetengine.php'             => '1.1',
		'ac-addon-metabox/ac-addon-metabox.php'                 => '1.3',
		'ac-addon-ninjaforms/ac-addon-ninjaforms.php'           => '1.6',
		'ac-addon-pods/ac-addon-pods.php'                       => '1.7',
		'ac-addon-types/ac-addon-types.php'                     => '1.8',
		'ac-addon-woocommerce/ac-addon-woocommerce.php'         => '3.7',
		'ac-addon-yoast-seo/ac-addon-yoast-seo.php'             => '1.2',
		'media-library-assistant/index.php'                     => '2.83',
	];

	// Deprecated basenames since 4.2
	$versions['cac-addon-acf/cac-addon-acf.php'] = $versions['ac-addon-acf/ac-addon-acf.php'];
	$versions['cac-addon-woocommerce/cac-addon-woocommerce.php'] = $versions['ac-addon-woocommerce/ac-addon-woocommerce.php'];

	$plugins = (array) get_plugins();
	$deactivated_plugins = false;

	foreach ( $versions as $basename => $version ) {
		if ( ! array_key_exists( $basename, $plugins ) ) {
			continue;
		}

		$current_version = $plugins[ $basename ]['Version'];

		if ( ! $version || version_compare( $version, $current_version, '>' ) ) {

			if ( is_plugin_active( $basename ) ) {
				$deactivated_plugins = true;
				deactivate_plugins( [ $basename ] );
			}

			add_action( 'after_plugin_row_' . $basename, function ( $plugin_file ) {
				$message = sprintf( __( 'This plugin is not compatible with %s. It cannot be activated because it might stop the WordPress Admin from working properly. Try updating this plugin to the latest version and then activate it again. ', 'codepress-admin-columns' ), 'Admin Columns Pro ' . ACP_VERSION );

				?>

				<tr class="plugin-update-tr">
					<td colspan="100%" class="plugin-update colspanchange">
						<style>
							.plugins tr[data-plugin='<?php echo $plugin_file; ?>'] th,
							.plugins tr[data-plugin='<?php echo $plugin_file; ?>'] td {
								box-shadow: none !important;
							}
						</style>

						<div class="update-message notice inline notice-error notice-alt">
							<p><?php echo wp_kses_post( $message ); ?></p>
						</div>
					</td>
				</tr>

				<?php
			}, 5 );
		}
	}

	if ( $deactivated_plugins ) {
		$protocol = is_ssl() ? 'https' : 'http';
		$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		wp_redirect( $url );
		exit;
	}

}, 9 );