<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

/*
* Menu Settings
*/
add_action( 'admin_menu', 'wpuf_menu' );

function wpuf_menu(){
	
    add_menu_page( 'WP Ultimate Firewall', 'Ultimate Firewall', 'administrator', 'wpuf_plugin_dashboard_page', 'wpuf_plugin_dashboard_page', "dashicons-shield" );
	
	add_submenu_page( 'wpuf_plugin_dashboard_page', 'Dashboard &lsaquo; WP Ultimate Firewall', __( 'Dashboard', 'ua-protection-lang' ), 'administrator', 'wpuf_plugin_dashboard_page' );
	
	add_submenu_page( 'wpuf_plugin_dashboard_page', 'Admin Settings &lsaquo; WP Ultimate Firewall', __( 'Admin Settings', 'ua-protection-lang' ), 'administrator', 'wpuf_admin_settings_page', 'wpuf_admin_settings_page' );
	
	add_submenu_page( 'wpuf_plugin_dashboard_page', 'Optimization Settings &lsaquo; WP Ultimate Firewall', __( 'Optimization Settings', 'ua-protection-lang' ), 'administrator', 'wpuf_optimization_settings_page', 'wpuf_optimization_settings_page' );
	
	add_submenu_page( 'wpuf_plugin_dashboard_page', 'Security &amp; Firewall Settings &lsaquo; WP Ultimate Firewall', __( 'Security &amp; Firewall Settings', 'ua-protection-lang' ), 'administrator', 'wpuf_firewall_settings_page', 'wpuf_firewall_settings_page' );	
	
	add_submenu_page( 'wpuf_plugin_dashboard_page', 'Access Settings &lsaquo; WP Ultimate Firewall', __( 'Access Settings', 'ua-protection-lang' ), 'administrator', 'wpuf_access_settings_page', 'wpuf_access_settings_page' );	
	
}


/*
* Call
*/
require WPUF_DIR . 'admin/dashboard.php';
require WPUF_DIR . 'admin/admin.php';
require WPUF_DIR . 'admin/optimization.php';
require WPUF_DIR . 'admin/firewall.php';
require WPUF_DIR . 'admin/access.php';