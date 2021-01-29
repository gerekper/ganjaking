<?php
use League\Fractal;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM_Pro\Core\Textdomain\Textdomain;
use WeDevs\PM\Core\WP\Register_Scripts;
use WeDevs\PM\Core\WP\Enqueue_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Pro_Enqueue_Scripts;
use WeDevs\PM_Pro\Core\WP\Register_Scripts as Pro_Register_Scripts;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM_Pro\Integrations\Models\Integrations ;

function pm_pro_get_text( $key ) {
    return Textdomain::get_text( $key);
}

function pm_pro_get_logo() {
    $logo_id = pm_get_setting('logo');
    if ( $logo_id == 0 ) {
        return null;
    }
    return WeDevs\PM\Core\File_System\File_System::get_file($logo_id);
}

function save_discuss_privacy_field( $discuss, $request ) {
    if ( !isset( $request['privacy'] ) ){
        return ;
    }
    $meta = Meta::firstOrCreate([
        'entity_id'   => $discuss->id,
        'entity_type' => 'discussion_board',
        'meta_key'    => 'privacy',
        'project_id'  => $request['project_id'],
    ]);
    $meta->meta_value  = $request['privacy'] == 'true' ? 1 : 0;
    if( $meta->save() ) {
        return $meta;
    }
}

function save_task_list_privacy_field( $taskList, $request ) {
    if ( !isset( $request['privacy'] ) ){
        return ;
    }
    $meta = Meta::firstOrCreate([
        'entity_id'   => $taskList->id,
        'entity_type' => 'task_list',
        'meta_key'    => 'privacy',
        'project_id'  => $request['project_id'],
    ]);
    $meta->meta_value  = $request['privacy'] == 'true' ? 1 : 0;
    if( $meta->save() ) {
        return $meta;
    }
}

function save_task_privacy_field( $task, $request ) {
    if ( !isset( $request['privacy'] ) ){
        return ;
    }
    $meta = Meta::firstOrCreate([
        'entity_id'   => $task->id,
        'entity_type' => 'task',
        'meta_key'    => 'privacy',
        'project_id'  => $request['project_id'],
    ]);
    $meta->meta_value  = $request['privacy'] == 'true' ? 1 : 0;
    if( $meta->save() ) {
        return $meta;
    }
}

function save_task_recurrence_data( $task, $request ) {
    if ( !isset( $request['recurrence_data'] ) ){
        return ;
    }

    $meta = Meta::firstOrCreate([
        'entity_id'   => $task->id,
        'entity_type' => 'task',
        'meta_key'    => 'recurrence',
        'project_id'  => $request['project_id'],
    ]);

    $meta->meta_value  = serialize( $request['recurrence_data'] );

    if( $meta->save() ) {
        return $meta;
    }
}

function save_milestone_privacy_field( $milestone, $request ) {
    if ( !isset( $request['privacy'] ) ){
        return ;
    }
    $meta = Meta::firstOrCreate([
        'entity_id'   => $milestone->id,
        'entity_type' => 'milestone',
        'meta_key'    => 'privacy',
        'project_id'  => $request['project_id'],
    ]);
    $meta->meta_value  = $request['privacy'] == 'true' ? 1 : 0;
    if( $meta->save() ) {
        return $meta;
    }
}

function pm_get_project_capabilities( $transformer, $project ) {
    $transformer['capabilities'] = pm_pro_get_project_capabilities( $project->id );
    $transformer['slack'] = pm_get_setting( 'slack', $project->id );
    $transformer['git_bit'] = pm_get_setting( 'git_bit', $project->id );
    $transformer['git_bit_hash'] = get_option('projectId_git_bit_hash_'.$project->id);

    return $transformer;
}


