<?php
/**
 * Module Name: Sub Task
 * Description: Break down your to-dos into smaller tasks for better management and project tracking.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/sub-task.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

use WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Models\Sub_Tasks;
use WeDevs\PM\Common\Models\Assignee;
use WeDevs\PM_Pro\Modules\Sub_Tasks\Core\Action;

add_action( 'admin_enqueue_scripts', 'pm_pro_register_sub_task_scripts' );
add_action( 'wp_enqueue_scripts', 'pm_pro_register_sub_task_scripts' );
add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_sub_task_scripts' );
add_action( 'pm_load_shortcode_script', 'pm_pro_enqueue_sub_tasks_script' );
add_filter( 'pm_task_deleted_users', 'pm_pro_task_deleted_users', 10, 2 );

//task user cant be deleted if the user exist in subtask
function pm_pro_task_deleted_users( $deleted_users, $task ) {
    $subtask_ids = pm_pro_get_subtask_by_task_id( $task->id );
    $subtask_ids = wp_list_pluck( $subtask_ids, 'id' );

    foreach ( $deleted_users as $key => $deleted_user ) {
        $has_users = Assignee::whereIn( 'task_id', $subtask_ids )
            ->where( 'assigned_to', $deleted_user['assigned_to'] )
            ->get()
            ->toArray();

        if ( ! empty( $has_users ) ) {
            unset( $deleted_users[$key] );
        }
    }

    return $deleted_users;
}

function pm_pro_register_sub_task_scripts() {
	$view_path = dirname (__FILE__) . '/views/assets/';
	wp_register_script( 'sub-tasks', plugins_url( 'views/assets/js/sub-tasks.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/sub-tasks.js' ), true );
	wp_register_style( 'sub-tasks', plugins_url( 'views/assets/css/sub-tasks.css', __FILE__ ), array(), filemtime( $view_path . 'css/sub-tasks.css' ) );
}

function pm_pro_admin_load_sub_task_scripts() {
    if (
        isset( $_GET['page'] )
            &&
        $_GET['page'] == 'pm_projects'
    ) {
        pm_pro_enqueue_sub_tasks_script();
    }
}

function pm_pro_enqueue_sub_tasks_script() {
    pm_pro_register_sub_task_scripts();
	wp_enqueue_script( 'sub-tasks' );
	wp_enqueue_style( 'sub-tasks' );
}

add_filter( 'pm_pro_load_router_files', function( $files ) {
	$sub_tasks_router_files = glob( __DIR__ . "/routes/*.php" );

	return array_merge( $files, $sub_tasks_router_files );
});

add_filter( 'pm_task_transform', function ( $task_transform ) {
	$task_transform['new_sub_task_form'] = false;
	$task_transform['sub_tasks'] = pm_pro_get_subtask_by_task_id( $task_transform['id'] );
	$task_transform['sub_task_content'] = false;

	return $task_transform;
});

function pm_pro_get_subtask_by_task_id( $task_id ) {
    return Sub_Tasks::select('id')->where('parent_id', $task_id)->get()->toArray();
}

//new list_tasks_filter_query
add_filter('list_tasks_filter_query', function( $task_collection ) {
    global $wpdb;
    $subtask = pm_tb_prefix() . 'pm_tasks';

    $task_collection->selectRaw(
        "GROUP_CONCAT(
            DISTINCT
            CONCAT(
                '{',
                    '\"', 'id', '\"', ':' , '\"', IFNULL(sub.id, '') , '\"'
                ,'}'
            ) SEPARATOR '|'
        ) as sub_tasks"
    )
    ->leftJoin( $subtask . ' AS sub', function( $join ) use($subtask) {
        $join->on( $subtask . '.id', '=',  'sub.parent_id' );
    });

    return $task_collection;
}, 10 );

add_filter( 'pm_list_task_transormer', function( $task, $item ) {

    $task['sub_tasks'] = [];

    if( ! empty( $item->sub_tasks ) ) {
        $subtasks = explode( '|', $item->sub_tasks );

        foreach ( $subtasks as $key => $subtask ) {
            $subtask = str_replace('`', '"', $subtask);
            $subtask = json_decode( $subtask );

            if ( empty( $subtask->id ) ) continue;

            $task['sub_tasks'][] = [
                'id' => $subtask->id,
            ];
        }
    }

    return $task;

}, 10, 2 );

function pm_pro_get_sub_tasks( $params = [] ) {
    return \WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Helper\Sub_Task::get_results( $params );
}

new Action();







