<?php
/**
 * Module Name: Recurring Task
 * Description: Repeatedly creates tasks if you set recurrence.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/task-recurring.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

use WeDevs\PM_Pro\Modules\Task_Recurring\Create_Recurrent_Tasks as CRT;


add_action( 'admin_enqueue_scripts', 'register_task_recurrent' );
add_action( 'wp_enqueue_scripts', 'register_task_recurrent' );
add_action( 'admin_enqueue_scripts', 'pm_pro_admin_load_task_recurrent' );

add_action( 'pm_load_shortcode_script', 'register_task_recurrent', 10 );
add_action( 'pm_load_shortcode_script', 'pm_pro_enqueue_task_recurrent_script', 20 );

function register_task_recurrent() {
    $view_path = dirname (__FILE__) . '/views/assets/';
    wp_register_script( 'task-recurrent', plugins_url( 'views/assets/js/task-recurrent.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/task-recurrent.js' ), true );
}

function pm_pro_admin_load_task_recurrent() {
    if (
        isset( $_GET['page'] )
        &&
        $_GET['page'] == 'pm_projects'
    ) {
        pm_pro_enqueue_task_recurrent_script();
    }
}

function pm_pro_enqueue_task_recurrent_script() {
    wp_enqueue_script( 'task-recurrent' );
    // wp_enqueue_style( 'task-recurrent' );
}



new CRT();





