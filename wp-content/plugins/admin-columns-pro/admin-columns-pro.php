<?php
/*
Plugin Name: Admin Columns Pro
Version: 6.3.3
Description: Customize columns on the administration screens for post(types), users and other content. Filter and sort content, and edit posts directly from the posts overview. All via an intuitive, easy-to-use drag-and-drop interface.
Author: AdminColumns.com
Author URI: https://www.admincolumns.com
Plugin URI: https://www.admincolumns.com
Requires PHP: 7.2
Requires at least: 5.3
Text Domain: codepress-admin-columns
Domain Path: /languages/
*/

if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! is_admin()) {
    return;
}

define('ACP_FILE', __FILE__);
define('ACP_VERSION', '6.3.3');

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Deactivate Admin Columns
 */
deactivate_plugins('codepress-admin-columns/codepress-admin-columns.php');

/**
 * Load Admin Columns
 */
add_action('plugins_loaded', static function () {
    require_once 'admin-columns/codepress-admin-columns.php';
});

/**
 * Load Admin Columns Pro
 */
add_action('after_setup_theme', static function () {
    $dependencies = new AC\Dependencies(plugin_basename(ACP_FILE), ACP_VERSION);
    $dependencies->requires_php('7.2');

    if ($dependencies->has_missing()) {
        return;
    }

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/api.php';

    /**
     * For loading external resources like column settings.
     * Can be called from plugins and themes.
     */
    do_action('acp/ready', ACP());
}, 5);