function pm_check_discuss_privacy( $discuss, $project_id ) {
    if ( ! pm_user_can( 'view_private_message', $project_id ) ) {
        $discuss = $discuss->where( function($q) {
            $q->doesntHave( 'metas', 'and', function ($query) {
                $query->where( 'meta_key', '=', 'privacy' )
                    ->where( 'meta_value', '!=', 0 );
            });
            $q->orWhere( pm_tb_prefix().'pm_boards.created_by', '=', get_current_user_id() );
        } );
    }

    return $discuss;
}

function pm_pro_check_task_list_privacy_query( $task_list, $project_id ) {
    $tb_tasks     = pm_tb_prefix() . 'pm_tasks';
    $tb_meta      = pm_tb_prefix() . 'pm_meta';
    $current_user = get_current_user_id();

    $task_list->selectRaw(
        "GROUP_CONCAT(
            DISTINCT
            CONCAT(
                '{',
                    '\"', 'meta_key', '\"', ':' , '\"', IFNULL($tb_meta.meta_key, '') , '\"', ',',
                    '\"', 'meta_value', '\"', ':' , '\"', IFNULL($tb_meta.meta_value, '') , '\"'
                ,'}'
            ) SEPARATOR '|'
        ) as meta"
    );

    if ( ! pm_user_can( 'view_private_list', $project_id ) ) {

        $task_list = $task_list->where( function( $q ) use( $current_user ) {
            $q->where( function( $qu ) use( $current_user ) {
                $qu->where( function( $q3 ) use( $current_user ) {
                    $q3->where( pm_tb_prefix() .'pm_meta.meta_key', '=', 'privacy' );
                    $q3->where( pm_tb_prefix() .'pm_meta.meta_value', '!=', 1 );
                });
               $qu->orwhere( pm_tb_prefix() .'pm_boards.created_by', '=',  $current_user );
            });
            $q->orWhereNull( pm_tb_prefix() .'pm_meta.meta_key' );
            $q->orWhere( pm_tb_prefix() .'pm_meta.meta_key', '!=', 'privacy' );
        });

    }

    return $task_list;
}

function pm_pro_list_tasks_filter_query( $query, $args ) {
    $meta = pm_tb_prefix() . 'pm_meta';
    $task = pm_tb_prefix() . 'pm_tasks';
    $project_id = empty( $args['project_id'] ) ? 0 : intval( $args['project_id'] );

    $query->selectRaw(
        "GROUP_CONCAT(
            DISTINCT
            CONCAT(
                '{',
                    '\"', 'meta_key', '\"', ':' , '\"', IFNULL($meta.meta_key, '') , '\"', ',',
                    '\"', 'meta_value', '\"', ':' , '\"', IFNULL($meta.meta_value, '') , '\"'
                ,'}'
            ) SEPARATOR '|'
        ) as meta"
    )
    ->leftJoin( $meta, function( $join ) use($task, $meta) {
        $join->on( $task . '.id', '=', $meta . '.entity_id' )
            ->where( $meta . '.entity_type', '=', 'task' );

    });

    return $query;
}

function pm_pro_set_list_task_data( $task, $item ) {
    if ( ! empty( $item->meta ) ) {
        $metas = explode( '|', $item->meta );

        foreach ( $metas as $key => $meta ) {
            $meta = str_replace('`', '"', $meta);
            $meta = json_decode( $meta );

            if ( empty( $meta->meta_key ) ) continue;

            $task['meta'][$meta->meta_key] = $meta->meta_value;
        }
    }

    return $task;
}

function pm_pro_set_list_data( $list, $item ) {
    if ( ! empty( $item->meta ) ) {
        $metas = explode( '|', $item->meta );

        foreach ( $metas as $key => $meta ) {
            $meta = str_replace('`', '"', $meta);
            $meta = json_decode( $meta );

            if ( empty( $meta->meta_key ) ) continue;

            $list['meta'][$meta->meta_key] = $meta->meta_value;
        }
    }

    return $list;
}

