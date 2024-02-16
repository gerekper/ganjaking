<?php
/*
Plugin Name: WP Ultimate Firewall
Plugin URI: https://premium.divcoder.com/wp-ultimate-firewall/
Description: Protect your website strongly in real time.
Author: Divcoder
Version: 1.9.0
Author URI: https://premium.divcoder.com
Authoras URI: https://premium.divcoder.com
Domain Path: /languages
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Plugin DIR URL
define( 'WPUF_URL', plugin_dir_url( __FILE__ ) );
//Plugin DIR Path
define( 'WPUF_DIR', plugin_dir_path( __FILE__ ) );

//Admin Styles
add_action( 'admin_init', 'wpuf_echo_css' );
function wpuf_echo_css() {
	//Add CSS Style for Admin Panel
   wp_enqueue_style( 'wpuf-style', WPUF_URL."admin/assets/css/style.css",array(), "1.0.0" );
}

/*
* Load Admin Settings
*/
include WPUF_DIR ."admin/index.php";

/*
* Load Functions
*/
include WPUF_DIR ."functions/wpuf-admin-functions.php";
include WPUF_DIR ."functions/wpuf-optimization-functions.php";
include WPUF_DIR ."functions/wpuf-firewall-functions.php";
include WPUF_DIR ."functions/wpuf-access-functions.php";

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wpuf_language() {
  load_plugin_textdomain( 'ua-protection-lang', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

//Load Plugin Functions
add_action( 'plugins_loaded', 'wpuf_language' );

/*
* Default Options for Plugin
*/
function wpuf_activation() {
	$get_admin_Email = get_option('admin_email');

	add_option("wpuf_select_set","1");
	
	add_option("wpuf_mail_alarm","1");
		add_option("wpuf_mail_notify", $get_admin_Email);
		add_option("wpuf_mail_alarm_spam","1");
		add_option("wpuf_mail_alarm_admin","1");
		add_option("wpuf_mail_alarm_hacker","0");
		add_option("wpuf_mail_alarm_fc","0");
		add_option("wpuf_mail_alarm_proxy","0");
		add_option("wpuf_mail_alarm_bruteforce","1");
	
	add_option("wpuf_recaptcha_sitekey","000000000000000000000000000000");
	add_option("wpuf_recaptcha_secretkey","000000000000000000000000000000");
	add_option("wpuf_uptimerobot_api","000000000000000000000000000000");
	add_option("wpuf_security_ban","2");
	
	add_option("wpuf_gzip_comp","0");
	add_option("wpuf_page_minifier","0");
	add_option("wpuf_lazy_load","0");
	add_option("wpuf_disable_emojis","0");
	add_option("wpuf_author_redirect","0");
	add_option("wpuf_remove_shortlinks","0");
	
	add_option("wpuf_remove_jquery_migrate","0");
	add_option("wpuf_remove_query_strings","0");
	add_option("wpuf_headtofooter_opt","0");
	add_option("wpuf_remove_feeds","0");
	
	add_option("wpuf_browser_caching","0");
	add_option("wpuf_browser_cache_time","3600");
	add_option("wpuf_asydef_attr","0");
	
	add_option("wpuf_woo_remove_scripts","0");
	add_option("wpuf_remove_bp_scripts","0");
	add_option("wpuf_bbp_style_remover","0");
	
	add_option("wpuf_header_sec","0");
	add_option("wpuf_pingback_disable","0");
	add_option("wpuf_proxy_protection","0");
	add_option("wpuf_access_security","0");
	add_option("wpuf_comment_sec_wc","0");
	add_option("wpuf_content_security","0");
	add_option("wpuf_disable_rcp_lgus","0");
	add_option("wpuf_xr_security","0");
	add_option("wpuf_wpscan_protection","0");
	
	add_option("wpuf_tor_protection","0");
	add_option("wpuf_sql_protection","0");
	add_option("wpuf_badbot_protection","0");
	add_option("wpuf_fakebot_protection","0");
	add_option("wpuf_disable_fileedit","0");
	
	add_option("wpuf_spam_attacks","0");
		add_option("wpuf_spam_attacks_general","1");
		add_option("wpuf_spam_attacks_bf","1");
		add_option("wpuf_spam_attacks_psp","0");	
		
	add_option("wpuf_recaptcha_protection_lrl","0");
		add_option("wpuf_recaptcha_protection_lrl_login","1");
		add_option("wpuf_recaptcha_protection_lrl_registration","1");
		add_option("wpuf_recaptcha_protection_lrl_lpf","0");
	
}

//Register Options
register_activation_hook( __FILE__, 'wpuf_activation' );

// Plugin WP-Admin Settings Text
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wp_ss_plugin_page');

function wp_ss_plugin_page( $links ) {
    $links[] = '<a href="' . admin_url( 'admin.php?page=wpuf_plugin_dashboard_page' ) . '">' . __('Settings') . '</a>';
    return $links;
}