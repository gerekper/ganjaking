<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WBOOSTSALES_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woocommerce-boost-sales" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_ADMIN', VI_WBOOSTSALES_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_FRONTEND', VI_WBOOSTSALES_DIR . "frontend" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_LANGUAGES', VI_WBOOSTSALES_DIR . "languages" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_INCLUDES', VI_WBOOSTSALES_DIR . "includes" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_TEMPLATES', VI_WBOOSTSALES_DIR . "templates" . DIRECTORY_SEPARATOR );
$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'VI_WBOOSTSALES_CSS', $plugin_url . "/css/" );
define( 'VI_WBOOSTSALES_CSS_DIR', VI_WBOOSTSALES_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_JS', $plugin_url . "/js/" );
define( 'VI_WBOOSTSALES_JS_DIR', VI_WBOOSTSALES_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'VI_WBOOSTSALES_IMAGES', $plugin_url . "/images/" );

/*Include functions file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "mobile_detect.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "mobile_detect.php";
}
/*Include functions file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "data.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "data.php";
}
if ( is_file( VI_WBOOSTSALES_INCLUDES . "upsells.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "upsells.php";
}
if ( is_file( VI_WBOOSTSALES_INCLUDES . "cross-sells.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "cross-sells.php";
}
if ( is_file( VI_WBOOSTSALES_INCLUDES . "discount-bar.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "discount-bar.php";
}
if ( is_file( VI_WBOOSTSALES_INCLUDES . "functions.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "functions.php";
}


/*Include image library file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "image-field.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "image-field.php";
}

/*Include functions file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "fields.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "fields.php";
}
/*Include functions file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "check_update.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "check_update.php";
}
/*Include functions file*/
if ( is_file( VI_WBOOSTSALES_INCLUDES . "update.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "update.php";
}
if ( is_file( VI_WBOOSTSALES_INCLUDES . "support.php" ) ) {
	require_once VI_WBOOSTSALES_INCLUDES . "support.php";
}
vi_include_folder( VI_WBOOSTSALES_ADMIN, 'VI_WBOOSTSALES_Admin_' );
vi_include_folder( VI_WBOOSTSALES_FRONTEND, 'VI_WBOOSTSALES_Frontend_' );