function pm_check_task_list_privacy( $task_list, $project_id ) {
    if ( ! pm_user_can( 'view_private_list', $project_id ) ) {

        $task_list = $task_list->doesntHave( 'metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'privacy' )
                ->where( 'meta_value', '!=', 0 );
        });
    }
    return $task_list;
}

function pm_pro_task_query_join($task, $project_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'pm_meta';
    if ( ! pm_user_can( 'view_private_task', $project_id ) ) {
        $task = "LEFT JOIN $table as mt on itasks.id = mt.`entity_id` AND `mt`.`entity_type` = 'task'";
    }
    return $task;
}

function pm_pro_task_query_where($task, $project_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'pm_meta';
    $current_user = get_current_user_id();
    if ( ! pm_user_can( 'view_private_task', $project_id ) ) {
        $task = " AND ( (
            (`mt`.`meta_key` = 'privacy' AND `mt`.`meta_value` != 1)
            OR
            (`mt`.`meta_key` != 'privacy')
            OR
            (`mt`.`meta_key` is null)
        ) or itasks.created_by = $current_user )";
    }
    return $task;
}

function pm_check_task_privacy( $task, $project_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'pm_meta';
    if ( ! pm_user_can( 'view_private_task', $project_id ) ) {

        $task = $task->doesntHave('metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'privacy' )
                ->where( 'meta_value', '!=', 0 );
        })->doesntHave( 'task_lists.metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'privacy' )
                    ->where( 'meta_value', '!=', 0 );

        } );
    }
    return $task;
}

function pm_check_task_filter_privacy( $task, $project_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'pm_meta';
    if ( ! pm_user_can( 'view_private_task', $project_id ) ) {

        $task = $task->doesntHave('metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'privacy' )
                ->where( 'meta_value', '!=', 0 );
        });
    }
    return $task;
}

function pm_check_task_list_recurrence( $task, $project_id ) {  //for re...task

    if ( ! pm_user_can( 'view_private_task', $project_id ) ) {
        $task = $task->doesntHave('metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'recurrence' )
                  ->where( 'meta_value', '!=', 0 );
        })->doesntHave( 'task_lists.metas', 'and', function ($query) {
            $query->where( 'meta_key', '=', 'recurrence' )
                  ->where( 'meta_value', '!=', '0' );

        } );
    }

    return $task;
}

function pm_check_milestone_privacy( $milestone, $project_id ) {
    if ( ! pm_user_can( 'view_private_milestone', $project_id ) ) {
        $milestone = $milestone->where( function( $q ) {
            $q->doesntHave( 'metas', 'and', function ( $query ) {
                $query->where( 'meta_key', '=', 'privacy' )
                    ->where( 'meta_value', '!=', 0 );
            });
            $q->orWhere( pm_tb_prefix().'pm_boards.created_by', '=', get_current_user_id() );
        });
    }
    return $milestone;
}
function pm_file_privacy_query ( $file, $project_id ) {
    if ( !pm_user_can( 'view_private_file', $project_id ) ) {
        $file = $file->doesntHave('meta', 'and', function ( $q ) {
                    $q->where( 'meta_key', '=', 'private' )
                        ->where( 'meta_value', '!=', 0 );
                });
    }
    return $file;
}

function pm_add_create_meta( $resource, $request ) {
    return $resource->setMeta([
        'permission' => [
            'can_create' => pm_user_can( 'create_message', $request->get_param( 'project_id' ) )
        ]
    ]);
}

function pm_pro_get_jed_locale_data( $local_data ) {
    $local_data['pm_pro'] = pm_get_jed_locale_data( 'pm-pro', pm_pro_config('define.path') . '/languages/' );
    return $local_data ;
}

function pm_project_text_editor($config) {
    $config['external_plugins']['mention'] = config('frontend.assets_url') . 'vendor/tinymce/plugins/mention/plugin.min.js';
    $config['plugins'] = $config['plugins'] . ' mention';
    return $config;
}

