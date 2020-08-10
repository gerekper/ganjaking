<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Parent configuration item controller class
 *
 * @class RightPress_Item_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Item_Controller
{

    protected $plugin_prefix        = null;
    protected $context              = null;
    protected $items                = null;
    protected $items_are_grouped    = false;

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
     * Get item statically
     *
     * @access public
     * @param string $key
     * @param string $context
     * @return object|bool
     */
    public static function get_item($key, $context = null)
    {
        if ($class = get_called_class()) {
            if ($class !== 'RightPress_Item_Controller') {
                $controller = $class::get_instance();
                return $controller->get_item_internal($key, $context);
            }
        }

        return false;
    }

    /**
     * Get items statically
     *
     * @access public
     * @param string $context
     * @return array
     */
    public static function get_items($context = null)
    {
        if ($class = get_called_class()) {
            if ($class !== 'RightPress_Item_Controller') {
                $controller = $class::get_instance();
                return $controller->get_items_internal($context);
            }
        }

        return array();
    }

    /**
     * Get items for display statically
     *
     * @access public
     * @param string $context
     * @return array
     */
    public static function get_items_for_display($context = null)
    {
        if ($class = get_called_class()) {
            if ($class !== 'RightPress_Item_Controller') {
                $controller = $class::get_instance();
                return $controller->get_items_for_display_internal($context);
            }
        }

        return array();
    }

    /**
     * Check if item exists statically
     *
     * @access public
     * @param string $key
     * @param string $context
     * @return bool
     */
    public static function item_exists($key, $context = null)
    {
        if ($class = get_called_class()) {
            if ($class !== 'RightPress_Item_Controller') {
                $controller = $class::get_instance();
                return $controller->own_item_exists($key, $context);
            }
        }

        return false;
    }

    /**
     * Get item
     *
     * @access public
     * @param string $key
     * @param string $context
     * @return object|bool
     */
    public function get_item_internal($key, $context = null)
    {
        $item = false;

        // Get all items
        $items = self::get_items();

        // Items are grouped
        if ($this->items_are_grouped) {

            // Get group key
            if ($group_key = $this->extract_group_key($key)) {

                // Get item key
                $key = $this->extract_key($key, $group_key);

                // Check if item exists
                if (isset($items[$group_key]['children'][$key])) {
                    $item = $items[$group_key]['children'][$key];
                }
            }
        }
        // Items are not grouped
        else {

            // Check if item exists
            if (isset($items[$key])) {
                $item = $items[$key];
            }
        }

        // Check by context
        if ($item !== false && $context !== null) {

            // Get supported contexts
            $contexts = $item->get_contexts();

            if (empty($contexts) || !in_array($context, $contexts, true)) {
                return false;
            }
        }

        // Return item
        return $item;
    }

    /**
     * Get items
     *
     * @access public
     * @param string $context
     * @return array
     */
    public function get_items_internal($context = null)
    {
        // Register methods if needed
        if ($this->items === null) {
            if ($this->items_are_grouped) {
                $this->items = $this->register_grouped_items();
            }
            else {
                $this->items = $this->register_items();
            }
        }

        // Return items filtered by context
        if ($context !== null) {
            return $this->filter_items_by_context($this->items, $context);
        }

        // Return all items
        return $this->items;
    }

    /**
     * Get items for display
     *
     * @access public
     * @param string $context
     * @return array
     */
    public function get_items_for_display_internal($context = null)
    {
        $result = array();

        // Get items
        $items = self::get_items($context);

        // Wrap ungrouped items into a group
        if (!$this->items_are_grouped) {
            $items = array('group_key' => array('children' => $items));
        }

        // Iterate over items
        foreach ($items as $item_group_key => $item_group) {
            foreach ($item_group['children'] as $item_key => $item) {

                // Item is disabled
                if (!$item->is_enabled()) {
                    continue;
                }

                // Add item group if needed
                if ($this->items_are_grouped && !isset($result[$item_group_key])) {
                    $result[$item_group_key] = array(
                        'label'     => $item_group['label'],
                        'options'  => array(),
                    );
                }

                // Add item
                if ($this->items_are_grouped) {
                    $result[$item_group_key]['options'][$item_key] = $item->get_label();
                }
                else {
                    $result[$item_key] = $item->get_label();
                }
            }
        }

        // Return items for display
        return $result;
    }

    /**
     * Filter items by context
     *
     * @access public
     * @param array $items
     * @param string $context
     * @return array
     */
    public function filter_items_by_context($items, $context)
    {
        // Wrap ungrouped items into a group
        if (!$this->items_are_grouped) {
            $items = array('group_key' => array('children' => $items));
        }

        // Iterate over item groups
        foreach ($items as $item_group_key => $item_group) {

            // Iterate over items
            foreach ($item_group['children'] as $item_key => $item) {

                // Check context
                $contexts = $item->get_contexts();

                if (empty($contexts) || !in_array($context, $contexts, true)) {
                    unset($items[$item_group_key]['children'][$item_key]);
                }
            }

            // Remove empty item groups
            if ($this->items_are_grouped) {
                if (empty($item_group['children'])) {
                    unset($items[$item_group_key]);
                }
            }
        }

        // Return filtered items
        if ($this->items_are_grouped) {
            return $items;
        }
        else {
            return $items['group_key']['children'];
        }
    }

    /**
     * Register and return grouped items
     *
     * @access public
     * @return array
     */
    public function register_grouped_items()
    {
        // Register item groups
        $item_groups = apply_filters($this->get_items_filter_prefix($this->context) . $this->item_key . '_groups', array());

        // Iterate over item groups
        foreach ($item_groups as $item_group_key => $item_group) {

            // Register items for current item group
            $item_groups[$item_group_key]['children'] = $this->register_items($item_group_key);
        }

        // Return item groups with items
        return $item_groups;
    }

    /**
     * Register and return items
     *
     * @access public
     * @param string $group_key
     * @return array
     */
    public function register_items($group_key = null)
    {
        return apply_filters($this->get_items_filter_prefix($this->context, $group_key) . $this->item_key . '_items', array(), $this);
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
     * Check if item exists
     *
     * @access public
     * @param string $key
     * @param string $context
     * @return bool
     */
    public function own_item_exists($key, $context = null)
    {
        return (bool) $this->get_item_internal($key, $context);
    }

    /**
     * Extract group key from combined key
     *
     * @access public
     * @param string $combined_key
     * @return string|bool
     */
    public function extract_group_key($combined_key)
    {
        foreach (self::get_items() as $potential_group_key => $potential_group) {
            if (strpos($combined_key, ($potential_group_key . '__')) === 0) {
                return $potential_group_key;
            }
        }

        return false;
    }

    /**
     * Extract key from combined key
     *
     * @access public
     * @param string $combined_key
     * @param string $group_key
     * @return string|bool
     */
    public function extract_key($combined_key, $group_key = null)
    {
        if ($group_key !== null || ($group_key = $this->extract_group_key($combined_key))) {
            return preg_replace('/^' . $group_key . '__/i', '', $combined_key);
        }

        return false;
    }





}
