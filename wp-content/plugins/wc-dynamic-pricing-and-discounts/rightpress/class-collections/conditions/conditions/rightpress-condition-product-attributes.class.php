<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product.class.php';

/**
 * Condition: Product - Attributes
 *
 * @class RightPress_Condition_Product_Attributes
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Attributes extends RightPress_Condition_Product
{

    protected $key      = 'attributes';
    protected $method   = 'list_advanced';
    protected $fields   = array(
        'after' => array('product_attributes'),
    );
    protected $position = 40;

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

        return esc_html__('Product attributes', 'rightpress');
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

        // No parent or child ids set
        if (empty($params['item_id']) && empty($params['child_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined.');
        }

        // Get selected variation attributes
        $variation_attributes = !empty($params['variation_attributes']) ? $params['variation_attributes'] : array();

        // Select correct product id to get attributes for
        $product_id = !empty($params['child_id']) ? $params['child_id'] : $params['item_id'];

        // Return product attributes
        return RightPress_Help::get_wc_product_attribute_ids($product_id, $variation_attributes);
    }





}
