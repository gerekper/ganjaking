<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order-items.class.php';

/**
 * Condition: Order Items - Product Categories
 *
 * @class RightPress_Condition_Order_Items_Product_Categories
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Items_Product_Categories extends RightPress_Condition_Order_Items
{

    protected $key      = 'product_categories';
    protected $method   = 'list_advanced';
    protected $fields   = array(
        'after' => array('product_categories'),
    );
    protected $position = 30;

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

        return esc_html__('Order items - Categories', 'rightpress');
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

        // Get order product variation ids
        return RightPress_Help::get_wc_order_product_category_ids($params['item_id']);
    }





}
