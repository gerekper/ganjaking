<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-shipping.class.php';

/**
 * Condition: Shipping - State
 *
 * @class RightPress_Condition_Shipping_State
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Shipping_State extends RightPress_Condition_Shipping
{

    protected $key      = 'state';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('states'),
    );
    protected $position = 20;

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

        return __('Shipping state', 'rightpress');
    }

    /**
     * Get shipping value
     *
     * @access public
     * @param object $customer
     * @return mixed
     */
    public function get_shipping_value($customer)
    {

        $shipping_country = $customer->get_shipping_country();
        $shipping_state = $customer->get_shipping_state();

        if ($shipping_country && $shipping_state) {
            return $shipping_country . '_' . $shipping_state;
        }

        return null;
    }





}
