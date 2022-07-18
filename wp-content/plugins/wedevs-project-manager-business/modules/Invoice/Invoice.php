<?php
/**
 * Module Name: Project Invoice
 * Description: Generate invoice for your projects anytime; print, download and send emails to your client.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/invoice.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */
use WeDevs\PM\Core\WP\Enqueue_Scripts as PM_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as PM_Pro_Scripts;

pm_pro_load_invoice_libs();

new WeDevs\PM_Pro\Modules\Invoice\Core\Paypal\Paypal;

define( 'PM_PRO_INVOICE_PATH', plugin_dir_path(__FILE__) );
define( 'PM_PRO_INVOICE_URL', plugin_dir_url(__FILE__) );

if ( function_exists( 'pm_is_request' ) ) {
	if ( pm_is_request('frontend') ) {
		include_once( 'includes/Shortcodes.php' );
	}
}


WeDevs\PM_Pro\Modules\Invoice\includes\Shortcodes::init();

add_action( 'admin_enqueue_scripts', 'register_invoice_scripts' );
add_action( 'wp_enqueue_scripts', 'register_invoice_scripts' );
add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_invoice_scripts' );

add_action( 'pm_load_shortcode_script', 'register_invoice_scripts', 10 );
add_action( 'pm_load_shortcode_script', 'pm_pro_invoice_scripts', 20 );


function pm_pro_admin_load_invoice_scripts() {
	if (
		isset( $_GET['page'] )
			&&
		$_GET['page'] == 'pm_projects'
	) {
		pm_pro_invoice_scripts();
	}
}

function pm_pro_common_invoice_localize() {
	wp_localize_script('pm-const', 'PM_Pro_Invoice', [
		'is_active_time_tracker' => pm_pro_is_module_active('Time_Tracker/Time_Tracker.php')
	]);
}

function register_invoice_scripts() {
	$view_path = dirname (__FILE__) . '/views/assets/';
	wp_register_script( 'pm-pro-invoice', plugins_url( 'views/assets/js/invoice.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/invoice.js' ), true );
	wp_register_style( 'pm-pro-invoice', plugins_url( 'views/assets/css/invoice.css', __FILE__ ), array(), filemtime( $view_path . 'css/invoice.css' ) );
}


function pm_pro_invoice_scripts() {
	wp_enqueue_script( 'pm-pro-invoice' );
	wp_enqueue_style( 'pm-pro-invoice' );
	pm_pro_common_invoice_localize();
}

function pm_pro_invoice_front_end_scripts( $project_id ) {
	$view_path = dirname (__FILE__) . '/views/assets/';
	wp_enqueue_script( 'pm-const' );
	PM_Scripts::localize_scripts();
	do_action('pm_pro_invoice_front_end_script');
	wp_enqueue_script( 'pm-pro-invoice-front-end', plugins_url( 'views/assets/js/invoice-frontend.js', __FILE__ ), array('pm-config'), filemtime( $view_path . 'js/invoice-frontend.js' ), true );

	$localize_data = PM_Pro_Scripts::localize_data();

	$localize_data['project_id']   = $project_id;
	$localize_data['listener_url'] = add_query_arg( 'action', 'pm_paypal_success', home_url( '/' ) );
	$localize_data['return_url']   = add_query_arg( 'action', 'pm_paypal_success', get_permalink() );
	$localize_data['bloginfo_name'] = get_bloginfo( 'name' );


	wp_localize_script( 'pm-const', 'PM_Pro_Vars', $localize_data );
	pm_pro_common_invoice_localize();
	wp_enqueue_style( 'pm-pro-invoice', plugins_url( 'views/assets/css/invoice.css', __FILE__ ), array(), filemtime( $view_path . 'css/invoice.css' ) );
}



add_filter( 'pm_pro_load_router_files', function( $files ) {
    $router_files = glob( __DIR__ . "/routes/*.php" );

    return array_merge( $files, $router_files );
});

add_filter('pm_pro_schema_migrations', function( $files ) {
	$schema_path = array(
		'\\WeDevs\\PM_Pro\\Modules\\Invoice\\Db\\Migrations\\Create_Invoice_Table'
	);
	return array_merge( $files, $schema_path );
});

function pm_pro_load_invoice_libs() {
    $files = glob( __DIR__ . "/libs/*.php" );

    if ( $files === false ) {
        throw new RuntimeException( "Failed to glob for lib files" );
    }

    foreach ($files as $file) {
        require_once $file;
    }

    unset( $file );
    unset( $files );
}

add_action('pm-activation-invoice', 'pm_pro_create_invoice_page', 10, 1);

function pm_pro_create_invoice_page( $module_info ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    $page_data = array(
        'post_status'    => 'publish',
        'post_author'    => get_current_user_id(),
        'comment_status' => 'close',
        'ping_status'    => 'close',
        'post_type'      => 'page',
        'post_parent'    => 0,
    );

    $pm_pages  = get_option( 'pm_pages', [] );

    if (  empty( $pm_pages['invoice'] ) ) {

        $page_title = __( 'Invoice', 'pmi' );

        $page_data['post_title']   = $page_title;
        $page_data['post_content'] = "[pm_invoice]";

        $e = wp_insert_post( $page_data, true );

        if ( ! is_wp_error( $e ) ) {
        	$pm_pages['invoice'] = $e;
            update_option( 'pm_pages', $pm_pages );
        }
    }
}

add_action( 'pm-activation-invoice', 'pm_pro_invoice_install', 10 );
add_action( 'wp_initialize_site', 'pm_pro_invoice_after_insert_site', 110 );

function pm_pro_invoice_install() {
    if ( is_multisite() && is_network_admin() ) {
        $sites = get_sites();

        foreach ( $sites as $key => $site ) {
            pm_pro_invoice_after_insert_site( $site );
        }
    } else {
        pm_pro_invoice_run_install();
    }
}

function pm_pro_invoice_after_insert_site( $blog ) {
    switch_to_blog( $blog->blog_id );

    pm_pro_invoice_run_install();

    restore_current_blog();
}

function pm_pro_invoice_run_install() {
    pm_pro_create_invoice_table();
}

function pm_pro_create_invoice_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pm_invoice';
	// `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: Incomplete; 1: Complete; 2: Partial',
	// `partial` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1: Partial; 0: Not Partial;',
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
	  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` varchar(255) NOT NULL,
	  `client_id` int(11) UNSIGNED NOT NULL,
	  `project_id` int(11) UNSIGNED NOT NULL,
	  `status` tinyint(4) NOT NULL DEFAULT 0,
	  `start_at` timestamp NULL DEFAULT NULL,
	  `due_date` timestamp NULL DEFAULT NULL,
	  `discount` double(8,2) NOT NULL DEFAULT '0.00',
	  `partial` tinyint(4) NOT NULL DEFAULT 0,
	  `partial_amount` double(8,2) NOT NULL DEFAULT '0.00',
	  `terms` text,
	  `client_note` text,
	  `items` longtext NOT NULL,
	  `created_by` int(11) UNSIGNED DEFAULT NULL,
	  `updated_by` int(11) UNSIGNED DEFAULT NULL,
	  `created_at` timestamp NULL DEFAULT NULL,
	  `updated_at` timestamp NULL DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `project_id` (`project_id`),
	  KEY `client_id` (`client_id`),
	  FOREIGN KEY (`project_id`) REFERENCES `{$wpdb->prefix}pm_projects` (`id`) ON DELETE CASCADE
	) DEFAULT CHARSET=utf8";


	dbDelta( $sql );
}







