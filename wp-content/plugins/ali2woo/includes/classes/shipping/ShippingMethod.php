<?php

/**
 * Description of ShippingMethod.
 * woocommerce_shipping_init
 * @author Ali2Woo Team
 *
 * @include_action: plugins_loaded
 */

namespace Ali2Woo;

if (class_exists('\WC_Shipping_Method')):
    class ShippingMethod extends \WC_Shipping_Method
    {

        /**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct($instance_id = 0)
        {

            $this->id = 'a2w';

            $this->method_title = 'Ali2Woo';

            $this->method_description = 'Ali2Woo adds a new invisible shipping method to manage shipping costs.';


            //todo: here we can check our option
            $this->enabled = 'yes';
        }

        /**
         * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping($package = array())
        {
        }

    }

    add_action('a2w_install', 'Ali2Woo\reset_wc_shipping_method_count');

    function reset_wc_shipping_method_count()
    {
        delete_transient('wc_shipping_method_count_legacy');
        delete_transient('wc_shipping_method_count');
    }

    if (get_setting('aliship_frontend')) :

        //set woocommerce_enable_shipping_calc to "yes"
        //to show the shipping calculator in the cart
        update_option('woocommerce_enable_shipping_calc', 'yes', 'no');

        add_filter('woocommerce_shipping_methods', 'Ali2Woo\add_shipping_method');

        function add_shipping_method($methods)
        {
            //we need at least one shipping method to show shipping calculator in the cart
            $methods[] = 'Ali2Woo\ShippingMethod';
            return $methods;
        }

    endif;
endif;