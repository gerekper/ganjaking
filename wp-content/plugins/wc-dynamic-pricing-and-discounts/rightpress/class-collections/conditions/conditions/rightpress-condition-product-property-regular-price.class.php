<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Regular Price
 *
 * @class RightPress_Condition_Product_Property_Regular_Price
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Regular_Price extends RightPress_Condition_Product_Property
{

    protected $key      = 'regular_price';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('decimal'),
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

        return esc_html__('Product regular price', 'rightpress');
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

        // Get product regular price
        $price = $this->get_product($params)->get_regular_price('edit');

        // Return price if it is set
        return $price !== '' ? (float) $price : null;
    }





}
