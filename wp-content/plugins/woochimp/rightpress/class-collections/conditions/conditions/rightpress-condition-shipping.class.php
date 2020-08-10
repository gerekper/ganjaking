<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Shipping
 *
 * @class RightPress_Condition_Shipping
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Shipping extends RightPress_Condition
{

    protected $group_key        = 'shipping';
    protected $group_position   = 210;
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

        return __('Shipping Address', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        $value = null;

        // Attempt to get value
        if (RightPress_Help::is_request('frontend') && is_object(WC()->customer)) {
            $value = $this->get_shipping_value(WC()->customer);
        }

        return !RightPress_Help::is_empty($value) ? $value : null;
    }





}
