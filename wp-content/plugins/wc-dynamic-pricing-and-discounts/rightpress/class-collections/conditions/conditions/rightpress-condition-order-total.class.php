<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order.class.php';

/**
 * Condition: Order - Total
 *
 * @class RightPress_Condition_Order_Total
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Total extends RightPress_Condition_Order
{

    protected $key      = 'total';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('decimal'),
    );
    protected $position = 10;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return esc_html__('Order total', 'rightpress');
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

        // Get order total
        return $this->get_order($params)->get_total();
    }





}
