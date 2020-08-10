<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-object-controller-interface.php';

/**
 * RightPress_Object_Controller
 *  > RightPress_WP_Object_Controller
 *     > RightPress_WP_Custom_Object_Controller
 *     > RightPress_WP_Custom_Post_Object_Controller
 *        > RightPress_WP_Log_Entry_Controller
 *  > RightPress_WC_Object_Controller
 *     > RightPress_WC_Product_Object_Controller
 *     > RightPress_WC_Custom_Order_Object_Controller
 * RightPress_WC_Custom_Order_Controller
 */

/**
 * Object Controller
 *
 * @class RightPress_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object_Controller implements RightPress_Object_Controller_Interface
{

    // Data store reference
    protected $data_store = null;

    // Properties with default values
    protected $is_chronologic       = false;
    protected $is_editable          = false;
    protected $supports_comments    = false;
    protected $supports_metadata    = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Load data store
        $data_store_class = $this->get_data_store_class();
        $this->data_store = new $data_store_class;
    }

    /**
     * Prefix public hook
     *
     * @access public
     * @param string $hook
     * @return string
     */
    public function prefix_public_hook($hook)
    {

        return $this->get_plugin_public_prefix() . $this->get_object_name() . '_' . $hook;
    }

    /**
     * Prefix private hook
     *
     * @access public
     * @param string $hook
     * @return string
     */
    public function prefix_private_hook($hook)
    {

        return $this->get_object_key() . '_' . $hook;
    }

    /**
     * Prefix error code
     *
     * @access public
     * @param string $error_code
     * @return string
     */
    public function prefix_error_code($error_code)
    {

        return $this->get_object_key() . '_' . $error_code;
    }

    /**
     * Prefix status
     *
     * @access public
     * @param string $status
     * @return string
     */
    public function prefix_status($status)
    {

        return $this->get_status_prefix() . $status;
    }

    /**
     * Remove prefix from status
     *
     * @access public
     * @param string $status
     * @return string
     */
    public function unprefix_status($status)
    {

        $prefix = $this->get_status_prefix();

        if (substr($status, 0, strlen($prefix)) === $prefix) {
            $status = substr($status, strlen($prefix));
        }

        return $status;
    }

    /**
     * Get status prefix
     *
     * @access public
     * @return string
     */
    public function get_status_prefix()
    {

        return '';
    }

    /**
     * Prefix database key
     *
     * @access public
     * @param string $key
     * @return string
     */
    public function prefix_database_key($key)
    {

        return $this->get_database_key_prefix() . $key;
    }

    /**
     * Get database key prefix
     *
     * Used when storing plugin object specific values as post meta
     * to avoid conflicts with other plugins using the same meta keys
     *
     * @access public
     * @return string
     */
    public function get_database_key_prefix()
    {

        return '_' . substr($this->get_plugin_private_prefix(), 0, -1) . ':';
    }

    /**
     * Get object key
     *
     * @access public
     * @param string $prefix
     * @return string
     */
    public function get_object_key($prefix = '')
    {

        return $prefix . $this->get_plugin_private_prefix() . $this->get_object_name();
    }

    /**
     * Check if object is chronologic
     *
     * @access public
     * @return bool
     */
    public function is_chronologic()
    {

        return $this->is_chronologic;
    }

    /**
     * Check if object is editable
     *
     * @access public
     * @return bool
     */
    public function is_editable()
    {

        return $this->is_editable;
    }

    /**
     * Check if object supports comments
     *
     * @access public
     * @return bool
     */
    public function supports_comments()
    {

        return $this->supports_comments;
    }

    /**
     * Check if object supports meta data
     *
     * @access public
     * @return bool
     */
    public function supports_metadata()
    {

        return $this->supports_metadata;
    }

    /**
     * Get data store
     *
     * @access public
     * @return object
     */
    public function get_data_store()
    {

        return $this->data_store;
    }

    /**
     * Get object
     *
     * @access public
     * @param int|object $object
     * @return object|bool
     */
    public function get_object($object)
    {

        // Attempt to load object
        try {

            // Get object class
            $object_class = $this->get_object_class();

            // Load object
            return new $object_class($object, $this->data_store, $this);
        }
        // Unable to load object
        catch (Exception $e) {

            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Create new object
     *
     * @access public
     * @return object|bool
     */
    public function create_new_object()
    {

        return $this->get_object(0);
    }





}
