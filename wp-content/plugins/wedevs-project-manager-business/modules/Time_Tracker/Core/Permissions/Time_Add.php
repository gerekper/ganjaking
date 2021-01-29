<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core\Permissions;

use WeDevs\PM\Core\Permissions\Abstract_Permission;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker;
use Reflection;
use WP_REST_Request;

class Time_Add extends Abstract_Permission {

    public function check() {
        $user_id   = get_current_user_id();
        $task_id   = $this->request->get_param( 'task_id' );

        $start1   = $this->request->get_param( 'start' );
        $start  = strtotime( $start1 );
        $stop1   = $this->request->get_param( 'stop' );


        // $checkTImeFormet = preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $stop1) ;
        $checkTImeFormet = preg_match("/^[0-9]?[0-9]:[0-5][0-9]?$/", $stop1) ;

        if(!$checkTImeFormet){
            return new \WP_Error( 'TimeTracker', __( "Time format should be HH:mm [ Ex: 01:30 ]", "pm-pro" ) );
        }

        $stop   = strtotime( $stop1 );

        $task      = Task::with( [ 'assignees'=> function ($q) use( $user_id ) {
            $q->where( 'assigned_to', $user_id );
        } ])->find( $task_id );

        if ( $task->assignees->isEmpty() ) {
            return new \WP_Error( 'TimeTracker', __( "User not assign in this task", "pm-pro" ) );
        }

        $time = Time_Tracker::where( 'user_id', $user_id )
            ->where(function( $q ) use( $start, $stop ) {
                $q->whereBetween( 'start', [$start, $stop] )
                ->orWhereBetween( 'stop', [$start, $stop] );
            })
            ->get();

        if ( !$time->isEmpty() ) {
            //return new \WP_Error( 'TimeTracker', __( "Time already added between {$start1} and {$stop1}. ", "pm-pro" ) );
        }

        return true;


    }
}
