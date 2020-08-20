<?php

namespace WeDevs\PM_Pro\Modules\time_tracker\libs;

use WeDevs\PM_Pro\Modules\time_tracker\libs\Reports;


class Report_Users {

    use Reports;

    private $tasks = [];
    private $lists = [];
    private $projects = [];

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
        $users = empty($users) ? [] : explode( ',', $users );

        //$users = $this->user_for_sadik_vi();

        $time_logs = $this->get_time_log( $params['startDate'], $params['endDate'], $users );

        $time_logs = $this->get_tasks( $time_logs );

        $time_logs = $this->get_task_lists( $time_logs );

        $time_logs = $this->get_task_projects( $time_logs );

        $time_logs = $this->group_by_title( $time_logs );

        return [
            'type' => 'get_users_by_task_estimated_time',
            'users' => $time_logs
        ];
    }

    function group_by_title( $time_logs ) {
        $tasks = [];
        $lists = [];

        foreach ( $time_logs as $key => $value ) {
            foreach ( $value['tasks'] as $task ) {
                $tasks[$task['title']][] = $task;
            }

            foreach ( $value['lists'] as $list ) {
                $lists[$list['title']][] = $list;
            }
        }

        $group_lists = [];

        foreach ( $lists as $list_title => $list ) {
            $seconds    = array_sum( wp_list_pluck( $list, 'seconds' ) );
            $estimation = array_sum( wp_list_pluck( $list, 'estimation' ) );

            $group_lists[] = [
                'title'      => $list_title,
                'seconds'    => $seconds,
                'estimation' => $estimation
            ];
        }

        $group_tasks = [];

        foreach ( $tasks as $task_title => $task ) {
            $seconds    = array_sum( wp_list_pluck( $task, 'seconds' ) );
            $estimation = array_sum( wp_list_pluck( $task, 'estimation' ) );

            $group_tasks[] = [
                'title'      => $task_title,
                'seconds'    => $seconds,
                'estimation' => $estimation
            ];
        }

        foreach ( $time_logs as $key => $value ) {
            $time_logs[$key]['tasks'] = $group_tasks;
            $time_logs[$key]['lists'] = $group_lists;
        }

        return $time_logs;

    }

    function user_for_sadik_vi() {
        global $wpdb;

        $time_tb = $wpdb->prefix . 'pm_time_tracker';

        $sql = "SELECT DISTINCT user_id FROM $time_tb WHERE 1=1";

        $results = $wpdb->get_results( $sql );

        $users = wp_list_pluck( $results, 'user_id' );

        return $users;
    }

    private function get_users_by_subtask_estimated_time( $params ) {
        $users = $params['users'];
        $users = empty($users) ? [] : explode( ',', $users );

        $time_logs = $this->get_time_log( $params['startDate'], $params['endDate'], $users );

        $time_logs = $this->get_subtasks( $time_logs );

        // $time_logs = $this->get_tasks( $time_logs );

        // $time_logs = $this->get_task_lists( $time_logs );

        // $time_logs = $this->get_task_projects( $time_logs );

        return [
            'type' => 'get_users_by_subtask_estimated_time',
            'users' => $time_logs
        ];
    }

    private function get_subtasks( $time_logs ) {
        global $wpdb;

        $task_ids = [];

        foreach ( $time_logs as $key => $time_log ) {
            $task_ids = array_merge( $task_ids, array_keys( $time_log['tasks'] ) );
        }

        $subtask_ids = $this->get_subtask_ids_from_task_ids( $task_ids );

    }

    private function get_task_projects( $time_logs ) {
        global $wpdb;

        $project_ids = [];

        foreach ( $time_logs as $user_id => $time_log ) {
            $project_ids = array_merge( $project_ids, array_keys( $time_log['projects'] ) );
        }

        $project_ids = array_unique( $project_ids );

        $tb_project  = pm_tb_prefix() . 'pm_projects';
        $row_ids  = implode(',', $project_ids);
        $row_ids  = empty( $row_ids ) ? 0 : $row_ids;

        $sql = "SELECT id,title
            FROM $tb_project
            WHERE
            id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );

        $projects = wp_list_pluck( $results, 'title', 'id' );

        foreach ( $time_logs as $user_id => $value ) {
            foreach ( $value['projects'] as $project_id => $project_val ) {
                $task_ids  = array_unique( $project_val['task_ids'] );
                $estimation   = 0;
                //$e_minutes = 0;
                $e_seconds = 0;

                foreach ( $task_ids as $key => $task_id ) {
                    if(empty($this->tasks[$task_id])) continue;
                    $task = $this->tasks[$task_id];

                    if(empty( $task['title'] )) continue;

                    //$e_hours   = $e_hours+$task['estimated_hours'];
                    $estimation = $estimation+$task['estimation'];
                    $e_seconds = $e_seconds+$task['seconds'];
                }

                $time_logs[$user_id]['projects'][$project_id]['estimation'] = $estimation;
                //$time_logs[$user_id]['projects'][$project_id]['estimated_minutes'] = $e_minutes;
                $time_logs[$user_id]['projects'][$project_id]['seconds'] = $e_seconds;
                $time_logs[$user_id]['projects'][$project_id]['title'] = empty( $projects[$project_id] ) ? '' : $projects[$project_id];
            }
        }

        return $time_logs;
    }

    private function get_task_lists( $time_logs ) {
        global $wpdb;

        $list_ids = [];

        foreach ( $time_logs as $user_id => $time_log ) {
            $list_ids = array_merge( $list_ids, array_keys( $time_log['lists'] ) );
        }

        $list_ids = array_unique( $list_ids );

        $tb_board  = pm_tb_prefix() . 'pm_boards';
        $row_ids  = implode(',', $list_ids);
        $row_ids  = empty( $row_ids ) ? 0 : $row_ids;

        $sql = "SELECT id,title
            FROM $tb_board
            WHERE
            id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );

        $lists = wp_list_pluck( $results, 'title', 'id' );

        foreach ( $time_logs as $user_id => $value ) {
            foreach ( $value['lists'] as $list_id => $list_val ) {
                $task_ids  = array_unique( $list_val['task_ids'] );
                $estimation = 0;
                $e_seconds = 0;

                foreach ( $task_ids as $key => $task_id ) {
                    if(empty($this->tasks[$task_id])) continue;

                    $task = $this->tasks[$task_id];

                    if(empty( $task['title'] )) continue;

                    $estimation = $estimation+$task['estimation'];
                    $e_seconds = $e_seconds+$task['seconds'];
                }

                $time_logs[$user_id]['lists'][$list_id]['estimation'] = $estimation;
                $time_logs[$user_id]['lists'][$list_id]['seconds'] = $e_seconds;
                $time_logs[$user_id]['lists'][$list_id]['title'] = empty( $lists[$list_id] ) ? '' : $lists[$list_id];
            }
        }

        return $time_logs;
    }

    private function get_tasks( $time_logs ) {
        global $wpdb;

        $task_ids = [];

        foreach ( $time_logs as $key => $time_log ) {
            $task_ids = array_merge( $task_ids, array_keys( $time_log['tasks'] ) );
        }

        $task_ids = array_unique( $task_ids );

        $tasks = $this->get_estimated_time_by_task_ids( $task_ids );

        foreach ( $time_logs as $user_id => $time_log ) {
            foreach ( $time_log['tasks'] as $task_id => $task ) {
                $new_val = empty( $tasks[$task_id] ) ? [] : $tasks[$task_id];

                if( empty( $new_val['title'] ) ) {
                    unset( $time_logs[$user_id]['tasks'][$task_id] );
                    continue;
                }

                $time_logs[$user_id]['tasks'][$task_id]['estimation'] = empty( $new_val['estimation'] ) ? 0 : $new_val['estimation'];
                $time_logs[$user_id]['tasks'][$task_id]['title'] = empty( $new_val['title'] ) ? '' : $new_val['title'];

                //Set task information for list report
                $this->tasks[$task_id] = $time_logs[$user_id]['tasks'][$task_id];
            }
        }

        return $time_logs;
    }

    private function get_time_log( $start_date, $end_date, $users ) {
        global $wpdb;

        $tb_time     = pm_tb_prefix() . 'pm_time_tracker';
        $start_date  = date( 'Y-m-d 23:59:59', strtotime( $start_date ) );
        $end_date    = date( 'Y-m-d 23:59:59', strtotime( $end_date ) );
        $row_users   = implode( ',', $users );
        $row_users   = empty( $row_users ) ? 0 : $row_users;

        $sql = "SELECT *
            from $tb_time as tm
            WHERE 1=1
            AND
            tm.user_id IN ($row_users)
            AND
            (tm.created_at>='$start_date' AND tm.created_at<='$end_date')";

        $results = $wpdb->get_results( $sql );

        $time_logs = [];

        foreach ( $results as $key => $result ) {

            $time_logs[$result->user_id]['tasks'][$result->task_id][] = $result;
            $time_logs[$result->user_id]['projects'][$result->project_id][] = $result;
            $time_logs[$result->user_id]['lists'][$result->list_id][] = $result;
        }

        $data = [];

        foreach ( $time_logs as $user_id => $time_log ) {
            $data[$user_id]['user_id'] = $user_id;

            foreach ( $time_log['tasks'] as $task_id => $task ) {
                $seconds = wp_list_pluck( $task, 'total' );

                $data[$user_id]['tasks'][$task_id]['seconds'] = array_sum( $seconds );
                $data[$user_id]['tasks'][$task_id]['task_id'] = $task_id;
            }

            foreach ( $time_log['lists'] as $list_id => $list ) {
                $seconds  = wp_list_pluck( $list, 'total' );
                $task_ids = wp_list_pluck( $list, 'task_id' );

                $data[$user_id]['lists'][$list_id]['seconds']  = array_sum( $seconds );
                $data[$user_id]['lists'][$list_id]['list_id']  = $list_id;
                $data[$user_id]['lists'][$list_id]['task_ids'] = $task_ids;

            }

            foreach ( $time_log['projects'] as $project_id => $project ) {
                $seconds  = wp_list_pluck( $project, 'total' );
                $task_ids = wp_list_pluck( $project, 'task_id' );

                $data[$user_id]['projects'][$project_id]['seconds']    = array_sum( $seconds );
                $data[$user_id]['projects'][$project_id]['project_id'] = $project_id;
                $data[$user_id]['projects'][$project_id]['task_ids']   = $task_ids;

            }
        }

        return $data;
    }

    static function export_csv( $reports ) {
        //pmpr($reports);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        foreach ( $reports['users'] as $report ) {
            $user = get_user_by( 'id', $report['user_id'] );

            fputcsv( $output, [$user->display_name] );
            fputcsv( $output, ['Projects'] );
            fputcsv( $output, ['Title', 'Working Hours'] );

            foreach ( $report['projects'] as $project ) {
                $wtime = pm_pro_second_to_time( $project['seconds'] );
                $hour = empty( $wtime['hour'] ) ? 0 : $wtime['hour'];
                $minute = empty( $wtime['minute'] ) ? 0 : $wtime['minute'];
                fputcsv( $output, [$project['title'], $hour.':'.$minute] );
            }

            fputcsv( $output, ['', ''] );
            fputcsv( $output, ['Lists'] );
            fputcsv( $output, ['Title', 'Working Hours'] );

            foreach ( $report['lists'] as $list ) {
                $wtime = pm_pro_second_to_time( $list['seconds'] );
                $hour = empty( $wtime['hour'] ) ? 0 : $wtime['hour'];
                $minute = empty( $wtime['minute'] ) ? 0 : $wtime['minute'];

                fputcsv( $output, [$list['title'], $hour.':'.$minute] );
            }

            fputcsv( $output, ['', ''] );
            fputcsv( $output, ['Task'] );
            fputcsv( $output, ['Title', 'Working Hours'] );

            foreach ( $report['tasks'] as $task ) {
                $wtime = pm_pro_second_to_time( $task['seconds'] );
                $hour = empty( $wtime['hour'] ) ? 0 : $wtime['hour'];
                $minute = empty( $wtime['minute'] ) ? 0 : $wtime['minute'];

                fputcsv( $output, [$task['title'], $hour.':'.$minute] );
            }

            fputcsv( $output, ['', ''] );

        }

        fclose($output);

        exit();
    }

}



