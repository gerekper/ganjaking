<?php
use \WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker;
use WeDevs\PM\Task\Models\Task;
use Carbon\Carbon;

/*function pm_pro_is_user_can_start_time( $user_id = false ) {
	$user_id = absint( $user_id ) ? $user_id : get_current_user_id();

	$user_time = Time_Tracker::where( 'user_id', $user_id )
		->where( 'run_status', '1' )
		->first();

	if ( ! $user_time ) {
		return true;
	}

	return false;
}*/

function pm_pro_is_user_can_start_time( $user_id = false ) {
    $user_id = absint( $user_id ) ? $user_id : get_current_user_id();

    $user_time = Time_Tracker::where( 'user_id', $user_id )
        ->where( 'run_status', '1' )
        ->first();

    if ( ! $user_time ) {
        return [
            'status' => true,
        ];
    }

    $task = Task::where('id',$user_time->task_id)
                ->where('project_id',$user_time->project_id)
                ->first();
    return [
        'response' => $task,
        'status' => false,
        'current_time' => $user_time
    ];
}



function pm_pro_is_user_can_stop_time( $task_id, $user_id = false ) {
    $user_id = absint( $user_id ) ? $user_id : get_current_user_id();

    $user_time = Time_Tracker::where( 'user_id', $user_id )
        ->where( 'run_status', '1' )
        ->where('task_id', $task_id )
        ->first();

    if ( $user_time ) {
        return true;
    }

    return false;
}

function pm_pro_is_user_can_delete_time( $time_id, $user_id = false ) {
    $user_id = absint( $user_id ) ? $user_id : get_current_user_id();
    $time = Time_Tracker::find( $time_id );
    if ( $time &&  (int) $time->user_id == $user_id ) {
        return true;
    }

    return false;
}

function pm_pro_get_total_time( $times ) {
    $times = $times->toArray();

	$total = 0;

	foreach ( $times as $key => $time) {

		if ( $time['total'] <= 0 ) {
			$sub_total =  strtotime( current_time( 'mysql' ) ) - $time['start'];
			$total     = $sub_total + $total;

		} else {
			$total = $time['total'] + $total;
		}
	}

	return pm_pro_second_to_time( $total );
}

function pm_pro_get_total_time_2( $times ) {
    $total = 0;

    foreach ( $times as $key => $time) {

        if ( $time['total']['total_second'] <= 0 ) {
            $sub_total =  strtotime( current_time( 'mysql' ) ) - strtotime( $time['start']['datetime'] );
            $total     = $sub_total + $total;

        } else {
            $total = $time['total']['total_second'] + $total;
        }
    }

    return pm_pro_second_to_time( $total );
}


function pm_pro_is_time_running( $time ) {

    if ( ! is_array($time) ) {
        $time = $time->toArray();
    }

	$total = wp_list_pluck( $time, 'run_status' );

	return in_array( 1, $total );
}

function pm_pro_second_to_time( $seconds ) {
    $total_second = $seconds;
    // extract hours
    $hours = floor( $seconds / (60 * 60) );

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor( $divisor_for_minutes / 60 );

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil( $divisor_for_seconds );

    // return the final array
    $obj = array(
        'hour' => str_pad( (int) $hours, 2, '0', STR_PAD_LEFT ),
        'minute' => str_pad( (int) $minutes, 2, '0', STR_PAD_LEFT ),
        'second' => str_pad( (int) $seconds, 2, '0', STR_PAD_LEFT ),
        'total_second' => $total_second
    );

    return $obj;
}

function pm_pro_tt_after_delete_task($task, $request_params) {

    $task_id = $task->id;
    $project_id = $task->project_id;

    // Select the time
    $times = Time_Tracker::where( 'task_id', $task_id )
        ->where( 'project_id', $project_id)
        ->where('stop', 0)->get()->toArray();

    foreach ( $times as $key => $time) {
        $user_time = Time_Tracker::where( 'task_id', $task_id )
            ->where( 'project_id', $project_id)
            ->where( 'stop', 0)
            ->where( 'user_id', $time['user_id'] )
            ->first();

        if ( $user_time ) {
            $stop  = strtotime( current_time( 'mysql' ) );
            $total = $stop - $time['start'];

            $data = [
                'stop'       => $stop,
                'total'      => $total,
                'run_status' => 0,
            ];

            $user_time->update_model( $data );
        }
    }
    // if ( $time ) {
    //     $stop  = strtotime( current_time( 'mysql' ) );
    //     $total = $stop - $time->start;

    //     $data = [
    //         'stop'       => $stop,
    //         'total'      => $total,
    //         'run_status' => 0,
    //     ];

    //     $time->update_model( $data );
    // }
}

