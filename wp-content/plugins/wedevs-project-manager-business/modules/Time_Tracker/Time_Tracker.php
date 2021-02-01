<?php
/**
 * Module Name: Time Tracker
 * Description: Track time for each of your project tasks for increasing overall team productivity.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/time-tracker.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */
use League\Fractal;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Transformers\Time_Tracker_Transformer;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Transformers\New_Time_Tracker_Transformer;



pm_pro_load_time_tracker_libs();

add_filter( 'pm_before_create_task', 'pm_pro_timetracker_before_create_task', 10, 3 );
add_filter( 'pm_before_update_task', 'pm_pro_timetracker_before_update_task', 10, 4 );

add_filter( 'pm_pro_before_create_subtask', 'pm_pro_timetracker_before_create_subtask', 10, 2 );
add_filter( 'pm_pro_before_update_subtask', 'pm_pro_timetracker_before_update_subtask', 10, 3 );

add_filter( 'pm_after_create_subtask', 'pm_pro_after_create_subtask', 10, 2 );
add_filter( 'pm_after_assignees', 'pm_pro_after_update_assignees', 10, 2 );

add_action( 'admin_enqueue_scripts', 'register_time_tracker_scripts' );
add_action( 'wp_enqueue_scripts', 'register_time_tracker_scripts' );
// add_action( 'wp_enqueue_scripts', 'pm_pro_enqueue_time_tracker_script' );
add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_time_tracker_scripts' );

add_action( 'pm_load_shortcode_script', 'register_time_tracker_scripts', 10 );
add_action( 'pm_load_shortcode_script', 'pm_pro_enqueue_time_tracker_script', 20 );

add_action( 'pm_after_delete_task', 'pm_pro_after_delete_task', 10 ,2 );
add_action( 'pm_before_delete_task_list', 'pm_pro_after_delete_task_list', 10 ,2 );


function register_time_tracker_scripts() {
    $view_path = dirname (__FILE__) . '/views/assets/';
    wp_register_script( 'time-tracker', plugins_url( 'views/assets/js/time-tracker.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/time-tracker.js' ), true );
    wp_register_style( 'time-tracker', plugins_url( 'views/assets/css/time-tracker.css', __FILE__ ), array(), filemtime( $view_path . 'css/time-tracker.css' ) );
}

function pm_pro_admin_load_time_tracker_scripts() {
    if (
        isset( $_GET['page'] )
            &&
        $_GET['page'] == 'pm_projects'
    ) {
        pm_pro_enqueue_time_tracker_script();
    }
}

function pm_pro_enqueue_time_tracker_script() {
    wp_enqueue_script( 'time-tracker' );
    wp_enqueue_style( 'time-tracker' );
}

add_filter( 'pm_pro_load_router_files', function( $files ) {
    $router_files = glob( __DIR__ . "/routes/*.php" );

    return array_merge( $files, $router_files );
});

add_filter('pm_pro_schema_migrations', function( $files ) {
    $schema_path = array(
        '\\WeDevs\\PM_Pro\\Modules\\Time_Tracker\\Db\\Migrations\\Create_Time_Tracker_Table'
    );
    return array_merge( $files, $schema_path );
});

function pm_pro_load_time_tracker_libs() {
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

add_filter('pm_task_transform', function( $data, $item ) {

    $time = $item->task_model( 'time_tracker' )
        ->where( 'user_id', get_current_user_id() )
        ->get();

    $totalTaskTime = $item->task_model( 'time_tracker' )->sum('total');


    $resource   = new Collection( $time, new Time_Tracker_Transformer );
    $total_time = pm_pro_get_total_time( $time );
    $running    = pm_pro_is_time_running( $time );

    $resource->setMetaValue( 'totalTime', $total_time );
    $resource->setMetaValue( 'totalTaskTime', pm_pro_second_to_time($totalTaskTime) );
    $resource->setMetaValue( 'running', $running );

    $data['time'] = pm_get_response( $resource );
    $data['is_stop_watch_visible'] = $running;
    $data['custom_time_form'] = false;

    return $data;
}, 10, 2);

add_filter('pm_after_transformer_list_tasks', function( $tasks ) {

    if ( empty( $tasks['data'] ) ) {
        return $tasks;
    }

    $task_ids = wp_list_pluck( $tasks['data'], 'id' );
    $time_collection = Time_Tracker::whereIn( 'task_id', $task_ids )
        ->where( 'user_id', get_current_user_id() )
        ->get();

    $resource   = new Collection( $time_collection, new New_Time_Tracker_Transformer );

    $times = pm_get_response( $resource );
    $tasks = pm_pro_set_times_in_task( $tasks, $times );

    return $tasks;
});


add_filter( 'task_model', function( $self, $key ) {
    if ( $key != 'time_tracker' ) {
        return $self;
    }

    return $self->hasMany( 'WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker', 'task_id' );
}, 10, 2 );

// action when a task will complete
add_action( 'pm_changed_task_status', 'stop_all_time_traking_for_tak', 10, 2);

function stop_all_time_traking_for_tak( $task, $old_status ) {
    if( $task->status === Task::$status[0] ) {
        return;
    }

    $times = $task->task_model( 'time_tracker' )
                ->where('run_status', 1)
                ->get()
                ->toArray();

    if ( empty( $times ) ) {
        return;
    }

    foreach ( $times as $time ) {
        $stop  = strtotime( current_time( 'mysql' ) );
        $total = $stop - $time['start'];

        $data = [
            'stop'       => $stop,
            'total'      => $total,
            'run_status' => 0,
        ];

        Time_Tracker::find( $time['id'] )->update_model( $data );
    }

}

add_action( 'pm-activation-time_tracker', 'pm_pro_time_tracker_install', 10 );
add_action( 'wp_initialize_site', 'pm_pro_time_tracker_after_insert_site', 110 );

function pm_pro_time_tracker_install() {
    if ( is_multisite() && is_network_admin() ) {
        $sites = get_sites();

        foreach ( $sites as $key => $site ) {
            pm_pro_time_tracker_after_insert_site( $site );
        }
    } else {
        pm_pro_time_tracker_run_install();
    }
}

function pm_pro_time_tracker_after_insert_site( $blog ) {
    switch_to_blog( $blog->blog_id );

    pm_pro_time_tracker_run_install();

    restore_current_blog();
}

function pm_pro_time_tracker_run_install() {
    pm_pro_create_time_tracker_table();
}

function pm_pro_create_time_tracker_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pm_time_tracker';

    // `run_status` tinyint(4) NOT NULL COMMENT '1: Running; 0: Stop;',
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `user_id` int(11) UNSIGNED NOT NULL,
          `project_id` int(11) UNSIGNED NOT NULL,
          `list_id` int(11) UNSIGNED NOT NULL,
          `task_id` int(11) UNSIGNED NOT NULL,
          `start` int(11) UNSIGNED NOT NULL,
          `stop` int(11) UNSIGNED NOT NULL,
          `total` int(11) UNSIGNED NOT NULL,
          `run_status` tinyint(4) NOT NULL,
          `created_by` int(11) UNSIGNED DEFAULT NULL,
          `updated_by` int(11) UNSIGNED DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `task_id` (`task_id`),
          KEY `project_id` (`project_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );
}

add_action( 'pm_before_delete_task', 'pm_pro_tt_after_delete_task', 10, 2 );
add_action( 'cpm_task_update', 'pm_pro_tt_before_task_update', 10, 3 );





