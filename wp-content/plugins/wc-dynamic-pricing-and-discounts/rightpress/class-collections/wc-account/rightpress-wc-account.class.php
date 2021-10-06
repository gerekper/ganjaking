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

        // Endpoint setup
        add_action('init', array($this, 'endpoint_setup'), 1);

        // Add menu items
        add_filter('woocommerce_account_menu_items', array($this, 'add_menu_items'), $this->menu_priority);

        // Register query vars
        add_filter('woocommerce_get_query_vars', array($this, 'register_wc_query_vars'));

        // Maybe flush rewrite rules
        add_action('wp_loaded', array($this, 'maybe_flush_rewrite_rules'));

        // Add rewrite rules
        add_filter('rewrite_rules_array', array($this, 'add_rewrite_rules'));
    }

    /**
     * Endpoint setup
     *
     * @access public
     * @return void
     */
    public function endpoint_setup()
    {

        // Iterate over endpoints
        foreach ($this->endpoints as $endpoint) {

            // Rewrite endpoint
            add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);

            // Content callback
            $callback = array($this, ('wc_account_endpoint_' . str_replace('-', '_', $endpoint)));
            add_action("woocommerce_account_{$endpoint}_endpoint", $callback, 0);

            // Title callback
            add_filter("woocommerce_endpoint_{$endpoint}_title", array($this, 'change_endpoint_page_title'), 0, 2);
        }
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
     * Register WooCommerce query vars
     *
     * @access public
     * @param array $vars
     * @return array
     */
    public function register_wc_query_vars($vars)
    {

        foreach ($this->endpoints as $endpoint) {
            $vars[$endpoint] = $endpoint;
        }

        return $vars;
    }

    /**
     * Change endpoint page title
     *
     * @access public
     * @param string $title
     * @param string $endpoint
     * @return string
     */
    public function change_endpoint_page_title($title, $endpoint)
    {

        global $wp;

        // Get var value
        $var_value = isset($wp->query_vars[$endpoint]) ? $wp->query_vars[$endpoint] : null;

        // Get endpoint page title
        $title = $this->get_endpoint_page_title($endpoint, $var_value);

        // Return title
        return $title;
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
