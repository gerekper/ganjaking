<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Parent class for various configuration items - rule types, conditions, condition methods etc.
 *
 * @class RightPress_Item
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Item
{

    protected $plugin_prefix    = null;
    protected $context          = null;
    protected $group_key        = null;
    protected $group_position   = 10;
    protected $position         = 10;

    protected $controller = null;

    /**
     * Singleton control
     *
     * Child classes must define instance property:
     * protected static $instance = false;
     *
     * @access public
     * @return object
     */
    public static function get_instance()
    {

        // Get called child class
        $class = get_called_class();

        // Instantiate child class if it does not exist yet
        if (!$class::$instance) {
            $class::$instance = new $class;
        }

        // Return instance of child
        return $class::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get plugin prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_prefix()
    {

        // Plugin prefix must be defined by child classes
        if ($this->plugin_prefix === null) {
            RightPress_Help::doing_it_wrong(__METHOD__, 'Plugin prefix must be defined by child class.', '1.0');
            exit;
        }

        return $this->plugin_prefix;
    }

    /**
     * Set up item groups hook
     *
     * Hook name examples:
     * rp_wcdpd_product_pricing_method_groups
     * wccf_condition_groups
     *
     * @access public
     * @return void
     */
    public function hook_group()
    {

        add_filter($this->get_items_filter_prefix($this->context) . $this->item_key . '_groups', array($this, 'register_group'), $this->group_position);
    }

    /**
     * Set up items hook
     *
     * Hook name examples:
     * rp_wcdpd_product_pricing_simple_method_items
     * wccf_cart_condition_items
     *
     * @access public
     * @return void
     */
    public function hook()
    {
        add_filter($this->get_items_filter_prefix($this->context, $this->group_key) . $this->item_key . '_items', array($this, 'register'), $this->position, 2);
    }

    /**
     * Get items filter prefix
     *
     * @access public
     * @param string $context
     * @param string $group_key
     * @return string
     */
    public function get_items_filter_prefix($context = null, $group_key = null)
    {

        // Start with plugin prefix
        $prefix = $this->get_plugin_prefix();

        // Append context
        if ($context !== null) {
            $prefix .= $context . '_';
        }

        // Append group key
        if ($group_key !== null) {
            $prefix .= $group_key . '_';
        }

        return $prefix;
    }

    /**
     * Register item group
     *
     * @access public
     * @param array $item_groups
     * @return array
     */
    public function register_group($item_groups)
    {

        // Add group
        if (!isset($item_groups[$this->group_key])) {
            $item_groups[$this->group_key] = array(
                'label' => $this->get_group_label(),
            );
        }

        return $item_groups;
    }

    /**
     * Register item
     *
     * @access public
     * @param array $items
     * @param object $controller
     * @return array
     */
    public function register($items, $controller)
    {
        // Make sure item with this key does not exist
        if (!isset($items[$this->key])) {

            // Set controller
            $this->controller = $controller;

            // Add item
            $items[$this->key] = $this;
        }

        return $items;
    }

    /**
     * Get contexts
     *
     * @access public
     * @return array|null
     */
    public function get_contexts()
    {
        return isset($this->contexts) ? $this->contexts : null;
    }

    /**
     * Check if item is enabled
     *
     * @access public
     * @return bool
     */
    public function is_enabled()
    {
        return true;
    }

    /**
     * Get controller
     *
     * @access public
     * @return object
     */
    public function get_controller()
    {

        return $this->controller;
    }

    /**
     * Get key
     *
     * @access public
     * @return string
     */
    public function get_key()
    {

        return $this->key;
    }

    /**
     * Get group key
     *
     * @access public
     * @return string
     */
    public function get_group_key()
    {

        return $this->group_key;
    }

    /**
     * Get combined key
     *
     * @access public
     * @return string
     */
    public function get_combined_key()
    {

        return $this->get_group_key() . '__' . $this->get_key();
    }





}
