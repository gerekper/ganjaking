<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-shipping.class.php';

/**
 * Condition: Shipping - Zone
 *
 * @class RightPress_Condition_Shipping_Zone
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Shipping_Zone extends RightPress_Condition_Shipping
{

    protected $key      = 'zone';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('shipping_zones'),
    );
    protected $position = 40;

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

        return __('Shipping zone', 'rightpress');
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

        // Get shipping zone
        $zone = wc_get_shipping_zone(array(
            'destination' => array(
                'country'   => $customer->get_shipping_country(),
                'state'     => $customer->get_shipping_state(),
                'postcode'  => $customer->get_shipping_postcode(),
            ),
        ));

        // Return shipping zone id
        return $zone ? (string) $zone->get_id() : null;
    }





}
