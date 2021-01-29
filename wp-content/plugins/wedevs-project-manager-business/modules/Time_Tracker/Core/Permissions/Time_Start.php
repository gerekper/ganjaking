<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use Reflection;
use WP_REST_Request;

class Time_Start extends Abstract_Permission {

    public function check() {
        $user_id   = get_current_user_id();
        $task_id   = $this->request->get_param( 'task_id' );
        $task      = Task::with('assignees')->find( $task_id );
        $assignees = $task->assignees->toArray();
        $assignees = wp_list_pluck( $assignees, 'assigned_to' );

        $task_data = $task->getAttributes();

		if ( $task ) {

            if ( ! $assignees ) {
                return new \WP_Error( 'time_status', __( "No user assign in this task", "pm" ) );
            }

            if ( ! in_array( $user_id, $assignees ) ) {
                return new \WP_Error( 'time_status', __( "You are not assign in this task", "pm" ) );
            }

            if ( $task_data['status'] == '1' ) {
                return new \WP_Error( 'time_status', __( "Please at first mark undone the task", "pm" ) );
            }
        }

        // $is_current_user_has_running_time = pm_pro_is_user_can_start_time( $user_id );

        // if ( array_key_exists('status' ,$is_current_user_has_running_time) ) {
        //     if(! $is_current_user_has_running_time['status']) {

        //         $task = $is_current_user_has_running_time['response'];
        //         $current_time = $is_current_user_has_running_time['current_time'];
        //         $wp_err = new \WP_Error( 'time_status', __(
        //             "You are already tracking time for <b>[" . $task->title . "]</b></br>" .
        //             "<a href='javascript:void(0)' task='". json_encode($task) ."' time='". json_encode($current_time) ."' onclick='stop_watch(this)'><b>Click here</b></a>  to stop the running task and continue with the new one." .
        //             "</b>", "pm" ) );
        //         return $wp_err ;
        //     }
        // }

        return true;
    }
}
