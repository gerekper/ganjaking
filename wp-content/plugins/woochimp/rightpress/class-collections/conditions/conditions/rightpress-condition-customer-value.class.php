<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Customer Value
 *
 * @class RightPress_Condition_Customer_Value
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Value extends RightPress_Condition
{

    protected $group_key        = 'customer_value';
    protected $group_position   = 160;
    protected $is_customer      = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {

        return __('Customer - Value', 'rightpress');
    }





}
