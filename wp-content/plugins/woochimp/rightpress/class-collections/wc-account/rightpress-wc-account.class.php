<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Account Controller
 *
 * @class RightPress_WC_Account
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Account
{

    protected $endpoints        = array();
    protected $menu_priority    = 10;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Register endpoints
        add_action('init', array($this, 'register_endpoints'), 1);

        // Register query vars
        add_filter('query_vars', array($this, 'register_query_vars'));

        // Maybe flush rewrite rules
        add_action('wp_loaded', array($this, 'maybe_flush_rewrite_rules'));

        // Add rewrite rules
        add_filter('rewrite_rules_array', array($this, 'add_rewrite_rules'));

        // Add menu items
        add_filter('woocommerce_account_menu_items', array($this, 'add_menu_items'), $this->menu_priority);

        // Maybe change endpoint page title
        add_filter('the_title', array($this, 'maybe_change_endpoint_page_title'));
    }

    /**
     * Register endpoints
     *
     * @access public
     * @return void
     */
    public function register_endpoints()
    {

        // Iterate over endpoints
        foreach ($this->endpoints as $endpoint) {

            // Register rewrite endpoint
            add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);

            // Register callbacks
            $callback = array($this, ('wc_account_endpoint_' . str_replace('-', '_', $endpoint)));
            add_action(('woocommerce_account_' . $endpoint . '_endpoint'), $callback);
        }
    }

    /**
     * Register query vars
     *
     * @access public
     * @param array $vars
     * @return array
     */
    public function register_query_vars($vars)
    {

        foreach ($this->endpoints as $endpoint) {
            $vars[] = $endpoint;
        }

        return $vars;
    }

    /**
     * Maybe flush rewrite rules if ours are not present
     *
     * @access public
     * @return void
     */
    public function maybe_flush_rewrite_rules()
    {

        $rules = get_option('rewrite_rules');

        foreach ($this->endpoints as $endpoint) {
            if (!isset($rules['(.?.+?)/' . $endpoint . '(/(.*))?/?$'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                break;
            }
        }
    }

    /**
     * Add rewrite rules
     *
     * @access public
     * @param array $rules
     * @return array
     */
    public function add_rewrite_rules($rules)
    {

        foreach ($this->endpoints as $endpoint) {
            $rules['(.?.+?)/' . $endpoint . '(/(.*))?/?$'] = 'index.php?pagename=$matches[1]&' . $endpoint . '=$matches[3]';
        }

        return $rules;
    }

    /**
     * Add account menu items
     *
     * @access public
     * @param array $menu_items
     * @return array
     */
    public function add_menu_items($menu_items)
    {

        // Iterate over menu items
        foreach ($this->get_menu_items() as $menu_item) {

            // Insert after Orders
            if (isset($menu_items['orders'])) {
                $menu_items = RightPress_Help::insert_to_array_after_key($menu_items, 'orders', $menu_item);
            }
            // Insert after Dashboard
            else if (isset($menu_items['dashboard'])) {
                $menu_items = RightPress_Help::insert_to_array_after_key($menu_items, 'dashboard', $menu_item);
            }
            // Insert at the beginning of the list
            else {
                $menu_items = array_merge($menu_item, $menu_items);
            }
        }

        return $menu_items;
    }

    /**
     * Get menu items
     *
     * @access public
     * @return array
     */
    public function get_menu_items()
    {

        return array();
    }

    /**
     * Maybe change endpoint page title
     *
     * @access public
     * @param string $title
     * @return string
     */
    public function maybe_change_endpoint_page_title($title)
    {

        global $wp_query;

        // Check if we are on the frontend account page
        if (!is_null($wp_query) && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {

            // Iterate over endpoints
            foreach ($this->endpoints as $endpoint) {

                // Check if query var is set
                if (isset($wp_query->query_vars[$endpoint])) {

                    // Get endpoint page title
                    if ($endpoint_page_title = $this->get_endpoint_page_title($endpoint, $wp_query->query_vars[$endpoint])) {

                        // Set new title
                        $title = $endpoint_page_title;

                        // Do not proceed to other endpoints
                        break;
                    }
                }
            }

            // Remove filter
            remove_filter('the_title', array($this, 'maybe_change_endpoint_title'));
        }

        // Return potentially changed title
        return $title;
    }

    /**
     * Get endpoint page title
     *
     * @access public
     * @param string $endpoint
     * @param string $var_value
     * @return string|null
     */
    public function get_endpoint_page_title($endpoint, $var_value)
    {

        return null;
    }





}
