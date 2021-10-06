<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Shipping Class
 *
 * @class RightPress_Condition_Product_Property_Shipping_Class
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Shipping_Class extends RightPress_Condition_Product_Property
{

    protected $key      = 'shipping_class';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('shipping_classes'),
    );
    protected $position = 60;

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

        return esc_html__('Product shipping class', 'rightpress');
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

        // Check if at least one id is set
        if (empty($params['item_id']) && empty($params['child_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined.');
        }

        // Get shipping class
        $shipping_class = RightPress_Help::get_wc_product_shipping_class_id($params['item_id'], (!empty($params['child_id']) ? $params['child_id'] : null));

        // Return shipping class
        return $shipping_class ? $shipping_class : null;
    }





}
