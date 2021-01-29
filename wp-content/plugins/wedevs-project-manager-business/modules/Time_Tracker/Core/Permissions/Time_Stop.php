<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use Reflection;
use WP_REST_Request;

class Time_Stop extends Abstract_Permission {

    public function check() {
        $user_id   = get_current_user_id();
        $task_id   = $this->request->get_param( 'task_id' );
        $task      = Task::with('assignees')->find( $task_id );
        $assignees = $task->assignees->toArray();//pluck( 'assigned_to' )->all();
        $assignees = wp_list_pluck( $assignees, 'assigned_to' );

         if ( $task ) {

            if ( ! $assignees ) {
                return new \WP_Error( 'time_status', __( "No user assign in this task", "pm" ) );
            }

            if ( ! in_array( $user_id, $assignees ) ) {
                return new \WP_Error( 'time_status', __( "You are not assign in this task", "pm" ) );
            }
        }

    	if ( ! pm_pro_is_user_can_stop_time( $task_id, $user_id ) ) {
    		return new \WP_Error( 'time_status', __( "This task has no running time tracker.", "pm" ) );
    	}

        return true;
    }
}
