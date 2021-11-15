<?php
/**
 * Module Name: KanBan Board
 * Description: Turn your projects into Trello like boards and organize them using drag and drop feature.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/kanban-board.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

use WeDevs\PM\Common\Models\Boardable;

add_action( 'wp_enqueue_scripts', 'register_kanboard_scripts' );
// add_action( 'wp_enqueue_scripts', 'pm_pro_enqueue_kanboard_script' );
add_action( 'admin_enqueue_scripts', 'register_kanboard_scripts' );
add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_kanboard_scripts' );

add_action( 'pm_load_shortcode_script', 'register_kanboard_scripts', 10 );
add_action( 'pm_load_shortcode_script', 'pm_pro_enqueue_kanboard_script', 20 );

add_action( 'pm_after_create_task', ['WeDevs\PM_Pro\Modules\Kanboard\Src\Controllers\Kanboard_Controller','after_create_task'], 10, 2 );
add_action( 'pm_changed_task_status', ['WeDevs\PM_Pro\Modules\Kanboard\Src\Controllers\Kanboard_Controller','before_change_task_status'], 10, 3 );
add_action( 'pm_after_new_comment', ['WeDevs\PM_Pro\Modules\Kanboard\Src\Controllers\Kanboard_Controller','after_new_comment'], 10, 2 );

function register_kanboard_scripts() {
    $view_path = dirname (__FILE__) . '/views/assets/';

    wp_register_script( 'kanboard', plugins_url( 'views/assets/js/kanboard.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/kanboard.js' ), true );
    wp_register_style( 'kanboard', plugins_url( 'views/assets/css/kanboard.css', __FILE__ ), array(), filemtime( $view_path . 'css/kanboard.css' ) );
}

function pm_pro_admin_load_kanboard_scripts() {
    if (
        isset( $_GET['page'] )
            &&
        $_GET['page'] == 'pm_projects'
    ) {
        pm_pro_enqueue_kanboard_script();
    }
}

function pm_pro_enqueue_kanboard_script() {
    wp_enqueue_script( 'kanboard' );
    wp_enqueue_style( 'kanboard' );
}

add_filter( 'pm_pro_load_router_files', function( $files ) {
    $router_files = glob( __DIR__ . "/Routes/*.php" );

    return array_merge( $files, $router_files );
});

add_action('pm_after_create_task', function($task, $request) {
    if ( empty( $request['kan_board_id'] ) ) {
        return;
    }
    $kanboard_id = $request['kan_board_id'];
    $boardable = Boardable::where( 'board_id', $kanboard_id )
            ->where( 'board_type', 'kanboard' )
            ->where( 'boardable_type', 'task' )
            ->orderBy( 'order', 'DESC' )
            ->first();

    if ( $boardable ) {
        $order = $boardable->order + 1;
    } else {
        $order = 0;
    }

	$boardable    = Boardable::create([
        'board_id'       => $kanboard_id,
        'board_type'     => 'kanboard',
        'boardable_id'   => $task->id,
        'boardable_type' => 'task',
        'order'          => $order,
    ]);
}, 10, 2);

function pm_kbc_get_jed_locale_data( $local_data ) {
    $local_data['pm_kbc'] = pm_get_jed_locale_data( 'pm-kbc',   __DIR__. '/languages/' );
    return $local_data ;
}
add_filter ( 'pm_get_jed_locale_data', 'pm_kbc_get_jed_locale_data' );

