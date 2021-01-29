<?php

namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use WP_REST_Request;

class Edit_Sub_Task extends Abstract_Permission {

    public function check() {
        $id = $this->request->get_param( 'sub_task_id' );
        $project_id = $this->request->get_param( 'project_id' );
        $user_id = get_current_user_id();

        if ( pm_user_can_access( pm_manager_cap_slug() ) )  {
            return true;
        }

        if ( $user_id ) {

        	if ( $project_id && pm_is_manager( $project_id, $user_id ) ) {
	            return true;
	        }
            $task = Task::with('assignees')->find( $id );

	        if ( isset( $task->created_by ) && $task->created_by == $user_id ){
	        	return true;
	        }

            if ( pm_user_can_complete_task( $task, $user_id ) ) {
                return true;
            }

        }

        return new \WP_Error( 'SubTask', __( "You have no permission.", "pm" ) );
    }
}
