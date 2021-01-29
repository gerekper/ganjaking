<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use Reflection;
use WP_REST_Request;

class Time_Delete extends Abstract_Permission {

    public function check() {
        $user_id    = get_current_user_id();
        $time_id    = $this->request->get_param( 'time_id' );
        $project_id = $this->request->get_param( 'project_id' );

        if ( $user_id ) {
            // if ( pm_has_manage_capability() ) {
            //     return true;
            // }

            // if ( $project_id && pm_is_manager( $project_id, $user_id ) ) {
            //     return true;
            // }

        	if ( pm_pro_is_user_can_delete_time( $time_id, $user_id ) ) {
                return true;
            }
        }

        return new \WP_Error( 'time_status', __( "You have no permission to delete time", "pm" ) );
    }
}