function active_daily_digest_event( $settings ) {
    if ( pm_get_setting( 'daily_digest' ) === "true" ) {
        if ( ! wp_next_scheduled( 'pm_daily_digest' ) ) {
            wp_schedule_event( time(), 'daily', 'pm_daily_digest' );
        }
    } else if ( pm_get_setting( 'daily_digest' ) === "false" ) {
        wp_clear_scheduled_hook( 'pm_daily_digest' );
    }
}



/**
 * ==========================
 * WP ERP integration
 * ==========================
 */

/**
 * Load Content to single employee's task tab
 *
 * @param array $response
 * @param array $request_params
 *
 * @return void
 */
function employee_task_tab() {
    echo '<div id="wedevs-pm"></div>';

    //pro scripts
    Pro_Register_Scripts::scripts();
    Pro_Register_Scripts::styles();

    // free scripts
    Register_Scripts::scripts();
    Register_Scripts::styles();

    if ( pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' ) ) {
        pm_pro_enqueue_sub_tasks_script();
    }
    if ( pm_pro_is_module_active( 'Time_Tracker/Time_Tracker.php' ) ) {
        pm_pro_enqueue_time_tracker_script();
    }
    if ( pm_pro_is_module_active( 'Kanboard/Kanboard.php' ) ) {
        pm_pro_enqueue_kanboard_script();
    }
    if ( pm_pro_is_module_active( 'Gantt/Gantt.php' ) ) {
        pm_pro_gantt_script();
    }
    if ( pm_pro_is_module_active( 'Invoice/Invoice.php' ) ) {
        pm_pro_invoice_scripts();
    }

    wp_enqueue_style( 'pm-frontend-style' );

    //pro scripts
    Pro_Enqueue_Scripts::scripts();
    Pro_Enqueue_Scripts::styles();

    // free scripts
    Enqueue_Scripts::scripts();
    Enqueue_Scripts::styles();
}

function pm_on_profile_tab( $profile_tab ) {
    $profile_tab['employee_task'] = array(
        'title'    => __( 'Tasks', 'cpm' ),
        'callback' => 'employee_task_tab'
    );

    return $profile_tab;
}

/**
 * Add task tab on employee single page
 *
 * @param string $tab_url
 * @param string $tab
 * @param integer $employee_id
 *
 * @return string
 */
function pm_employee_task_tab_url( $tab_url, $tab, $employee_id ) {
    if ( 'employee_task' === $tab ) {
        $tab_url = $tab_url . '#/my-tasks/' . $employee_id;
    }

    return $tab_url;
}

/**
 * Assign employees from department when creating a project
 *
 * @param array $response
 * @param array $request_params
 *
 * @return void
 */
