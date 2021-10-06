<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-purchase-history-quantity.class.php';

/**
 * Condition: Purchase History Quantity - Products
 *
 * @class RightPress_Condition_Purchase_History_Quantity_Products
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Quantity_Products extends RightPress_Condition_Purchase_History_Quantity
{

    protected $key          = 'products';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('products'),
        'after'     => array('number'),
    );
    protected $main_field   = 'number';
    protected $position     = 10;

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

        return esc_html__('Quantity purchased - Products', 'rightpress');
    }





}
