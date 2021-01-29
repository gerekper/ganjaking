<?php

namespace WeDevs\PM_Pro\Core\WP;

use WeDevs\PM_Pro\Core\WP\Menu;
use WeDevs\PM_Pro\Core\WP\Register_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Enqueue_Scripts;
//use PM\Project\Project_Ajax;
use WeDevs\PM_Pro\Core\File_System\File_System as File_System;
use WeDevs\PM_Pro\Core\Notifications\Notification;
use WeDevs\PM_Pro\Core\Update\Update;
use WeDevs\PM_Pro\Core\Upgrades\Upgrade;
use WeDevs\PM_Pro\Core\Update\License_Notification;
use WeDevs\PM_Pro\Integrations\Models\Integrations as Integr;
use WeDevs\PM_Pro\Core\Update\Update as License;
use WeDevs\PM_Pro\Duplicate\Controllers\Duplicate_Controller;

class Frontend {

    /**
     * Plan type
     *
     * @var string
     */
    //private $plan = 'project-manager-pro';

    /**
     * Constructor for the PM class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        if (
            ! License::is_license_active()
                &&
            pm_pro_config( 'app.environment' ) == 'production'
        ) {
            $this->license_instantiate();
            return;
        }

        $this->includes();
        // instantiate classes
        $this->instantiate();

        // Initialize the action hooks
        $this->init_actions();

        // Initialize the action hooks
        $this->init_filters();
    }

    public function includes() {
        global $wedevs_pm_pro;
        $wedevs_pm_pro = true;

        // load all the active modules
        $modules = pm_pro_get_active_modules();
        $modules_path = pm_pro_config('define');

        if ( $modules ) {
            foreach ( $modules as $module_file ) {
                $module_file = $this->module_slug( $module_file );
                $module_path = $modules_path['module_path'] . '/' . $module_file;

                if ( file_exists( $module_path ) ) {
                    include_once $module_path;
                }
            }
        }
    }

    function module_slug( $path ) {
        $module = strtolower( $path );

        $updated = array (
            'custom_fields/custom_fields.php'   => 'Custom_Fields/Custom_Fields.php',
            'gantt/gantt.php'                   => 'Gantt/Gantt.php',
            'invoice/invoice.php'               => 'Invoice/Invoice.php',
            'kanboard/kanboard.php'             => 'Kanboard/Kanboard.php',
            'pm_buddypress/pm_buddypress.php'   => 'PM_Pro_Buddypress/PM_Pro_Buddypress.php',
            'sprint/sprint.php'                 => 'Sprint/Sprint.php',
            'stripe/stripe.php'                 => 'Stripe/Stripe.php',
            'sub_tasks/sub_tasks.php'           => 'Sub_Tasks/Sub_Tasks.php',
            'task_recurring/task_recurring.php' => 'Task_Recurring/Task_Recurring.php',
            'time_tracker/time_tracker.php'     => 'Time_Tracker/Time_Tracker.php',
            'woo_project/woo_project.php'       => 'Woo_Project/Woo_Project.php'
        );

        return empty( $updated[$path] ) ? $path : $updated[$path];
    }

    /**
     * All actions
     *
     * @return void
     */
    public function init_actions() {
        add_action( 'pm_menu_before_load_scripts', array( new Menu, 'admin_menu' ) );
        add_action( 'wp_ajax_pm_ajax_upload', array ( new File_System, 'ajax_upload_file' ) );
        add_action( 'pm_new_message_before_response', 'save_discuss_privacy_field', 10, 2 );
        add_action( 'pm_update_message_before_response', 'save_discuss_privacy_field', 10, 2 );
        add_action( 'pm_new_task_list_before_response', 'save_task_list_privacy_field', 10, 2 );
        add_action( 'pm_update_task_list_before_response', 'save_task_list_privacy_field', 10, 2 );
        add_action( 'pm_after_update_task', 'save_task_privacy_field', 10, 2 );
        add_action( 'pm_after_create_task', 'save_task_privacy_field', 10, 2 );
        add_action( 'pm_after_create_task', 'save_task_recurrence_data', 10, 2 ); //for re...task
        add_action( 'pm_after_update_task', 'save_task_recurrence_data', 10, 2 ); //for re...task
        add_action( 'pm_new_milestone_before_response', 'save_milestone_privacy_field', 10, 2 );
        add_action( 'pm_update_milestone_before_response', 'save_milestone_privacy_field', 10, 2 );
        add_action( 'admin_enqueue_scripts', array ( $this, 'register_scripts' ) );
        add_action( 'init', array( $this, 'shortcode_instantiate' ) );
        add_action( 'pm_after_save_settings', 'active_daily_digest_event' );
        add_action( 'pm_after_new_project', 'assign_employees_to_project', 10, 2 );
        add_action( 'pm_after_update_project', 'assign_employees_to_project', 10, 2 );
        add_action( 'erp_hr_employee_job_info_create', 'update_erp_department_user', 10, 1);
        add_action( 'erp_hr_dept_before_updated', 'update_erp_department', 10, 2);
        add_action( 'pm_after_create_task', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'after_create_task'], 10, 2 );
        add_action( 'pm_after_update_task', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'after_update_task'], 10, 2 );
        add_action( 'pm_after_delete_task', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'after_delete_task'], 10, 2 );

        add_action( 'pm_pro_license_update', ['WeDevs\PM_Pro\Core\Update\Update', 'update_license'] );
        add_action( 'pm_after_save_settings', 'pm_pro_after_save_settings' );

        add_action( 'admin_bar_menu', array( new Menu, 'create_frontend_menu' ), 2000 );
    }

    /**
     * All filters
     *
     * @return void
     */
    public function init_filters() {
        add_filter( 'pm_project_transformer', 'pm_get_project_capabilities', 10, 2 );
        add_filter( 'pm_milestone_index_query', 'pm_check_milestone_privacy', 10, 2 );
        add_filter( 'pm_milestone_show_query', 'pm_check_milestone_privacy', 10, 2 );
        add_filter( 'pm_task_index_query', 'pm_check_task_privacy', 10, 2 );
        add_filter( 'pm_task_show_query', 'pm_check_task_privacy', 10, 2 );
        add_filter( 'pm_task_query', 'pm_check_task_privacy', 10, 2 );
        add_filter( 'pm_task_filter_query', 'pm_check_task_filter_privacy', 10, 2 );
        add_filter( 'pm_complete_task_query', 'pm_check_task_privacy', 10, 2 );
        add_filter( 'pm_incomplete_task_query', 'pm_check_task_privacy', 10, 2 );
        add_filter( 'pm_filter_task_permission', 'pm_check_task_privacy', 10, 2 );

        add_filter( 'pm_incomplete_task_query_join', 'pm_pro_task_query_join', 10, 2 );
        add_filter( 'pm_incomplete_task_query_where', 'pm_pro_task_query_where', 10, 2 );
        add_filter( 'pm_complete_task_query_join', 'pm_pro_task_query_join', 10, 2 );
        add_filter( 'pm_complete_task_query_where', 'pm_pro_task_query_where', 10, 2 );

        add_filter( 'list_tasks_filter_query', 'pm_pro_list_tasks_filter_query', 10, 2 );
        add_filter( 'pm_list_task_transormer', 'pm_pro_set_list_task_data', 10, 2 );
        add_filter( 'pm_task_list_transform', 'pm_pro_set_list_data', 10, 2 );

        //add_filter( 'pm_complete_task_query_join', 'pm_check_task_privary_row', 10, 2 );
        //add_filter( 'pm_incomplete_task_query', 'pm_check_task_list_recurrence', 10, 2 ); // rec data
        add_filter( 'pm_discuss_index_query', 'pm_check_discuss_privacy', 10, 2 );
        add_filter( 'pm_discuss_show_query', 'pm_check_discuss_privacy', 10, 2 );
        add_filter( 'pm_discuss_query', 'pm_check_discuss_privacy', 10, 2 );
        add_filter( 'pm_task_list_index_query', 'pm_check_task_list_privacy', 10, 2 );
        add_filter( 'pm_task_list_show_query', 'pm_check_task_list_privacy', 10, 2 );
        add_filter( 'pm_task_list_query', 'pm_check_task_list_privacy', 10, 2 );
        add_filter( 'pm_task_list_check_privacy', 'pm_pro_check_task_list_privacy_query', 10, 2 );
        add_filter( 'pm_get_messages', 'pm_add_create_meta', 10, 2 );
        add_filter( 'pm_get_message', 'pm_add_create_meta', 10, 2 );
        add_filter( 'pm_get_jed_locale_data', 'pm_pro_get_jed_locale_data' );
        add_filter( 'todo_list_text_editor', 'pm_project_text_editor' );
        add_filter( 'erp_hr_employee_single_tabs', 'pm_on_profile_tab' );
        add_filter( 'erp_hr_employee_tab_url', 'pm_employee_task_tab_url', 10, 3 );
        add_filter( 'pm_project_transformer', 'pm_get_project_department', 10, 2 );
        add_filter( 'pm_project_transformer', ['WeDevs\PM_Pro\Settings\Controllers\Settings_Controller', 'set_project_labels'], 10, 2 );
        add_filter( 'pm_file_query', 'pm_file_privacy_query', 10, 2 );
        add_filter( 'pm_task_label', ['WeDevs\PM_Pro\Settings\Controllers\Settings_Controller', 'get_task_labels'] );
        add_filter( 'pm_single_project_query', 'pm_pro_project_query', 10, 2);
        add_filter( 'pm_task_model_labels', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'task_model_labels'] );
        add_filter( 'pm_task_transform', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'label_transform'], 10, 2 );
        add_filter( 'pm_after_transformer_list_tasks', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'set_labales'], 10, 2 );
        add_filter( 'pm_task_with', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'set_labales_in_task'], 10, 3 );
        add_filter( 'pm_task_duplicate_after', ['\WeDevs\PM_Pro\Label\Controllers\Task_Label_Controller', 'duplicate_task_label'], 10, 4 );
        add_filter('pm_get_task', [$this, 'pm_get_task'], 10, 2);

        // task list create permission check
        add_filter( 'pm_check_permission', 'pm_privacy_check', 10, 3 );
        add_filter( 'pm_check_task_filter_list_permission', 'pm_pro_task_filter_list_permission', 10, 2 );
        add_filter( 'pm_task_join', 'pm_pro_task_join', 10 );
        add_filter( 'pm_task_where', 'pm_pro_task_where', 10, 2 );

        add_filter( 'pm_access_capabilities', 'pm_pro_menu_access_capabilities' );
    }

    /**
     * instantiate clatask
     * @return void
     */
    public function license_instantiate() {
        if ( is_admin() ) {
            new Upgrade();
            new update( pm_pro_config( 'app.plan' ) );
        }

        \WeDevs\PM_Pro\Core\Rewrites\Rewrite::instance();
    }

    /**
     * instantiate clatask
     * @return void
     */
    public function instantiate() {
        Integrations::init();
        new Notification();

        $this->license_instantiate();
    }

    public function register_scripts() {
        Register_Scripts::scripts();
        Register_Scripts::styles();
    }

    public function shortcode_instantiate() {
        Shortcodes::init();
    }

    public function pm_get_task($response,$request){
        if(array_key_exists('activities',$response['data']) && array_key_exists('comments',$response['data'])){
            $response['data']['activities'] = $this->pm_modify_activities($response['data']['activities'] , $request) ;
            if(class_exists('WeDevs\PM_Pro\Integrations\Models\Integrations')){
                $response['data']['comments']['data']= $this->pm_modify_comments($response,$request);
            }
        }else{
            $response = $this->pm_modify_activities($response,$request) ;
        }
        return $response;
    }

    public function pm_modify_activities($response,$request){
        for($i=0; $i < count($response['data']);$i++){
            if(isset($response['data'][$i]['meta']['int_source']) && empty($response['data'][$i]['actor']['data']) ){
                $response['data'][$i]['actor']['data']['display_name'] = '['.ucfirst($response['data'][$i]['meta']['int_source']).'] '. $response['data'][$i]['meta']['username']  ;
                $response['data'][$i]['actor']['data']['avatar_url'] = "http://2.gravatar.com/avatar/2ce274bc61d00731e73c033d90cb0d73?s=96&d=mm&r=g";
            }
        }
        return $response ;
    }

    public function pm_modify_comments($response,$request){
        $project_id = $response['data']['project_id'] ;
        $intg_comments = Integr::where('project_id', $project_id)
            ->where('type','issues_comments')
            ->get();
        $intg_comments_blank = [];
        foreach($intg_comments as $intgc){
            $intg_comments_blank[$intgc['foreign_key']] = $intgc ;
        }
        $project_comments = $response['data']['comments']['data'] ;
        $comments_blank = [];
        foreach($project_comments as $cmnt){
            if(empty($cmnt['creator']['data'])){
                $cmnt['creator']['data']['username'] = $intg_comments_blank[$cmnt['id']]['source'];
                $cmnt['creator']['data']['nicename'] = $intg_comments_blank[$cmnt['id']]['source'];
                $cmnt['creator']['data']['email'] = '';
                $cmnt['creator']['data']['display_name'] = '['.$intg_comments_blank[$cmnt['id']]['source'].'] ' . $intg_comments_blank[$cmnt['id']]['username'];
                $cmnt['creator']['data']['manage_capability'] = 1;
                $cmnt['creator']['data']['create_capability'] = 1;
                $cmnt['creator']['data']['avatar_url'] = "http://2.gravatar.com/avatar/2ce274bc61d00731e73c033d90cb0d73?s=96&d=mm&r=g";
                $cmnt['creator']['data']['roles'] = [];
                $comments_blank[] = $cmnt ;
            }else{
                $comments_blank[] = $cmnt ;
            }
        }
        return  $comments_blank ;
    }

}
