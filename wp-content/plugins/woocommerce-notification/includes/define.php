<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WNOTIFICATION_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woocommerce-notification" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_ADMIN', VI_WNOTIFICATION_DIR . "admin" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_FRONTEND', VI_WNOTIFICATION_DIR . "frontend" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_LANGUAGES', VI_WNOTIFICATION_DIR . "languages" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_INCLUDES', VI_WNOTIFICATION_DIR . "includes" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_TEMPLATES', VI_WNOTIFICATION_DIR . "templates" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_CACHE', WP_CONTENT_DIR . "/cache/woonotification/" );
$plugin_url = plugins_url( 'woocommerce-notification' );
//$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '', $plugin_url );
define( 'VI_WNOTIFICATION_SOUNDS', VI_WNOTIFICATION_DIR . "sounds" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_SOUNDS_URL', $plugin_url . "/sounds/" );

define( 'VI_WNOTIFICATION_CSS', $plugin_url . "/css/" );
define( 'VI_WNOTIFICATION_CSS_DIR', VI_WNOTIFICATION_DIR . "css" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_FONT', $plugin_url . "/fonts/" );
define( 'VI_WNOTIFICATION_FONT_DIR', VI_WNOTIFICATION_DIR . "fonts" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_JS', $plugin_url . "/js/" );
define( 'VI_WNOTIFICATION_JS_DIR', VI_WNOTIFICATION_DIR . "js" . DIRECTORY_SEPARATOR );
define( 'VI_WNOTIFICATION_IMAGES', $plugin_url . "/images/" );
define( 'VI_WNOTIFICATION_BACKGROUND_IMAGES', VI_WNOTIFICATION_IMAGES . 'background/' );
define( 'VI_WNOTIFICATION_BACKGROUND_IMAGES_2019', VI_WNOTIFICATION_IMAGES . 'background-2019/' );


/*Include functions file*/
if ( is_file( VI_WNOTIFICATION_INCLUDES . "functions.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "functions.php";
}
if ( is_file( VI_WNOTIFICATION_INCLUDES . "data.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "data.php";
}
/*Include functions file*/
if ( is_file( VI_WNOTIFICATION_INCLUDES . "check_update.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "check_update.php";
}
if ( is_file( VI_WNOTIFICATION_INCLUDES . "update.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "update.php";
}
if ( is_file( VI_WNOTIFICATION_INCLUDES . "support.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "support.php";
}
if ( is_file( VI_WNOTIFICATION_INCLUDES . "mobile_detect.php" ) ) {
	require_once VI_WNOTIFICATION_INCLUDES . "mobile_detect.php";
}

vi_include_folder( VI_WNOTIFICATION_ADMIN, 'VI_WNOTIFICATION_Admin_' );
vi_include_folder( VI_WNOTIFICATION_FRONTEND, 'VI_WNOTIFICATION_Frontend_' );
