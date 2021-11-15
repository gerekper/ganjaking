<?php
/**
 * Module Name: BuddyPress Integration
 * Description: Manage your projects group wise directly from the frontend using this premium integration.
 * Module URI: https://wedevs.com/products/plugins/wp-project-manager-pro/buddypress/
 * Thumbnail URL: /views/assets/images/buddy-press.png
 * Author: weDevs
 * Version: 2.0.0
 * Author URI: https://wedevs.com
 */
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM\Common\Models\Assignee;

class PM_Pro_BuddyPress {

    /**
     * Project manager is install ?
     * initial value is false
     */
    static $buddypress = true;
    static $pm         = true;

    /**
     * Constructor for the CPM_Woo_Order class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses add_action()
     */
    function __construct() {
        $this->init_actions();
        $this->init_filters();
    }

    /**
     * init module action
     *
     * @return  [void]
     */
    public function init_actions() {
                    //load module script
        add_action( 'pm_project_new', array($this, 'project_created'), 10, 2);
        add_action( 'groups_member_after_save', array( $this, 'buddypress_action' ) );
        add_action( 'groups_leave_group', array($this, 'after_leave_group'), 10, 2 );
        add_action( 'groups_remove_member', array( $this, 'groups_remove_member' ), 10, 2 );
        add_action( 'pm_project_duplicate', array( $this, 'after_duplicate_project' ), 10, 2 );

    }

    /**
     * initialize filters
     * @return [void]
     */
    public function init_filters() {
        add_filter( 'pm_project_where_query', array( $this, 'query_group_project' ), 10, 2 );
        add_filter( 'pm_project_join_query', array( $this, 'project_meta_join_query' ), 10, 2 );
    }

    public function after_duplicate_project($old_project_id, $new_project_id) {
        $is_admin = empty( $_REQUEST['is_admin'] ) ? false : true;

        if ( $is_admin ) {
            return;
        }

        $group_id = empty( $_REQUEST['bp_group_id'] ) ? false : intval($_REQUEST['bp_group_id']);

        if( ! $group_id ) {
            return;
        }

        $meta = Meta::firstOrCreate([
            'entity_id'     => $group_id,
            'entity_type'   => 'pm_buddypress',
            'meta_key'      => 'group_id',
            'meta_value'    => $group_id,
            'project_id'    => $new_project_id
        ]);
    }

    public function project_meta_join_query( $join, $params ) {
        $is_admin = empty( $params['is_admin'] ) ? false : true;

        if ( $is_admin ) {
            return $join;
        }

        global $wpdb;

        $join .= " LEFT JOIN {$wpdb->prefix}pm_meta as bpmeta ON bpmeta.project_id={$wpdb->prefix}pm_projects.id";

        return $join;
    }

    public function query_group_project( $where, $prams ) {
        $is_admin = empty( $prams['is_admin'] ) ? false : true;

        if ( $is_admin ) {
            return $where;
        }

        global $wpdb;
        $group_id = isset( $prams['group_id'] ) && $prams['group_id'] ? $prams['group_id'] : false ;

        if ( $group_id ) {
            $where .= $wpdb->prepare( "
                AND bpmeta.entity_type=%s
                AND bpmeta.meta_key=%s
                AND bpmeta.meta_value=%d",
                'pm_buddypress', 'group_id',$group_id
            );
        }

        return $where;

    }