function pm_pro_tt_before_task_update( $list_id, $task_id, $request_params ) {

    $task_id = $request_params['task_id'];
    $project_id = $request_params['project_id'];
    $assignes = empty( $request_params['assignees'] ) ? [] : $request_params['assignees'];

    // Select the time
    $time = Time_Tracker::where( 'task_id', $task_id )
        ->where( 'project_id', $project_id)
        ->where( 'stop', 0)
        ->get()->toArray();

    $user_ids = wp_list_pluck($time, 'user_id');
    $starts = wp_list_pluck($time, 'start', 'user_id');

    $users = array_diff( $user_ids, $assignes );

    foreach ($users as $key => $user_id) {
        $user_time = Time_Tracker::where( 'task_id', $task_id )
            ->where( 'project_id', $project_id)
            ->where( 'stop', 0)
            ->where( 'user_id', $user_id )
            ->first();

        if ( $user_time ) {
            $stop  = strtotime( current_time( 'mysql' ) );
            $total = $stop - $user_time->start;

            $data = [
                'stop'       => $stop,
                'total'      => $total,
                'run_status' => 0,
            ];

            $user_time->update_model( $data );
        }
    }
}

function pm_pro_set_times_in_task( $tasks, $times ) {

    $time_attr = [];

    foreach ( $times['data'] as $key => $time ) {
        $time_attr[$time['task_id']][] = $time;
    }

    foreach ( $tasks['data'] as $key => $task ) {
        $tasks['data'][$key]['time']['data'] = empty( $time_attr[$task['id']] ) ? [] : $time_attr[$task['id']];
        $tasks['data'][$key]['time']['meta']['running'] = pm_pro_is_time_running( $tasks['data'][$key]['time']['data'] );
        $tasks['data'][$key]['time']['meta']['totalTime'] = pm_pro_get_total_time_2( $tasks['data'][$key]['time']['data'] );

        $tasks['data'][$key]['is_stop_watch_visible'] = pm_pro_is_time_running( $tasks['data'][$key]['time']['data'] );
        $tasks['data'][$key]['custom_time_form'] = false;
    }

    return $tasks;
}

function pm_pro_timetracker_before_create_task( $data, $board_id, $request ) {

    if ( ! isset( $data['estimation'] ) ) {
        $data['estimation'] = 0;
    };

    if( ! pm_pro_has_subtask_estimated_time( $data ) ) {
        $data['estimation'] = 0;
    } else {
        $data['estimation'] = pm_pro_get_hour_to_minute( $data['estimation'] );

    }

    return $data;
}

function pm_pro_timetracker_before_update_task( $params, $list_id, $task_id, $task ) {

    if ( ! isset( $params['estimation'] ) ) {
        $params['estimation'] = 0;
    };

    if( ! pm_pro_has_estimated_time_permission( $task, $params ) ) {
        $params['estimation'] = 0;
    } else {
        $params['estimation'] = pm_pro_get_hour_to_minute( $params['estimation'] );

    }

    return $params;
}

function pm_pro_has_estimated_time_permission( $task, $params ) {

    if ( pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' ) ) {
        $subtasks = WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Models\Sub_Tasks::select('id')
            ->where('parent_id', $task->id)
            ->get()
            ->toArray();

        if ( count( $subtasks ) ) {
            return false;
        }
    }

    if ( empty( $params['assignees'] ) ) {
        return true;
    }

    if ( count( $params['assignees'] ) > 1 ) {
        return false;
    }

    if ( ! count( $params['assignees'] ) ) {
        return false;
    }

    if ( isset( $params['assignees'][0] ) && empty( $params['assignees'][0] ) ) {
        return false;
    }

    return true;
}

