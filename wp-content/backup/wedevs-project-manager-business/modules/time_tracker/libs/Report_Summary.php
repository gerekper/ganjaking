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
        }
    }

    private function get_summary_user_projects( $params ) {
        $users = $this->get_users_from_time_log($params['startDate'], $params['endDate']);
        $users  = $this->filter_projects_from_time_log( $users );

        foreach ( $users as $key => $user ) {
            unset( $user->task_ids );
            unset( $user->seconds );
        }

        return [
            'type' => 'get_summary_user_by_projects',
            'users' => $users
        ];
    }

    private function filter_projects_from_time_log( $users ) {
        $all_project_ids = [];

        foreach ( $users as $user ) {
            $projects = explode( '|', $user->projects );
            $format_projects = [];

            foreach ( $projects as $project ) {
                $project       = str_replace('`', '"', $project);
                $project       = json_decode( $project );
                if ( ! empty( $project->project_id ) ) {
                    $format_projects[$project->project_id][] = $project;
                }


            }

            $user->projects = $format_projects;
        }

        foreach ( $users as $key => $user ) {
            foreach ( $user->projects as $project_id => $project ) {
                $total_second = wp_list_pluck( $project, 'total' );
                $user->projects[$project_id] = array_sum( $total_second );
                $all_project_ids[$project_id] = $project_id;
            }
        }

        $db_projects = Project::whereIn( 'id', $all_project_ids )->get()->toArray();
        $projects = [];

        foreach ( $db_projects as $project ) {
            $projects[$project['id']] = $project;
        }

        foreach ( $users as $user ) {
            foreach ( $user->projects as $project_id => $second ) {
                if( !empty( $projects[$project_id] ) ) {
                    $projects[$project_id]['w_seconds'] = $second;
                    $user->projects[$project_id] = $projects[$project_id];
                }
            }
        }

        return $users;
    }

    private function get_summary_project_users( $params ) {
        $projects  = $this->get_projects_from_time_log( $params['startDate'], $params['endDate'] );
        $projects  = $this->filter_users_from_time_log( $projects );

        foreach ( $projects as $key => $project ) {
            unset( $project->task_ids );
            unset( $project->seconds );
        }

        return [
            'type' => 'get_summary_project_by_users',
            'projects' => $projects
        ];

    }

    private function filter_users_from_time_log( $projects ) {
        $all_user_ids = [];
        foreach ( $projects as $project ) {
            $users = explode( '|', $project->users );
            $format_users = [];

            foreach ( $users as $user ) {
                $user       = str_replace('`', '"', $user);
                $user       = json_decode( $user );
                if ( ! empty( $user->user_id ) ) {
                    $format_users[$user->user_id][] = $user;
                }


            }

            $project->users = $format_users;
        }

        foreach ( $projects as $key => $project ) {
            foreach ( $project->users as $user_id => $user ) {
                $total_second = wp_list_pluck( $user, 'total' );
                $project->users[$user_id] = array_sum( $total_second );
                $all_user_ids[$user_id] = $user_id;
            }
        }

        $db_users = User::whereIn( 'id', $all_user_ids )->get()->toArray();
        $users = [];

        foreach ( $db_users as $user ) {
            $users[$user['ID']] = $user;
        }

        foreach ( $projects as $project ) {
            foreach ( $project->users as $user_id => $second ) {
                if( !empty( $users[$user_id] ) ) {
                    $users[$user_id]['w_seconds'] = $second;
                    $project->users[$user_id] =$users[$user_id];
                }
            }
        }

        return $projects;
    }


    public function get_summary_users( $params ) {
        return $this->get_summary_users_by_task_estimated_time( $params );
    }

    public function get_summary_users_by_task_estimated_time( $params ) {
        $users = $this->get_users_from_time_log($params['startDate'], $params['endDate']);

        $task_ids  = array_merge(wp_list_pluck( $users, 'task_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $est_times = $this->get_estimated_time_by_task_ids( $merge_ids );

        foreach ( $users as $key => $user ) {
            $task_estimation = [];

            foreach ( $user->task_ids as $id_key => $task_id ) {
                $task_estimation[$task_id] = empty( $est_times[$task_id] ) ? 0 : $est_times[$task_id]['estimation'];
            }

            $users[$key]->task_estimation = array_sum( $task_estimation );
        }

        return [
            'type' => 'get_user_all_projects_by_task_estimated_time',
            'users' => $users
        ];
    }

    public function get_summary_users_by_subtask_estimated_time( $params ) {
        $users = $this->get_users_from_time_log( $params['startDate'], $params['endDate'] );
        $this->set_subtask_ids_in_user( $users );

        $subtask_ids = array_merge(wp_list_pluck( $users, 'subtask_ids' ));
        $merge_ids = [];

        foreach ( $subtask_ids as $key => $subtask_id ) {
            $merge_ids = array_merge( $merge_ids, $subtask_id );
        }

        $est_times = $this->get_estimated_time_by_subtask_ids( $merge_ids );

        foreach ( $users as $key => $project ) {
            $subtask_estimation = [];

            foreach ( $project->subtask_ids as $id_key => $subtask_id ) {
                $subtask_estimation[$subtask_id] = empty( $est_times[$subtask_id] ) ? 0 : $est_times[$subtask_id];
            }

            $users[$key]->subtask_estimation = array_sum( $subtask_estimation );
        }

        return [
            'type' => 'get_user_all_projects_by_subtask_estimated_time',
            'users' => $users
        ];
    }

    public function get_summary_list_type( $params ) {
        return $this->get_summary_list_type_by_task_estimated_time( $params );
    }

    public function get_summary_list_type_by_task_estimated_time( $params ) {
        $lists     = $this->get_lists_from_time_log( $params['startDate'], $params['endDate'] );
        $task_ids  = array_merge(wp_list_pluck( $lists, 'task_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $est_times = $this->get_estimated_time_by_task_ids( $merge_ids );

        foreach ( $lists as $key => $project ) {
            $task_estimation = [];

            foreach ( $project->task_ids as $id_key => $task_id ) {
                $task_estimation[$task_id] = empty( $est_times[$task_id] ) ? 0 : $est_times[$task_id]['estimation'];
            }

            $lists[$key]->task_estimation = array_sum( $task_estimation );
        }

        $lists = $this->lists_group_by_title( $lists );

        return [
            'type' => 'get_summary_list_type_by_task_estimated_time',
            'lists' => $lists
        ];
    }

    function lists_group_by_title( $task_lists ) {
        $lists = [];

        foreach ( $task_lists as $key => $value ) {
            $lists[$value->list_title][] = $value;
        }

        $group_lists = [];

        foreach ( $lists as $list_title => $list ) {
            $seconds    = array_sum( wp_list_pluck( $list, 'working_time' ) );
            $estimation = array_sum( wp_list_pluck( $list, 'task_estimation' ) );

            $group_lists[] = [
                'list_title'      => $list_title,
                'working_time'    => $seconds,
                'task_estimation' => $estimation
            ];
        }

        return $group_lists;
    }

    public function get_summary_list_type_by_subtask_estimated_time( $params ) {
        $lists = $this->get_lists_from_time_log( $params['startDate'], $params['endDate'] );
        $this->set_subtask_ids_in_list( $lists );

        $task_ids = array_merge(wp_list_pluck( $lists, 'subtask_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $est_times = $this->get_estimated_time_by_subtask_ids( $merge_ids );

        foreach ( $lists as $key => $list ) {
            $subtask_estimation = [];

            foreach ( $list->subtask_ids as $id_key => $subtask_id ) {
                $subtask_estimation[$subtask_id] = empty( $est_times[$subtask_id] ) ? 0 : $est_times[$subtask_id];
            }

            $lists[$key]->subtask_estimation = array_sum( $subtask_estimation );
        }

        return [
            'type' => 'get_summary_list_type_by_subtask_estimated_time',
            'lists' => $lists
        ];
    }

    public function get_summary_all_projects( $params ) {

        return $this->get_summary_all_projects_by_task_estimated_time( $params );

    }

    public function get_summary_all_projects_by_task_estimated_time( $params ) {

        $projects  = $this->get_projects_from_time_log( $params['startDate'], $params['endDate'] );
        $task_ids  = array_merge(wp_list_pluck( $projects, 'task_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $est_times = $this->get_estimated_time_by_task_ids( $merge_ids );

        foreach ( $projects as $key => $project ) {
            $task_estimation = [];

            foreach ( $project->task_ids as $id_key => $task_id ) {
                $task_estimation[$task_id] = empty( $est_times[$task_id] ) ? 0 : $est_times[$task_id]['estimation'];
            }

            $projects[$key]->task_estimation = array_sum( $task_estimation );
        }

        return [
            'type' => 'get_summary_all_projects_by_task_estimated_time',
            'projects' => $projects
        ];
    }


    public function get_summary_all_projects_by_subtask_estimated_time( $params ) {
        $projects = $this->get_projects_from_time_log( $params['startDate'], $params['endDate'] );
        $this->set_subtask_ids_in_project( $projects );

        $subtask_ids = array_merge(wp_list_pluck( $projects, 'subtask_ids' ));
        $merge_ids = [];

        foreach ( $subtask_ids as $key => $subtask_id ) {
            $merge_ids = array_merge( $merge_ids, $subtask_id );
        }

        $est_times = $this->get_estimated_time_by_subtask_ids( $merge_ids );

        foreach ( $projects as $key => $project ) {
            $subtask_estimation = [];

            foreach ( $project->subtask_ids as $id_key => $subtask_id ) {
                $subtask_estimation[$subtask_id] = empty( $est_times[$subtask_id] ) ? 0 : $est_times[$subtask_id];
            }

            $projects[$key]->subtask_estimation = array_sum( $subtask_estimation );
        }

        return [
            'type' => 'get_summary_all_projects_by_subtask_estimated_time',
            'projects' => $projects
        ];
    }

    public function get_users_from_time_log( $start_date, $end_date ) {
        global $wpdb;

        $tb_users     = $wpdb->base_prefix . 'users';
        $tb_user_meta = $wpdb->base_prefix . 'usermeta';
        $tb_time      = pm_tb_prefix() . 'pm_time_tracker';
        $start_date   = date( 'Y-m-d 23:59:59', strtotime( $start_date ) );
        $end_date     = date( 'Y-m-d 23:59:59', strtotime( $end_date ) );

        if ( is_multisite() ) {
            $meta_key = pm_user_meta_key();

            $sql_user_time = "SELECT usr.ID as user_id, usr.display_name as display_name, usr.user_email as user_email,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'task_id', '\"', ':' , '\"', IFNULL(tm.task_id, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as task_ids,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'project_id', '\"', ':' , '\"', IFNULL(tm.project_id, '') , '\"',
                            ',',
                            '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as projects,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as seconds
                from $tb_time as tm
                LEFT JOIN $tb_users as usr ON tm.user_id=usr.ID
                LEFT JOIN $tb_user_meta as umeta ON umeta.user_id = usr.ID
                WHERE 1=1
                AND umeta.meta_key='$meta_key'
                AND
                (tm.created_at>='$start_date' AND tm.created_at<='$end_date')
                GROUP BY (tm.user_id)";
        } else {
            $sql_user_time = "SELECT usr.ID as user_id, usr.display_name as display_name, usr.user_email as user_email,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'task_id', '\"', ':' , '\"', IFNULL(tm.task_id, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as task_ids,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'project_id', '\"', ':' , '\"', IFNULL(tm.project_id, '') , '\"',
                            ',',
                            '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as projects,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as seconds
                from $tb_time as tm
                LEFT JOIN $tb_users as usr ON tm.user_id=usr.ID
                WHERE 1=1
                AND
                (tm.created_at>='$start_date' AND tm.created_at<='$end_date')
                GROUP BY (tm.user_id)";
        }

        $sql_user_time = "SELECT usr.ID as user_id, usr.display_name as display_name, usr.user_email as user_email,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'task_id', '\"', ':' , '\"', IFNULL(tm.task_id, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as task_ids,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'project_id', '\"', ':' , '\"', IFNULL(tm.project_id, '') , '\"',
                        ',',
                        '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as projects,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as seconds
            from $tb_time as tm
            LEFT JOIN $tb_users as usr ON tm.user_id=usr.ID
            WHERE 1=1
            AND
            (tm.created_at>='$start_date' AND tm.created_at<='$end_date')
            GROUP BY (tm.user_id)";

        $results = $wpdb->get_results( $sql_user_time );

        foreach ( $results as $key => $result ) {

            if(empty($result->user_id)) unset($results[$key]);

            $id_attrs = explode( '|', $result->task_ids );
            $task_ids = [];

            foreach ( $id_attrs as $id_key => $id_attr ) {
                $meta       = str_replace('`', '"', $id_attr);
                $meta       = json_decode( $meta );
                $task_ids[] =  $meta->task_id;

            }

            $result->task_ids = $task_ids;

            $second_attrs = explode( '|', $result->seconds );
            $seconds = [];

            foreach ( $second_attrs as $second_key => $second_attr ) {
                $meta      = str_replace( '`', '"', $second_attr );
                $meta      = json_decode( $meta );
                $seconds[] =  $meta->total;

            }

            $result->seconds = $seconds;
            $result->working_time = array_sum( $seconds );
        }

        return $results;
    }

    public function get_lists_from_time_log( $start_date, $end_date ) {
        global $wpdb;

        $tb_lists = pm_tb_prefix() . 'pm_boards';
        $tb_time     = pm_tb_prefix() . 'pm_time_tracker';
        $start_date  = date( 'Y-m-d 23:59:59', strtotime( $start_date ) );
        $end_date    = date( 'Y-m-d 23:59:59', strtotime( $end_date ) );

        $sql = "SELECT bo.id as list_id, bo.title as list_title,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'task_id', '\"', ':' , '\"', IFNULL(tm.task_id, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as task_ids,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as seconds
            from $tb_time as tm
            LEFT JOIN $tb_lists as bo ON tm.list_id=bo.id
            WHERE 1=1
            AND
            (tm.created_at>='$start_date' AND tm.created_at<='$end_date')
            GROUP BY (tm.list_id)";

        $results = $wpdb->get_results( $sql );

        foreach ( $results as $key => $result ) {

            if( empty($result->list_id) ) unset( $results[$key] );

            $id_attrs = explode( '|', $result->task_ids );
            $task_ids = [];

            foreach ( $id_attrs as $id_key => $id_attr ) {
                $meta       = str_replace('`', '"', $id_attr);
                $meta       = json_decode( $meta );
                $task_ids[] =  $meta->task_id;

            }

            $result->task_ids = $task_ids;

            $second_attrs = explode( '|', $result->seconds );
            $seconds = [];

            foreach ( $second_attrs as $second_key => $second_attr ) {
                $meta      = str_replace( '`', '"', $second_attr );
                $meta      = json_decode( $meta );
                $seconds[] =  $meta->total;

            }
            $result->seconds = $seconds;
            $result->working_time = array_sum( $seconds );
        }

        return $results;
    }

    public function get_projects_from_time_log( $start_date, $end_date ) {
        global $wpdb;

        $tb_projects = pm_tb_prefix() . 'pm_projects';
        $tb_time     = pm_tb_prefix() . 'pm_time_tracker';
        $start_date  = date( 'Y-m-d 23:59:59', strtotime( $start_date ) );
        $end_date    = date( 'Y-m-d 23:59:59', strtotime( $end_date ) );

        $sql_project = "SELECT pj.id as project_id, pj.title as project_title,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'task_id', '\"', ':' , '\"', IFNULL(tm.task_id, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as task_ids,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'user_id', '\"', ':' , '\"', IFNULL(tm.user_id, '') , '\"', ',',
                        '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as users,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'total', '\"', ':' , '\"', IFNULL(tm.total, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as seconds
            from $tb_time as tm
            LEFT JOIN $tb_projects as pj ON tm.project_id=pj.id
            WHERE 1=1
            AND
            (tm.created_at>='$start_date' AND tm.created_at<='$end_date')
            GROUP BY (tm.project_id)";

        $results = $wpdb->get_results( $sql_project );

        foreach ( $results as $key => $result ) {

            if(empty($result->project_id)) unset($results[$key]);

            $id_attrs = explode( '|', $result->task_ids );
            $task_ids = [];

            foreach ( $id_attrs as $id_key => $id_attr ) {
                $meta       = str_replace('`', '"', $id_attr);
                $meta       = json_decode( $meta );
                $task_ids[] =  $meta->task_id;

            }

            $result->task_ids = $task_ids;

            $second_attrs = explode( '|', $result->seconds );
            $seconds = [];

            foreach ( $second_attrs as $second_key => $second_attr ) {
                $meta      = str_replace( '`', '"', $second_attr );
                $meta      = json_decode( $meta );
                $seconds[] =  $meta->total;

            }

            $result->working_time = array_sum( $seconds );
        }

        return $results;
    }

    public function set_subtask_ids_in_list( &$lists ) {
        global $wpdb;

        $list_ids = wp_list_pluck( $lists, 'list_id' );

        $tb_boardable = pm_tb_prefix() . 'pm_boardables';
        $row_ids = implode(',', $list_ids);

        $sql = "SELECT bo.board_id as list_id, bo.boardable_id as subtask_id
            FROM $tb_boardable as bo
            WHERE
            bo.board_id IN ($row_ids)
            AND
            bo.board_type='task_list'
            AND
            bo.boardable_type='sub_task'";

        $results = $wpdb->get_results( $sql );
        $ids = [];

        foreach ( $results as $key => $result ) {
            $ids[$result->list_id][] = $result->subtask_id;
        }

        foreach ( $lists as $key => $list ) {
            $list->subtask_ids = empty( $ids[$list->list_id] ) ? [] : $ids[$list->list_id];
        }
    }

    public function set_subtask_ids_in_project( &$projects ) {
        global $wpdb;

        $task_ids = array_merge(wp_list_pluck( $projects, 'task_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $tb_task = pm_tb_prefix() . 'pm_tasks';
        $row_ids = implode(',', $merge_ids);

        $sql = "SELECT id, parent_id, project_id
            FROM $tb_task
            WHERE
            parent_id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );

        $project_subtasks = [];

        foreach ( $results as $key => $result ) {
            if(empty($result->project_id)) {
                continue;
            }
            $project_subtasks[$result->project_id][] = $result->id;
        }

        foreach ( $projects as $key => $project ) {
            $project->subtask_ids = empty( $project_subtasks[$project->project_id] ) ? [] : $project_subtasks[$project->project_id];
        }
    }

    public function set_subtask_ids_in_user( &$users ) {
        global $wpdb;

        $task_ids  = array_merge(wp_list_pluck( $users, 'task_ids' ));
        $merge_ids = [];

        foreach ( $task_ids as $key => $task_id ) {
            $merge_ids = array_merge( $merge_ids, $task_id );
        }

        $tb_task      = pm_tb_prefix() . 'pm_tasks';
        $tb_assignees = pm_tb_prefix() . 'pm_assignees';
        $row_ids      = implode(',', $merge_ids);

        $sql = "SELECT asi.task_id as subtask_id, asi.assigned_to as user_id
            FROM $tb_task as tk
            LEFT JOIN $tb_assignees as asi ON tk.id=asi.task_id
            WHERE
            tk.parent_id IN ($row_ids)";

        $results = $wpdb->get_results( $sql );

        $user_subtasks = [];

        foreach ( $results as $key => $result ) {
            if(empty($result->user_id)) {
                continue;
            }
            $user_subtasks[$result->user_id][] = $result->subtask_id;
        }

        foreach ( $users as $key => $user ) {
            $user->subtask_ids = empty( $user_subtasks[$user->user_id] ) ? [] : $user_subtasks[$user->user_id];
        }
    }


}
