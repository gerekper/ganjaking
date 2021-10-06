<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-checkout.class.php';

/**
 * Condition: Checkout - Shipping Method
 *
 * @class RightPress_Condition_Checkout_Shipping_Method
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Checkout_Shipping_Method extends RightPress_Condition_Checkout
{

    protected $key      = 'shipping_method';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('shipping_methods'),
    );
    protected $position = 20;

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

        return esc_html__('Shipping method', 'rightpress');
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

        // Get WooCommerce session
        if ($session = RightPress_Help::get_wc_session()) {

            // Get chosen shipping methods
            if ($shipping_methods = $session->get('chosen_shipping_methods')) {

                $result = array();

                // Get single shipping method
                // TODO: We should introduce multiple shipping method support
                $shipping_method = array_shift($shipping_methods);

                // Add full shipping method
                $result[] = $shipping_method;

                // Add parent shipping method
                $result[] = strtok($shipping_method, ':');

                // Add shipping method instance if extra options are available in format method:1-2 (WCDPD issue #716)
                if (preg_match('/^.+\:\d+\-\d+$/', $shipping_method)) {
                    $result[] = substr($shipping_method,0, strrpos($shipping_method,'-'));
                }

                // Return value
                return $result;
            }
        }

        return null;
    }





}
