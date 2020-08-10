<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object.class.php';

/**
 * WordPress Custom Post Object Class
 *
 * @class RightPress_WP_Custom_Post_Object
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Post_Object extends RightPress_WP_Object
{

    /**
     * Constructor
     *
     * @access public
     * @param mixed $object
     * @param object $data_store
     * @param object $controller
     * @return void
     */
    public function __construct($object, $data_store, $controller)
    {

        // Call parent constructor
        parent::__construct($object, $data_store, $controller);
    }





}
