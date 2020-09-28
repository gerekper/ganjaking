<?php
namespace WeDevs\PM_Pro\Calendar\Controllers;

use WP_REST_Request;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Milestone\Models\Milestone;
use WeDevs\PM\Project\Models\Project;
use Carbon\Carbon;
use WeDevs\PM\Calendar\Transformers\Calendar_Transformer;
use WeDevs\PM\User\Models\User_Role;


class Calendar_Controller {

    use Transformer_Manager;

    public $all_users = [];

    private function filter_users( $param_users, $project ) {
        if ( empty( $project ) ) {
            return [];
        }

        $project_users   = empty( $project['assignees']['data'] ) ? [] : $project['assignees']['data'];
        $project_users   = wp_list_pluck( $project_users, 'id' );
        $param_users     = pm_get_prepare_data( $param_users );
        $filtered_users  = array_intersect($project_users, $param_users);
        $current_user_id = get_current_user_id();

        if ( count( $param_users ) &&  empty( $filtered_users ) ) {
            return [];
        }

        if ( empty( $filtered_users ) ) {

            if ( in_array( $current_user_id, $project_users ) ) {
                return $project_users;
            }

            if ( pm_has_manage_capability() ) {
                return $project_users;
            }

            if ( pm_is_manager( $project['id'] ) ) {
                return $project_users;
            }
        }

        return $filtered_users;
    }

