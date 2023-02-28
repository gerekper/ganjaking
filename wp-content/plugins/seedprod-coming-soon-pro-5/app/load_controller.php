<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
// must load first
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-utils.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/cpt.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/admin-bar-menu.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/class-seedprod-notifications.php';

// helper functions

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-woocommerce.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/sitelogo-functions.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/templateparts.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-post-info.php';


require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-inline-help.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-wpforms.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-rafflepress.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-lp.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-theme-template.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-woocommerce-template-tags.php';



require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-business-reviews.php';


require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-csp-mm.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/nestednavmenu.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-seedprod-gallery.php';



require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-404.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-loginp.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/render-domain-mapping.php';
if ( 'pro' === SEEDPROD_PRO_BUILD ) {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/tracking.php';
}

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/setup-wizard.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/backwards_compatibility.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/subscriber.php';
add_action( 'plugins_loaded', array( 'SeedProd_Pro_Render', 'get_instance' ) );

add_action( 'plugins_loaded', array( 'SeedProd_Pro_Render_404', 'get_instance' ) );

add_action( 'plugins_loaded', array( 'SeedProd_Notifications', 'get_instance' ) );

if ( is_admin() ) {
	// Admin Only
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/settings.php';
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/lpage.php';
	
	if ( seedprod_pro_cu( 'themebuilder' ) ) {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/theme-templates.php';
	}
	
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/edit_with_seedprod.php';
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/functions-addons.php';
	if ( 'lite' == SEEDPROD_PRO_BUILD ) {
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/class-seedprod-review.php';
	}
}

// Load on Public and Admin
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/license.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/includes/upgrade.php';



require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/export-import-theme-functions.php';
require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/export-import-landing-functions.php';

require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/import-cross-site-functions.php';



/**
* API Updates
*/
if ( ! class_exists( 'SeedProd_Updater' ) ) {
	// load our custom updater
	include SEEDPROD_PRO_PLUGIN_PATH . 'app/class-updater.php';
}

/**
 * Update SeedProd.
 *
 * @return void
 */
function seedprod_pro_updater() {
	$seedprod_api_key = seedprod_pro_get_api_key();
	$endpoint         = 'plugin-info';

	$data = array();

	// Go ahead and initialize the updater.
	new SeedProd_Updater(
		array(
			'plugin_name' => 'SeedProd',
			'plugin_slug' => 'seedprod-pro',
			'plugin_path' => plugin_basename( SEEDPROD_PRO_SLUG ),
			'plugin_url'  => trailingslashit( home_url() ),
			'remote_url'  => SEEDPROD_PRO_API_URL . $endpoint,
			'version'     => SEEDPROD_PRO_VERSION,
			'key'         => $seedprod_api_key,
			'data'        => $data,
		)
	);
}
add_action( 'admin_init', 'seedprod_pro_updater', 0 );