function assign_employees_to_project( $response, $request_params ) {
    if(!class_exists('\WeDevs\ERP\HRM\Department')) {
        return;
    }

    if ( ! empty( $request_params['department_id'] ) ) {
        $department_id   = absint( $request_params['department_id'] );
        $project_id      = $response['data']['id'];

        $department      = new \WeDevs\ERP\HRM\Department( $department_id );
        $department_lead = $department->get_lead();
        $employees       = erp_hr_get_employees( [ 'department' => $department_id ] );
        $role_manager    = 1;
        $role_co_worker  = 2;
        // Project is updating
        if ( isset( $request_params['assignees'] ) ) {
            $department_meta = pm_get_meta( $project_id, $project_id, 'erp_department', 'department_id' );
            $department_meta_value = absint( $department_meta->meta_value );

            if ( $department_meta->meta_value ) {
                $prev_department = new \WeDevs\ERP\HRM\Department( $department_meta_value );
                $prev_department_lead = $prev_department->get_lead();

                $prev_employees    = erp_hr_get_employees( [ 'department' =>  $department_meta_value ] );
                $prev_employees_id = wp_list_pluck( $prev_employees, 'ID' );

                // we need to remove previous department lead
                if ( ! empty( $prev_department_lead ) ) {
                    array_push( $prev_employees_id, $prev_department_lead->id );
                }

                User_Role::whereIn('user_id', $prev_employees_id)->where('project_id', $project_id)->delete();
            }
        }

        if ( ! empty( $department_lead ) ) {

            role_first_or_create([
                'role_id'    => $role_manager,
                'user_id'    => $department_lead->id,
                'project_id' => $project_id,
                ]);
            }

        foreach ( $employees as $employee ) {
            role_first_or_create([
                'role_id'    => $role_co_worker,
                'user_id'    => $employee->ID,
                'project_id' => $project_id,
            ]);
        }

        pm_update_meta( $project_id, $project_id, 'erp_department', 'department_id', $department_id );
    } else if ( isset ($request_params['department_id'] )) {
        $department_id   = absint( $request_params['department_id'] );
        $project_id      = $response['data']['id'];
        $has_dept =   pm_get_meta( $project_id, $project_id, 'erp_department', 'department_id' );
        $employees       = erp_hr_get_employees( [ 'department' => (int)$has_dept->meta_value ] );

        //If the department user assign any task then you can not remove or update the department from the project
        if ( !empty( $employees ) ) {
            $emp_ids = wp_list_pluck( $employees, 'user_id' );
            $tasks = pm_get_tasks( ['users' =>  $emp_ids] );

            if ( empty( $tasks['data'] ) ) {
                pm_update_meta( $project_id, $project_id, 'erp_department', 'department_id', $department_id );
            }

        } else {
            pm_update_meta( $project_id, $project_id, 'erp_department', 'department_id', $department_id );
        }
    }
}

/**
 * Get department id when fetching a project
 *
 * @param array $transformer
 * @param object $project
 *
 * @return array
 */
function pm_get_project_department( $transformer, $project ) {
    $department_id = pm_get_meta( $project->id, $project->id, 'erp_department', 'department_id' );

    if ( $department_id ) {
        $transformer['department_id'] = $department_id->meta_value;
    }

    return $transformer;
}


function update_erp_department_user ( $user_id ) {

    $newDepartment = empty( $_POST['work']['department'] ) ? (empty( $_POST['department'] ) ? '': absint( $_POST['department']) ) : absint( $_POST['work']['department']);

    if (empty( $newDepartment ) ) {
        return ;
    }

    $employee       = new \WeDevs\ERP\HRM\Employee($user_id);
    $employeeData   = $employee->get_erp_user();
    $department     =  $employeeData->department;

    $projects_ids = WeDevs\PM\Common\Models\Meta::where('entity_type', 'erp_department')
        ->where('meta_key', 'department_id')
        ->where('meta_value', $department)
        ->get(['project_id'])->toArray();

    $projects_ids = wp_list_pluck($projects_ids, 'project_id');

    if (!empty($projects_ids)) {
        foreach ( $projects_ids as $project_id ) {
            $role = User_Role::where( 'user_id', $user_id)
                ->where('project_id', $project_id)
                ->first();

            if ( $role ) {
                $role->delete();
            }

        }
    }

    $projects_ids = WeDevs\PM\Common\Models\Meta::where('entity_type', 'erp_department')
        ->where('meta_key', 'department_id')
        ->where('meta_value', $newDepartment)
        ->get(['project_id'])->toArray();

    $projects_ids = wp_list_pluck($projects_ids, 'project_id');

    if ( !empty( $projects_ids ) ) {

        foreach ( $projects_ids as $project_id ) {
            role_first_or_create([
                'role_id'   => 2,
                'user_id' => $user_id,
                'project_id' => $project_id,
            ]);
        }
    }
}

