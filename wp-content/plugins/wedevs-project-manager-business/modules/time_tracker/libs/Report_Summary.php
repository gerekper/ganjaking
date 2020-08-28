<?php

namespace WeDevs\PM_Pro\Modules\time_tracker\libs;

use WeDevs\PM_Pro\Modules\time_tracker\libs\Reports;
use WeDevs\PM\User\Models\User;
use WeDevs\PM\Project\Models\Project;


class Report_Summary {

    use Reports;

    private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    static function summary( $params ) {
        $summary_type = $params['summaryType'];

        switch ( $summary_type ) {
            case 'all_project':
                return self::getInstance()->get_summary_all_projects( $params );
                break;

            case 'list_type':
                return self::getInstance()->get_summary_list_type( $params );
                break;

            case 'all_user':
                return self::getInstance()->get_summary_users( $params );
                break;

            case 'project_uers':
                return self::getInstance()->get_summary_project_users( $params );
                break;

            case 'user_projects':
                return self::getInstance()->get_summary_user_projects( $params );
                break;

            case 'task_type':
                return self::getInstance()->get_summary_task_type( $params );
                break;

            case 'sub_task_type':
                return self::getInstance()->get_summary_sub_task_type( $params );
                break;
        }
    }

    private function get_summary_user_projects( $params ) {

        return [
            'type' => 'get_summary_user_by_projects',
            'users' => []
        ];
    }

