<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Core;

use WeDevs\PM_Pro\Modules\Custom_Fields\Src\Controllers\Custom_Field_Controller;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load general WP action hook
 */
class Actions {
    /**
     * This plugin's instance.
     *
     * @var CoBlocks_Accordion_IE_Support
     */
    private static $instance;

    /**
     * Registers the plugin.
     */
    public static function instance() {
         if ( !self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * The Constructor.
     */
    public function __construct() {
        add_action( 'pm-activation-custom_fields', [ $this, 'install' ] );
        add_action( 'wp_initialize_site', [ $this, 'after_insert_site' ], 110 );
        add_action( 'pm_after_task_duplicate', [ $this, 'after_task_duplicate' ], 10, 2 );
    }


    function after_task_duplicate( $new_task, $old_task ) {

        $old_task_id    = $old_task->id;
        $old_task_pj_id = $old_task->project_id;

        $custom_field_values = ( new Custom_Field_Controller() )->get_tasks_custom_fields_value([
            'project_id' => $old_task_pj_id,
            'task_id'    => $old_task_id,
            'with'       => 'value'
        ]);

        $project_id = $new_task['data']['project_id'];
        $task_id    = $new_task['data']['id'];

        foreach ( $custom_field_values['data'] as $key => $custom_field_value ) {

            $value = (array) $custom_field_value['value'];

            ( new Custom_Field_Controller() )->store_field_value([
                'project_id' => $project_id,
                'task_id'    => $task_id,
                'field_id'   => $custom_field_value['id'],
                'color'      => empty( $value ) ? '' : $value['color'],
                'value'      => empty( $value ) ? '' : $value['value'],
            ]);
        }
    }

    function install() {
        if ( is_multisite() && is_network_admin() ) {
            $sites = get_sites();

            foreach ( $sites as $key => $site ) {
                $this->after_insert_site( $site );
            }
        } else {
            $this->run_install();
        }
    }

    function after_insert_site( $blog ) {
        switch_to_blog( $blog->blog_id );

        $this->run_install();

        restore_current_blog();
    }

    function run_install() {
        $this->create_table();
    }

    function create_table() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;

        //Crate custom field table
        $custom_field = $wpdb->prefix . 'pm_custom_fields';

        $sql1 = "CREATE TABLE IF NOT EXISTS {$custom_field} (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `project_id` int(11) DEFAULT NULL,
          `title` varchar(100) DEFAULT NULL,
          `description` text,
          `type` varchar(50) DEFAULT NULL,
          `optional_value` text,
          `order` int(11) DEFAULT 0,
          -- `created_at` timestamp NULL DEFAULT NULL,
          -- `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `project_id` (`project_id`)
        ) DEFAULT CHARSET=utf8";

        dbDelta( $sql1 );

        //Crate task custom field table
        $task_custom_field = $wpdb->prefix . 'pm_task_custom_fields';

        $sql2 = "CREATE TABLE IF NOT EXISTS {$task_custom_field} (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `field_id` int(11) DEFAULT NULL,
          `project_id` int(11) DEFAULT NULL,
          `list_id` int(11) DEFAULT NULL,
          `task_id` int(11) DEFAULT NULL,
          `value` text,
          `color` varchar(30) DEFAULT NULL,
          -- `created_at` timestamp NULL DEFAULT NULL,
          -- `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `field_id` (`field_id`),
          KEY `project_id` (`project_id`),
          KEY `list_id` (`list_id`),
          KEY `task_id` (`task_id`)
        ) DEFAULT CHARSET=utf8";

        dbDelta( $sql2 );
    }
}
