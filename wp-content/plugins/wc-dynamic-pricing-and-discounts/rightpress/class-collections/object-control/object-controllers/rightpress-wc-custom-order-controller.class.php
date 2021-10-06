<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-wc-custom-order-controller-interface.php';

/**
 * WooCommerce Custom Order Controller
 *
 * @class RightPress_WC_Custom_Order_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order_Controller implements RightPress_WC_Custom_Order_Controller_Interface
{

    // Data store reference
    protected $data_store = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Load data store
        $data_store_class = $this->get_data_store_class();
        $this->data_store = new $data_store_class;

        // Register custom order type
        add_action('init', array($this, 'register_custom_wc_order_type'), 6);

        // Register custom order statuses
        add_action('init', array($this, 'register_custom_wc_order_statuses'), 8);
    }

    /**
     * Get data store
     *
     * @access public
     * @return object
     */
    public function get_data_store()
    {

        return $this->data_store;
    }

    /**
     * Register custom WooCommerce order type
     *
     * @access public
     * @return void
     */
    public function register_custom_wc_order_type()
    {

        // Register order type
        wc_register_order_type($this->get_post_type(), array(

            // Post type arguments
            'description'       => $this->get_post_labels()['name'],
            'menu_position'     => $this->get_menu_position(),
            'menu_icon'         => $this->get_menu_icon(),
            'capability_type'   => $this->get_data_store()->get_capability_type(),
            'map_meta_cap'      => true,
            'public'            => false,
            'show_ui'           => true,
            'hierarchical'      => false,
            'supports'          => false,
            'rewrite'           => false,
            'labels'            => $this->get_post_labels(),

            // WooCommerce order arguments
            'exclude_from_orders_screen'        => true,
            'add_order_meta_boxes'              => true,
            'exclude_from_order_count'          => true,
            'exclude_from_order_views'          => true,
            'exclude_from_order_webhooks'       => true,
            'exclude_from_order_reports'        => true,
            'exclude_from_order_sales_reports'  => true,
            'class_name'                        => $this->get_object_class(),
        ));

        // Register data store for custom order type
        add_filter('woocommerce_data_stores', function ($data_stores) {

            $key = str_replace('_', '-', $this->get_post_type());

            $data_stores[$key]  = $this->get_data_store_class();

            return $data_stores;
        });
    }

    /**
     * Register custom order statuses
     *
     * @access public
     * @return void
     */
    public function register_custom_wc_order_statuses()
    {

        // Get regular order statuses
        $order_statuses = wc_get_order_statuses();

        // Get custom order statuses
        $statuses = $this->get_custom_order_statuses();

        // Iterate over custom order statuses
        foreach ($statuses as $status => $status_data) {

            // Prefix status
            $prefixed_status = 'wc-' . $status;

            // Do not register regular order statuses
            if (!isset($order_statuses[$prefixed_status])) {

                // Register custom order status
                register_post_status($prefixed_status, array(
                    'label'                     => $status_data['label'],
                    'public'                    => false,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => $status_data['label_count'],
                ));
            }
        }
    }

    /**
     * Get WooCommerce valid order statuses
     *
     * @access public
     * @return array
     */
    public static function get_wc_valid_order_statuses()
    {

        $class      = get_called_class();
        $instance   = $class::get_instance();

        return RightPress_Help::prefix(array_keys($instance->get_custom_order_statuses()), 'wc-');
    }

    /**
     * Get custom order statuses
     *
     * Plugins may override this method to provide a list of custom statuses
     *
     * @access public
     * @return array
     */
    public function get_custom_order_statuses()
    {

        return array();
    }





}
