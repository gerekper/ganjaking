<?php
namespace WeDevs\PM_Pro\Reports\Controllers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Project\Transformers\Project_Transformer;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Task\Transformers\Task_Transformer;
use WP_REST_Request;
use \WeDevs\ORM\Eloquent\Facades\DB;
use WeDevs\PM_Pro\Modules\Invoice\core\PDF\PDF;
use WeDevs\PM\Milestone\Models\Milestone;

class Reports_Controller {

    use Transformer_Manager, Request_Filter;

    public function task_reports( WP_REST_Request $request ) {

        global $wpdb;
        $per_page   = $request->get_param('per_page');
        $page       = $request->get_param('page');
        $status     = intval( $request->get_param('status') );
        $category   = $request->get_param('category');
        $project_id = intval( $request->get_param('project_id') );
        $user_id    = intval( $request->get_param('user_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = $per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        $estimation = 0;
        $task_count = 0;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $projects = Project::with([
            'task_lists' => function ($task_list) use ($start_date, $due_date, $user_id, &$estimation, &$task_count, $status) {
                $task_list->with([
                    'tasks' => function ($task) use ($start_date, $due_date, $user_id, &$estimation, &$task_count, $status) {

                        if ( !empty( $user_id ) ) {
                            $task = $task->whereHas('assignees', function ($q) use ($user_id) {
                                $q->where('assigned_to', $user_id);
                            });
                        }

                        $task = $task->where('parent_id', 0)
                            ->where('start_at', '>=', $start_date)
                            ->where('due_date', '<=', $due_date)
                            ->where('status', $status)
                            ->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );

                        $estimation = $estimation + (int) $task->sum('estimation');
                        $task_count = $task_count + (int) $task->count();

                    }])
                    ->whereHas('tasks', function ($query) use ($start_date, $due_date, $user_id, $status) {

                        if ( !empty($user_id) ) {
                            $query = $query->whereHas('assignees', function ($q) use ($user_id) {
                                $q->where('assigned_to', $user_id);
                            });
                        }

                        $query->where('parent_id', 0)
                            ->where('start_at', '>=', $start_date)
                            ->where('due_date', '<=', $due_date)
                            ->where('status', $status);

                    });
            },
        ])

        ->whereHas('tasks', function ($query) use ($start_date, $due_date, $user_id, $status) {

            if ( !empty($user_id) ) {
                $query = $query->whereHas('assignees', function ($q) use ($user_id) {
                    $q->where('assigned_to', $user_id);
                });
            }

            $query->where('parent_id', 0)
                ->where('start_at', '>=', $start_date)
                ->where('due_date', '<=', $due_date)
                ->where('status', $status);

        })

        ->orderBy('created_at', 'DESC');

        if (!empty($project_id) && $project_id != -1 && $project_id != 'false' ) {
            $projects = $projects->where('id', $project_id);
        }

        if ($per_page == '-1') {
            $per_page = $projects->count();
        }
        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees'];
        });
        //pmpr($projects->groupby('sdfasd')->get()->toArray()); die();
        $projects_paginate = $projects->paginate($per_page);

        $project_collection = $projects_paginate->getCollection();
        $resource = new Collection($project_collection, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => $projects->count(),
            'total_result' => (int) $task_count,
            'estimation' => (int) $estimation,
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($projects_paginate));

