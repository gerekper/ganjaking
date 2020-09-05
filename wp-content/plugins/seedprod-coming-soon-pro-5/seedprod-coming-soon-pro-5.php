<?php
/*
Plugin Name: SeedProd Coming Soon Page Pro
Plugin URI: https://www.seedprod.com
Description: The Ultimate Coming Soon & Maintenance Mode Plugin
Version:  5.12.8
Author: SeedProd
Author URI: http://www.seedprod.com
Text Domain: seedprod-coming-soon-pro
Domain Path: /languages
License: PHP Licensed under the GPLv2, Javascript and CSS are Proprietary and can not be redistributed without consent of copyright holder.
Copyright 2018 SeedProd LLC (email : john@seedprod.com, twitter : @seedprod)
*/

/**
 * Default Constants
 */
define( 'SEED_CSPV5_SHORTNAME', 'seed_cspv5' ); // Used to reference namespace functions.
define( 'SEED_CSPV5_SLUG', 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php' ); // Used for settings link.
define( 'SEED_CSPV5_TEXTDOMAIN', 'seedprod-coming-soon-pro' ); // Your textdomain
define( 'SEED_CSPV5_PLUGIN_NAME', __( 'Coming Soon Page Pro', 'seedprod-coming-soon-pro' ) ); // Plugin Name shows up on the admin settings screen.
define( 'SEED_CSPV5_VERSION', '5.12.8'); // Plugin Version Number.
define( 'SEED_CSPV5_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // Example output: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/seed_cspv5/
define( 'SEED_CSPV5_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // Example output: http://localhost:8888/wordpress/wp-content/plugins/seed_cspv5/
define( 'SEED_CSPV5_SUBSCRIBERS_TABLENAME', 'csp3_subscribers' );
define( 'SEED_CSPV5_PAGES_TABLENAME', 'cspv5_pages' );
define( 'SEED_CSPV5_THEME_BASE_URL', 'https://s3.amazonaws.com/static.seedprod.com/themes/' );
define( 'SEED_CSPV5_API_URL', 'https://api.seedprod.com/v3/update' );
define( 'SEED_CSPV5_THEME_API_URL', 'https://api.seedprod.com/v3/themes' );
define( 'SEED_CSPV5_BACKGROUND_SEARCH_API_URL', 'https://api.seedprod.com/v3/background_search' );
define( 'SEED_CSPV5_BACKGROUND_API_URL', 'https://api.seedprod.com/v3/backgrounds' );
define( 'SEED_CSPV5_THEME_DEV', false);


//define( 'SEED_CSPV5_BACKGROUND_API_URL', 'http://v3app.seedprod.dev/v3/background_search' );




/**
 * Load Translation
 */
function seed_cspv5_load_textdomain() {
    load_plugin_textdomain( 'seedprod-coming-soon-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'seed_cspv5_load_textdomain');


/**
 * Upon activation of the plugin set defaults
 */
function seed_cspv5_activation(){
  global $seed_cspv5_settings_defaults;
  require_once( SEED_CSPV5_PLUGIN_PATH.'admin/default-settings.php' );

  add_option('seed_cspv5_settings_content',$seed_cspv5_settings_defaults['seed_cspv5_settings_content']);

  // Disable mojo coming soon
  if(get_option( 'mm_coming_soon' ) === 'true'){
    update_option( 'mm_coming_soon', 'false' );
  }

  add_option('seed_cspv5_token',strtolower(wp_generate_password(32,false, false)));


  // seed_cspv5_add_rules();
  // flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'seed_cspv5_activation' );


/**
 * Upon deactivation of the plugin
 */
function seed_cspv5_deactivation(){
  flush_rewrite_rules();
}
//register_deactivation_hook( __FILE__, 'seed_cspv5_deactivation' );


//add_action('init','seed_cspv5_add_rules');
function seed_cspv5_add_rules(){
  add_rewrite_rule('stores/?([^/]*)','index.php?pagename=stores&store_id=$matches[1]','top');
}

//add_filter('query_vars','seed_cspv5_add_query_var');
function seed_cspv5_add_query_var($vars){
  $vars[] = 'store_id';
  return $vars;

}




/***************************************************************************
 * Load Required Files
 ***************************************************************************/
// Global Settings Var
global $seed_cspv5_settings;

require_once( SEED_CSPV5_PLUGIN_PATH.'admin/get-settings.php' );
$seed_cspv5_settings = seed_cspv5_get_settings();

// Class to render pages
require_once( SEED_CSPV5_PLUGIN_PATH.'inc/class-seed-cspv5.php' );
add_action( 'plugins_loaded', array( 'SEED_CSPV5', 'get_instance' ) );


if( is_admin() ) {
	// Admin Only
	require_once( SEED_CSPV5_PLUGIN_PATH.'admin/config-settings.php' );
    require_once( SEED_CSPV5_PLUGIN_PATH.'admin/admin.php' );
    // Load Admin
    add_action( 'plugins_loaded', array( 'SEED_CSPV5_ADMIN', 'get_instance' ) );
} else {
	// Public only
}

// Welcome Page
if(!defined('SEED_CSP_API_KEY')){
  register_activation_hook( __FILE__, 'seed_cspv5_welcome_screen_activate' );
}
function seed_cspv5_welcome_screen_activate() {
  set_transient( '_seed_cspv5_welcome_screen_activation_redirect', true, 30 );
}


if(!defined('SEED_CSP_API_KEY')){
  add_action( 'admin_init', 'seed_cspv5_welcome_screen_do_activation_redirect' );
}
function seed_cspv5_welcome_screen_do_activation_redirect() {
  // Bail if no activation redirect
    if ( ! get_transient( '_seed_cspv5_welcome_screen_activation_redirect' ) ) {
    return;
  }

  // Delete the redirect transient
  delete_transient( '_seed_cspv5_welcome_screen_activation_redirect' );

  // Bail if activating from network, or bulk
  if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    return;
  }

  // Redirect to bbPress about page
  wp_safe_redirect( add_query_arg( array( 'page' => 'seed_cspv5_welcome' ), admin_url( 'options-general.php' ) ) );
}

if(!defined('SEED_CSP_API_KEY')){
  add_action('admin_menu', 'seed_cspv5_welcome_screen_pages');
}

function seed_cspv5_welcome_screen_pages() {
  add_options_page(
    'Welcome To SeedProd',
    'Welcome To SeedProd',
    'read',
    'seed_cspv5_welcome',
    'seed_cspv5_welcome_screen_content'
  );
}

function seed_cspv5_welcome_screen_content() {
  require_once(SEED_CSPV5_PLUGIN_PATH.'admin/license.php');
}
add_action( 'admin_head', 'seed_cspv5_welcome_screen_remove_menus' );
function seed_cspv5_welcome_screen_remove_menus() {
    remove_submenu_page( 'options-general.php', 'seed_cspv5_welcome' );
}

/**
 * SeedProd Functions
 */
require_once(SEED_CSPV5_PLUGIN_PATH.'inc/functions.php');


// Api Updates

/**
* API Updates
*/

if( !class_exists( 'SeedProd_Updater_cspv5' ) ) {
    // load our custom updater
    include( dirname( __FILE__ ) . '/seedprod-updater-cspv5.php' );
}
function seed_cspv5_plugin_updater() {

    $seed_cspv5_api_key = '';
    $seed_emaillist = "";
    $seed_admin_email = get_option( 'admin_email','' );
    if(defined('SEED_CSP_API_KEY')){
        $seed_cspv5_api_key = SEED_CSP_API_KEY;
    }
    if(empty($seed_cspv5_api_key)){
        $seed_cspv5_api_key = get_option('seed_cspv5_license_key');
    }
    $csp_page_id = get_option('seed_cspv5_coming_soon_page_id');
    if(!empty($csp_page_id)){
        $page_id = get_option('seed_cspv5_coming_soon_page_id');
        global $wpdb;
        $tablename = $wpdb->prefix . SEED_CSPV5_PAGES_TABLENAME;
        $sql = "SELECT * FROM $tablename WHERE id= %d";
        $safe_sql = $wpdb->prepare($sql,$page_id);
        $page = $wpdb->get_row($safe_sql);

        if(!empty($page->mailprovider)){
            $seed_emaillist = $page->mailprovider;
        }
    }
    $data = array();
    $data['emaillist'] = $seed_emaillist;
    $data['admin_email'] = $seed_admin_email;
    // retrieve our license key from the DB
    //$license_key = trim( get_option( 'edd_sample_license_key' ) );
    // setup the updater

    $seedprod_updater = new SeedProd_Updater_cspv5( SEED_CSPV5_API_URL, __FILE__, array(
            'license'   => $seed_cspv5_api_key,        // license key (used get_option above to retrieve from DB)
            'data'      => $data
        )
    );

}
add_action( 'admin_init', 'seed_cspv5_plugin_updater', 0 );




add_action( 'admin_head', 'seed_cspv5_set_user_settings' );
function seed_cspv5_set_user_settings() {
  if(isset($_GET['page']) && $_GET['page'] == 'seed_cspv5'){
              $user_id = get_current_user_id();
              $options = get_user_option( 'user-settings', $user_id );
              parse_str($options,$user_settings);
              $user_settings['imgsize'] = 'full';
              update_user_option( $user_id, 'user-settings', http_build_query($user_settings), false );
              update_user_option( $user_id, 'user-settings-time', time(), false );
  }
}



// Multisite Initial Install License Check
function seed_cspv5_check_license_updater(){
    if(get_option('seed_cspv5_a') === false){
    add_option('seed_cspv5_license_key',SEED_CSP_API_KEY);
    add_option('seed_cspv5_token',strtolower(wp_generate_password(32,false, false)));

    $params = array(
        'action'     => 'info',
        'license_key'=> SEED_CSP_API_KEY,
        'slug'       => SEED_CSPV5_SLUG,
        'domain'        => home_url(),
        'installed_version' => SEED_CSPV5_VERSION,
        'token'      => get_option('seed_cspv5_token'),
    );
    $request = wp_remote_post( SEED_CSPV5_API_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $params ) );
    if ( ! is_wp_error( $request ) ) {
                $request = wp_remote_retrieve_body( $request );
                $arequest = json_decode($request);
                $nag = $arequest->message;

                update_option('seed_cspv5_license_key',$api_key);
                
                update_option('seed_cspv5_api_message',$nag);
                if($arequest->status == '200'){
                    update_option('seed_cspv5_api_nag','');
                    update_option('seed_cspv5_a',true);
                    update_option('seed_cspv5_per',$arequest->per);
                }elseif($arequest->status == '401'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per','');
                }elseif($arequest->status == '402'){
                    update_option('seed_cspv5_api_nag',$nag);
                    update_option('seed_cspv5_a',false);
                    update_option('seed_cspv5_per',$arequest->per);

                }     


            }
    }
}

if(defined('SEED_CSP_API_KEY')){
  if(isset($_GET['page']) && $_GET['page'] == 'seed_cspv5'){
    add_action( 'admin_init', 'seed_cspv5_check_license_updater');
  }
}

/**
* ManageWP Updates
*/
require_once( SEED_CSPV5_PLUGIN_PATH.'managewp-plugins-api.php' );

// Clear caches
add_action( 'update_option_seed_cspv5_settings_content', 'seed_cspv5_clear_known_caches', 10, 3 );

function seed_cspv5_clear_known_caches($o,$n){
  try {
    if(isset($o['status']) && isset($n['status'])){
      if($o['status'] != $n['status']){

        // Clear Litespeed cache
        method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) && LiteSpeed_Cache_API::purge_all() ;

        // WP Super Cache
        if ( function_exists( 'wp_cache_clear_cache' ) ) {
          wp_cache_clear_cache();
        }

        // W3 Total Cahce
        if ( function_exists( 'w3tc_pgcache_flush' ) ) {
          w3tc_pgcache_flush();
        }

        // Site ground
        if ( class_exists( 'SG_CachePress_Supercacher' ) && method_exists( 'SG_CachePress_Supercacher ',  'purge_cache' )) {
          SG_CachePress_Supercacher::purge_cache(true);
        }

        // Endurance Cache
        if ( class_exists( 'Endurance_Page_Cache' ) ) {
          $e = new Endurance_Page_Cache;
          $e->purge_all();
        }

        // WP Fastest Cache
        if ( isset($GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache') ) {
          $GLOBALS['wp_fastest_cache']->deleteCache(true);
        }

      }
    }
  } catch (Exception $e) {}
}

