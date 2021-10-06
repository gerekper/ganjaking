<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product.class.php';

/**
 * Condition: Product - Product
 *
 * @class RightPress_Condition_Product_Product
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Product extends RightPress_Condition_Product
{

    protected $key      = 'product';
    protected $method   = 'list';
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

        return esc_html__('Product', 'rightpress');
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

        // Check if item id is defined
        if (empty($params['item_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined.');
        }

        // Return product id
        return $params['item_id'];
    }





}
