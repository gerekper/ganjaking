<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order-items.class.php';

/**
 * Condition: Order Items - Products
 *
 * @class RightPress_Condition_Order_Items_Products
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Items_Products extends RightPress_Condition_Order_Items
{

    protected $key      = 'products';
    protected $method   = 'list_advanced';
    protected $fields   = array(
        'after' => array('products'),
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

        return esc_html__('Order items - Products', 'rightpress');
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

        // Check if items are defined
        if (empty($params['item_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Order is not defined.');
        }

        // Get order product ids
        return RightPress_Help::get_wc_order_product_ids($params['item_id']);
    }





}
