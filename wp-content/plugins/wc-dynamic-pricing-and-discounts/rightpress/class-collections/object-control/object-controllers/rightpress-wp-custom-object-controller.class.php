<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object-controller.class.php';
require_once 'interfaces/rightpress-wp-custom-object-controller-interface.php';

/**
 * WordPress Custom Object Controller
 *
 * @class RightPress_WP_Custom_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Object_Controller extends RightPress_WP_Object_Controller implements RightPress_WP_Custom_Object_Controller_Interface
{

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

        // Register meta table
        if ($this->supports_metadata()) {
            add_action('init', array($this, 'register_meta_table'), 0);
            add_action('switch_blog', array($this, 'register_meta_table'), 0);
        }
    }

    /**
     * Get status prefix
     *
     * @access public
     * @return string
     */
    public function get_status_prefix()
    {

        return str_replace('_', '-', $this->get_plugin_private_prefix());
    }

    /**
     * Get meta table name
     *
     * @access public
     * @return string
     */
    public function get_meta_table_name()
    {
        return $this->get_object_key() . 'meta';
    }

    /**
     * Register meta table
     *
     * @access public
     * @return void
     */
    public function register_meta_table()
    {
        global $wpdb;

        // Get meta table name
        $table_name = $this->get_meta_table_name();

        // Register meta table
        $wpdb->$table_name  = $wpdb->prefix . $table_name;
        $wpdb->tables[]     = $table_name;
    }



}
