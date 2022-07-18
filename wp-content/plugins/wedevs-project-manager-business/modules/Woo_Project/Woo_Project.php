<?php
/**
 * Module Name: WooCommerce Order
 * Description: Create projects instantly for each of the orders placed on your WooCommerce store.
 * Module URI: https://wedevs.com/wp-project-manager-pro/
 * Thumbnail URL: /views/assets/images/woo-project.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Project\Controllers\Project_Controller;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM_Pro\Duplicate\Controllers\Duplicate;
class Woo_Project {

    /**
     * Project manager is install ?
     * initial value is false
     */
    private static $pm = true;

    /**
     * Project manager pro is install ?
     * initial value is false
     */
    private static $pm_pro = true;

    /**
     * Woocommerce is install ?
     *
     */

    private static $woocommerce = true;

    /**
     * Constructor for the CPM_Woo_Order class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses add_action()
     */
    function __construct() {
        //pmpr("hello");
        $this->init_actions();
        $this->init_filters();
        return $this;
    }

    /**
     * Initialize module woo Project
     * @return [void] [description]
     */
    public static function init ( ) {

        if ( !class_exists( 'WeDevs\PM_Pro\Core\WP\Frontend' ) ) {

            Woo_Project::$pm_pro = false;
            add_action( 'admin_notices', array( 'Woo_Project', 'notice' ) );
            return;
        }

        if ( !class_exists( 'Woocommerce' ) ) {

            Woo_Project::$woocommerce = false;
            add_action( 'admin_notices', array( 'Woo_Project', 'notice' ) );
            return;
        }

        return new Woo_Project();
    }

    /**
     * init module action
     *
     * @return  [void]
     */
    public function init_actions ( ) {
        add_action( 'pm_menu_before_load_scripts', array($this, 'register_submenu'));
        add_action( 'woocommerce_order_status_changed', array($this, 'order_status_changed'), 10, 3 );
        add_action( 'pm_load_shortcode_script', [ $this, 'woo_project_load_scripts' ] );
    }

    /**
     * initialize filters
     * @return [void]
     */
    public function init_filters ( ) {
        add_filter( 'pm_pro_load_router_files', array($this, 'load_router_file'));
    }

    /**
     * Load router files
     * @param  array $files Array of files from filters
     * @return array
     */
    public static function load_router_file ( $files ) {
        $woo_project_router_files = glob( __DIR__ . "/routes/*.php" );

        return array_merge( $files, $woo_project_router_files );
    }

    /**
     * Register submenu under project menu
     * @param  string $home
     * @return void
     */
    public function register_submenu ( $home ) {
        global $submenu;
        $ismanager = pm_has_manage_capability();
        if ( !$ismanager ) {
            return ;
        }
        $submenu['pm_projects'][] = [ __( 'Woo Project', 'pm-pro' ), 'read', 'admin.php?page=pm_projects#/woo-project' ];

        // Script and style should load after menu register
        add_action( 'admin_print_styles-' . $home, array($this, 'woo_project_load_scripts') );
    }

    /**
     * Load script and style for this module
     * @return void
     */
    public function woo_project_load_scripts ( ) {
        $view_path = dirname (__FILE__) . '/views/assets/';
        wp_enqueue_script( 'woo-project', plugins_url( 'views/assets/js/woo-project.js', __FILE__ ), array('pm-const'), filemtime( $view_path . 'js/woo-project.js' ), true );
        wp_enqueue_style( 'woo-project', plugins_url( 'views/assets/css/woo-project.css', __FILE__ ), array(), filemtime( $view_path . 'css/woo-project.css' ) );
    }


    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public static function notice ( ) {

        if ( !self::$pm_pro ) {
            printf( __('<div class="error"><p><strong>WP Project Manager</strong> is not installed or inactive. Please install/activate the plugin for Project Manager - BuddyPress add-on to work.</p> If you do not have the pro version, <strong><a href="%s">you can use the free version</a>.</strong></div>', 'pm-pro'), get_site_url() . '/wp-admin/plugin-install.php?tab=search&s=wp+project+manager' );
        }

        if ( !self::$woocommerce ) {
            printf( __('<div class="error"><p><strong>Woocommerce</strong> is not installed or inactive. Please <strong><a href="%s">install Woocommerce</a></strong> for Project Manager - Woo Project add on to work.</p></div>', 'pm-pro'), get_site_url() . '/wp-admin/plugin-install.php?tab=search&type=term&s=woocommerce' );
        }

    }

    public function order_status_changed ( $order_id ) {
        pm_log('order', $order_id);
        $order     = new WC_Order( $order_id );
        $items     = $order->get_items();
        $settings  = pm_get_setting( 'woo_project' );
        $order_id  = $order->get_id();
        $client_id = $order->get_customer_id();

        foreach ($items as $order_item_id => $order_info ) {
            $product_id = $order_info['product_id'];
            foreach ($settings as $setting ) {
                if ( in_array($product_id, $setting['product_ids']) ){
                    if ( 'duplicate' == $setting['action'] &&  !empty($setting['project_id'])){
                        $this->duplicate_project( $order_id,  $order_info, $setting );
                    } else if ( 'create' == $setting['action']){
                        $this->create_project ( $order_id,  $order_info, $setting, $client_id );
                    }
                }
            }
        }
        return $this;
    }

    public function create_project ( $order_id, $order_info, $setting, $client_id ) {
        global $wpdb;

        $meta = Meta::firstOrCreate([
            'entity_id'     => $order_id,
            'entity_type'   =>'woo_project',
            'meta_key'      => 'related_product',
            'meta_value'    => $order_info['product_id'],
        ]);

        if ( !$meta->project_id ){
            $project = Project::create([
                'title'         => $order_id . '# - ' . $order_info['name'],
                'description'   =>'',
            ]);

            // Create list inbox when create a project.
		    ( new Project_Controller() )->create_list_inbox( $project->id );

            $role_project_id = null;

            if ( ! empty( intval( $project->id ) ) ) {
                User_Role::firstOrCreate([
                    'user_id'    => $client_id,
                    'role_id'    => 3,
                    'project_id' => $project->id,
                ]);

                // Insert into role project.
                $wpdb->insert(
                    $wpdb->prefix . 'pm_role_project',
                    [
                        'role_id'    => 3,
                        'project_id' => $project->id,
                    ],
                    [
                        '%d',
                        '%d',
                    ]
                );

                $role_project_id = $wpdb->insert_id;
            }

            if ( !empty($setting['assignees']) && is_array($setting['assignees']) ){
                foreach ($setting['assignees'] as $assignee) {
                    User_Role::firstOrCreate([
                        'user_id'    => $assignee['user_id'],
                        'role_id'    => $assignee['role_id'],
                        'project_id' => $project->id,
                    ]);

                    // Insert into role project users.
                    if ( ! empty( $role_project_id ) ) {
                        $wpdb->insert(
                            $wpdb->prefix . 'pm_role_project_users',
                            [
                                'role_project_id' => $role_project_id,
                                'user_id'         => $assignee['user_id'],
                            ],
                            [
                                '%d',
                                '%d',
                            ]
                        );
                    }
                }
            }
            $meta->project_id = $project->id;
            $meta->save();
        }

    }

    function update_wop_project( $project_id  ) {
        $woop = get_post_meta( $project_id, '_cpm_wop_project', TRUE );

        if( $woop == '1' )
        {
            $project = get_post($project_id) ;
            CPM_Project::getInstance()->update_user_role( $project_id, $project->post_author, 'client' )  ;
        }
    }

    public function duplicate_project ( $order_id, $order_info, $setting ) {

        $meta = Meta::firstOrCreate([
            'entity_id'     => $order_id,
            'entity_type'   =>'woo_project',
            'meta_key'      => 'related_product',
            'meta_value'    => $order_info['product_id'],
        ]);

        if ( !$meta->project_id ) {
            $project = Duplicate::init()->project_duplicate( $setting['project_id'] );
            $meta->project_id = $project->id;
            $meta->save();
        }
    }

}

Woo_Project::init();
