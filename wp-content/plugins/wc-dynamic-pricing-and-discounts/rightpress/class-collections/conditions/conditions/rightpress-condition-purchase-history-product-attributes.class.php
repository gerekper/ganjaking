<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-purchase-history.class.php';

/**
 * Condition: Purchase History - Product Attributes
 *
 * @class RightPress_Condition_Purchase_History_Product_Attributes
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Product_Attributes extends RightPress_Condition_Purchase_History
{

    protected $key          = 'product_attributes';
    protected $method       = 'list_advanced';
    protected $fields       = array(
        'before'    => array('timeframe_span'),
        'after'     => array('product_attributes'),
    );
    protected $main_field   = 'product_attributes';
    protected $position     = 40;

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

        return esc_html__('Purchased - Attributes', 'rightpress');
    }

    /**
     * Get value by order
     *
     * @access protected
     * @param int $order_id
     * @return array
     */
    protected function get_purchase_history_value_by_order($order_id)
    {

        return RightPress_Help::get_wc_order_product_attribute_ids($order_id);
    }





}