        return $this->get_response($resource);
    }

    public function overdue_tasks( WP_REST_Request $request ) {

    }

    public function overdue_tasks_csv( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $query_pj_id = $request->get_param('project_id');
        $csv         = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $csv['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total   = $results['meta']['total_tasks'];
        $user_id = intval( $request->get_param('users') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('due_date_start') ) ? $start_date : $request->get_param('due_date_start');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( (int) $query_pj_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $query_pj_id] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        fputcsv( $output, [$date] );
        fputcsv( $output, ['',''] );
        fputcsv( $output, ['Project', 'Co Worker', 'Total Result'] );
        fputcsv( $output, [$all_project, $all_user, $total] );
        fputcsv( $output, ['',''] );

        foreach ( $csv['data'] as $key => $result ) {

            fputcsv( $output, [ __( 'Project title: ', 'pm-pro'  ) . $result['title'] ] );

            foreach ( $result['task_lists']['data'] as $key => $list_results ) {

                fputcsv( $output, [__( 'Task List title: ', 'pm-pro'  ) . $list_results['title']] );
                fputcsv( $output, ['',''] );
                fputcsv( $output, [__('Tasks', 'pm-pro'), __('Due Date','pm-pro'), __('Created At','pm-pro'), __('Created By','pm-pro')] );

                foreach ( $list_results['tasks']['data'] as $key => $task ) {
                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $due_date = $task['due_date']['date'];
                    $created_at = $task['created_at']['date'];
                    $creator = $task['creator']['data']['display_name'];

                    fputcsv( $output, [$task['title'], $due_date, $created_at, $creator] );
                }

                fputcsv( $output, ['',''] );
            }
        }

        fclose($output);

        exit();
    }

    public function overdue_tasks_PDF( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $query_pj_id = $request->get_param('project_id');
        $pdf         = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $pdf['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total      = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('users') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('due_date_start') ) ? $start_date : $request->get_param('due_date_start');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( (int) $query_pj_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $query_pj_id] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        ob_start();
            require_once pm_pro_config('define.view_path') . '/reports/overdue-tasks.php';
        $output = ob_get_clean();

        return self::generate_PDF( $output );
    }

    public static function generate_PDF ( $output, $options = '' ) {
        if ( class_exists( 'WeDevs\PM_Pro\Modules\Invoice\core\PDF\PDF' ) ) {
            return PDF::generator( $output, $options );
        }

        return false;
    }

    public function completed_tasks(WP_REST_Request $request) {

    }

    public function completed_tasks_csv( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $query_pj_id = $request->get_param('project_id');
        $csv         = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $csv['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total      = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('users') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('completed_at_start') ) ? $start_date : $request->get_param('completed_at_start');
        $due_date = empty( $request->get_param('completed_at') ) ? $end_date : $request->get_param('completed_at');

        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( (int) $query_pj_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $query_pj_id] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        fputcsv( $output, [$date] );
        fputcsv( $output, ['',''] );
        fputcsv( $output, ['Project', 'Co Worker', 'Total Result'] );
        fputcsv( $output, [$all_project, $all_user, $total] );
        fputcsv( $output, ['',''] );

        foreach ( $csv['data'] as $key => $result ) {

            fputcsv( $output, [ __( 'Project title: ', 'pm-pro'  ) . $result['title'] ] );

            foreach ( $result['task_lists']['data'] as $key => $list_results ) {

                fputcsv( $output, [__( 'Task List title: ', 'pm-pro'  ) . $list_results['title']] );
                fputcsv( $output, ['',''] );
                fputcsv( $output, [__('Tasks', 'pm-pro'), __('Due Date','pm-pro'), __('Created At','pm-pro'), __('Created By','pm-pro')] );

                foreach ( $list_results['tasks']['data'] as $key => $task ) {

                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $due_date = $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name'];

                    fputcsv( $output, [$task['title'], $due_date, $created_at, $creator] );
                }

                fputcsv( $output, ['',''] );
            }
        }

        fclose($output);

        exit();
    }

    // public function completed_tasks_PDF( WP_REST_Request $request ) {
    //     $user_id = $request->get_param('current_user_id');
    //     wp_set_current_user( $user_id );

    //     $results    = $this->task_reports( $request );
    //     $total      = $results['meta']['total_result'];
    //     $user_id    = intval( $request->get_param('user_id') );
    //     $project_id = intval( $request->get_param('project_id') );

    //     $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
    //     $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

    //     $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
    //     $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

    //     $date       = 'Date between ' . $start_date . ' to ' . $due_date;

    //     if ( empty( $project_id ) ) {
    //         $all_project = __('All Project', 'pm-pro');
    //     } else {
    //         $all_project = empty( $results['data'] ) ? '' : $results['data'][0]['title'];
    //     }

    //     if ( empty( $user_id ) ) {
    //         $all_user = __('All Coworker', 'pm-pro');
    //     } else {
    //         $user = get_user_by( 'id', $user_id );
    //         $all_user = $user->display_name;
    //     }

    //     ob_start();
    //         require_once pm_pro_config('define.view_path') . '/reports/completed-tasks.php';
    //     $output = ob_get_clean();

    //     return self::generate_PDF( $output );
    // }

    public function completed_tasks_PDF( WP_REST_Request $request ) {

        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $query_pj_id = $request->get_param('project_id');
        $pdf         = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $pdf['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total      = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('users') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('completed_at_start') ) ? $start_date : $request->get_param('completed_at_start');
        $due_date = empty( $request->get_param('completed_at') ) ? $end_date : $request->get_param('completed_at');

        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( (int) $query_pj_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $query_pj_id] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        ob_start();
            require_once pm_pro_config('define.view_path') . '/reports/completed-tasks.php';
        $output = ob_get_clean();

        return self::generate_PDF( $output );
    }

    public function user_activities( WP_REST_Request $request ) {
    }

    public function user_activities_csv( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $results    = $this->task_reports( $request );
        $total      = $results['meta']['total_result'];
        $user_id    = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date       = 'Date between ' . $start_date . ' to ' . $due_date;

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $all_project = empty( $results['data'] ) ? '' : $results['data'][0]['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        fputcsv( $output, [$date] );
        fputcsv( $output, ['',''] );
        fputcsv( $output, ['Project', 'Co Worker', 'Total Result'] );
        fputcsv( $output, [$all_project, $all_user, $total] );
        fputcsv( $output, ['',''] );

        foreach ( $results['data'] as $key => $result ) {

            fputcsv( $output, [ __( 'Project title: ', 'pm-pro'  ) . $result['title'] ] );

            foreach ( $result['task_lists']['data'] as $key => $list_results ) {

                fputcsv( $output, [__( 'Task List title: ', 'pm-pro'  ) . $list_results['title']] );
                fputcsv( $output, ['',''] );
                fputcsv( $output, [__('Tasks', 'pm-pro'), __('Date Between','pm-pro'), __('Created At','pm-pro'), __('Tracked Time','pm-pro'), __('Created By','pm-pro')] );

                foreach ( $list_results['tasks']['data'] as $key => $task ) {

                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name'];

                    if ( !empty( $task['time'] && !empty( $task['time']['meta']['totalTime'] ) ) ) {
                        $time = $task['time']['meta']['totalTime'];
                        $trackd_time = $task['time']['meta']['totalTime']['hour'] . ':' . $task['time']['meta']['totalTime']['minute'] . ':' . $task['time']['meta']['totalTime']['second'];
                    } else {
                        $trackd_time = '00:00:00';
                    }

                    fputcsv( $output, [$task['title'], $date_between, $created_at, $trackd_time, $creator] );
                }

                fputcsv( $output, ['',''] );
            }
        }

        fclose($output);

        exit();
    }

    public function user_activities_PDF( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $pdf = [];

        $results = pm_get_activities( $request->get_params() );

        foreach ( $results['data'] as $key => $result ) {
            $pdf[$result['committed_at']['date']][] = $result;
        }

        pmpr($pdf); die();

        $total      = $results['meta']['pagination']['total'];
        $user_id    = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date       = 'Date between ' . $start_date . ' to ' . $due_date;

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $all_project = empty( $results['data'] ) ? '' : $results['data'][0]['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        ob_start();
            require_once pm_pro_config('define.view_path') . '/reports/user-activity-tasks.php';
        $output = ob_get_clean();

        return self::generate_PDF( $output );

    }

    public function user_tasks(WP_REST_Request $request) {
        $per_page   = $request->get_param('per_page');
        $page       = $request->get_param('page');
        $status     = $request->get_param('status');
        $category   = $request->get_param('category');
        $project_id = intval( $request->get_param('project_id') );
        $user_id    = intval( $request->get_param('user_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $end_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $task_view  = $request->get_param('task_view');

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = 15; //$per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $user_id = !empty($user_id) ? $user_id : get_current_user_id();

        $task = Task::whereHas('assignees', function ($q) use ($user_id) {
            $q->where('assigned_to', $user_id);
        })
            ->where('parent_id', 0);

        if (!empty($project_id) && $project_id != -1) {
            $task = $task->where(pm_tb_prefix() . 'pm_tasks.project_id', $project_id);
        }

        if (!empty($start_date) && pm_get_setting('task_start_field') === "true") {

            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = empty($end_date) ? data('Y-m-d', strtotime(current_time('mysql'))) : date('Y-m-d', strtotime($end_date));

            $task = $task->whereBetween('start_at', [$start_date, $end_date])->orderBy('start_at');

        } else if (!empty($end_date)) {

            $end_date = date('Y-m-d', strtotime($end_date));
            $start_date = empty($start_date) ? data('Y-m-d', strtotime(current_time('mysql'))) : date('Y-m-d', strtotime($start_date));

            $task = $task->whereBetween('due_date', [$start_date, $end_date])->orderBy('due_date');

        }
        // $task = $task->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );
        $task_paginate = $task->paginate($per_page, ['*']);
        $tasks_collectoin = $task_paginate->getCollection();

        $tasks = $tasks_collectoin->toArray();
        $task_ids = wp_list_pluck($tasks, 'id');
        $estimation = wp_list_pluck($tasks, 'estimation');
        $project_ids = wp_list_pluck($tasks, 'project_id');

        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees', 'completer'];
        });

        if ($task_view) {

            $resource = new Collection($tasks_collectoin, new Task_Transformer);

            $resource->setMeta([
                'total_projects' => count($project_ids),
                'total_result' => count($task_ids),
                'estimation' => array_sum($estimation),
            ]);

            $resource->setPaginator(new IlluminatePaginatorAdapter($task_paginate));

            return $this->get_response($resource);

        }

        $projects = Project::with([
            'task_lists' => function ($task_list) use ($task_ids) {
                $task_list->with([
                    'tasks' => function ($tasks_query) use ($task_ids) {
                        $tasks_query->whereIn(pm_tb_prefix() . 'pm_tasks.id', $task_ids)
                            ->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );
                    },
                ])
                    ->whereHas('tasks', function ($tasks_query) use ($task_ids) {
                        $tasks_query->whereIn(pm_tb_prefix() . 'pm_tasks.id', $task_ids);
                    });
            },
        ])
        ->whereHas('tasks', function ($tasks_query) use ($task_ids) {
            $tasks_query->whereIn(pm_tb_prefix() . 'pm_tasks.id', $task_ids);
        })
        ->get();

        if (!empty($project_id) && $project_id != -1) {
            $projects = $projects->where('id', $project_id);
        }

        $resource = new Collection($projects, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => count($project_ids),
            'total_result' => count($task_ids),
            'estimation' => array_sum($estimation),
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($task_paginate));

        return $this->get_response($resource);
    }

    public function project_tasks( WP_REST_Request $request ) {
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $task_status = $request->get_param('task_status');
        $category = $request->get_param('category');
        $project_id = $request->get_param('project_id');

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = $per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        $estimation = 0;
        $task_count = 0;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $projects = Project::with([
            'task_lists' => function ($task_list) use ($task_status, &$estimation, &$task_count) {
                $task_list->with([
                    'tasks' => function ($tasks) use ($task_status, &$estimation, &$task_count) {
                        $tasks = $tasks->where('parent_id', 0);

                        if (isset($task_status)) {
                            $tasks = $tasks->where('status', $task_status);
                        }

                        $tasks = $tasks->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );

                        $estimation = $estimation + (int) $tasks->sum('estimation');
                        $task_count = $task_count + (int) $tasks->count();
                    },
                ])
                    ->whereHas('tasks', function ($tasks) use ($task_status) {
                        $tasks = $tasks->where('parent_id', 0);

                        if (isset($task_status)) {
                            $tasks = $tasks->where('status', $task_status);
                        }
                    });
            },
        ])
            ->whereHas('tasks', function ($tasks) use ($task_status) {
                $tasks = $tasks->where('parent_id', 0);

                if (isset($task_status)) {
                    $tasks = $tasks->where('status', $task_status);
                }
            });

        if (!empty($project_id) && $project_id != -1) {
            $projects = $projects->where('id', $project_id);
        }

        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees'];
        });

        if ($per_page == -1) {
            $per_page = $projects->count();
        }

        $projects_paginate = $projects->paginate($per_page);

        $project_collection = $projects_paginate->getCollection();
        $resource = new Collection($project_collection, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => $projects->count(),
            'total_result' => (int) $task_count,
            'estimation' => (int) $estimation,
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($projects_paginate));

        return $this->get_response($resource);

    }

    public function milestone_tasks( WP_REST_Request $request ) {
        $per_page     = $request->get_param('per_page');
        $page         = $request->get_param('page');
        $project_id   = $request->get_param('project_id');
        $milestone_id = $request->get_param('milestone_id');

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        if (empty($milestone_id)) {
            return $this->get_response(null, [
                'message' => pm_get_text('success_messages.no_element'),
            ]);
        }

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = $per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        $estimation = 0;
        $task_count = 0;

        $milestone = Milestone::find( $milestone_id );

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $projects = Project::with([
            'task_lists' => function ($task_list) use ($milestone_id, $start_date, $due_date, &$estimation, &$task_count) {
                $task_list->where('status', '!=', '0')
                    ->with([
                        'tasks' => function ($tasks) use (&$estimation, $start_date, $due_date, &$task_count) {
                            $tasks = $tasks->where('parent_id', 0 )
                                ->where( 'start_at', '>=', $start_date )
                                ->where( 'due_date', '<=', $due_date )
                                ->with( 'creator', 'assignees' );

                            $tasks = $tasks->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );
                            $estimation = $estimation + (int) $tasks->sum('estimation');
                            $task_count = $task_count + (int) $tasks->count();
                    },
                ])
                ->whereExists( function ( $query ) use ( $milestone_id ) {
                    $query->select( DB::raw( 1 ) )
                        ->from(pm_tb_prefix() . 'pm_boardables')
                        ->whereRaw("board_id=" . $milestone_id . "
                        and board_type='milestone'
                        and boardable_type = 'task_list'
                        and boardable_id=" . pm_tb_prefix() . "pm_boards.id"
                        );
                });
            },
        ])
        ->where('id', $project_id);

        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees'];
        });

        if ($per_page == -1) {
            $per_page = $projects->count();
        }

        $projects_paginate = $projects->paginate($per_page);

        $project_collection = $projects_paginate->getCollection();
        $resource = new Collection($project_collection, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => $projects->count(),
            'total_result' => (int) $task_count,
            'estimation' => (int) $estimation,
            'milestone'  => $milestone->toArray()
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($projects_paginate));

        return $this->get_response($resource);

    }

    public function milestone_tasks_CSV( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $csv     = [ 'data' => [] ];
        $milestone = [];

        if ( count( $results['data'] ) ) {
            $milestone = $results['data'][0]['milestone']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $csv['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total   = $results['meta']['total_tasks'];
        $user_id = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_at') ) ? $start_date : $request->get_param('start_at');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $all_project = empty( $results['data'] ) ? '' : $results['data'][0]['title'];
        }

        $milestone_title = $milestone['title'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        fputcsv( $output, [$date] );
        fputcsv( $output, ['',''] );
        fputcsv( $output, ['Project', 'Milestone', 'Total Result'] );
        fputcsv( $output, [$all_project, $milestone_title, $total] );
        fputcsv( $output, ['',''] );

        foreach ( $csv['data'] as $key => $result ) {

            fputcsv( $output, [ __( 'Project title: ', 'pm-pro'  ) . $result['title'] ] );

            foreach ( $result['task_lists']['data'] as $key => $list_results ) {

                fputcsv( $output, [__( 'Task List title: ', 'pm-pro'  ) . $list_results['title']] );
                fputcsv( $output, ['',''] );
                fputcsv( $output, [__('Tasks', 'pm-pro'), __('Due Date','pm-pro'), __('Created At','pm-pro'), __('Created By','pm-pro')] );

                foreach ( $list_results['tasks']['data'] as $key => $task ) {
                    $date_between = $task['start_at']['date'] . __(' to ', 'pm-pro') . $task['due_date']['date'];
                    $due_date     = $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name'];

                    fputcsv( $output, [$task['title'], $due_date, $created_at, $creator] );
                }

                fputcsv( $output, ['',''] );
            }
        }

        fclose($output);

        exit();
    }

    public function milestone_tasks_PDF( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );

        $pdf     = [ 'data' => [] ];
        $milestone = [];

        if ( count( $results['data'] ) ) {
            $milestone = $results['data'][0]['milestone']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $pdf['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total      = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_at') ) ? $start_date : $request->get_param('start_at');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');


        $date = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $all_project = empty( $results['data'] ) ? '' : $results['data'][0]['title'];
        }

        $milestone_title = isset( $milestone['title'] ) ? $milestone['title'] : '';

        ob_start();
            require_once pm_pro_config('define.view_path') . '/reports/milestone-tasks.php';
        $output = ob_get_clean();

        return self::generate_PDF( $output );

    }

    public function unassigned_tasks( WP_REST_Request $request ) {

        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $project_id = $request->get_param('project_id');

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_date') ) ? $start_date : $request->get_param('start_date');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = $per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        $estimation = 0;
        $task_count = 0;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $projects = Project::with([
            'task_lists' => function ($task_list) use ( $start_date, $due_date, &$estimation, &$task_count ) {
                $task_list->with([
                    'tasks' => function ($tasks) use ( $start_date, $due_date, &$estimation, &$task_count ) {
                        $tasks = $tasks->where( 'start_at', '>=', $start_date )
                            ->where( 'due_date', '<=', $due_date )
                            ->whereDoesntHave('assignees');

                        $tasks = $tasks->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );
                        $estimation = $estimation + (int) $tasks->sum('estimation');
                        $task_count = $task_count + (int) $tasks->count();
                    },
                ])
                ->whereHas('tasks', function ($tasks) use ( $start_date, $due_date ) {
                    $tasks = $tasks->where( 'start_at', '>=', $start_date )
                            ->where( 'due_date', '<=', $due_date )
                            ->whereDoesntHave('assignees');
                });
            },
        ])
        ->whereHas('tasks', function ($tasks) {
            $tasks = $tasks->whereDoesntHave('assignees');
        });

        if (!empty($project_id) && $project_id != -1) {
            $projects = $projects->where('id', $project_id);
        }

        if ($per_page == -1) {
            $per_page = $projects->count();
        }

        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees'];
        });

        $projects_paginate = $projects->paginate($per_page);

        $project_collection = $projects_paginate->getCollection();
        $resource = new Collection($project_collection, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => $projects->count(),
            'total_result' => (int) $task_count,
            'estimation' => (int) $estimation,
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($projects_paginate));

        return $this->get_response($resource);

    }

    public function unassigned_CSV( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );
        $csv     = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $csv['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $csv['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total      = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_at') ) ? $start_date : $request->get_param('start_at');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date   = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $request->get_param('project_id')] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");

        fputcsv( $output, [$date] );
        fputcsv( $output, ['',''] );
        fputcsv( $output, ['Project', 'Total Result'] );
        fputcsv( $output, [$all_project, $total] );
        fputcsv( $output, ['',''] );

        foreach ( $csv['data'] as $key => $result ) {

            fputcsv( $output, [ __( 'Project title: ', 'pm-pro'  ) . $result['title'] ] );

            foreach ( $result['task_lists']['data'] as $key => $list_results ) {

                fputcsv( $output, [__( 'Task List title: ', 'pm-pro'  ) . $list_results['title']] );
                fputcsv( $output, [__('Tasks', 'pm-pro'), __('Due Date','pm-pro'), __('Created At','pm-pro'), __('Status','pm-pro'), __('Created By','pm-pro')] );

                foreach ( $list_results['tasks']['data'] as $key => $task ) {

                    $due_date     = $task['due_date']['date'];
                    $created_at   = $task['created_at']['date'];
                    $creator      = $task['creator']['data']['display_name'];
                    $status       = $task['status'];


                    fputcsv( $output, [$task['title'], $due_date, $created_at, $status, $creator] );
                }

                fputcsv( $output, ['',''] );
            }
        }

        fclose($output);

        exit();
    }

    public function unassigned_PDF( WP_REST_Request $request ) {
        $user_id = $request->get_param('current_user_id');
        wp_set_current_user( $user_id );

        $data = $request->get_params();

        if ( isset( $data['per_page'] ) ) {
            unset( $data['per_page'] );
        }

        $results = pm_get_tasks( $data );
        $pdf     = [ 'data' => [] ];

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $pdf['data'][$project_id] = (array) $result['project']['data'];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id] = [
                'title' => $result['task_list_title']
            ];
        }

        foreach ( $results['data'] as $key => $result ) {
            $project_id = $result['project_id'];
            $list_id    = $result['task_list_id'];
            $task_id    = $result['id'];

            $pdf['data'][$project_id]['task_lists']['data'][$list_id]['tasks']['data'][$task_id] = $result;
        }

        $total   = $results['meta']['total_tasks'];
        $user_id    = intval( $request->get_param('user_id') );
        $project_id = intval( $request->get_param('project_id') );

        $start_date = date( 'Y-m-01', strtotime( current_time('mysql') ) );
        $end_date   = date( 'Y-m-d', strtotime( current_time('mysql') ) );

        $start_date = empty( $request->get_param('start_at') ) ? $start_date : $request->get_param('start_at');
        $due_date = empty( $request->get_param('due_date') ) ? $end_date : $request->get_param('due_date');

        $date       = 'Date between ' . pm_date_format( $start_date ) . ' to ' . pm_date_format( $due_date );

        if ( empty( $project_id ) ) {
            $all_project = __('All Project', 'pm-pro');
        } else {
            $project = pm_get_projects( ['id' => $request->get_param('project_id')] );
            $all_project = $project['data']['title'];
        }

        if ( empty( $user_id ) ) {
            $all_user = __('All Coworker', 'pm-pro');
        } else {
            $user = get_user_by( 'id', $user_id );
            $all_user = $user->display_name;
        }

        ob_start();
            require_once pm_pro_config('define.view_path') . '/reports/unassigned-tasks.php';
        $output = ob_get_clean();

        return self::generate_PDF( $output );
    }

    public function advance_tasks(WP_REST_Request $request)
    {
        $filters = $request->get_param('filters');
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $csv = $request->get_param('csv');

        $per_page_from_settings = pm_get_setting('project_per_page');
        $per_page_from_settings = $per_page_from_settings ? $per_page_from_settings : 15;

        $per_page = $per_page ? $per_page : $per_page_from_settings;
        $page = $page ? $page : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $filters     = collect($filters);
        $user_ids    = $filters->where('skey', 'assigned')->values()->get(0)['svalue'];
        $project_ids = $filters->where('skey', 'project')->values()->get(0)['svalue'];
        $status      = $filters->where('skey', 'status')->values()->get(0)['svalue'];
        $payable     = $filters->where('skey', 'payable')->values()->get(0)['svalue'];
        $time        = $filters->where('skey', 'time')->values()->get(0)['svalue'];
        $start_at    = isset($time['start_at']) ? date('Y-m-d', strtotime($time['start_at'])) : '';
        $due_date    = isset($time['due_date']) ? date('Y-m-d', strtotime($time['due_date'])) : '';

        $projects = Project::with(
            [
                'task_lists' => function ($task_lists) use ($user_ids, $status, $payable, $time, $start_at, $due_date, &$estimation, &$task_count) {
                    $task_lists->with(
                        [
                            'tasks' => function ($tasks) use ($user_ids, $status, $payable, $time, $start_at, $due_date, &$estimation, &$task_count) {
                                $tasks = $tasks->where('parent_id', 0)->with('creator', 'assignees');

                                if ($user_ids) {
                                    $tasks = $tasks->whereHas('assignees', function ($q) use ($user_ids) {
                                        $q->whereIn('assigned_to', $user_ids);
                                    });
                                }

                                if (isset($status)) {
                                    $tasks = $tasks->where('status', $status);
                                }

                                if (!empty($payable)) {
                                    $tasks = $tasks->where('payable', $payable);
                                }

                                if (!empty($start_at)) {
                                    $tasks = $tasks->where('start_at', '>=', $start_at);
                                }

                                if (!empty($due_date)) {
                                    $tasks = $tasks->where('due_date', '<=', $due_date);
                                }
                                $tasks = $tasks->orderBy( pm_tb_prefix() . 'pm_boardables.order', 'ASC' );

                                $estimation = $estimation + (int) $tasks->sum('estimation');
                                $task_count = $task_count + (int) $tasks->count();

                            },
                        ]
                    );
                },
            ]
        );

        if ($project_ids) {
            $projects = $projects->whereIn('id', $project_ids);
        }

        if ($per_page == -1) {
            $per_page = $projects->count();
        }

        add_filter('pm_project_transformer_default_includes', function ($defaut) {
            return ['task_lists'];
        });
        add_filter('pm_task_list_transformer_default_includes', function ($defaut) {
            return ['tasks'];
        });
        add_filter('pm_task_transformer_default_includes', function ($default) {
            return ['creator', 'assignees'];
        });

        $projects_paginate = $projects->paginate($per_page);

        $project_collection = $projects_paginate->getCollection();
        $resource = new Collection($project_collection, new Project_Transformer);

        $resource->setMeta([
            'total_projects' => $projects->count(),
            'total_result' => (int) $task_count,
            'estimation' => (int) $estimation,
        ]);

        $resource->setPaginator(new IlluminatePaginatorAdapter($projects_paginate));

        $resource = $this->get_response( $resource );

        if ($csv == 'true' || $csv === true) {
            $this->advance_tasks_csv( $resource );
        }

        return $resource;
    }

    function advance_tasks_csv( $response ) {

        header('Content-Type: html/csv');
        header('Content-Disposition: attachment; filename=data.csv');
        $output = fopen("php://output", "w");


        foreach ( $response['data'] as $key => $data ) {
            fputcsv( $output, ['Project: ' . $data['title']] );
            fputcsv( $output, ['', ''] );

            foreach ( $data['task_lists']['data'] as $list_key => $list ) {

                if ( empty( $list['tasks']['data'] ) ) {
                    continue;
                }
                fputcsv( $output, ['Task List: ' . $list['title']] );

                fputcsv( $output, ['Task', 'Assigned to', 'Assigned Date', 'Due Date', 'Status'] );

                foreach ( $list['tasks']['data'] as $tk_key => $task ) {
                    $title = $task['title'];
                    $assigned_to = wp_list_pluck( $task['assignees']['data'], 'display_name');
                    $assigned_to = implode(', ', $assigned_to);
                    $assign_date = '';

                    if ( empty( $task['start_at'] ) ) {
                        $assign_date = $task['created_at']['date'];
                    } else {
                        $assign_date = $task['start_at']['date'];
                    }

                    if ( !empty( $task['due_date'] ) ) {
                        $due_date = $task['due_date']['date'];
                    }
                    $status = $task['status'];

                    fputcsv( $output, [$title, $assigned_to, $assign_date, $due_date, $status] );
                }
            }

            fputcsv( $output, ['', ''] );
        }

        fclose($output);

        exit();
    }

    function test_get_all_project() {
        $sql_project = "SELECT pj.id as project_id, pj.title as project_title, pj.created_at as created_at
            from pm_pm_projects as pj

            WHERE
             (pj.created_at>='2013-01-18 23:59:59' AND pj.created_at<='2018-10-10 23:59:59')";

        $sql_task = "SELECT tk.id as task_id, tk.title as task_title, tk.created_at as created_at,
            GROUP_CONCAT(
                DISTINCT
                CONCAT(
                    '{',
                        '\"', 'meta_key', '\"', ':' , '\"', IFNULL(mt.meta_key, '') , '\"', ',',
                        '\"', 'meta_value', '\"', ':' , '\"', IFNULL(mt.meta_value, '') , '\"'
                    ,'}'
                ) SEPARATOR '|'
            ) as task_meta
            from pm_pm_tasks as tk

            LEFT JOIN pm_pm_meta as mt ON tk.id=mt.entity_id AND mt.entity_type='task'
            LEFT JOIN pm_pm_boardables as bl ON tk.id=bl.boardable_id

            WHERE
                tk.project_id IN (1,2,5,6,9,74)
                AND
                (tk.created_at>='2013-01-18 23:59:59' AND tk.created_at<='2019-10-10 23:59:59')
                AND
                bl.boardable_type = 'task'
                GROUP BY (tk.id)";

        $sql_user = "SELECT DISTINCT usr.ID as user_id, usr.user_email as user_email, usr.display_name as display_name

            from pm_users as usr

            LEFT JOIN pm_pm_assignees as sg ON usr.ID=sg.assigned_to

            WHERE
                sg.task_id IN (1,2,5,6,9,74)";
    }
}