function update_erp_department( $department, $fields ) {
    if ( $fields['lead'] == '0' ) {
        return;
    }

    $projects_ids = WeDevs\PM\Common\Models\Meta::where('entity_type', 'erp_department')
        ->where('meta_key', 'department_id')
        ->where('meta_value', $department)
        ->get(['project_id'])->toArray();

    $projects_ids = wp_list_pluck($projects_ids, 'project_id');

    if ( !empty( $projects_ids ) ) {

        foreach ( $projects_ids as $project_id ) {

            role_first_or_create([
                'role_id'   => 1,
                'user_id' => absint($fields['lead']),
                'project_id' => $project_id,
            ]);
        }
    }
}

function role_first_or_create ( $data ) {
    $role = User_Role::where( [
        'user_id'    =>  $data['user_id'],
        'project_id' => $data['project_id']
    ] )->first();

    if ( $role ) {
        $role->role_id = $data['role_id'];
        // var_dump($role); die();
        $role->save();
    } else {
        User_Role::create( [
            'role_id'    => $data['role_id'],
            'user_id'    => $data['user_id'],
            'project_id' => $data['project_id']
        ] );
    }
}

/**
 * Get locale code
 *
 * @since 2.0.9
 *
 * @return str
 */
function pm_pro_get_locale() {
    $locale = get_user_locale();

    // no need to add locale for en_US
    if ( 'en_US' === $locale ) {
        return;
    }

    $explod_locale = explode( '_', $locale );

    // make sure we have two segments - 1.lang, 2.country
    if ( count( $explod_locale ) < 2 ) {
        return $locale;
    }

    $lang = $explod_locale[0];
    $country = strtolower( $explod_locale[1] );

    if ( $lang === $country ) {
        $locale = $lang;
    } else {
        $locale = $lang . '-' . $country;
    }

    return $locale;
}

function pm_privacy_check( $bool, $project_id, $permission_name ) {
    if ( pm_user_can( $permission_name, $project_id) ){
        return true;
    }
    return false;
}

function pm_pro_task_filter_list_permission( $query, $request ) {
    $current_user    = $request->get_param('users');

    if ( empty( $assignees ) ) {
        $current_user = get_current_user_id();
    }

    $project_id   = $request->get_param( 'project_id' );
    $tb_meta      = pm_tb_prefix() . 'pm_meta';
    $tb_lists     = pm_tb_prefix() . 'pm_boards';

    if ( ! pm_user_can( 'view_private_list', $project_id ) ) {

        $query = $query->leftJoin( $tb_meta , function( $join ) use($tb_meta, $tb_lists) {
            $join->on( $tb_lists . '.id', '=', $tb_meta.'.entity_id' )
                ->where( function($q) use($tb_meta) {
                    $q->where( $tb_meta.'.entity_type', 'task_list' );
                    $q->orWhereNull( $tb_meta.'.entity_type' );
                });
        });

        $query->where( function( $q ) use( $current_user, $tb_meta ) {
            $q->where( function( $qu ) use( $current_user, $tb_meta ) {
                $qu->where( function( $q3 ) use( $current_user, $tb_meta ) {
                    $q3->where( $tb_meta.'.meta_key', '=', 'privacy' );
                    $q3->where( $tb_meta.'.meta_value', '!=', 1 );
                });

                if ( is_array( $current_user ) ) {
                    $qu->orWhereIn( pm_tb_prefix() .'pm_boards.created_by',  $current_user );
                } else {
                    $qu->orwhere( pm_tb_prefix() .'pm_boards.created_by', '=',  $current_user );
                }
            });
            $q->orWhereNull( $tb_meta.'.meta_key' );
            $q->orWhere( $tb_meta.'.meta_key', '!=', 'privacy' );
        });
    }

    return $query;
}

/**
 * [pm_pro_get_labels description]
 * @param  array  $params
 * @return [type]
 */
function pm_pro_get_labels( $params = [] ) {
     return \WeDevs\PM_Pro\Label\Helper\Label::get_results( $params );
}

/**
 * [pm_pro_menu_access_capabilities description]
 * @param  [type] $caps
 * @return [type]
 */
function pm_pro_menu_access_capabilities( $caps ) {
    return $caps;
}



