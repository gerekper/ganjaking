<?php
if(!class_exists('NinjaTableUpdateChecker')) {
	require 'NinjaTableUpdateChecker.php';
}

if(!class_exists('NinjaTableUpdater')) {
	require 'NinjaTableUpdater.php';
}

// Kick off our EDD class
new NinjaTableUpdateChecker( array(
	// The plugin file, if this array is defined in the plugin
	'plugin_file' => NINJAPRO_PLUGIN_FILE,
	// The current version of the plugin.
	// Also need to change in readme.txt and plugin header.
	'version' => NINJAPROPLUGIN_VERSION,
	// The main URL of your store for license verification
	'store_url' => 'https://wpmanageninja.com',
	// Your name
	'author' => 'WP Manage Ninja',
	// The URL to renew or purchase a license
	'purchase_url' => 'https://wpmanageninja.com/downloads/ninja-tables-pro-add-on/',
	// The URL of your contact page
	'contact_url' => 'https://wpmanageninja.com/contact',
	// This should match the download name exactly
	'item_id' => '273',
	// The option names to store the license key and activation status
	'license_key' => '_ninjatables_pro_license_key',
	'license_status' => '_ninjatables_pro_license_status',
	// Option group param for the settings api
	'option_group' => '_ninjatables_pro_license',
	// The plugin settings admin page slug
	'admin_page_slug' => 'ninja_tables',
	// If using add_menu_page, this is the parent slug to add a submenu item underneath.
	'activate_url' => admin_url('?page=ninja_tables#/tools/licensing'),
	// The translatable title of the plugin
	'plugin_title' => __( 'Ninja Tables Pro', 'ninja-tables-pro' ),
	'menu_slug' => 'ninja_tables',
	'menu_title' => __('Ninja Tables Pro', 'ninja-tables-pro'),
    // How much time (in seconds) the updater won't check the license.
    'cache_time' => 48 * 60 * 60
));