    public static function init() {
        if ( !class_exists( 'WeDevs\PM\Core\WP\Frontend' ) ) {

            PM_Pro_BuddyPress::$pm = false;
            add_action( 'admin_notices', array( 'PM_Pro_BuddyPress', 'notice' ) );
            return;
        }

        if ( !class_exists( 'BuddyPress' ) ) {

            PM_Pro_BuddyPress::$buddypress = false;
            add_action( 'admin_notices', array( 'PM_Pro_BuddyPress', 'notice' ) );
            return;
        }

        if ( bp_is_active( 'groups' ) ) {
            require_once dirname( __FILE__ ) . '/Src/PM_BP_Group_Extension.php';
            bp_register_group_extension( 'WeDevs\\PM_Pro\\Modules\\PM_Pro_BuddyPress\\Src\\PM_BP_Group_Extension' );
        }

        return new PM_Pro_BuddyPress();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    static function notice() {

        if ( !self::$pm ) {
            printf( __('<div class="error"><p><strong>WP Project Manager</strong> is not installed or inactive. Please install/activate the plugin for Project Manager - BuddyPress add-on to work.</p> If you do not have the pro version, <strong><a href="%s">you can use the free version</a>.</strong></div>', 'pm-pro'), get_site_url() . '/wp-admin/plugin-install.php?tab=search&s=wp+project+manager' );
        }

        if ( !self::$buddypress ) {
            printf( __('<div class="error"><p><strong>BuddyPress</strong> is missing. Please <strong><a href="%s">install BuddyPress</a></strong> for Project Manager - BuddyPress add-on to work.</p></div>', 'pm-pro'), get_site_url() . '/wp-admin/plugin-install.php?tab=search&type=term&s=buddypress' );
        }
    }

    public function project_created( $project, $request ) {
        $group_id = isset( $request['group_id'] ) && $request['group_id'] ? $request['group_id'] : false ;

        if ( !$group_id ) {
            return;
        }
        $project_id             = $project->id;

        $groups_member            = BP_Groups_Member::get_all_for_group( $group_id );
        $groups_admin             = BP_Groups_Member::get_group_administrator_ids( $group_id );
        $current_unser_id         = get_current_user_id();

        if ( $groups_member['members'] ) {
            foreach ( $groups_member['members'] as $user ) {
                $this->assign_users( $project_id, $user->user_id, 2 );
            }
        }


        foreach ( $groups_admin  as $user ) {
            if ( $user->user_id == get_current_user_id() ){
                continue ;
            }
            $this->assign_users( $project_id, $user->user_id, 1 );
        }

        $meta = Meta::firstOrCreate([
            'entity_id'     => $request['group_id'],
            'entity_type'   => 'pm_buddypress',
            'meta_key'      => 'group_id',
            'meta_value'    => $request['group_id'],
            'project_id'    => $project_id
        ]);

    }

    private function assign_users( $project_id, $user_id, $role_id ) {
        User_Role::firstOrCreate([
            'user_id'    => $user_id,
            'role_id'    => $role_id,
            'project_id' => $project_id,
        ]);
    }

    // public function query_where_group_project( $where, $query_params ) {
    //     global $wpdb;
    //     $tb_project_meta = "{$wpdb->prefix}pm_meta";
    //     $group_id = isset( $query_params['group_id'] ) && $query_params['group_id'] ? $query_params['group_id'] : false ;

    //     if ( $group_id ) {
    //        $where .= $wpdb->prepare( " AND {$wpdb->prefix}pm_meta.entity_type=%s", 'pm_buddypress' );
    //        $where .= $wpdb->prepare( " AND {$wpdb->prefix}pm_meta.meta_key=%s", 'group_id' );
    //        $where .= $wpdb->prepare( " AND {$wpdb->prefix}pm_meta.meta_value=%s", $group_id );
    //     }

    //     return $where;
    // }

    public function query_join_group_project ( $join, $query_params ) {
        global $wpdb;
        $group_id = isset( $query_params['group_id'] ) && $query_params['group_id'] ? $query_params['group_id'] : false ;

        if ( $group_id ) {
            $join = "LEFT JOIN {$wpdb->prefix}pm_meta ON {$wpdb->prefix}pm_meta.project_id={$wpdb->prefix}pm_projects.id";
        }

        return $join;
    }

    function buddypress_action( $self ) {

        $meta = Meta::where( 'entity_type', 'pm_buddypress' )
            ->where( 'meta_key', 'group_id' )
            ->where( 'meta_value', $self->group_id );

        $meta = $meta->get()->toArray();//pluck('project_id')->all();
        $project_ids = wp_list_pluck( $meta, 'project_id' );

        if ( sizeof($project_ids) <= 0 ) {
            return;
        }

        if ( $self->is_banned ) {
            $this->band_user( $self->user_id, $project_ids );
        } else {
            $this->check_member_status( $self, $project_ids );
        }
    }

    function band_user( $user_id, $project_ids ) {
        $project_ids = (array) $project_ids;

        foreach ( $project_ids as $project_id ) {
            // remove from project
            User_Role::where('user_id', $user_id)
                ->where( 'project_id', $project_id )
                ->delete();
            //Remove from task
            Assignee::where('assigned_to', $user_id)
                ->where( 'project_id', $project_id )
                ->delete();
        }

    }

    function check_member_status( $self, $project_ids ) {
        $project_ids = (array) $project_ids;
        if ( ! groups_is_user_member( $self->user_id, $self->group_id ) ) {
            return;
        }

        //Query all the group user role
        $user_roles = User_Role::where( 'user_id', $self->user_id )
                ->where( 'project_id', $project_ids )
                ->get();

        foreach ( $project_ids as $project_id ) {

            $user_role = $user_roles->where( 'project_id', $project_id )->first();

            if ( $user_role ) {

                if ( $self->is_admin ) {
                    $user_role->role_id = 1;
                }else {
                    if ( $user_role->role_id != 3 ) {
                        $user_role->role_id = 2;
                    }
                }

                $user_role->save();

            }else {

                if ( $self->is_admin ) {
                    $this->assign_users( $project_id, $self->user_id, 1);
                }else {
                    $this->assign_users( $project_id, $self->user_id, 2);
                }
            }
        }
    }

    /**
     * active after leave group
     *
     * @since 1.0
     *
     * @param int $group_id
     * @param int $user_id
     */
    function after_leave_group( $group_id, $user_id ) {

        $meta = Meta::where( 'entity_type', 'pm_buddypress' )
            ->where( 'meta_key', 'group_id' )
            ->where( 'meta_value', $group_id );

        $meta = $meta->get()->toArray();//pluck('project_id')->all();
        $project_ids = wp_list_pluck( $meta, 'project_id' );
        $this->band_user( $user_id, $project_ids );
    }

    /**
     * Remove group member
     *
     * @since 1.0
     *
     * @param int $group_id
     * @param int $user_id
     */
    function groups_remove_member( $group_id, $user_id ) {
        $meta = Meta::where( 'entity_type', 'pm_buddypress' )
            ->where( 'meta_key', 'group_id' )
            ->where( 'meta_value', $group_id );

        $meta = $meta->get()->toArray();//pluck('project_id')->all();
        $project_ids = wp_list_pluck( $meta, 'project_id' );
        $this->band_user( $user_id, $project_ids );
    }
}

PM_Pro_BuddyPress::init();

function pm_pro_bp_slug_name() {
    return 'projects';
}



