<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-purchase-history-value.class.php';

/**
 * Condition: Purchase History Value - Product Variations
 *
 * @class RightPress_Condition_Purchase_History_Value_Product_Variations
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Value_Product_Variations extends RightPress_Condition_Purchase_History_Value
{

    protected $key          = 'product_variations';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('product_variations'),
        'after'     => array('decimal'),
    );
    protected $main_field   = 'decimal';
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

        return esc_html__('Value purchased - Variations', 'rightpress');
    }





}
