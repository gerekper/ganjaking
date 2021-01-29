<?php

namespace WeDevs\PM_Pro\Modules\Time_Tracker\Core;

use WeDevs\PM_Pro\Modules\Time_Tracker\Core\Reports;
use WeDevs\PM\task\Helper\Task;
use WeDevs\PM_Pro\Modules\Sub_Tasks\Src\Helper\Sub_Task;
use WeDevs\PM_Pro\Modules\Time_Tracker\Src\Helper\Time_Tracker;


class Report_Users {

    use Reports;

    private $tasks = [];
    private $sub_tasks = [];
    private $times = [];
    private $lists = [];
    private $projects = [];
    private $report_for = 'task';

    private static $_instance;

    private static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    static function users( $params ) {
        return self::getInstance()->get_users_by_task_estimated_time( $params );
    }

    private function get_users_by_task_estimated_time( $params ) {
        $users = $params['users'];

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' )
        ) {
            $this->report_for = 'sub_task';
        }

        $this->get_task_by_user( $params )
            ->get_sub_task_by_user( $params )
            ->get_traking_time_by_user( $params )
            ->get_list_by_task_id( $params )
            ->get_project_by_user_id( $params )
            ->merge_with_projects();

        $projects       = $this->get_report_by_projects( $params );
        $sub_task_types = $this->get_report_by_sub_task_type( $params );
        $task_types     = $this->get_report_by_task_type( $params );
        $meta           = $this->get_meta( $params );
        $tasks          = $this->get_tasks( $params );
        $users          = $this->get_users( $params );

