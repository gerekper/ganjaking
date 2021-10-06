<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Stock Quantity
 *
 * @class RightPress_Condition_Product_Property_Stock_Quantity
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Stock_Quantity extends RightPress_Condition_Product_Property
{

    protected $key      = 'stock_quantity';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('number'),
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

        return esc_html__('Product stock quantity', 'rightpress');
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

        // Get product stock quantity
        return $this->get_product($params)->get_stock_quantity();
    }





}