    private function get_summary_task_type( $params ) {
        $projects = empty( $params['projects'] ) ? false : $params['projects'];

        $project_ids  = $projects ? pm_get_prepare_data( $params['projects'] ) : false;

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        $report_for = 'task';

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'sub_tasks/sub_tasks.php' )
        ) {
            $report_for = 'sub_task';
        }

        $projects = pm_get_projects([
            'id'   => $project_ids
        ]);

        if ( $project_ids ) {
            $results = pm_get_tasks([
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'status'            => '1',
                'project_id'        => $project_ids
            ]);
        } else {
            $results = pm_get_tasks([
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'status'            => '1',
            ]);
        }

        $task_ids = wp_list_pluck( $results['data'], 'id' );

        if ( $report_for == 'sub_task' ) {

            if ( empty( $project_ids ) ) {
                $subTasks = pm_pro_get_sub_tasks([
                    'start_at'          => $start_at,
                    'start_at_operator' => 'greater_than_equal',
                    'due_date'          => $due_date,
                    'due_date_operator' => 'less_than_equal',
                    'with'              => 'task',
                ]);
            } else {
                $subTasks = pm_pro_get_sub_tasks([
                    'start_at'          => $start_at,
                    'start_at_operator' => 'greater_than_equal',
                    'due_date'          => $due_date,
                    'due_date_operator' => 'less_than_equal',
                    'with'              => 'task',
                    'project_id'        => $project_ids
                ]);
            }

            $subtask_tks = [];

            foreach ( $subTasks['data'] as $key => $sub_task ) {
                $task_id = $sub_task['task']['data']['id'];
                $subtask_tks[$task_id] = $sub_task['task']['data'];
            }

            foreach ( $subtask_tks as $task_key => $task ) {
                $estimation = 0;

                foreach ( $subTasks['data'] as $key => $sub_task ) {

                    if (
                        $task['status'] == 'incomplete'
                            &&
                        $sub_task['status'] == 'incomplete'
                    ) {

                        continue;
                    }

                    if ( $task['id'] == $sub_task['parent_id'] ) {
                        $estimation = $estimation + $sub_task['estimation'];
                    }
                }

                $subtask_tks[$task_key]['estimation'] = $estimation;
            }

            $results['data'] = $subtask_tks;
        }

        // if ( $report_for == 'sub_task' ) {
        //     $subTasks = pm_pro_get_sub_tasks([
        //         'start_at'          => $start_at,
        //         'start_at_operator' => 'greater_than_equal',
        //         'due_date'          => $due_date,
        //         'due_date_operator' => 'less_than_equal',
        //         'with'              => 'task'
        //     ]);

        //     $sub_tasks = [];

        //     foreach ( $subTasks['data'] as $key => $sub_task ) {
        //         $sub_tasks[$sub_task['parent_id']][] = $sub_task;
        //     }

        //     foreach ( $sub_tasks as $task_id => $sub_task ) {
        //         $sub_tasks[$task_id] = array_sum( wp_list_pluck( $sub_task, 'estimation' ) );
        //     }

        //     foreach ( $results['data'] as $key => $task ) {
        //         $results['data'][$key]['estimation'] = empty( $sub_tasks[$task['id']] ) ? 0 : $sub_tasks[$task['id']];
        //     }
        // }

        $types           = [ 'data' => [], 'meta' => [] ];
        $formats         = [];
        $total_est_hours = 0;
        $total_inc_tasks = 0;

        foreach ( $results['data'] as $key => $task ) {

            if ( empty( $task['type'] ) ) {
                continue;
            }

            $formats[$task['type']['id']][] = $task;
        }

        foreach ( $formats as $type_id => $tasks ) {
            $estimation      = wp_list_pluck( $tasks, 'estimation' );
            $total_est       = array_sum( $estimation );
            $total_est       = empty( $total_est ) ? 0 : $total_est; //$total_est/3600;
            $total_tasks     = count( $tasks );

            $total_est_hours = $total_est_hours + $total_est;
            $total_inc_tasks = $total_inc_tasks + $total_tasks;

            $total_est_tf = pm_second_to_time( $total_est );
            $total_est_tf = $total_est_tf['hour'] .':'. $total_est_tf['minute'];

            $types['data'][$type_id] = [
                'tasks'                  => $tasks,
                'type'                   => $tasks[0]['type'],
                'total_estimation_hours' => number_format( $total_est/3600, 2, '.', ''),
                'total_estimation_hours_tf' => $total_est_tf,
                'total_incomplete_tasks' => $total_tasks
            ];
        }

        $total_est_hours_tf = pm_second_to_time( $total_est_hours );
        $total_est_hours_tf = $total_est_hours_tf['hour'] .':'. $total_est_hours_tf['minute'];

        $types['meta']['total_estimation_hours'] = number_format( $total_est_hours/3600, 2, '.', '');
        $types['meta']['total_estimation_hours_tf'] = $total_est_hours_tf;
        $types['meta']['total_incomplete_tasks'] = $total_inc_tasks;
        $types['projects'] = $projects;
        $types['type'] = 'task_type_summary';

        return $types;
    }

    private function get_summary_sub_task_type( $params ) {

        $projects = empty( $params['projects'] ) ? false : $params['projects'];

        $project_ids  = $projects ? pm_get_prepare_data( $params['projects'] ) : false;

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        if ( !function_exists( 'pm_pro_get_sub_tasks' ) ) {
            return [ 'data' => [], 'meta' => [] ];
        }

        $projects = pm_get_projects([
            'id'   => $project_ids
        ]);

        if ( empty( $project_ids ) ) {
            $results = pm_pro_get_sub_tasks([
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'with'              => 'task',
            ]);
        } else {
            $results = pm_pro_get_sub_tasks([
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'with'              => 'task',
                'project_id'        => $project_ids
            ]);
        }

        $filter_tasks = [];

        foreach ( $results['data'] as $key => $sub_task ) {
            if ( empty( $sub_task ) ) {
                continue;
            }

            if (
                $sub_task['status'] == 'incomplete'
                    &&
                $sub_task['task']['data']['status'] == 'incomplete'
            ) {
                continue;
            }

            if (
                $sub_task['status'] == 'incomplete'
                    &&
                $sub_task['task']['data']['status'] == 'complete'
            ) {
                $sub_task['completed_at'] = $sub_task['task']['data']['completed_at'];
            }

            $filter_tasks[$sub_task['id']] = $sub_task;
        }

        $results['data'] = $filter_tasks;

        $types = [ 'data' => [], 'meta' => [] ];
        $formats = [];
        $total_est_hours = 0;
        $total_inc_sub_tasks = 0;

        foreach ( $results['data'] as $key => $sub_task ) {

            if ( empty( $sub_task['type'] ) ) {
                continue;
            }

            $formats[$sub_task['type']['id']][] = $sub_task;
        }

        foreach ( $formats as $type_id => $sub_tasks ) {
            $estimation      = wp_list_pluck( $sub_tasks, 'estimation' );
            $total_est       = array_sum( $estimation );
            $total_est       = empty( $total_est ) ? 0 : $total_est; //$total_est/3600;
            $total_sub_tasks = count( $sub_tasks );

            $total_est_hours = $total_est_hours + $total_est;
            $total_inc_sub_tasks = $total_inc_sub_tasks + $total_sub_tasks;

            $total_est_tf = pm_second_to_time( $total_est );
            $total_est_tf = $total_est_tf['hour'] .':'. $total_est_tf['minute'];

            $types['data'][$type_id] = [
                'sub_tasks'                  => $sub_tasks,
                'type'                       => $sub_tasks[0]['type'],
                'total_estimation_hours'     => number_format( $total_est/3600, 2, '.', ''),
                'total_estimation_hours_tf'  => $total_est_tf,
                'total_incomplete_sub_tasks' => $total_sub_tasks
            ];
        }

        $total_est_hours_tf = pm_second_to_time( $total_est_hours );
        $total_est_hours_tf = $total_est_hours_tf['hour'] .':'. $total_est_hours_tf['minute'];

        $types['meta']['total_estimation_hours'] = number_format( $total_est_hours/3600, 2, '.', '');
        $types['meta']['total_estimation_hours_tf'] = $total_est_hours_tf;
        $types['meta']['total_incomplete_sub_tasks'] = $total_inc_sub_tasks;
        $types['projects'] = $projects;
        $types['type'] = 'sub_task_type_summary';

        return $types;
    }


    public function get_summary_users( $params ) {
        return $this->get_summary_users_by_task_estimated_time( $params );
    }

    public function get_summary_users_by_task_estimated_time( $params ) {
        $projects = empty( $params['projects'] ) ? false : $params['projects'];
        $project_ids  = $projects ? pm_get_prepare_data( $params['projects'] ) : false;

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        $report_for = 'task';

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'sub_tasks/sub_tasks.php' )
        ) {
            $report_for = 'sub_task';
        }

        $users = [];

        $task_data = [ 'data' => [], 'meta' => [] ];
        $sub_task_data = [ 'data' => [], 'meta' => [] ];
        $meta = [];

        $total_tasks = 0;
        $total_task_estimation_hours = 0;
        $total_sub_tasks = 0;
        $total_sub_task_estimation_hours = 0;

        $projects = pm_get_projects([
            'id'   => $project_ids,
            'with' => 'assignees'
        ]);

        foreach ( $projects['data'] as $key => $project ) {
            foreach ( $project['assignees']['data'] as $key => $user ) {
                $users[$user->id] = $user->id;
            }
        }

        if ( empty( $project_ids ) ) {
            $args = [
                'users'             => $users,
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'status'            => '1',
            ];
        } else {
           $args = [
                'users'             => $users,
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'status'            => '1',
                'project_id'        => $project_ids
            ];
        }

        $tasks = pm_get_tasks( $args );

        foreach ( $tasks['data'] as $key => $task ) {
            foreach ( $task['assignees']['data'] as $key => $user ) {
                if ( !in_array( $user->id, $users ) ) {
                    continue;
                }

                $task_data['data'][$user->id]['estimation_hours'][] =  $task['estimation'];
                $task_data['data'][$user->id]['tasks'][]      =  $task;
                $task_data['data'][$user->id]['user']         =  $user;
            }
        }

        foreach ( $task_data['data'] as $user_id => $user_tasks ) {
            $estimation       = array_sum( $user_tasks['estimation_hours'] );
            $estimation_hours = empty( $estimation ) ? 0 : $estimation; //$estimation/3600;

            $estimation_hours_tf = pm_second_to_time( $estimation_hours );
            $estimation_hours_tf = $estimation_hours_tf['hour'] .':'. $estimation_hours_tf['minute'];

            $task_data['data'][$user_id]['estimation_hours'] = number_format( $estimation_hours/3600, 2, '.', '');
            $task_data['data'][$user_id]['estimation_hours_tf'] = $estimation_hours_tf;
            $task_data['data'][$user_id]['in_task_count']    = count( $user_tasks['tasks'] );

            $total_tasks = $total_tasks + count( $user_tasks['tasks'] );
            $total_task_estimation_hours = $total_task_estimation_hours + $estimation_hours;
        }

        if ( function_exists( 'pm_pro_get_sub_tasks' )  ) {
            unset( $args['status'] );
            $args['with'] = 'task';

            $sub_tasks = pm_pro_get_sub_tasks( $args );
            $filter_tasks = [];

            foreach ( $sub_tasks['data'] as $key => $sub_task ) {
                if ( empty( $sub_task ) ) {
                    continue;
                }

                if (
                    $sub_task['status'] == 'incomplete'
                        &&
                    $sub_task['task']['data']['status'] == 'incomplete'
                ) {
                    continue;
                }

                if (
                    $sub_task['status'] == 'incomplete'
                        &&
                    $sub_task['task']['data']['status'] == 'complete'
                ) {
                    $sub_task['completed_at'] = $sub_task['task']['data']['completed_at'];
                }

                $filter_tasks[$sub_task['id']] = $sub_task;
            }

            $sub_tasks['data'] = $filter_tasks;

            foreach ( $sub_tasks['data'] as $key => $task ) {
                foreach ( $task['assignees']['data'] as $key => $user ) {
                    if ( !in_array( $user->id, $users ) ) {
                        continue;
                    }

                    $sub_task_data['data'][$user->id]['estimation_hours'][] =  $task['estimation'];
                    $sub_task_data['data'][$user->id]['sub_tasks'][]  =  $task;
                    $sub_task_data['data'][$user->id]['user']         =  $user;
                }
            }

            foreach ( $sub_task_data['data'] as $user_id => $user_sub_tasks ) {
                $estimation       = array_sum( $user_sub_tasks['estimation_hours'] );
                $estimation_hours = empty( $estimation ) ? 0 : $estimation; //$estimation/3600;

                $estimation_hours_tf = pm_second_to_time( $estimation_hours );
                $estimation_hours_tf = $estimation_hours_tf['hour'] .':'. $estimation_hours_tf['minute'];

                $sub_task_data['data'][$user_id]['estimation_hours']    = number_format( $estimation_hours/3600, 2, '.', '');
                $sub_task_data['data'][$user_id]['estimation_hours_tf'] = $estimation_hours_tf;
                $sub_task_data['data'][$user_id]['in_task_count']       = count( $user_sub_tasks['sub_tasks'] );

                $total_sub_tasks = $total_sub_tasks + count( $user_sub_tasks['sub_tasks'] );
                $total_sub_task_estimation_hours = $total_sub_task_estimation_hours + $estimation_hours;
            }
        }

        $total_task_estimation_hours_tf = pm_second_to_time( $total_task_estimation_hours );
        $total_task_estimation_hours_tf = $total_task_estimation_hours_tf['hour'] .':'. $total_task_estimation_hours_tf['minute'];

        $total_sub_task_estimation_hours_tf = pm_second_to_time( $total_sub_task_estimation_hours );
        $total_sub_task_estimation_hours_tf = $total_sub_task_estimation_hours_tf['hour'] .':'. $total_sub_task_estimation_hours_tf['minute'];

        $meta['total_tasks'] = $total_tasks;
        $meta['total_task_estimation_hours'] = number_format( $total_task_estimation_hours/3600, 2, '.', '' );
        $meta['total_task_estimation_hours_tf'] = $total_task_estimation_hours_tf;

        $meta['total_sub_tasks'] = $total_sub_tasks;
        $meta['total_sub_task_estimation_hours'] = number_format( $total_sub_task_estimation_hours/3600, 2, '.', '' );
        $meta['total_sub_task_estimation_hours_tf'] = $total_sub_task_estimation_hours_tf;

        return [
            'type'     => 'get_user_all_projects_by_task_estimated_time',
            'task'     => $task_data,
            'sub_task' => $sub_task_data,
            'meta'     => $meta,
            'projects' => $projects,
            'report_for' => $report_for
        ];
    }

    public function get_summary_users_by_subtask_estimated_time( $params ) {


        return [
            'type' => 'get_user_all_projects_by_subtask_estimated_time',
            'users' => []
        ];
    }

    public function get_summary_list_type( $params ) {
        return $this->get_summary_list_type_by_task_estimated_time( $params );
    }

    public function get_summary_list_type_by_task_estimated_time( $params ) {


        return [
            'type' => 'get_summary_list_type_by_task_estimated_time',
            'lists' => []
        ];
    }


    public function get_summary_list_type_by_subtask_estimated_time( $params ) {


        return [
            'type' => 'get_summary_list_type_by_subtask_estimated_time',
            'lists' => $lists
        ];
    }

    public function get_summary_all_projects( $params ) {

        return $this->get_summary_all_projects_by_task_estimated_time( $params );

    }

    public function get_summary_all_projects_by_task_estimated_time( $params ) {

        if (
            function_exists( 'pm_pro_is_module_active' )
                &&
            pm_pro_is_module_active( 'sub_tasks/sub_tasks.php' )
        ) {
            $report_for = 'sub_task';
        }

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );


        $total_incomplete_tasks = 0;
        $total_incomplete_sub_tasks = 0;
        $total_task_estimation_hours = 0;
        $total_sub_task_estimation_hours = 0;
        $projs_total_users = 0;

        $db_sub_tasks = [ 'data' => [] ];
        $db_tasks = [ 'data' => [] ];

        $projects = pm_get_projects([
            'with' => 'assignees'
        ]);

        $project_ids = wp_list_pluck( $projects['data'], 'id' );

        if ( $report_for == 'sub_task' ) {

            $args = [
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'with'            => 'task',
                'project_id'        => $project_ids
            ];

            $db_sub_tasks = pm_pro_get_sub_tasks( $args );
            $filter_tasks = [];

            foreach ( $db_sub_tasks['data'] as $key => $sub_task ) {
                if ( empty( $sub_task ) ) {
                    continue;
                }

                if (
                    $sub_task['status'] == 'incomplete'
                        &&
                    $sub_task['task']['data']['status'] == 'incomplete'
                ) {
                    continue;
                }

                if (
                    $sub_task['status'] == 'incomplete'
                        &&
                    $sub_task['task']['data']['status'] == 'complete'
                ) {
                    $sub_task['completed_at'] = $sub_task['task']['data']['completed_at'];
                }

                $filter_tasks[$sub_task['id']] = $sub_task;
            }

            $db_sub_tasks['data'] = $filter_tasks;

        } else {
            $args = [
                'start_at'          => $start_at,
                'start_at_operator' => 'greater_than_equal',
                'due_date'          => $due_date,
                'due_date_operator' => 'less_than_equal',
                'status'            => '1',
                'project_id'        => $project_ids
            ];

            $db_tasks = pm_get_tasks( $args );
        }


        foreach ( $projects['data'] as $pkey => $project ) {

            foreach ( $db_tasks['data'] as $tkey => $task ) {
                if ( $project['id'] == $task['project_id'] ) {
                    $projects['data'][$pkey]['tasks']['data'][] = $task;
                }
            }

            foreach ( $db_sub_tasks['data'] as $skey => $sub_task ) {
                if ( $project['id'] == $sub_task['project_id'] ) {
                    $projects['data'][$pkey]['sub_tasks']['data'][] = $sub_task;
                }
            }

            foreach ( $project['assignees']['data'] as $ukey => $user ) {
                $projects['data'][$pkey]['users']['data'][$user->id] = $user;
            }
        }

        foreach ( $projects['data'] as $pkey => $project ) {

            if ( ! empty( $project['tasks'] ) ) {
                $total_inc_tasks       = count( $project['tasks']['data'] );
                $estimation            = wp_list_pluck( $project['tasks']['data'], 'estimation' );
                $task_estimation_hours = array_sum( $estimation );
                $task_estimation_hours = empty( $task_estimation_hours ) ? 0 : $task_estimation_hours; //$task_estimation_hours/3600;
            } else {
                $total_inc_tasks = 0;
                $task_estimation_hours = 0;
            }

            if ( ! empty( $project['sub_tasks'] ) ) {
                $total_inc_sub_tasks       = count( $project['sub_tasks']['data'] );
                $estimation                = wp_list_pluck( $project['sub_tasks']['data'], 'estimation' );
                $sub_task_estimation_hours = array_sum( $estimation );
                $sub_task_estimation_hours = empty( $sub_task_estimation_hours ) ? 0 : $sub_task_estimation_hours; //$sub_task_estimation_hours/3600;
            } else {
                $total_inc_sub_tasks = 0;
                $sub_task_estimation_hours = 0;
            }

            if ( ! empty( $project['users'] ) ) {
                $total_users = count( $project['users']['data'] );

            } else {
                $total_users = 0;
            }

            $task_estimation_hours_tf = pm_second_to_time( $task_estimation_hours );
            $task_estimation_hours_tf = $task_estimation_hours_tf['hour'] .':'. $task_estimation_hours_tf['minute'];

            $sub_task_estimation_hours_tf = pm_second_to_time( $sub_task_estimation_hours );
            $sub_task_estimation_hours_tf = $sub_task_estimation_hours_tf['hour'] .':'. $sub_task_estimation_hours_tf['minute'];

            $projects['data'][$pkey]['tasks']['meta']['total_incomplete_tasks'] = $total_inc_tasks;
            $projects['data'][$pkey]['tasks']['meta']['task_estimation_hours'] = $task_estimation_hours;
            $projects['data'][$pkey]['tasks']['meta']['task_estimation_hours_tf'] = $task_estimation_hours_tf;
            $projects['data'][$pkey]['total_incomplete_tasks'] = $total_inc_tasks;
            $projects['data'][$pkey]['task_estimation_hours'] = $task_estimation_hours;
            $projects['data'][$pkey]['task_estimation_hours_tf'] = $task_estimation_hours_tf;

            $projects['data'][$pkey]['sub_tasks']['meta']['total_incomplete_sub_tasks'] = $total_inc_sub_tasks;
            $projects['data'][$pkey]['sub_tasks']['meta']['sub_task_estimation_hours'] = number_format( $sub_task_estimation_hours/3600, 2, '.', ' ');
            $projects['data'][$pkey]['sub_tasks']['meta']['sub_task_estimation_hours_tf'] = $sub_task_estimation_hours_tf;
            $projects['data'][$pkey]['total_incomplete_sub_tasks'] = $total_inc_sub_tasks;
            $projects['data'][$pkey]['sub_task_estimation_hours'] = number_format( $sub_task_estimation_hours/3600, 2, '.', ' ');
            $projects['data'][$pkey]['sub_task_estimation_hours_tf'] = $sub_task_estimation_hours_tf;

            $projects['data'][$pkey]['users']['meta']['total_users'] = $total_users;
            $projects['data'][$pkey]['total_users'] = $total_users;

            $total_incomplete_tasks          = $total_incomplete_tasks + $total_inc_tasks;
            $total_incomplete_sub_tasks      = $total_incomplete_sub_tasks + $total_inc_sub_tasks;
            $total_task_estimation_hours     = $total_task_estimation_hours + $task_estimation_hours;
            $total_sub_task_estimation_hours = $total_sub_task_estimation_hours + $sub_task_estimation_hours;
            $projs_total_users               = $projs_total_users + $total_users;
        }

        $total_task_estimation_hours_tf = pm_second_to_time( $total_task_estimation_hours );
        $total_task_estimation_hours_tf = $total_task_estimation_hours_tf['hour'] .':'. $total_task_estimation_hours_tf['minute'];

        $total_sub_task_estimation_hours_tf = pm_second_to_time( $total_sub_task_estimation_hours );
        $total_sub_task_estimation_hours_tf = $total_sub_task_estimation_hours_tf['hour'] .':'. $total_sub_task_estimation_hours_tf['minute'];

        $projects['meta']['total_incomplete_tasks']             = $total_incomplete_tasks;
        $projects['meta']['total_incomplete_sub_tasks']         = $total_incomplete_sub_tasks;
        $projects['meta']['total_task_estimation_hours']        = number_format( $total_task_estimation_hours/3600, 2, '.', ' ');
        $projects['meta']['total_task_estimation_hours_tf']     = $total_task_estimation_hours_tf;
        $projects['meta']['total_sub_task_estimation_hours']    = number_format( $total_sub_task_estimation_hours/3600, 2, '.', ' ');
        $projects['meta']['total_sub_task_estimation_hours_tf'] = $total_sub_task_estimation_hours_tf;
        $projects['meta']['total_users']                        = $projs_total_users;


        return [
            'type'       => 'get_summary_all_projects_by_task_estimated_time',
            'projects'   => $projects,
            'report_for' => $report_for
        ];
    }

    public static function export_csv( $reports, $params ) {
        $summary_type = $params['summaryType'];

        switch ( $summary_type ) {
            case 'all_project':
                return self::getInstance()->get_all_project_csv( $reports, $params );
                break;

            case 'all_user':
                return self::getInstance()->get_users_csv( $reports, $params );
                break;

            case 'task_type':
                return self::getInstance()->get_task_type_csv( $reports, $params );
                break;

            case 'sub_task_type':
                return self::getInstance()->get_sub_task_type_csv( $reports, $params );
                break;
        }
    }

    private function get_task_type_csv( $reports, $params ) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        $start_at = empty( $params['startDate'] )
            ? date( 'Y-m-01', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['startDate'] ) );

        $due_date = empty( $params['endDate'] )
            ? date( 'Y-m-t', strtotime( current_time( 'mysql' ) ) )
            : date( 'Y-m-d', strtotime( $params['endDate'] ) );

        fputcsv( $output, ['Report For Tasks Type', 'Date between', "$start_at to $due_date" ] );

        $total_estimation = $reports['meta']['total_estimation_hours'];
        $total_items = $reports['meta']['total_incomplete_tasks'];

        fputcsv( $output, ['Total Estimation Hours', $total_estimation ] );
        fputcsv( $output, ["Completed Tasks Count", $total_items ] );

        $projects = '';

        foreach ( $reports['projects']['data'] as $key => $project ) {
            $projects .= $project['title'] . ', ';
        }

        fputcsv( $output, ["Projects", $projects ] );
        fputcsv( $output, [ __( 'Task Type Name', 'pm-pro' ), __( 'Completed Task Count', 'pm-pro' ), __('Est. Hour', 'pm-pro') ] );

        foreach ( $reports['data'] as $key => $item ) {
            $type_title = empty( $item['type'] ) ? '' : $item['type']['title'];
            fputcsv( $output, [ $type_title, $item['total_incomplete_tasks'], $item['total_estimation_hours'] ] );
        }

        fputcsv( $output, [ __( 'Total', 'pm-pro' ), $total_items, $total_estimation ] );

        fclose($output);
        exit();
    }

    private function get_sub_task_type_csv( $reports, $params ) {
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
            pm_pro_is_module_active( 'time_tracker/time_tracker.php' )
        ) {
            $type_name = 'Subtasks';
        }

        fputcsv( $output, ['Report For Subtasks Type', 'Date between', "$start_at to $due_date" ] );

        $total_estimation = $reports['meta']['total_estimation_hours'];
        $total_items = $reports['meta']['total_incomplete_sub_tasks'];

        fputcsv( $output, ['Total Estimation Hours', $total_estimation ] );
        fputcsv( $output, ["Completed Subtasks Count", $total_items ] );

        $projects = '';

        foreach ( $reports['projects']['data'] as $key => $project ) {
            $projects .= $project['title'] . ', ';
        }

        fputcsv( $output, ["Projects", $projects ] );
        fputcsv( $output, [ __( 'Subtask Type Name', 'pm-pro' ), __( 'Completed Subtask Count', 'pm-pro' ), __('Est. Hour', 'pm-pro') ] );

        foreach ( $reports['data'] as $key => $item ) {
            $type_title = empty( $item['type'] ) ? '' : $item['type']['title'];

            fputcsv( $output, [ $type_title, $item['total_incomplete_sub_tasks'], $item['total_estimation_hours'] ] );
        }

        fputcsv( $output, [ __( 'Total', 'pm-pro' ), $total_items, $total_estimation ] );

        fclose($output);
        exit();
    }

    private function get_users_csv( $reports, $params ) {
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
            pm_pro_is_module_active( 'time_tracker/time_tracker.php' )
        ) {
            $type_name = 'Subtasks';
        }

        fputcsv( $output, ['Report For All Users', 'Date between', "$start_at to $due_date" ] );

        $total_estimation = $type_name == 'Tasks'
            ? $reports['meta']['total_task_estimation_hours']
            : $reports['meta']['total_sub_task_estimation_hours'];

        $total_items = $type_name == 'Tasks'
            ? $reports['meta']['total_tasks']
            : $reports['meta']['total_sub_tasks'];

        fputcsv( $output, ['Total Estimation Hours', $total_estimation ] );
        fputcsv( $output, ["Completed {$type_name} Count", $total_items ] );

        $projects = '';

        foreach ( $reports['projects']['data'] as $key => $project ) {
            $projects .= $project['title'] . ', ';
        }

        fputcsv( $output, ["Projects", $projects ] );
        fputcsv( $output, [ __( 'User Name', 'pm-pro' ), $type_name, __('Est. Hour', 'pm-pro') ] );
        $items = $reports['report_for'] == 'sub_task' ? $reports['sub_task']['data'] : $reports['task']['data'];

        foreach ( $items as $key => $item ) {
            fputcsv( $output, [ $item['user']->display_name, $item['in_task_count'], $item['estimation_hours'] ] );
        }

        fputcsv( $output, [ __( 'Total', 'pm-pro' ), $total_items, $total_estimation ] );

        fclose($output);

        exit();

    }

    private function get_all_project_csv( $reports, $params ) {
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
            pm_pro_is_module_active( 'time_tracker/time_tracker.php' )
        ) {
            $type_name = 'Subtasks';
        }

        fputcsv( $output, ['Report For Projects', 'Date between', "$start_at to $due_date" ] );

        $total_estimation = $type_name == 'Tasks'
            ? $reports['projects']['meta']['total_task_estimation_hours']
            : $reports['projects']['meta']['total_sub_task_estimation_hours'];

        $total_items = $type_name == 'Tasks'
            ? $reports['projects']['meta']['total_incomplete_tasks']
            : $reports['projects']['meta']['total_incomplete_sub_tasks'];

        fputcsv( $output, ['Total Estimation Hours', $total_estimation ] );
        fputcsv( $output, ["Completed {$type_name} Count", $total_items ] );

        fputcsv( $output, [ __('Project Name', 'pm-pro'), __('Users', 'pm-pro'), $type_name, __('Est. Hour', 'pm-pro') ] );

        foreach ( $reports['projects']['data'] as $key => $project ) {
            $users = '';

            foreach ( $project['users']['data'] as $key => $user ) {
                $users .= $user->display_name . ', ';
            }

            $estimation = $type_name == 'Tasks'
                ? $project['task_estimation_hours']
                : $project['sub_task_estimation_hours'];

            $items = $type_name == 'Tasks'
                ? $project['total_incomplete_tasks']
                : $project['total_incomplete_sub_tasks'];

            fputcsv( $output, [ $project['title'], $users, $items, $estimation ] );
        }

        fputcsv( $output, [ __('Total', 'pm-pro'), '', $total_items, $total_estimation ] );

        fclose($output);

        exit();
    }

}
