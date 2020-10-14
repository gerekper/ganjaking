<?php
namespace WeDevs\PM_Pro\Core\WP;

use WeDevs\PM\Role\Models\Role;
use PM_Pro_Create_Table;

class Active {
	function __construct() {
        $cpm_version = get_option('cpm_version');

        if ( $cpm_version && version_compare( $cpm_version, '2.0' , '<' ) ) {
            add_action( 'admin_notices', 'pm_version_notice' );
            return;
        }
        if ( !$cpm_version && !class_exists( 'WeDevs\\PM\\Core\\WP\\Frontend' ) ) {
            add_action( 'admin_notices', 'pm_notice' );
            return;
        }

		pm_pro_load_libs();
        new PM_Pro_Create_Table;

		$this->maybe_activate_modules();
        //$this->active_daily_digest();
        $this->create_pages();
        $this->active_client_role();
        //$this->create_label_table();

        update_option( 'pm_pro_version', pm_pro_config('app.version') );
        update_option( 'pm_pro_db_version', pm_pro_config('app.db_version') );
	}

	/**
     * Activate all the modules for the first time
     *
     * @return void
     */
    public function maybe_activate_modules() {
        global $wpdb;

        $has_installed = $wpdb->get_row( "SELECT option_id FROM {$wpdb->options} WHERE option_name = 'pm_pro_active_modules'" );

        if ( $has_installed ) {
            return;
        }

        $modules = pm_pro_get_modules();

        if ( $modules ) {
            foreach ($modules as $module_file => $data) {
                pm_pro_activate_module( $module_file );
            }
        }
    }

    /**
     * Run actions on `plugins_loaded` hook
     *
     * @since 2.0.0
     *
     * @return void
     */
    // public function active_daily_digest() {
    //     if ( function_exists( 'pm_get_setting' ) ) {
    //         $digest = pm_get_setting( 'daily_digest' );

    //         $digest = empty( $digest ) ? true : ( $digest === 'true' ) ? true : false;
    //     }

    //     if ( $digest ) {
    //         if ( ! wp_next_scheduled( 'pm_daily_digest' ) ) {
    //             wp_schedule_event( time(), 'daily', 'pm_daily_digest' );
    //         }
    //     }
    // }

    /**
     * Create Frontend Page if they not exist
     *
     * @since  1.4.3
     *
     * @return void
     */
    public function create_pages() {
        global $wpdb;
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }
        $page_data = array(
            'post_status'    => 'publish',
            'post_author'    => get_current_user_id(),
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_type'      => 'page',
            'post_parent'    => 0,
        );

        // Create Project Page
        $cpm_pages = get_option( 'cpm_page', [] );
        $pm_pages  = get_option( 'pm_pages', [] );
        $page      = empty( $cpm_pages ) ? $pm_pages : $cpm_pages;

        if ( empty( $page['project'] ) || get_page($page['project']) == null ) {

            $page_title = __( 'Projects', 'pm-pro' );

            $page_data['post_title']   = $page_title;
            $page_data['post_content'] = "[pm]";

            $e = wp_insert_post( $page_data, true );

            if ( ! is_wp_error( $e ) ) {
                $page['project'] = $e;
            }
        }
        update_option( 'pm_pages', $page );
    }

    public function active_client_role() {
        if ( class_exists('WeDevs\\PM\\Role\\Models\\Role') ) {
            Role::where( 'slug', 'client' )->update( [ 'status' => 1 ] );
        }
    }

}
