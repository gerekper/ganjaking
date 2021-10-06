<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-purchase-history-quantity.class.php';

/**
 * Condition: Purchase History Quantity - Product Variations
 *
 * @class RightPress_Condition_Purchase_History_Quantity_Product_Variations
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Quantity_Product_Variations extends RightPress_Condition_Purchase_History_Quantity
{

    protected $key          = 'product_variations';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('product_variations'),
        'after'     => array('number'),
    );
    protected $main_field   = 'number';
    protected $position     = 20;

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

        return esc_html__('Quantity purchased - Variations', 'rightpress');
    }





}
