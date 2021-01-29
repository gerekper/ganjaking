<?php
namespace WeDevs\PM_Pro\Modules\Sub_Tasks\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use WP_REST_Request;

class Create_Sub_Task extends Abstract_Permission {
    public function check() {
		$project_id = $this->request->get_param( 'project_id' );
		$id         = $this->request->get_param( 'task_id' );
		$user_id    = get_current_user_id();

        if ( pm_user_can_access( pm_manager_cap_slug() ) )  {
            return true;
        }

        if ( pm_is_manager( $project_id, $user_id ) ) {
            return true;
        }

        if ( pm_user_can( 'create_task', $project_id) ) {

            $task = Task::with('assignees')->find( $id );
            if ( $task->created_by == $user_id ) {
            	return true;
            }

            if( $task->assignees->where( 'assigned_to', $user_id )->count() ) {
            	return true;
            }
        }

        return new \WP_Error( 'SubTask', __( "You have no permission.", "pm" ) );
    }
}
