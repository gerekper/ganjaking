<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order-shipping.class.php';

/**
 * Condition: Order Shipping - Method
 *
 * @class RightPress_Condition_Order_Shipping_Method
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Shipping_Method extends RightPress_Condition_Order_Shipping
{

    protected $key      = 'method';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('shipping_methods'),
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

        return esc_html__('Shipping method', 'rightpress');
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

        // Get order shipping methods
        if ($shipping_methods = $this->get_order($params)->get_shipping_methods()) {

            // Get single shipping method
            // TODO: We should introduce multiple shipping method support
            $shipping_method = array_shift($shipping_methods);

            // Return shipping method as both parent shipping method id and combined instance identifier
            return array(
                $shipping_method,
                strtok($shipping_method, ':'),
            );
        }

        return null;
    }





}
