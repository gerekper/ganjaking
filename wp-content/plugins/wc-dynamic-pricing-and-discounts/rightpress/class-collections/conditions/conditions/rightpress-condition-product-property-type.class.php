<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Type
 *
 * @class RightPress_Condition_Product_Property_Type
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Type extends RightPress_Condition_Product_Property
{

    protected $key      = 'type';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('product_types'),
    );
    protected $position = 50;

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

        return esc_html__('Product type', 'rightpress');
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

        // Return product type
        return $this->get_product($params, true)->get_type();
    }





}
