<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product.class.php';

/**
 * Condition: Product - Variation
 *
 * @class RightPress_Condition_Product_Variation
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Variation extends RightPress_Condition_Product
{

    protected $key      = 'variation';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('product_variations'),
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

        return esc_html__('Product variation', 'rightpress');
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

        // Check if item id is set
        if (empty($params['item_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined.');
        }

        // Get variation id
        return !empty($params['child_id']) ? $params['child_id'] : null;
    }





}
