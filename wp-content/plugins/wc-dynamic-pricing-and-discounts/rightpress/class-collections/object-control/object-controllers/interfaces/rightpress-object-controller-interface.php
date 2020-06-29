<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Object Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_Object_Controller_Interface
{

    /**
     * Get plugin public prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_public_prefix();

    /**
     * Get plugin private prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_private_prefix();

    /**
     * Get object name
     *
     * @access public
     * @return string
     */
    public function get_object_name();

    /**
     * Get object class
     *
     * @access public
     * @return string
     */
    public function get_object_class();

    /**
     * Get data store class
     *
     * @access public
     * @return string
     */
    public function get_data_store_class();





}
