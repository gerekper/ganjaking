<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Src\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker;
use WeDevs\PM\Task\Transformers\Task_Transformer;

class New_Time_Tracker_Transformer extends TransformerAbstract {

    use Resource_Editors;



    public function transform( Time_Tracker $item ) {

        return [
            'id'         => $item->id,
            'user_id'    => $item->user_id,
            'project_id' => $item->project_id,
            'list_id'    => $item->list_id,
            'task_id'    => $item->task_id,
            'start'      => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $item->start ) ) ),
            'stop'       => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $item->stop ) ) ),
            'total'      => pm_pro_second_to_time( $item->total ),
            'run_status' => $item->run_status,
            'created_by' => $item->created_by,
            'updated_by' => $item->updated_by,
            'updated_at' => $item->updated_at,
            'created_at' => format_date( $item->created_at ),
            'user'       => $this->get_users( $item->user_id )
        ];
    }

    public function get_users( $user_id ) {
        $user = get_user_by( 'id', $user_id );

        return [
            'id'                => (int) $user->ID,
            'username'          => $user->user_login,
            'nicename'          => $user->user_nicename,
            'email'             => $user->user_email,
            'profile_url'       => $user->user_url,
            'display_name'      => $user->display_name,
            'manage_capability' => (int) pm_has_manage_capability($user->ID),
            'create_capability' => (int) pm_has_project_create_capability($user->ID),
            'avatar_url'        => get_avatar_url( $user->user_email ),
        ];
    }

}
