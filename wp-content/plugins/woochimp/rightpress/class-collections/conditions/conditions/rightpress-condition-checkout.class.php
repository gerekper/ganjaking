<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Checkout
 *
 * @class RightPress_Condition_Checkout
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Checkout extends RightPress_Condition
{

    protected $group_key        = 'checkout';
    protected $group_position   = 200;
    protected $is_cart          = true;

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

        return __('Checkout', 'rightpress');
    }





}
