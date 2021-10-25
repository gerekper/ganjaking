<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Customer
 *
 * @class RightPress_Condition_Customer
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer extends RightPress_Condition
{

    protected $group_key        = 'customer';
    protected $group_position   = 150;
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

        return __('Customer', 'rightpress');
    }





}
