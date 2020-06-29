<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-shipping.class.php';

/**
 * Condition: Shipping - Country
 *
 * @class RightPress_Condition_Shipping_Country
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Shipping_Country extends RightPress_Condition_Shipping
{

    protected $key      = 'country';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('countries'),
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

        return __('Shipping country', 'rightpress');
    }

    /**
     * Get shipping value
     *
     * @access public
     * @param object $customer
     * @return mixed
     */
    public function get_shipping_value($customer)
    {

        return $customer->get_shipping_country();
    }





}
