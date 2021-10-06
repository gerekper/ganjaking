<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Sale Price
 *
 * @class RightPress_Condition_Product_Property_Sale_Price
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Sale_Price extends RightPress_Condition_Product_Property
{

    protected $key      = 'sale_price';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('decimal'),
    );
    protected $position = 15;

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

        return esc_html__('Product sale price', 'rightpress');
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

        // Get product sale price
        $price = $this->get_product($params)->get_sale_price('edit');

        // Return price if it is set
        return $price !== '' ? (float) $price : null;
    }





}
