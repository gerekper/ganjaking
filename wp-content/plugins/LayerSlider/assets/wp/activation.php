<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Activation events
add_action('admin_init', 'layerslider_activation_redirect');

// Activation and de-activation hooks
add_action('admin_init', 'layerslider_activation_routine');
register_activation_hook(LS_ROOT_FILE, 'layerslider_activation');
register_deactivation_hook(LS_ROOT_FILE, 'layerslider_deactivation_scripts');
register_uninstall_hook(LS_ROOT_FILE, 'layerslider_uninstall_scripts');


// Update handler
$oldVersion = get_option( 'ls-plugin-version', '1.0.0' );
if( $oldVersion !== LS_PLUGIN_VERSION ) {
	update_option( 'ls-plugin-version', LS_PLUGIN_VERSION );
	layerslider_update_scripts( $oldVersion, LS_PLUGIN_VERSION );
}

// Redirect to LayerSlider's main admin page after plugin activation.
// Should not trigger on multisite bulk activation or after upgrading
// the plugin to a newer versions.
function layerslider_activation_redirect() {
	if(get_option('layerslider_do_activation_redirect', false)) {
		delete_option('layerslider_do_activation_redirect');
		if(isset($_GET['activate']) && !isset($_GET['activate-multi'])) {
			wp_redirect( admin_url( 'admin.php?page=layerslider-options&section=about' ) );
		}
	}
}

function layerslider_activation( ) {

	// Plugin activation routines should take care of this, but
	// call DB scripts anyway to avoid user intervention issues
	// like partially removing the plugin by only deleting the
	// database table.
	layerslider_create_db_table();

	// Call "activated" hook
	if( has_action('layerslider_activated') ) {
		do_action('layerslider_activated');
	}

	// Redirect to LS's admin page after activation
	update_option('layerslider_do_activation_redirect', 1);
}

function layerslider_activation_routine( ) {

	// Bail out early if everything is up-to-date
	// and there is nothing to be done.
	if( ! version_compare( get_option('ls-db-version', '1.0.0'), LS_DB_VERSION, '<' ) ) {
		return;
	}

	// Update database
	layerslider_create_db_table();
	update_option('ls-db-version', LS_DB_VERSION);

	// Fresh installation
	if( ! get_option('ls-installed') ) {
		update_option('ls-installed', 1);


		// Call "installed" hook
		if(has_action('layerslider_installed')) {
			do_action('layerslider_installed');
		}
	}

	// Install date
	if( ! get_option('ls-date-installed', 0) ) {
		update_option('ls-date-installed', time());
	}
}

function layerslider_update_scripts( $oldVersion, $currentVersion ) {

	// Make sure database is up-to-date,
	// perform any changes that might be
	// required by an update.
	layerslider_activation_routine();

	// Make sure to empty all caches due
	// to any potential data handling changes
	// introduced in an update.
	if( function_exists( 'layerslider_delete_caches' ) ) {
		layerslider_delete_caches();
	}

	// Run our any update function we may
	// provide for the affected versions.
	layerslider_run_upgrade_scripts( $oldVersion );



	// Trigger 'layerslider_updated' action
	// hook, so 3rd parties can run their own
	// updates scripts (if any).
	if( has_action( 'layerslider_updated' ) ) {
		do_action( 'layerslider_updated' );
	}
}


function layerslider_run_upgrade_scripts( $oldVersion ) {

	include LS_ROOT_PATH.'/wp/upgrades.php';

	foreach( $upgrades as $upgradeVersion => $upgradeCallback ) {

		if( version_compare( $oldVersion, $upgradeVersion, '<' ) ) {
			$upgradeCallback();
		}
	}
}


function layerslider_create_db_table() {

	global $wpdb;
	$charset_collate = '';

	// Get DB collate
	if( ! empty($wpdb->charset) ) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}

	if( ! empty($wpdb->collate) ) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Table for Sliders
	dbDelta("CREATE TABLE {$wpdb->prefix}layerslider (
			  id int(10) NOT NULL AUTO_INCREMENT,
			  group_id int(10),
			  author int(10) NOT NULL DEFAULT 0,
			  name varchar(100) DEFAULT '',
			  slug varchar(100) DEFAULT '',
			  data mediumtext NOT NULL,
			  date_c int(10) NOT NULL,
			  date_m int(10) NOT NULL,
			  schedule_start int(10) NOT NULL DEFAULT 0,
			  schedule_end int(10) NOT NULL DEFAULT 0,
			  flag_hidden tinyint(1) NOT NULL DEFAULT 0,
			  flag_deleted tinyint(1) NOT NULL DEFAULT 0,
			  flag_popup tinyint(1) NOT NULL DEFAULT 0,
			  flag_group tinyint(1) NOT NULL DEFAULT 0,
			  PRIMARY KEY  (id)
			) $charset_collate;");


	// Table for Slider Revisions
	dbDelta("CREATE TABLE {$wpdb->prefix}layerslider_revisions (
		  id int(10) NOT NULL AUTO_INCREMENT,
		  slider_id int(10) NOT NULL,
		  author int(10) NOT NULL DEFAULT 0,
		  data mediumtext NOT NULL,
		  date_c int(10) NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;");
}


// Utility function to verify database tables.
// Returns true if no issues were detected.
function layerslider_verify_db_tables() {

	global $wpdb;


	// Step 1: Check DB version
	if( version_compare( get_option('ls-db-version', '1.0.0'), LS_DB_VERSION, '<' ) ) {
		return false;
	}



	// Step 2: Verify that the DB tables exist
	$layerslider = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}layerslider'");
	$layerslider_revisions = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}layerslider_revisions'");

	if( empty( $layerslider ) || empty( $layerslider_revisions ) ) {
		return false;
	}


	// Step 3: Some hand picked things to look for
	$popup = $wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}layerslider` LIKE 'flag_group'");

	if( empty( $popup ) ) {
		return false;
	}


	// No error, just return true
	return true;
}


function layerslider_deactivation_scripts() {

	// Remove capability option, so a user can restore
	// his access to the plugin if set the wrong capability
	// delete_option('layerslider_custom_capability');

	// Remove the help pointer entry to remind a user for the
	// help menu when start to use the plugin again
	delete_user_meta(get_current_user_id(), 'layerslider_help_wp_pointer');

	// Call user hooks
	if(has_action('layerslider_deactivated')) {
		do_action('layerslider_deactivated');
	}
}

function layerslider_uninstall_scripts() {

	// Call user hooks
	update_option('ls-installed', 0);
	if(has_action('layerslider_uninstalled')) {
		do_action('layerslider_uninstalled');
	}
}

?>
