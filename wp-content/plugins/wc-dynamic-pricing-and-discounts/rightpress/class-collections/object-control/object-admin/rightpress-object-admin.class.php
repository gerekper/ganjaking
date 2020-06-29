<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-object-admin-interface.php';

/**
 * RightPress_Object_Admin
 *  > RightPress_WP_Object_Admin
 *     > RightPress_WP_Custom_Object_Admin
 *     > RightPress_WP_Post_Object_Admin
 *        > RightPress_WC_Custom_Order_Object_Admin
 *        > RightPress_WP_Custom_Post_Object_Admin
 *           > RightPress_WP_Log_Entry_Admin
 *  > RightPress_WC_Object_Admin
 *     > RightPress_WC_Product_Object_Admin
 * RightPress_WC_Custom_Order_Admin
 *
 * Note: We have an intermediary class RightPress_WP_Post_Object_Admin here and use it as a parent class for
 * RightPress_WC_Custom_Order_Object_Admin to reuse some of the custom post admin customization methods
 */

/**
 * Object Admin
 *
 * @class RightPress_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object_Admin implements RightPress_Object_Admin_Interface
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get controller instance
     *
     * @access public
     * @return object
     */
    public function get_controller()
    {

        $controller_class = $this->get_controller_class();

        return $controller_class::get_instance();
    }





}
