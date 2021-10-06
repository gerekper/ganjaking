<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-purchase-history.class.php';

/**
 * Condition: Purchase History - Products
 *
 * @class RightPress_Condition_Purchase_History_Products
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Products extends RightPress_Condition_Purchase_History
{

    protected $key          = 'products';
    protected $method       = 'list_advanced';
    protected $fields       = array(
        'before'    => array('timeframe_span'),
        'after'     => array('products'),
    );
    protected $main_field   = 'products';
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

        return esc_html__('Purchased - Products', 'rightpress');
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

        return RightPress_Help::get_wc_order_product_ids($order_id);
    }





}
