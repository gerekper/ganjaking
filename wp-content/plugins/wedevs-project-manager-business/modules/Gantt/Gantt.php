<?php
/**
 * Module Name: Gantt Chart
 * Description: Create detailed Gantt charts for your projects and become a professional project manager.
 * Plugin URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/gantt-chart.png
 * Author: weDevs
 * Text Domain: pm-gant
 * Domain Path: /languages
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

use WeDevs\PM_Pro\Modules\Gantt\Src\Models\Gantt;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use League\Fractal;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM_Pro\Modules\Gantt\Src\Transformers\Link_Transformer;

add_action( 'admin_enqueue_scripts', 'register_gantt_scripts' );
add_action( 'wp_enqueue_scripts', 'register_gantt_scripts' );

add_action( 'pm_load_shortcode_script', 'register_gantt_scripts', 10 );
add_action( 'pm_load_shortcode_script', 'pm_pro_gantt_script', 20 );

add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_gantt_scripts' );
add_action( 'pm_load_shortcode_script', 'pm_pro_gantt_script' );

function register_gantt_scripts() {
    $view_path = dirname (__FILE__) . '/views/assets/';
    wp_register_script( 'pmpro-dhtmlx-gantt', plugins_url( 'views/assets/vendor/dhtmlxgantt.js', __FILE__ ), array('pm-vue-router'), filemtime( $view_path . 'vendor/dhtmlxgantt.js' ), true );
    wp_register_script( 'gantt', plugins_url( 'views/assets/js/gantt.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/gantt.js' ), true );
    wp_register_style( 'gantt', plugins_url( 'views/assets/css/gantt.css', __FILE__ ), array(), filemtime( $view_path . 'css/gantt.css' ) );
}

function pm_pro_admin_load_gantt_scripts() {
    if (
        isset( $_GET['page'] )
            &&
        $_GET['page'] == 'pm_projects'
    ) {
        pm_pro_gantt_script();
    }
}

function pm_pro_gantt_script() {
    wp_enqueue_script( 'pmpro-dhtmlx-gantt' );
    wp_enqueue_script( 'gantt' );
    wp_enqueue_style( 'gantt' );
    wp_localize_script( 'gantt', 'PM_Pro_gantt', [] );
}

add_filter( 'pm_pro_load_router_files', function( $files ) {
    $router_files = glob( __DIR__ . "/Routes/*.php" );

    return array_merge( $files, $router_files );
});

add_filter('pm_pro_schema_migrations', function( $files ) {
	$schema_path = array(
		'\\WeDevs\\PM_Pro\\Modules\\Gantt\\Db\\Migrations\\Create_Gantt_Chart_Links_Table'
	);
	return array_merge( $files, $schema_path );
});

//new list_tasks_filter_query
add_filter('list_tasks_filter_query', function( $task_collection ) {
    global $wpdb;
    $gantt = pm_tb_prefix() . 'pm_gantt_chart_links';
    $task = pm_tb_prefix() . 'pm_tasks';

    $task_collection->selectRaw(
        "GROUP_CONCAT(
            DISTINCT
            CONCAT(
                '{',
                    '\"', 'id', '\"', ':' , '\"', IFNULL($gantt.id, '') , '\"' , ',',
                    '\"', 'source', '\"', ':' , '\"', IFNULL($gantt.source, '') , '\"' , ',',
                    '\"', 'target', '\"', ':' , '\"', IFNULL($gantt.target, '') , '\"' , ',',
                    '\"', 'type', '\"', ':' , '\"', IFNULL($gantt.type, '') , '\"'
                ,'}'
            ) SEPARATOR '|'
        ) as gantt_links"
    )
    ->leftJoin( $gantt, function( $join ) use($task, $gantt) {
        $join->on( $task . '.id', '=', $gantt . '.source' );
    });

    return $task_collection;
}, 10 );

add_filter( 'pm_list_task_transormer', function( $task, $item ) {

    $task['gantt_links'] = ['data'=>[]];

    if( ! empty( $item->gantt_links ) ) {
        $gantts = explode( '|', $item->gantt_links );

        foreach ( $gantts as $key => $gantt ) {
            $gantt = str_replace('`', '"', $gantt);
            $gantt = json_decode( $gantt );

            if ( empty( $gantt->id ) ) continue;

            $task['gantt_links']['data'][] = [
                'id'     => $gantt->id,
                'source' => $gantt->source,
                'target' => $gantt->target,
                'type'   => $gantt->type,
            ];
        }
    }

    return $task;

}, 10, 2 );

//Old
add_filter('pm_task_transform', function( $data, $item ) {

    $links = $item->task_model( 'gantt_links' )
        ->get();

    $resource  = new collection( $links, new Link_Transformer );
    $data['gantt_links'] = pm_get_response( $resource );

    return $data;
}, 10, 2);

add_filter( 'task_model', function( $self, $key ) {
    if ( $key != 'gantt_links' ) {
        return $self;
    }

    return $self->hasMany( 'WeDevs\PM_Pro\Modules\Gantt\Src\Models\Gantt', 'source' );
}, 10, 2 );


add_action( 'pm-activation-gantt', 'pm_pro_gantt_install', 10 );
add_action( 'wp_initialize_site', 'pm_pro_gantt_after_insert_site', 110 );

function pm_pro_gantt_install() {
    if ( is_multisite() && is_network_admin() ) {
        $sites = get_sites();

        foreach ( $sites as $key => $site ) {
            pm_pro_gantt_after_insert_site( $site );
        }
    } else {
        pm_pro_gantt_run_install();
    }
}

function pm_pro_gantt_after_insert_site( $blog ) {
    switch_to_blog( $blog->blog_id );

    pm_pro_gantt_run_install();

    restore_current_blog();
}

function pm_pro_gantt_run_install() {
    pm_gantt_create_gantt_chart_table();
}

function pm_gantt_create_gantt_chart_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pm_gantt_chart_links';

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `source` int(11) UNSIGNED NOT NULL,
          `target` int(11) UNSIGNED NOT NULL,
          `type` int(11) UNSIGNED NOT NULL,
          `created_by` int(11) UNSIGNED DEFAULT NULL,
          `updated_by` int(11) UNSIGNED DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`source`) REFERENCES `{$wpdb->prefix}pm_tasks` (`id`) ON DELETE CASCADE,
          FOREIGN KEY (`target`) REFERENCES `{$wpdb->prefix}pm_tasks` (`id`) ON DELETE CASCADE
        ) DEFAULT CHARSET=utf8";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
