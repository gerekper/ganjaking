<?php defined('ABSPATH') or die;

/*
Plugin Name: Ninja Tables Pro
Description: The Pro Add-On of Ninja Tables, the best Responsive Table Plugin for WordPress.
Version: 5.0.3
Author: WPManageNinja
Author URI: https://ninjatables.com/
Plugin URI: https://wpmanageninja.com/downloads/ninja-tables-pro-add-on/
License: GPLv2 or later
Text Domain: ninja-tables-pro
Domain Path: /language
*/

if (defined('NINJAPRO_PLUGIN_FILE')) {
    return;
}

update_option('_ninjatables_pro_license_status', 'valid');
update_option('_ninjatables_pro_license_key', 'B5E0B5F8DD8689E6ACA49DD6E6E1A930');

define('NINJAPRO_PLUGIN_FILE', __FILE__);
defined('NINJAPROPLUGIN_VERSION') or define('NINJAPROPLUGIN_VERSION', '5.0.3');

require_once("ninja-tables-pro-boot.php");

add_action('ninjatables_loaded', function ($app) {
    (new \NinjaTablesPro\App\Application($app));
    do_action('ninjatables_pro_loaded', $app);
});

include NINJAPROPLUGIN_PATH . 'app/Library/updater/ninja_table_pro_updater.php';