function pm_pro_has_subtask_estimated_time( $params ) {

    if ( ! isset( $params['assignees'] ) ) return 0;

    if ( ! is_array( $params['assignees'] ) ) {
        return false;
    }

    if ( count( $params['assignees'] ) > 1 ) {
        return false;
    }

    if ( ! count( $params['assignees'] ) ) {
        return false;
    }

    if ( isset( $params['assignees'][0] ) && empty( $params['assignees'][0] ) ) {
        return false;
    }

    return true;
}

function pm_pro_timetracker_before_create_subtask( $data ) {
    //$request = $request->get_params();

    if ( ! isset( $data['estimation'] ) ) {
        $data['estimation'] = 0;
    };

    if( ! pm_pro_has_subtask_estimated_time( $data ) ) {
        $data['estimation'] = 0;
    } else {
        $data['estimation'] = pm_pro_get_hour_to_minute( $data['estimation'] );

    }

    return $data;
}

function pm_pro_timetracker_before_update_subtask( $data, $sub_task ) {

    $status  = empty( $data['status'] ) ? false : $data['status'];

    if ( $status ) {
        $data['completed_by'] = get_current_user_id();
        $data['completed_at'] = Carbon::now();
    } else {
        $data['completed_by'] = null;
        $data['completed_at'] = null;
    }

    if ( isset( $data['estimation']) && empty( $data['estimation'] ) ) {
        $data['estimation'] = 0;
    };

    if( ! pm_pro_has_subtask_estimated_time( $data ) ) {
        if ( ! $status && isset( $data['estimation'] ) && empty( $data['estimation'] ) ) {
            $data['estimation'] = 0;
        }
    } else {
        $data['estimation'] = pm_pro_get_hour_to_minute( $data['estimation'] );
    }

    return $data;
}


function pm_pro_get_hour_to_minute( $time ) {
    $pos = strpos( $time, ":" );

    if ( $pos === false ) {
        if( absint( $time ) ) {
            return absint($time);
        }

        return 0;
    }

    $explode = explode( ':', $time );
    $hours   = absint( $explode[0] );
    $minutes = absint( $explode[1] );

    if( $minutes > 59 ) {
       $minutes = substr( $minutes, 0, -1);
    }

    return ($hours*60)+$minutes;
}

function pm_pro_after_create_subtask( $task, $request ) {
    //$request = $request->get_params();
    $task_id = $task->parent_id;
    $task_data = WeDevs\PM\Task\Models\Task::find( $task_id );

    if ( ! empty( $task_data ) ) {
        $task_data->estimation = 0;
        $task_data->save();
    }
}

function pm_pro_after_update_assignees( $task, $assignees ) {

    if ( empty( $assignees ) ) {
        $task_id = $task->parent_id;
        $task_data = WeDevs\PM\Task\Models\Task::find( $task_id );

        if ( ! empty( $task_data ) ) {
            $task_data->estimation = 0;
            $task_data->save();
        }
    }

    if ( !empty( $assignees ) && !empty($assignees[0]) ) {
        $task_id = $task->parent_id;
        $task_data = WeDevs\PM\Task\Models\Task::find( $task_id );

        if ( ! empty( $task_data ) ) {
            $task_data->estimation = 0;
            $task_data->save();
        }
    }

    if ( !empty( $assignees ) && count($assignees) > 1 ) {
        $task_id = $task->parent_id;
        $task_data = WeDevs\PM\Task\Models\Task::find( $task_id );

        if ( ! empty( $task_data ) ) {
            $task_data->estimation = 0;
            $task_data->save();
        }
    }
}

function pm_pro_after_delete_task( $task_id, $project_id ) {
    // Select the time
    WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker::where( 'task_id', $task_id )
        ->delete();
}

function pm_pro_after_delete_task_list( $task_list_id, $project_id ) {
    // Select the time
    WeDevs\PM_Pro\Modules\Time_Tracker\Src\Models\Time_Tracker::where( 'list_id', $task_list_id )
        ->where( 'project_id', $project_id )
        ->delete();
}

function pm_pro_get_times( $params = [] ) {
    return \WeDevs\PM_Pro\Modules\Time_Tracker\Src\Helper\Time_Tracker::get_results( $params );
}