    public function index( WP_REST_Request $request ) {
        global $wpdb;

        $project_id    = $request->get_param( 'project_id' );
        $project       = pm_get_projects( ['id' => $project_id, 'with' => 'assignees'] );
        $project       = empty( $project['data'] ) ? [] : $project['data'];
        $start         = $request->get_param( 'start' );
        $end           = $request->get_param( 'end' );
        $events        = [];
        $users         = $this->filter_users( $request->get_param( 'users' ), $project );
        $users         = empty( $users ) ? [0] : $users;
        $user_id       = get_current_user_id();
        $tb_tasks      = pm_tb_prefix() . 'pm_tasks';
        $tb_boards     = pm_tb_prefix() . 'pm_boards';
        $tb_boardables = pm_tb_prefix() . 'pm_boardables';
        $tb_assignees  = pm_tb_prefix() . 'pm_assignees';
        $tb_meta       = pm_tb_prefix() . 'pm_meta';
        $tb_projects   = pm_tb_prefix() . 'pm_projects';
        $tb_settings   = pm_tb_prefix() . 'pm_settings';

        $tb_users     = $wpdb->base_prefix . 'users';
        $tb_user_meta = $wpdb->base_prefix . 'usermeta';

        $tb_role_user  = pm_tb_prefix() . 'pm_role_user';
        $current_user_id = get_current_user_id();

        $project_ids = $this->get_current_user_project_ids( $project_id );

        $boards     = "SELECT id FROM $tb_boards WHERE type='task_list' and status=1";
        $get_boards = $wpdb->get_results( $boards );
        $boards_id  = wp_list_pluck( $get_boards, 'id' );
        $boards_id  = implode( ',', $boards_id );
        $boards_id  = empty( $boards_id ) ? 0 : $boards_id;

        if ( empty( $project_id ) ) {
            $where_projec_ids = "";
        } else {
            $where_projec_ids = "AND pj.id IN ($project_id)";
        }


        $users = implode( ',', $users );
        $where_users = " AND asin.assigned_to IN ($users)";


        if ( pm_has_manage_capability() ) {
            $where_users = '';
        }

        if ( pm_is_manager( intval( $project_ids ) ) ) {
            $where_users = '';
        }

        if ( is_multisite() ) {
            $meta_key = pm_user_meta_key();

            $event_query = "SELECT tsk.*,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'meta_key', '\"', ':' , '\"', IFNULL(tskmt.meta_key, '') , '\"', ',',
                            '\"', 'meta_value', '\"', ':' , '\"', IFNULL(tskmt.meta_value, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as task_meta,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'meta_key', '\"', ':' , '\"', IFNULL(boablmt.meta_key, '') , '\"', ',',
                            '\"', 'meta_value', '\"', ':' , '\"', IFNULL(boablmt.meta_value, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as list_meta,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'assigned_to', '\"', ':' , '\"', IFNULL(asins.assigned_to, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as assignees,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        IFNULL(sett.value, '')
                    ) SEPARATOR '|'
                ) as settings,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'title', '\"', ':' , '\"', IFNULL(pj.title, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as project,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'board_id', '\"', ':' , '\"', IFNULL(boabl.board_id, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as boardable,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'id', '\"', ':' , '\"', IFNULL(usr.ID, '') , '\"', ',',
                            '\"', 'display_name', '\"', ':' , '\"', IFNULL(usr.display_name, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as users

                FROM $tb_tasks as tsk

                LEFT JOIN $tb_boardables as boabl
                    ON (tsk.id=boabl.boardable_id AND boabl.board_type='task_list' AND boabl.boardable_type='task')

                LEFT JOIN $tb_boards as board
                    ON (boabl.board_id=board.id AND board.type='task_list')

                LEFT JOIN $tb_projects as pj ON (tsk.project_id=pj.id)

                -- For getting multipule assignee users in individual task
                LEFT JOIN $tb_assignees as asins ON tsk.id=asins.task_id

                -- For filter user
                LEFT JOIN $tb_assignees as asin ON tsk.id=asin.task_id

                -- For getting all users information
                LEFT JOIN $tb_users as usr ON asins.assigned_to=usr.ID
                LEFT JOIN $tb_user_meta as umeta ON umeta.user_id = usr.ID

                LEFT JOIN $tb_meta as tskmt
                    ON (tsk.id=tskmt.entity_id AND tskmt.entity_type='task')

                LEFT JOIN $tb_meta as boablmt
                    ON ( boabl.board_id=boablmt.entity_id AND boablmt.entity_type='task_list')

                LEFT JOIN $tb_settings as sett ON pj.id=sett.project_id AND sett.key='capabilities'

                WHERE 1=1
                    AND umeta.meta_key='$meta_key'
                    AND pj.status = 0
                    AND
                    (
                        (tsk.due_date >= '$start')
                            or
                        (tsk.due_date is null and tsk.start_at >= '$start')
                            or
                        (tsk.start_at is null and tsk.due_date >= '$start' )
                            or
                        ((tsk.start_at is null AND tsk.due_date is null) and tsk.created_at >= '$start')
                    )
                    AND
                    board.id IN ($boards_id)
                    $where_projec_ids

                    $where_users

                GROUP BY(tsk.id)";

        } else {

            $event_query = "SELECT tsk.*,
                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'meta_key', '\"', ':' , '\"', IFNULL(tskmt.meta_key, '') , '\"', ',',
                            '\"', 'meta_value', '\"', ':' , '\"', IFNULL(tskmt.meta_value, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as task_meta,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'meta_key', '\"', ':' , '\"', IFNULL(boablmt.meta_key, '') , '\"', ',',
                            '\"', 'meta_value', '\"', ':' , '\"', IFNULL(boablmt.meta_value, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as list_meta,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'assigned_to', '\"', ':' , '\"', IFNULL(asins.assigned_to, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as assignees,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        IFNULL(sett.value, '')
                    ) SEPARATOR '|'
                ) as settings,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'title', '\"', ':' , '\"', IFNULL(pj.title, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as project,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'board_id', '\"', ':' , '\"', IFNULL(boabl.board_id, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as boardable,

                GROUP_CONCAT(
                    DISTINCT
                    CONCAT(
                        '{',
                            '\"', 'id', '\"', ':' , '\"', IFNULL(usr.ID, '') , '\"', ',',
                            '\"', 'display_name', '\"', ':' , '\"', IFNULL(usr.display_name, '') , '\"'
                        ,'}'
                    ) SEPARATOR '|'
                ) as users

                FROM $tb_tasks as tsk

                LEFT JOIN $tb_boardables as boabl
                    ON (tsk.id=boabl.boardable_id AND boabl.board_type='task_list' AND boabl.boardable_type='task')

                LEFT JOIN $tb_boards as board
                    ON (boabl.board_id=board.id AND board.type='task_list')

                LEFT JOIN $tb_projects as pj ON (tsk.project_id=pj.id)

                -- For getting multipule assignee users in individual task
                LEFT JOIN $tb_assignees as asins ON tsk.id=asins.task_id

                -- For filter user
                LEFT JOIN $tb_assignees as asin ON tsk.id=asin.task_id

                -- For getting all users information
                LEFT JOIN $tb_users as usr ON asins.assigned_to=usr.ID

                LEFT JOIN $tb_meta as tskmt
                    ON (tsk.id=tskmt.entity_id AND tskmt.entity_type='task')

                LEFT JOIN $tb_meta as boablmt
                    ON ( boabl.board_id=boablmt.entity_id AND boablmt.entity_type='task_list')

                LEFT JOIN $tb_settings as sett ON pj.id=sett.project_id AND sett.key='capabilities'

                WHERE 1=1
                    AND pj.status = 0
                    AND
                    (
                        (tsk.due_date >= '$start')
                            or
                        (tsk.due_date is null and tsk.start_at >= '$start')
                            or
                        (tsk.start_at is null and tsk.due_date >= '$start' )
                            or
                        ((tsk.start_at is null AND tsk.due_date is null) and tsk.created_at >= '$start')
                    )
                    AND
                    board.id IN ($boards_id)
                    $where_projec_ids

                    $where_users

                GROUP BY(tsk.id)";
        }

        $events = $wpdb->get_results( $event_query );

        $user_roles = $wpdb->prepare("SELECT DISTINCT user_id, project_id, role_id FROM $tb_role_user WHERE user_id=%d", $current_user_id);
        $user_roles = $wpdb->get_results( $user_roles );


        $tasks   = $this->Calendar_Transformer( $events, $user_roles );
        $milestones = $this->get_milestones( $project_ids, $users, $start, $end, $user_roles );

        $merge = array_merge( $tasks, $milestones );

        wp_send_json_success( $merge );
    }

    public function get_milestones( $project_ids, $users, $start, $end, $user_roles ) {
        global $wpdb;

        $tb_board = pm_tb_prefix() . 'pm_boards';
        $tb_role_user  = pm_tb_prefix() . 'pm_role_user';
        $tb_meta       = pm_tb_prefix() . 'pm_meta';
        $tb_projects   = pm_tb_prefix() . 'pm_projects';
        $tb_settings   = pm_tb_prefix() . 'pm_settings';

        if ( ! empty( $users ) && is_array( $users ) ) {
            $users = implode( ',', $users );
            $where_users = " AND rousr.user_id IN ($users)";
        } else if ( ! empty( $users ) && !is_array( $users ) ) {
            $users = [$users];
            $users = implode( ',', $users );
            $where_users = " AND rousr.user_id IN ($users)";
        } else {
            $where_users = '';
        }

        $project_ids = is_array( $project_ids ) ? implode( ',', $project_ids ) : 0;
        $project_ids = empty( $project_ids ) ? 0 : $project_ids;

        $query = "SELECT DISTINCT bo.*,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'meta_key', '\"', ':' , '\"', IFNULL(bomt.meta_key, '') , '\"', ',',
                        '\"', 'meta_value', '\"', ':' , '\"', IFNULL(bomt.meta_value, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as milestone_meta,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    IFNULL(sett.value, '')
                ) SEPARATOR '|'
            ) as settings

            FROM $tb_board as bo

            LEFT JOIN $tb_role_user as rousr
                ON bo.project_id=rousr.project_id
            LEFT JOIN $tb_settings as sett
                ON bo.project_id=sett.project_id AND sett.key='capabilities'
            LEFT JOIN $tb_meta as bomt
                ON (bo.id=bomt.entity_id AND bomt.entity_type='milestone')

            WHERE 1=1
            AND bo.type='milestone'
            AND bo.project_id IN($project_ids)

            $where_users

            GROUP BY(bo.id)";

        $milestones = $wpdb->get_results( $query );
        $resource   = $this->Calendar_Milestone_Transformer( $milestones, $user_roles );

        return $resource;
    }

    public function Calendar_Milestone_Transformer( $milestones, $user_roles ) {
        $has_manage_cap = pm_has_manage_capability();
        $current_user_id = get_current_user_id();
        $roles = [];
        $transform_milestones = [];

        foreach ( $user_roles as $key => $user_role ) {
            $roles[$user_role->project_id][$user_role->user_id] = $user_role->role_id;
        }

        foreach ( $milestones as $key => $milestone ) {

            $role = 0;

            if ( ! empty( $roles[$milestone->project_id][$current_user_id] ) ) {
                $role = $roles[$milestone->project_id][$current_user_id];
            }

            $milestone->privacy  = $this->get_privacy_meta_value( $milestone->milestone_meta );
            $milestone->settings = $this->get_settings_value( $milestone->settings );

            if ( empty( $milestone->settings ) ) {
                $milestone->settings = [
                    'co_worker' => pm_default_co_caps(),
                    'client' => pm_default_client_caps()
                ];
            }

            if ( ! $this->milestone_view_permission(
                $has_manage_cap,
                $role,
                $milestone->privacy,
                $milestone->settings
            ) ) {
                continue;
            }

            $due_date = $this->get_meta_value( $milestone->milestone_meta, 'achieve_date' );

            $date = empty( $due_date ) ? format_date( $milestone->created_at ) : format_date( $due_date );

            $transform_milestones[] = [
                'id'            => (int) $milestone->id,
                'title'         => $milestone->title,
                'start'         => $date,
                'end'           => $date,
                'status'        => $milestone->status ? 'complete' : 'incomplete',
                'type'          => 'milestone',
                'project_id'    => $milestone->project_id,
                'created_at'    => format_date( $milestone->created_at ),
                'updated_at'    => format_date( $milestone->updated_at ),
            ];
        }

        return $transform_milestones;
    }

    public function get_meta_value( $event_meta, $meta_key ) {
        $metas = explode( '|', $event_meta );

        foreach ( $metas as $key => $meta ) {
            $meta = str_replace('`', '"', $meta);
            $meta = json_decode( $meta );

            if ( ! empty( $meta->meta_key ) && $meta->meta_key == $meta_key ) {
                return $meta->meta_value;
            }

        }

        return '';
    }

    public function milestone_view_permission(
        $has_manage_cap,
        $role,
        $milestone_privacy,
        $settings
    ) {

        if ( ! empty( (int)$has_manage_cap ) ) {
            return true;
        }

        if ( $role == 1 )  {
            return true;
        }

        if ( $milestone_privacy == 1 ) {
            if ( $role == 2 ) {

                if (
                    $settings['co_worker']['view_private_milestone'] == 'false'
                        ||
                    empty( $settings['co_worker']['view_private_milestone'] )
                ) {
                    return false;
                }
            }

            if ( $role == 3 ) {
                if (
                    $settings['client']['view_private_milestone'] == 'false'
                        ||
                    empty( $settings['client']['view_private_milestone'] )
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function get_current_user_project_ids( $project_id = false ) {
        global $wpdb;

        $tb_role_user  = pm_tb_prefix() . 'pm_role_user';
        $user_id = get_current_user_id();

        $project_ids = [];

        // IF empty project id
        if ( empty( $project_id ) && ! pm_has_manage_capability() ) {
            $project_query = $wpdb->prepare( "SELECT DISTINCT project_id FROM $tb_role_user WHERE user_id=%d", $user_id );

            $project_ids = $wpdb->get_results( $project_query );
            $project_ids = wp_list_pluck( $project_ids, 'project_id' );
        }

        if ( empty( $project_id ) && pm_has_manage_capability() ) {
            $project_query = $wpdb->prepare( "SELECT DISTINCT project_id FROM $tb_role_user WHERE 1=%s", 1 );

            $project_ids = $wpdb->get_results( $project_query );
            $project_ids = wp_list_pluck( $project_ids, 'project_id' );
        }

        // IF project id in array
        if ( ! empty( $project_id ) && is_array( $project_id ) ) {
            $project_ids = $project_id;
        }

        // IF project id in integar
        if ( ! empty( $project_id ) && ! is_array( $project_id ) ) {
            $project_ids = [$project_id];
        }

        return $project_ids;
    }

    public function Calendar_Transformer( $events, $user_roles ) {
        $current_user_id = get_current_user_id();
        $has_manage_cap = pm_has_manage_capability();
        $roles = [];
        $tasks = [];

        foreach ( $user_roles as $key => $user_role ) {
            $roles[$user_role->project_id][$user_role->user_id] = $user_role->role_id;
        }

        foreach ( $events as $key => $event ) {

            $role = 0;

            if ( ! empty( $roles[$event->project_id][$current_user_id] ) ) {
                $role = $roles[$event->project_id][$current_user_id];
            }

            $event->list_id       = $this->get_list_id( $event->boardable );
            $event->task_privacy  = $this->get_privacy_meta_value( $event->task_meta );
            $event->list_privacy  = $this->get_privacy_meta_value( $event->list_meta );
            $event->assignees     = $this->get_assignees_value( $event->assignees, $event->users );
            $event->settings      = $this->get_settings_value( $event->settings );
            $event->project_title = $this->get_project_title( $event->project );

            if ( empty( $event->settings ) ) {
                $event->settings = [
                    'co_worker' => pm_default_co_caps(),
                    'client' => pm_default_client_caps()
                ];
            }

            if ( ! $this->has_view_permission(
                    $has_manage_cap,
                    $role,
                    $event->list_privacy,
                    $event->task_privacy,
                    $event->settings,
                    $event->assignees
                )
            ) {
                continue;
            }

            $tasks[] = [
                'id'            => (int) $event->id,
                'title'         =>  $event->title,
                'start'         =>  $this->get_start( $event ),
                'end'           =>  $this->get_end( $event ),
                'status'        =>  $event->status ? 'complete' : 'incomplete',
                'type'          =>  'task',
                'project_id'    => $event->project_id,
                'created_at'    => format_date( $event->created_at ),
                'updated_at'    => format_date( $event->updated_at ),
                'assignees'     => $event->assignees
            ];
        }

        return $tasks;
    }

    public function get_end( $event ) {

        $time =  date( 'H:i:s', strtotime( $event->updated_at ) );

        if ( ! empty( $event->due_date ) ) {
            $date = format_date( $event->due_date );
        } else if ( ! empty( $event->start_at )) {
            $date = format_date( $event->start_at);
        } else {
            $date = format_date( $event->created_at );
        }

        $date['date'] = date( 'Y-m-d ' . $time, strtotime( $date['date'] ) );

        return $date;
    }

    public function get_start( $event ) {

        $time =  date( 'H:i:s', strtotime( $event->created_at ) );

        if ( !empty( $event->start_at ) ) {
            $date = format_date( $event->start_at);
        } else if ( isset( $event->due_date ) ) {
            $date = format_date( $event->due_date );
        } else {
            $date = format_date( $event->created_at );
        }

        $date['date'] = date( 'Y-m-d ' . $time, strtotime( $date['date'] ) );

        return $date;
    }

    public function has_view_permission(
        $has_manage_cap,
        $role,
        $list_privacy,
        $task_privacy,
        $settings,
        $assignees
    ) {

        $current_user_id = get_current_user_id();

        if ( ! empty( (int)$has_manage_cap ) ) {
            return true;
        }

        if ( $role == 1 )  {
            return true;
        }

        $assignees = wp_list_pluck( $assignees['data'], 'id' );

        if ( in_array( $current_user_id, $assignees ) ) {
            return true;
        }

        if ( $list_privacy == 1 ) {
            if ( $role == 2 ) {
                if (
                    $settings['co_worker']['view_private_list'] == 'false'
                        ||
                    empty( $settings['co_worker']['view_private_list'] )
                ) {
                    return false;
                }
            }

            if ( $role == 3 ) {
                if (
                    $settings['client']['view_private_list'] == 'false'
                        ||
                    empty( $settings['client']['view_private_list'] )
                ) {
                    return false;
                }
            }
        }

        if ( $task_privacy == 1 ) {
            if ( $role == 2 ) {

                if (
                    $settings['co_worker']['view_private_task'] == 'false'
                        ||
                    empty( $settings['co_worker']['view_private_task'] )
                ) {
                    return false;
                }
            }

            if ( $role == 3 ) {
                if (
                    $settings['client']['view_private_task'] == 'false'
                        ||
                    empty( $settings['client']['view_private_task'] )
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function get_list_id( $boardables ) {
        $boardables = explode( '|', $boardables );

        foreach ( $boardables as $key => $boardable ) {
            $boardable = str_replace('`', '"', $boardable);
            $boardable = json_decode( $boardable );

            if ( ! empty( $boardable->board_id ) ) {
                return $boardable->board_id;
            }
        }

        return '';
    }

    public function get_project_title( $projects ) {
        $projects = explode( '|', $projects );

        foreach ( $projects as $key => $project ) {
            $project = str_replace('`', '"', $project);
            $project = json_decode( $project );

            if ( ! empty( $project->title ) ) {
                return $project->title;
            }
        }

        return '';
    }

    public function get_settings_value( $settings ) {
        $settings = explode( '|', $settings );

        foreach ( $settings as $key => $setting ) {
            return !empty( $setting ) ? maybe_unserialize( $setting ) : '';
        }

        return [];
    }

    public function get_assignees_value( $assignees, $users ) {
        $expand_users = [];

        // $assignees = explode( '|', $assignees );

        // foreach ( $assignees as $key => $assignee ) {
        //     $assignee = str_replace('`', '"', $assignee);
        //     $assignee = json_decode( $assignee );

        //     if ( ! empty( $assignee->assigned_to ) ) {
        //         $return[] = $assignee->assigned_to;
        //     }
        // }

        $users = explode( '|', $users );

        foreach ( $users as $key => $user ) {
            $user = str_replace('`', '"', $user);
            $user = json_decode( $user );

            if ( ! empty( $user->id ) ) {
                $expand_users[] = [
                    'id'           => $user->id,
                    'display_name' => $user->display_name,
                    'avatar_url'   => get_avatar_url( $user->id )
                ];
            }
        }

        return [
            'data' => $expand_users
        ];

    }

    public function get_privacy_meta_value( $event_meta ) {
        $metas = explode( '|', $event_meta );

        foreach ( $metas as $key => $meta ) {
            $meta = str_replace('`', '"', $meta);
            $meta = json_decode( $meta );

            if ( ! empty( $meta->meta_key ) && $meta->meta_key == 'privacy' ) {
                return $meta->meta_value;
            }

        }

        return '';
    }

    public function indexs( WP_REST_Request $request ) {

        $project_id = $request->get_param( 'project_id' );
        $start      = $request->get_param( 'start' );
        $end        = $request->get_param( 'end' );
        $events     = array();
        $users      = $request->get_param( 'users' );
        $user_id    = get_current_user_id();

        // get user assigness project ids
        if ( empty( $project_id ) ) {
            $project_ids = User_Role::where( 'user_id', $user_id)->get(['project_id'])->toArray();
            $project_ids = wp_list_pluck($project_ids, 'project_id');
        }else {
            $project_ids =[$project_id];
        }

        $milestones = Milestone::with(
                [
                    'metas' => function ( $q ) {
                        $q->whereIn( 'meta_key', ['achieve_date', 'status'] );
                    }
                ]
            )
            ->whereIn( 'project_id', $project_ids)
            ->whereHas( 'metas' , function ( $q ) use( $start, $end ) {
                $q->where( function ( $q2 ) use( $start, $end ) {
                    $q2->where( 'meta_key', 'achieve_date' )
                    ->whereBetween( 'meta_value', array($start, $end) )
                    ->orWhereNull( 'meta_value' );
                } );

            });

        // Check milseotne is private or not and has permission to show private

        if ( !$this->can_view( 'view_private_milestone', $project_id,  $user_id ) ) {
            $milestones = $milestones->doesntHave('metas', 'and', function( $q ){
                $q->where( 'meta_key', '=', 'privacy' )
                    ->where( 'meta_value', '!=', '0' );
            });
        }
    }

    private function can_view( $can, $project_id, $user_id ) {

        if ( pm_has_manage_capability( $user_id ) ){
            return true;
        }

        if ( $project_id != -1  && pm_user_can( $can, $project_id ) ){
            return true;
        }

        return false;
    }

    public function get_projects( WP_REST_Request $request ) {

        $has_manage_cap = pm_has_manage_capability();

        if ( $has_manage_cap ) {
            $projects = pm_get_projects(
                [
                 'status' => 'incomplete',
                 'with' => 'assignees'
                ]
            );
        } else {
            $projects = pm_get_projects(
                [
                    'status' => 'incomplete',
                    'inUsers' => get_current_user_id(),
                    'with' => 'assignees'
                ]
            );
        }

        $users = [];

        foreach ( $projects['data'] as $key => $project ) {
            if ( empty( $project['assignees']['data'] ) ) {
                continue;
            }

            foreach ( $project['assignees']['data'] as $key => $user ) {
                $users[$user->id] = $user;
            }
        }

        wp_send_json_success(
            [
                'projects'        => $projects['data'],
                'users'         => $users,
                'has_manage_cap'  => $has_manage_cap,
                'current_user'    => wp_get_current_user(),
                'current_user_id' => get_current_user_id(),
            ]
        );

        exit();







        global $wpdb;

        $per_page               = $request->get_param( 'per_page' );
        $with                   = $request->get_param( 'with' );
        $page                   = $request->get_param( 'page' );
        $per_page_from_settings = pm_get_setting( 'project_per_page' );
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;
        $per_page               = $per_page ? $per_page : $per_page_from_settings;
        $page                   = $page ? $page : 1;
        $with                   = explode(',', $with);
        $left_join              = '';
        $current_user_id        = get_current_user_id();
        $where                  = '';

        if ( in_array( 'task_lists',$with ) ) {
            $tb_list = pm_tb_prefix() . 'pm_boards';
            $left_join .= " LEFT JOIN $tb_list as lst ON pj.id=lst.project_id AND lst.type='task_list'";
        }

        if ( in_array( 'users',$with ) ) {
            if ( is_multisite() ) {
                $tb_users     = $wpdb->base_prefix . 'users';
                $tb_user_meta = $wpdb->base_prefix . 'usermeta';
                $tb_role_user = pm_tb_prefix() . 'pm_role_user';
                $meta_key     = pm_user_meta_key();

                $left_join .= " LEFT JOIN $tb_role_user as rol ON pj.id=rol.project_id
                    LEFT JOIN $tb_users as usr ON rol.user_id=usr.ID
                    LEFT JOIN $tb_user_meta as umeta ON umeta.user_id = usr.ID";

                $where = " AND umeta.meta_key='$meta_key'";
            } else {
                $tb_users     = $wpdb->base_prefix . 'users';
                $tb_role_user = pm_tb_prefix() . 'pm_role_user';

                $left_join .= " LEFT JOIN $tb_role_user as rol ON pj.id=rol.project_id
                    LEFT JOIN $tb_users as usr ON rol.user_id=usr.ID";
            }

        }

        //if ( ! pm_has_manage_capability( $current_user_id ) ) {
            $tb_meta       = pm_tb_prefix() . 'pm_meta';
            $tb_settings   = pm_tb_prefix() . 'pm_settings';
            $left_join .= " LEFT JOIN $tb_meta as lstmt ON lst.id=lstmt.entity_id AND lstmt.entity_type='task_list'
                LEFT JOIN $tb_settings as sets ON pj.id=sets.project_id AND sets.key='capabilities'";

            //$where = " ";
            //$where = " AND rol.user_id = $current_user_id";
           // $where = " AND rol.user_id = $current_user_id";
        //}

        $tb_project = pm_tb_prefix() . 'pm_projects';

        $query = "SELECT pj.*,
            -- DISTINCT pj.id as project_id, pj.title as project_title,
            -- lst.id as list_id, lst.title as list_title,
            -- lstmt.meta_key as list_meta_key, lstmt.meta_value as list_meta_value,
            -- sets.value as project_settings,
            -- rol.role_id as role_id, rol.user_id
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'id', '\"', ':' , '\"', IFNULL(lst.id, '') , '\"', ',',
                        '\"', 'title', '\"', ':' , '\"', IFNULL(lst.title, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as lists,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'list_id', '\"', ':' , '\"', IFNULL(lstmt.entity_id, '') , '\"', ',',
                        '\"', 'meta_key', '\"', ':' , '\"', IFNULL(lstmt.meta_key, '') , '\"', ',',
                        '\"', 'meta_value', '\"', ':' , '\"', IFNULL(lstmt.meta_value, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as list_meta,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'user_id', '\"', ':' , '\"', IFNULL(rol.id, '') , '\"', ',',
                        '\"', 'role_id', '\"', ':' , '\"', IFNULL(rol.role_id, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as roles,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'user_id', '\"', ':' , '\"', IFNULL(usr.id, '') , '\"', ',',
                        '\"', 'display_name', '\"', ':' , '\"', IFNULL(usr.display_name, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as users,

            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    IFNULL(sets.value, '')
                ) SEPARATOR '|'
            ) as settings

            FROM $tb_project as pj

            $left_join

            WHERE 1=1

            $where
            GROUP BY(pj.id)";

        $results = $wpdb->get_results($query);
        $projects = $this->format_calendar_projects( $results );

        wp_send_json_success(
            [
                'projects' => $projects,
                'users'    => $this->all_users
            ]
        );
    }

    function format_calendar_projects( $projects ) {
        $has_manage_cap = pm_has_manage_capability();
        $current_user_id = get_current_user_id();

        $returns = [];

        foreach ( $projects as $key => $project ) {
            $users  = $this->gte_project_users( $project->users, $project->roles );
            $list_metas  = $this->gte_project_list_meta( $project->list_meta );

            //if user not in project
            if ( empty( $users[$current_user_id] ) && ! $has_manage_cap ) {
                continue;
            }

            $lists = $this->get_project_lists(
                $project->lists,
                $list_metas,
                $project->settings,
                $users,
                $has_manage_cap,
                $current_user_id
            );

            if ( pm_has_manage_capability( $current_user_id ) ) {
                foreach ( $users as $key => $user ) {
                    $this->all_users[$user['id']] = $user;
                }
            }

            $returns[] = [
                'id' => $project->id,
                'title' => $project->title,
                'project_status' => $project->status,
                'task_lists' => $lists,
                'assignees' => $users
            ];
        }

        return $returns;
    }

    public function gte_project_users( $users, $roles ) {
        $roles = $this->get_project_users_role( $roles );

        $expand_users = [];

        $users = explode( '|', $users );

        foreach ( $users as $key => $user ) {
            $user = str_replace('`', '"', $user);
            $user = json_decode( $user );

            if ( ! empty( $user->user_id ) ) {
                $expand_users[$user->user_id] = [
                    'id'           => $user->user_id,
                    'avatar_url'   => get_avatar_url( $user->user_id ),
                    'display_name' => $user->display_name,
                    'role'         => ! empty( $roles[$user->user_id] ) ? $roles[$user->user_id] : '',
                ];
            }
        }

        return $expand_users;
    }

    public function get_project_users_role( $roles ) {
        $expand_roles = [];

        $roles = explode( '|', $roles );

        foreach ( $roles as $key => $role ) {
            $role = str_replace('`', '"', $role);
            $role = json_decode( $role );

            if ( ! empty( $role->role_id ) ) {
                $expand_roles[$role->user_id] = $role->role_id;
            }
        }

        return $expand_roles;
    }

    public function get_project_lists( $lists, $metas, $settings, $users, $has_manage_cap, $current_user_id ) {

        $role         = ! empty( $users[$current_user_id] ) ? $users[$current_user_id]['role'] : '';
        $settings     = !empty( $settings ) ? maybe_unserialize( $settings ) : [];
        $expand_lists = [];
        $lists        = explode( '|', $lists );

        foreach ( $lists as $key => $list ) {
            $list = str_replace('`', '"', $list);
            $list = json_decode( $list );
            if ( empty( $list->id ) ) {
                continue;
            }

            $privacy = !empty( $metas[$list->id] ) ? $metas[$list->id]['meta_value'] : '';

            if ( ! $this->project_list_view_permission(
                $has_manage_cap,
                $role,
                $privacy,
                $settings

            ) ) {
                continue;
            }

            $expand_lists[] = [
                'id'    => $list->id,
                'title' => $list->title,
            ];

        }

        return $expand_lists;
    }

    public function project_list_view_permission(
        $has_manage_cap,
        $role,
        $list_privacy,
        $settings
    ) {

        if ( $has_manage_cap ||  $role == 1 ) {
            return true;
        }

        if ( $list_privacy == 1 ) {
            if ( $role == 2 ) {
                if (
                    ! empty( $settings['co_worker'] )
                    &&
                    ! $settings['co_worker']['view_private_list']
                ) {
                    return false;
                }
            }

            if ( $role == 3 ) {
                if (
                    ! empty( $settings['client'] )
                    &&
                    ! $settings['client']['view_private_list']
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function gte_project_list_meta( $metas ) {
        $expand_metas = [];

        $metas = explode( '|', $metas );

        foreach ( $metas as $key => $meta ) {
            $meta = str_replace('`', '"', $meta);
            $meta = json_decode( $meta );

            if ( ! empty( $meta->list_id ) && $meta->meta_key == 'privacy' ) {
                $expand_metas[$meta->list_id] = [
                    'list_id'    => $meta->list_id,
                    'meta_key'   => $meta->meta_key,
                    'meta_value' => $meta->meta_value,
                ];
            }
        }

        return $expand_metas;
    }

    public function get_project_assignees( $users ) {
        $expand_users = [];

        $users = explode( '|', $users );

        foreach ( $users as $key => $user ) {
            $user = str_replace('`', '"', $user);
            $user = json_decode( $user );

            if ( ! empty( $user->user_id ) ) {
                $expand_users[] = [
                    'id'           => $user->id,
                    'display_name' => $user->display_name,
                    'avatar_url'   => get_avatar_url( $user->id )
                ];
            }
        }

        return $expand_users;
    }

    public function get_resource( WP_REST_Request $request ) {
        global $wpdb;

        $project_id      = $request->get_param( 'project_id' );
        $start           = $request->get_param( 'start' );
        $end             = $request->get_param( 'end' );
        $users           = $request->get_param( 'users' );
        $tb_users        = $wpdb->base_prefix . 'users';
        $tb_user_meta    = $wpdb->base_prefix . 'usermeta';
        $tb_role_user    = pm_tb_prefix() . 'pm_role_user';
        $current_user_id = get_current_user_id();
        $project_ids     = [];
        $where           = '';
        $return_users    = [];

        //$users = [1,2,9];
        //$project_id = 4;

        // IF empty project id
        if ( empty( $project_id ) && ! pm_has_manage_capability() ) {
            $project_query = $wpdb->prepare( "SELECT DISTINCT project_id FROM $tb_role_user WHERE user_id=%d", $current_user_id );

            $project_ids = $wpdb->get_results( $project_query );
            $project_ids = wp_list_pluck( $project_ids, 'project_id' );
        }

        if ( empty( $project_id ) && pm_has_manage_capability() ) {
            $project_query = $wpdb->prepare( "SELECT DISTINCT project_id FROM $tb_role_user WHERE 1=%s", 1 );

            $project_ids = $wpdb->get_results( $project_query );
            $project_ids = wp_list_pluck( $project_ids, 'project_id' );
        }

        // IF project id in array
        if ( ! empty( $project_id ) && is_array( $project_id ) ) {
            $project_ids = $project_id;
        }

        // IF project id in integar
        if ( ! empty( $project_id ) && ! is_array( $project_id ) ) {
            $project_ids = [$project_id];
        }

        if ( ! empty( $users ) && is_array( $users ) ) {
            $find_users = implode( ',', $users );

            $where .= " AND rousr.user_id IN ($find_users)";
        }

        if ( ! empty( $users ) && !is_array( $users ) ) {
            $find_users = implode( ',', [$users] );

            $where .= " AND rousr.user_id IN ($find_users)";
        }

        $project_ids = implode( ',', $project_ids );
        $project_ids = empty( $project_ids ) ? 0 : $project_ids;

        if ( is_multisite() ) {
            $meta_key = pm_user_meta_key();

            $query = "SELECT DISTINCT usr.ID, usr.display_name, rousr.role_id
                FROM $tb_users AS usr
                LEFT JOIN $tb_role_user AS rousr ON usr.ID=rousr.user_id
                LEFT JOIN $tb_user_meta as umeta ON umeta.user_id = usr.ID
                WHERE 1=1
                AND rousr.project_id IN ($project_ids)
                AND umeta.meta_key='$meta_key'
                $where";

        } else {

            $query = "SELECT DISTINCT usr.ID, usr.display_name, rousr.role_id
                FROM $tb_users AS usr
                LEFT JOIN $tb_role_user AS rousr ON usr.ID=rousr.user_id
                WHERE 1=1
                AND rousr.project_id IN ($project_ids)
                $where";
        }

        $users = $wpdb->get_results( $query );

        foreach ( $users as $key => $user ) {
            $return_users[] = [
                'id'         => $user->ID,
                'avatar_url' => get_avatar_url( $user->ID ),
                'title'      => $user->display_name,
                'role_id'    => $user->role_id
            ];
        }

        wp_send_json_success($return_users);
    }
}

