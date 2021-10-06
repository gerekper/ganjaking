<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-custom-post-object-controller.class.php';

/**
 * WordPress Custom Post Type Based Log Entry Controller
 *
 * @class RightPress_WP_Log_Entry_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Log_Entry_Controller extends RightPress_WP_Custom_Post_Object_Controller
{

    protected $is_chronologic = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct();
    }

    /**
     * Get status list
     *
     * @access public
     * @return array
     */
    public function get_status_list()
    {

        return array(

            'processing' => array(
                'label'             => esc_attr_x('Processing', 'Log entry status', 'rightpress'),
                'label_count'       => _n_noop('Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'rightpress'),
                'system_change_to'  => array('success', 'warning', 'failed', 'error'),
            ),

            'success' => array(
                'label'             => esc_attr_x('Success', 'Log entry status', 'rightpress'),
                'label_count'       => _n_noop('Success <span class="count">(%s)</span>', 'Success <span class="count">(%s)</span>', 'rightpress'),
                'system_change_to'  => array('warning', 'failed', 'error'),
            ),

            'warning' => array(
                'label'             => esc_attr_x('Warning', 'Log entry status', 'rightpress'),
                'label_count'       => _n_noop('Warning <span class="count">(%s)</span>', 'Warning <span class="count">(%s)</span>', 'rightpress'),
                'system_change_to'  => array('failed', 'error'),
            ),

            'failed' => array(
                'label'             => esc_attr_x('Failed', 'Log entry status', 'rightpress'),
                'label_count'       => _n_noop('Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'rightpress'),
                'system_change_to'  => array('error'),
            ),

            'error' => array(
                'label'             => esc_attr_x('Error', 'Log entry status', 'rightpress'),
                'label_count'       => _n_noop('Error <span class="count">(%s)</span>', 'Error <span class="count">(%s)</span>', 'rightpress'),
                'system_change_to'  => array(),
            ),
        );
    }

    /**
     * Get default status
     *
     * @access public
     * @return string
     */
    public function get_default_status()
    {

        return 'processing';
    }

    /**
     * Get event types
     *
     * Returns ungrouped taxonomy terms
     *
     * @access public
     * @return array
     */
    public static function get_event_types()
    {

        $event_types = array();

        // Get called class
        $called_class = get_called_class();

        // Get controller instance
        $controller = $called_class::get_instance();

        // Get taxonomies with terms
        $taxonomies_with_terms = $controller->get_taxonomies_with_terms();

        // Iterate over term groups
        foreach ($taxonomies_with_terms['event_type']['grouped_terms'] as $term_group) {
            $event_types = array_merge($event_types, $term_group['terms']);
        }

        return $event_types;
    }

    /**
     * Get post type params
     *
     * @access public
     * @return array
     */
    public function get_post_type_params()
    {

        return array(
            'labels'            => array(
                'name'                  => esc_html__('Activity Log', 'rightpress'),
                'singular_name'         => esc_html__('Activity Log', 'rightpress'),
                'add_new'               => esc_html__('Add Log Entry', 'rightpress'),
                'add_new_item'          => esc_html__('Add New Log Entry', 'rightpress'),
                'edit_item'             => esc_html__('Edit Log Entry', 'rightpress'),
                'new_item'              => esc_html__('New Log Entry', 'rightpress'),
                'all_items'             => esc_html__('Activity Log', 'rightpress'),
                'view_item'             => esc_html__('View Log Entry', 'rightpress'),
                'search_items'          => esc_html__('Search Activity Log', 'rightpress'),
                'not_found'             => esc_html__('No Log Entries Found', 'rightpress'),
                'not_found_in_trash'    => esc_html__('No Log Entries Found In Trash', 'rightpress'),
                'parent_item_colon'     => '',
                'menu_name'             => esc_html__('Activity Log', 'rightpress'),
            ),
            'description'       => esc_html__('Activity Log', 'rightpress'),
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => ('edit.php?post_type=' . $this->get_main_post_type()),
            'menu_position'     => 59,
            'capability_type'   => $this->get_data_store()->get_capability_type(),
            'map_meta_cap'      => true,
            'capabilities'      => array(
                'create_posts' => 'do_not_allow',
            ),
            'supports'          => array('title'),
        );
    }

    /**
     * Create log entry and save it to database
     *
     * Note: This starts the logging process which must be ended explicitly by calling $log_entry->end_logging()
     *
     * @access public
     * @param array $properties
     * @param object $object        Object to set the log entry instance to; must implement method set_log_entry()
     * @return RightPress_WP_Log_Entry_Controller
     */
    public static function create_log_entry($properties, $object = null)
    {

        // Get called class
        $called_class = get_called_class();

        // Get controller instance
        $controller = $called_class::get_instance();

        // Create new object
        $log_entry = $controller->create_new_object();

        // Start logging
        $log_entry->start_logging();

        // Set properties
        $log_entry->set_properties($properties);

        // Save to database
        $log_entry->save();

        // Optionally set log entry to object
        if (is_a($object, 'RightPress_Object')) {
            $object->set_log_entry($log_entry);
        }

        // Return
        return $log_entry;
    }

    /**
     * Add log entry
     *
     * Wrapper for create_log_entry() when access to log entry object is not needed
     *
     * @access public
     * @param array $properties
     * @return void
     */
    public static function add_log_entry($properties)
    {

        // Get called class
        $called_class = get_called_class();

        // Create log entry
        $log_entry = $called_class::create_log_entry($properties);

        // End logging
        $log_entry->end_logging();
    }





}