        return [
            'type'           => 'get_users_by_task_estimated_time',
            'projects'       => $projects,
            'sub_task_types' => $sub_task_types,
            'task_types'     => $task_types,
            'meta'           => $meta,
            'tasks'          => $tasks,
            'users'          => $users,
            'report_for'     => $this->report_for
        ];

    }

    private function get_users_by_subtask_estimated_time( $params ) {
        $users = $params['users'];
        $users = empty($users) ? [] : explode( ',', $users );

        $time_logs = $this->get_time_log( $params['startDate'], $params['endDate'], $users );

        $time_logs = $this->get_subtasks( $time_logs );

        return [
            'type' => 'get_users_by_subtask_estimated_time',
            'users' => $time_logs
        ];
    }

    private function get_users( $params ) {
        $requested_users = pm_get_prepare_data( $params['users'] );

        $users = [];

        foreach ( $requested_users as $key => $user_id ) {
            foreach ( $this->projects as $key => $project ) {
                foreach ( $project->assignees['data'] as $key => $user ) {
                    if ( $user->id != $user_id ) {
                        continue;
                    }

                    $users[$user_id ] = $user;
                }
            }
        }

        return $users;
    }

    private function get_tasks( $params ) {
        $requested_users = pm_get_prepare_data( $params['users'] );

        $tasks = $this->tasks;
        $user_tasks = [ 'data' => [], 'meta' => [] ];
        $total_hours = 0;

        if ( $this->report_for == 'sub_task' ) {
            $tasks = $this->sub_tasks;
        }

        foreach ( $requested_users as $key => $user_id ) {

            $user_info = '';

            $user_tasks['data'][$user_id]['data'] = [];
            $user_tasks['data'][$user_id]['meta'] = [];

            foreach ( $tasks as $key => $task ) {
                $task_user_ids = wp_list_pluck( $task->assignees['data'], 'id' );

                foreach ( $task->assignees['data'] as $key => $user ) {

                    if ( $user->id != $user_id ) {
                        continue;
                    }

                    $project             = empty( $this->projects[$task->project_id] ) ? false : $this->projects[$task->project_id];
                    $task->project_title = $project ? $project->title : '';
                    $task->estimation    = empty( $task->estimation ) ? 0 : $task->estimation; //(float)number_format( (float)($task->estimation/3600), 2, '.', '');

                    $time = pm_second_to_time( $task->estimation );
                    $time = $time['hour'] .':'. $time['minute'];
                    $task->estimation_tf  = $time;

                    $user_tasks['data'][$user_id]['data'][] = $task;
                    $user_info = $user;

                    $total_hours = $total_hours + $task->estimation;
                }
            }

            $user_tasks['data'][$user_id]['meta']['user'] = $user_info;
        }

        $total_hours_tf = pm_second_to_time( $total_hours );
        $total_hours_tf = $total_hours_tf['hour'] .':'. $total_hours_tf['minute'];

        $user_tasks['meta']['total_hours'] = number_format( $total_hours/3600, 2, '.', '');
        $user_tasks['meta']['total_hours_tf'] = $total_hours_tf;

        return $user_tasks;
    }

    private function get_meta( $params ) {
        $requested_users = pm_get_prepare_data( $params['users'] );

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );


        $datediff = strtotime( $due_date ) - strtotime( $start_at );
        $day_count =  round($datediff / (60 * 60 * 24)) + 1;

        $tasks = $this->tasks;
        $meta = [];


        if ( $this->report_for == 'sub_task' ) {
            $tasks = $this->sub_tasks;
        }

        foreach ( $requested_users as $key => $user_id ) {
            $estimated_seconds  = 0;
            $completed_tasks    = 0;
            $types[$user_id] = [];

            $meta[$user_id] = [];
            $user_info = '';

            foreach ( $tasks as $key => $task ) {
                $task_user_ids = wp_list_pluck( $task->assignees['data'], 'id' );

                foreach ( $task->assignees['data'] as $key => $user ) {

                    if ( $user->id != $user_id ) {
                        continue;
                    }

                    $estimated_seconds = $estimated_seconds + $task->estimation;
                    $completed_tasks = $completed_tasks + 1;
                    $meta[$user_id]['user'] = $user;
                }
            }

            $total_est = pm_second_to_time( $estimated_seconds );
            $total_est = $total_est['hour'] .':'. $total_est['minute'];

            $total_estimation = empty( $estimated_seconds ) ? 0 : number_format( $estimated_seconds/3600, 2, '.', '');

            $avg_hour_task    = empty( $total_estimation ) ? 0 : $total_estimation/$completed_tasks;
            $avg_hour_task_tf = empty( $estimated_seconds ) ? 0 : $estimated_seconds/$completed_tasks;
            $avg_hour_task_tf = pm_second_to_time( $avg_hour_task_tf );
            $avg_hour_task_tf = $avg_hour_task_tf['hour'] .':'. $avg_hour_task_tf['minute'];

            $avg_work_hour    = empty( $day_count ) ? 0 : $total_estimation/$day_count;
            $avg_work_hour_tf = empty( $estimated_seconds ) ? 0 : $estimated_seconds/$completed_tasks;
            $avg_work_hour_tf = pm_second_to_time( $avg_work_hour_tf );
            $avg_work_hour_tf = $avg_work_hour_tf['hour'] .':'. $avg_work_hour_tf['minute'];

            $avg_task_day     = empty( $day_count ) ? 0 : $completed_tasks/$day_count;

            $meta[$user_id]['total_estimation']    = (float)number_format( (float)$total_estimation, 2, '.', '');
            $meta[$user_id]['total_estimation_tf'] = $total_est;

            $meta[$user_id]['avg_hour_task']       = (float)number_format( (float)$avg_hour_task, 2, '.', '');
            $meta[$user_id]['avg_hour_task_tf']    = $avg_hour_task_tf;

            $meta[$user_id]['avg_work_hour']       = (float)number_format( (float)$avg_work_hour, 2, '.', '');
            $meta[$user_id]['avg_work_hour_tf']    = $avg_work_hour_tf;

            $meta[$user_id]['avg_task_day']        = (float)number_format( (float)$avg_task_day, 2, '.', '');
            $meta[$user_id]['completed_tasks']     = $completed_tasks;
        }

        return $meta;
    }

    private function get_report_by_sub_task_type( $params ) {
        $requested_users = pm_get_prepare_data( $params['users'] );

        $sub_tasks = $this->sub_tasks;
        $types = [];
        $total_estimation = 0;
        $total_sub_tasks = 0;

        if (
            !function_exists( 'pm_pro_is_module_active' )
                &&
            !pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' )
        ) {
            return [ 'data' => [], 'meta' => [] ];
        }

        $types = [ 'data' => [], 'meta' => [] ];

        foreach ( $requested_users as $key => $user_id ) {
            $estimated_sub_task_seconds  = 0;
            $completed_sub_tasks         = 0;


            $types['data'][$user_id]['data'] = [];
            $types['data'][$user_id]['meta'] = [];

            $meta[$user_id] = [];
            $user_info = '';
            $user_total_estimation = 0;

            foreach ( $sub_tasks as $key => $sub_task ) {

                foreach ( $sub_task->assignees['data'] as $key => $user ) {

                    if ( $user->id != $user_id || empty( $sub_task->type ) ) {
                        continue;
                    }

                    $meta[$user_id][$sub_task->type['id']][] = $sub_task;
                    $user_info = $user;
                }
            }

            foreach ( $meta[$user_id] as $type_id => $items ) {

                if ( empty( $items ) ) {
                    continue;
                }
                $estimations = wp_list_pluck( $items, 'estimation' );
                $total_time  = pm_second_to_time( array_sum( $estimations ) );
                $total_time  = $total_time['hour'] .':'. $total_time['minute'];
                $total_in_st = count( $items );

                $total_estimation = $total_estimation + array_sum( $estimations );
                $total_sub_tasks = $total_sub_tasks + $total_in_st;

                $types['data'][$user_id]['data'][] = [
                    'type'            => $items[0]->type,
                    'estimated_hours' => number_format( array_sum( $estimations )/3600, 2, '.', ''),
                    'estimated_hours_tf' => $total_time,
                    'completed'       => $total_in_st
                ];

                $types['data'][$user_id]['meta'] = [
                    'user' => $user_info,
                    'report_for' => $this->report_for
                ];
            }
        }

        $total_estimation_tf  = pm_second_to_time( $total_estimation );
        $total_estimation_tf  = $total_estimation_tf['hour'] .':'. $total_estimation_tf['minute'];

        $types['meta']['total_estimation']    = number_format( $total_estimation/3600, 2, '.', '');
        $types['meta']['total_estimation_tf'] = $total_estimation_tf;
        $types['meta']['total_sub_tasks']     = $total_sub_tasks;

        return $types;
    }

    private function get_report_by_task_type( $params ) {
        $requested_users = pm_get_prepare_data( $params['users'] );

        $tasks           = $this->tasks;
        $types           = [ 'data' => [], 'meta' => [] ];
        $has_subtask     = 'no';
        $total_estimation = 0;

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' )
        ) {
            $has_subtask = 'yes';
        }

        if ( $has_subtask == 'yes' ) {
            $subtask_tks = [];

            foreach ( $this->sub_tasks as $key => $sub_task ) {
                $task_id = $sub_task->task['data']['id'];
                $subtask_tks[$task_id] = (Object)$sub_task->task['data'];
            }

            foreach ( $subtask_tks as $key => $task ) {
                $estimation = 0;

                foreach ( $this->sub_tasks as $key => $sub_task ) {

                    if (
                        $task->status == 'incomplete'
                            &&
                        $sub_task->status == 'incomplete'
                    ) {
                        continue;
                    }

                    if ( $task->id == $sub_task->parent_id ) {
                        $estimation = $estimation + $sub_task->estimation;
                    }
                }

                $task->estimation = $estimation;
            }

            $tasks = $subtask_tks;
        }

        foreach ( $requested_users as $key => $user_id ) {
            $estimated_task_seconds  = 0;
            $completed_tasks         = 0;
            $total_tasks = 0;

            $types['data'][$user_id]['data'] = [];
            $types['data'][$user_id]['meta'] = [];

            $meta[$user_id] = [];
            $user_info      = '';

            foreach ( $tasks as $key => $task ) {

                foreach ( $task->assignees['data'] as $key => $user ) {

                    if ( $user->id != $user_id || empty( $task->type ) ) {
                        continue;
                    }

                    $meta[$user_id][$task->type['id']][] = $task;
                    $user_info = $user;
                }
            }

            foreach ( $meta[$user_id] as $type_id => $items ) {

                if ( empty( $items ) ) {
                    continue;
                }

                $estimations           = wp_list_pluck( $items, 'estimation' );
                $user_total_estimation = number_format(array_sum( $estimations )/3600, 2, '.', '');
                $total_in_st           = count( $items );

                $user_total_estimation_tf = pm_second_to_time( array_sum( $estimations ) );
                $user_total_estimation_tf = $user_total_estimation_tf['hour'] .':'. $user_total_estimation_tf['minute'];

                $total_estimation = $total_estimation + array_sum( $estimations );
                $total_tasks = $total_tasks + $total_in_st;

                $types['data'][$user_id]['data'][] = [
                    'type'               => $items[0]->type,
                    'estimated_hours'    => $user_total_estimation,
                    'estimated_hours_tf' => $user_total_estimation_tf,
                    'completed'          => $total_in_st
                ];

                $types['data'][$user_id]['meta'] = [
                    'user' => $user_info,
                    'report_for' => $this->report_for
                ];
            }
        }

        $total_estimation_tf = pm_second_to_time( $total_estimation );
        $total_estimation_tf = $total_estimation_tf['hour'] .':'. $total_estimation_tf['minute'];

        $types['meta']['total_estimation']    = number_format($total_estimation/3600, 2, '.', '');
        $types['meta']['total_estimation_tf'] = $total_estimation_tf;
        $types['meta']['total_tasks']         = empty( $total_tasks ) ? 0 : $total_tasks;

        return $types;
    }

    private function get_report_by_projects( $params ) {

        $requested_users = pm_get_prepare_data( $params['users'] );
        $projects = [ 'data' => [], 'meta' => [] ];

        $total_tasks = 0;
        $total_sub_tasks = 0;
        $all_subtasks_est = 0;
        $all_tasks_est = 0;

        foreach ( $this->projects as $key => $project ) {

            $total_sub_task_estimation = 0;
            $total_task_estimation = 0;


            foreach ( $project->assignees['data'] as $key => $p_user ) {

                if ( ! in_array( $p_user->id, $requested_users ) ) {
                    continue;
                }

                $estimated_task_seconds     = 0;
                $estimated_sub_task_seconds = 0;
                $completed_minutes          = 0;
                $completed_tasks            = 0;
                $completed_sub_tasks        = 0;
                $completed_task_seconds     = 0;

                foreach ( $project->tasks as $key => $task ) {
                    $task_users = wp_list_pluck( $task->assignees['data'], 'id' );

                    if ( ! in_array( $p_user->id, $task_users ) ) {
                        continue;
                    }

                    $estimated_task_seconds = $estimated_task_seconds + $task->estimation;
                    $completed_tasks  = $completed_tasks + 1;

                    $total_tasks = $total_tasks + 1;
                    $total_task_estimation = $total_task_estimation + $task->estimation;
                }

                foreach ( $project->sub_tasks as $key => $sub_task ) {
                    $sub_task_users = wp_list_pluck( $sub_task->assignees['data'], 'id' );

                    if ( ! in_array( $p_user->id, $sub_task_users ) ) {
                        continue;
                    }

                    $estimated_sub_task_seconds = $estimated_sub_task_seconds + $sub_task->estimation;
                    $completed_sub_tasks  = $completed_sub_tasks + 1;

                    $total_sub_tasks = $total_sub_tasks + 1;
                    $total_sub_task_estimation = $total_sub_task_estimation + $sub_task->estimation;
                }

                foreach ( $project->times as $key => $time ) {
                    if ( $time->user_id != $p_user->id ) {
                        continue;
                    }

                    $completed_task_seconds = $completed_task_seconds + $time->total['total_second'];
                }

                $task_time = pm_second_to_time( $estimated_task_seconds );
                $task_time = $task_time['hour'] .':'. $task_time['minute'];

                $subtask_time = pm_second_to_time( $total_sub_task_estimation );
                $subtask_time = $subtask_time['hour'] .':'. $subtask_time['minute'];

                $completed_task_time = pm_second_to_time( $completed_task_seconds );
                $completed_task_time = $completed_task_time['hour'] .':'. $completed_task_time['minute'];

                if ( $this->report_for == 'task' ) {
                    if ( $estimated_task_seconds <= 0 ) {
                        continue;
                    }
                }

                if ( $this->report_for == 'sub_task' ) {
                    if ( $total_sub_task_estimation <= 0 ) {
                        continue;
                    }
                }

                $all_subtasks_est = $all_subtasks_est + $total_sub_task_estimation;
                $all_tasks_est = $all_tasks_est + $estimated_task_seconds;


                $projects['data'][$p_user->id]['data'][] = [
                    'project'                     => $project,
                    'estimated_task_hours'        => number_format( $estimated_task_seconds/3600, 2, '.', ''),
                    'estimated_task_hours_tf'     => $task_time,
                    'estimated_sub_task_hours'    => number_format( $total_sub_task_estimation/3600, 2, '.', ''),
                    'estimated_sub_task_hours_tf' => $subtask_time,
                    'completed_tasks'             => $completed_tasks,
                    'completed_sub_tasks'         => $completed_sub_tasks,
                    'completed_task_hours'        => number_format( $completed_task_seconds/3600, 2, '.', ''),
                    'completed_task_hours_tf'     => $completed_task_time
                ];

                $projects['data'][$p_user->id]['meta'] = [
                    'user' => $p_user,
                    'report_for' => $this->report_for
                ];
            }
        }

        $total_te = pm_second_to_time( $all_tasks_est );
        $total_te = $total_te['hour'] .':'. $total_te['minute'];

        $total_ste = pm_second_to_time( $all_subtasks_est );
        $total_ste = $total_ste['hour'] .':'. $total_ste['minute'];

        $projects['meta']['total_tasks']               = $total_tasks;
        $projects['meta']['total_task_estimation']     = number_format( $all_tasks_est/3600, 2, '.', '');
        $projects['meta']['total_task_estimation_tf']  = $total_te;
        $projects['meta']['total_sub_tasks']           = $total_sub_tasks;
        $projects['meta']['total_sub_task_estimation'] = number_format( $all_subtasks_est/3600, 2, '.', '');
        $projects['meta']['total_sub_task_estimation_tf'] = $total_ste;

        return $projects;
    }

    private function merge_with_projects() {

        foreach ( $this->projects as $key => $project ) {
            $project->tasks     = [];
            $project->sub_tasks = [];
            $project->times     = [];

            foreach ( $this->tasks as $key => $task ) {
                if ( $task->project_id == $project->id ) {
                    $project->tasks[$task->id] = $task;
                }
            }

            foreach ( $this->sub_tasks as $key => $sub_task ) {
                if ( $sub_task->project_id == $project->id ) {
                    $project->sub_tasks[$sub_task->id] = $sub_task;
                }
            }

            foreach ( $this->times as $key => $time ) {
                if ( $time->project_id == $project->id ) {
                    $project->times[] = $time;
                }
            }
        }

    }

    private function get_task_by_user( $params ) {
        $users    = $params['users'];
        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );


        $results = pm_get_tasks([
            'users'                => $users,
            'completed_at_start'   => $start_at,
            'completed_at'         => $due_date,
            'completed_at_between' => true,
            'status'               => '1',
            'orderby'              => 'completed_at'
        ]);

        $tasks = [];

        foreach ( $results['data'] as $key => $task ) {
            if ( empty( $task ) ) {
                continue;
            }

            $tasks[$task['id']] = (Object) $task;
        }

        $this->tasks = $tasks;

        return $this;
    }

    private function get_sub_task_by_user( $params ) {

        if ( ! function_exists( 'pm_pro_is_module_active' ) ) {
            return $this;
        }

        if( ! pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' ) ) {
            return $this;
        }

        $users    = $params['users'];
        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );


        $completed_task_ids = array( 0 );
        $results = array();

        foreach ( $this->tasks as $key => $task ) {
            if ( $task->status == 'complete' || $task->status == 1 ) {
                $completed_task_ids[$task->id] = $task->id;
            }
        }

        $subtask_completed_results = pm_pro_get_sub_tasks([
            'users'                => $users,
            'completed_at_start'   => $start_at,
            'completed_at'         => $due_date,
            'completed_at_between' => true,
            'with'                 => 'task',
            'status'               => '1',
            'orderby'              => 'completed_at'
        ]);

        $task_completed_results = pm_pro_get_sub_tasks([
            'users'              => $users,
            'with'               => 'task',
            'completed_at_operator' => 'null',
            'task'               => $completed_task_ids,
            'orderby'            => 'created_at'
        ]);

        foreach ( $subtask_completed_results['data'] as $key => $subtask ) {
            $results[$subtask['id']] = $subtask;
        }

        foreach ( $task_completed_results['data'] as $key => $subtask ) {
            $results[$subtask['id']] = $subtask;
        }

        $sub_tasks = [];

        foreach ( $results as $key => $sub_task ) {
            if ( empty( $sub_task ) ) {
                continue;
            }

            if ( $sub_task['estimation'] <= 0 ) {
                continue;
            }

            if (
                $sub_task['status'] == 'complete'
                    ||
                $sub_task['task']['data']['status'] == 'complete'
            ) {
                if ( $sub_task['status'] == 'incomplete' ) {
                   $sub_task['completed_at'] = $sub_task['task']['data']['completed_at'];
                }
                $sub_tasks[$sub_task['id']] = (Object) $sub_task;
            }
        }

        $this->sub_tasks = $sub_tasks;

        return $this;
    }

    private function get_traking_time_by_user( $params ) {
        if ( ! function_exists( 'pm_pro_is_module_active' ) ) {
            return $this;
        }

        if( ! pm_pro_is_module_active( 'Time_Tracker/Time_Tracker.php' ) ) {
            return $this;
        }

        $users    = $params['users'];
        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        $results = Time_Tracker::get_results( [
            'created_at'          => $start_at,
            'created_at_operator' => 'greater_than_equal',
            'updated_at'          => $due_date,
            'updated_at_operator' => 'less_than_equal',
            'run_status'          => 0,
        ] );

        $times = [];

        foreach ( $results['data'] as $key => $time ) {
            if ( empty( $time ) ) {
                continue;
            }

            $times[] = (Object) $time;
        }

        $this->times = $times;

        return $this;
    }

    private function get_list_by_task_id( $params ) {
        $query_args = [];

        if ( $this->report_for == 'task' ) {
            $tasks = $this->tasks;
            $id    = 'task_id';
        }

        if ( $this->report_for == 'sub_task' ) {
            $tasks = $this->sub_tasks;
            $id    = 'sub_task_id';
        }

        if ( empty( $tasks ) ) {
            return $this;
        }

        $task_ids = array_keys( $tasks );

        $results = pm_get_task_lists([
            $id => $task_ids
        ]);

        $lists = [];

        foreach ( $results['data'] as $key => $list ) {
            if ( empty( $list ) ) {
                continue;
            }

            $lists[$list['id']] = (Object) $list;
        }

        $this->lists = $lists;

        return $this;
    }

    private function get_project_by_user_id( $params ) {
        $users = $params['users'];

        if ( empty( $users ) ) {
            return $this;
        }

        $results = pm_get_projects([
            'with' => 'assignees',
            'inUsers' => $users
        ]);

        $projects = [];

        foreach ( $results['data'] as $key => $project ) {
            if ( empty( $project ) ) {
                continue;
            }

            $projects[$project['id']] = (Object) $project;
        }

        $this->projects =  $projects;

        return $this;
    }


    static function export_csv( $reports, $params ) {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        $type_name = 'Tasks';

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'Time_Tracker/Time_Tracker.php' )
        ) {
            $type_name = 'Subtasks';
        }


        fputcsv( $output, ['Report For Individual User', 'Date between', "$start_at to $due_date" ] );

        $reports['users'] = empty( $reports['users'] ) ? [] : $reports['users'];

        foreach ( $reports['users'] as $key => $user ) {
            fputcsv( $output, ['User Name', "$user->display_name"] );
        }

        $reports['meta'] = empty( $reports['meta'] ) ? [] : $reports['meta'];

        foreach ( $reports['meta'] as $user_id => $meta ) {
            fputcsv( $output, ['Total Estimation Hours', $meta['total_estimation'] ] );
            fputcsv( $output, ['Completed Task Count', $meta['completed_tasks'] ] );
            fputcsv( $output, ['Avg. Hour Per-task', $meta['avg_hour_task'] ] );
            fputcsv( $output, ['Avg. Work Hour Per-day', $meta['avg_work_hour'] ] );
            fputcsv( $output, ['Avg. Task Per-day', $meta['avg_task_day'] ] );
        }


        fputcsv( $output, [ $type_name ] );

        foreach ( $reports['tasks']['data'] as $key => $task_items ) {
            fputcsv( $output, [ __('Completed At', 'pm-pro'), __('Title', 'pm-pro'), __('Project', 'pm-pro'), __('Type', 'pm-pro'), __('Hour', 'pm-pro') ] );

            foreach ( $task_items['data'] as $key => $item ) {
                $type_title = empty( $item->type ) ? '' : $item->type['title'];
                fputcsv( $output, [ $item->completed_at['date'], $item->title, $item->project_title, $type_title, number_format( $item->estimation/3600, 2, '.', '') ] );
            }

            fputcsv( $output, [ '', '', '', 'Total', $reports['tasks']['meta']['total_hours'] ] );
        }

        fputcsv( $output, [ 'Projects' ] );

        foreach ( $reports['projects']['data'] as $key => $project_items ) {
            fputcsv( $output, [ __('Project Name', 'pm-pro'), $type_name, __('Est. Hour', 'pm-pro') ] );

            foreach ( $project_items['data'] as $key => $item ) {

                $count = $type_name == 'tasks' ? $item['completed_tasks'] : $item['completed_sub_tasks'];
                $estimation = $type_name == 'tasks' ? $item['estimated_task_hours'] : $item['estimated_sub_task_hours'];

                fputcsv( $output, [ $item['project']->title, $count, $estimation ] );
            }

            $total_count = $type_name == 'tasks' ? $reports['projects']['meta']['total_tasks'] : $reports['projects']['meta']['total_sub_tasks'];
            $total_estimation = $type_name == 'tasks' ? $reports['projects']['meta']['total_task_estimation'] : $reports['projects']['meta']['total_sub_task_estimation'];

            fputcsv( $output, [ 'Total', $total_count, $total_estimation ] );
        }


        fputcsv( $output, [ 'Task Type' ] );

        foreach ( $reports['task_types']['data'] as $key => $task_type ) {
            fputcsv( $output, [ __('Task Type', 'pm-pro'), __('Task Count', 'pm-pro'), __('Est. Hour', 'pm-pro') ] );

            foreach ( $task_type['data'] as $key => $item ) {

                fputcsv( $output, [ $item['type']['title'], $item['completed'], $item['estimated_hours'] ] );
            }

            fputcsv( $output, [ 'Total', $reports['task_types']['meta']['total_tasks'], $reports['task_types']['meta']['total_estimation'] ] );
        }

        if ( $type_name == 'Subtasks' ) {
            fputcsv( $output, [ 'Subtask Type' ] );

            foreach ( $reports['sub_task_types']['data'] as $key => $sub_task_type ) {
                fputcsv( $output, [ __('Task Type', 'pm-pro'), __('Subask Count', 'pm-pro'), __('Est. Hour', 'pm-pro') ] );

                foreach ( $sub_task_type['data'] as $key => $item ) {

                    fputcsv( $output, [ $item['type']['title'], $item['completed'], $item['estimated_hours'] ] );
                }

                fputcsv( $output, [ 'Total', $reports['sub_task_types']['meta']['total_sub_tasks'], $reports['sub_task_types']['meta']['total_estimation'] ] );
            }

        }

        fclose($output);

        exit();
    }

}



