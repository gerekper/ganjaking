<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - On Sale
 *
 * @class RightPress_Condition_Product_Property_On_Sale
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_On_Sale extends RightPress_Condition_Product_Property
{

    protected $key      = 'on_sale';
    protected $method   = 'boolean';
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

        return esc_html__('Product is on sale', 'rightpress');
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

        // Check if product has sale price set
        return $this->get_product($params)->get_sale_price('edit') !== '';
    }





}
