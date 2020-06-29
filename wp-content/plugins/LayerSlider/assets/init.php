<?php

// Prevent direct file access.
defined( 'LS_ROOT_FILE' ) || exit;

// Path info
// v6.2.0: LS_ROOT_URL is now set in the after_setup_theme action
// hook to provide a way for theme authors to override its value
define('LS_ROOT_PATH', __DIR__);

define('LS_DB_TABLE', 'layerslider');
define('LS_PLUGIN_SLUG', basename( dirname( LS_ROOT_FILE ) ) );
define('LS_PLUGIN_BASE', plugin_basename( LS_ROOT_FILE ) );
define('LS_MARKETPLACE_ID', '1362246');
define('LS_TEXTDOMAIN', 'LayerSlider');
define('LS_REPO_BASE_URL', 'https://repository.kreaturamedia.com/v4/');

if( ! defined('NL')  ) { define('NL', "\r\n"); }
if( ! defined('TAB') ) { define('TAB', "\t");  }

if( ! defined('DAY_IN_SECONDS') ) { define( 'DAY_IN_SECONDS', 24 * 3600 ); }
if( ! defined('MONTH_IN_SECONDS') ) {  define('MONTH_IN_SECONDS', ''); }

// Load & initialize plugin config class
include LS_ROOT_PATH.'/classes/class.ls.config.php';
LS_Config::init();

// Shared
include LS_ROOT_PATH.'/wp/compatibility.php';
include LS_ROOT_PATH.'/wp/scripts.php';
include LS_ROOT_PATH.'/wp/menus.php';
include LS_ROOT_PATH.'/wp/hooks.php';
include LS_ROOT_PATH.'/wp/widgets.php';
include LS_ROOT_PATH.'/wp/shortcodes.php';
include LS_ROOT_PATH.'/includes/slider_utils.php';
include LS_ROOT_PATH.'/classes/class.ls.modulemanager.php';
include LS_ROOT_PATH.'/classes/class.ls.posts.php';
include LS_ROOT_PATH.'/classes/class.ls.sliders.php';
include LS_ROOT_PATH.'/classes/class.ls.sources.php';
include LS_ROOT_PATH.'/classes/class.ls.popups.php';

if( get_option('ls_elementor_widget', true ) ) {
	include LS_ROOT_PATH.'/classes/class.ls.elementor.php';
}

// Back-end only
if( is_admin() ) {

	include LS_ROOT_PATH.'/wp/actions.php';
	include LS_ROOT_PATH.'/wp/activation.php';
	include LS_ROOT_PATH.'/wp/notices.php';
	include LS_ROOT_PATH.'/classes/class.ls.revisions.php';

	if( get_option('ls_tinymce_helper', true ) ) {
		include LS_ROOT_PATH.'/wp/tinymce.php';
	}

	LS_Revisions::init();
}

if( ! class_exists('KM_PluginUpdatesV3') ) {
	require_once LS_ROOT_PATH.'/classes/class.km.autoupdate.plugins.v3.php';
}

// Register [layerslider] shortcode
LS_Shortcode::registerShortcode();


// Add default skins.
// Reads all sub-directories (individual skins) from the given path.
LS_Sources::addSkins(LS_ROOT_PATH.'/static/layerslider/skins/');

// Popup
LS_Popups::init();


// Setup auto updates. This class also has additional features for
// non-activated sites such as fetching update info.
$GLOBALS['LS_AutoUpdate'] = new KM_PluginUpdatesV3( array(
	'name' 			=> 'LayerSlider WP',
	'repoUrl' 		=> LS_REPO_BASE_URL,
	'root' 			=> LS_ROOT_FILE,
	'version' 		=> LS_PLUGIN_VERSION,
	'itemID' 		=> LS_MARKETPLACE_ID,
	'codeKey' 		=> 'layerslider-purchase-code',
	'authKey' 		=> 'layerslider-authorized-site',
	'channelKey' 	=> 'layerslider-release-channel',
	'activationKey' => 'layerslider-activation-id'
));


// Load locales
add_action('plugins_loaded', 'layerslider_plugins_loaded');
function layerslider_plugins_loaded() {
	load_plugin_textdomain('LayerSlider', false, LS_PLUGIN_SLUG . '/assets/locales/' );
}


// Offering a way for authors to override LayerSlider resources by
// triggering filter and action hooks after the theme has loaded.
add_action('after_setup_theme', 'layerslider_after_setup_theme');
function layerslider_after_setup_theme() {

	// Set the LS_ROOT_URL constant
	$url = apply_filters('layerslider_root_url', plugins_url('', LS_ROOT_FILE));
	$url = $url . '/assets';

	define('LS_ROOT_URL', $url);

	// Trigger the layerslider_ready action hook
	layerslider_loaded();

	// Backwards compatibility for theme authors
	LS_Config::checkCompatibility();
}



// Sets up LayerSlider as theme-bundled version by
// disabling certain features and hiding premium notices.
function layerslider_set_as_theme() {

	LS_Config::setAsTheme();
}


function layerslider_hide_promotions() {
	LS_Config::set('promotions', false);
}
