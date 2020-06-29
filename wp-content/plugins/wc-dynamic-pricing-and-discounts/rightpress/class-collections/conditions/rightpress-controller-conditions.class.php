<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Conditions controller
 *
 * @class RightPress_Controller_Conditions
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Controller_Conditions extends RightPress_Item_Controller
{

    protected $item_key = 'condition';

    protected $items_are_grouped = true;

    protected $condition_fields_controller      = null;
    protected $condition_methods_controller     = null;

    protected $condition_check_cache = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Condition fields and condition methods controllers must be assigned by now
        if (!$this->condition_fields_controller || !$this->condition_methods_controller) {
            RightPress_Help::doing_it_wrong(__METHOD__, ('Condition fields controller and condition methods controller must be assigned.'), '1.0');
            exit;
        }

        // Call parent constructor
        parent::__construct();

        // Generate custom taxonomy conditions
        add_action('wp_loaded', array($this, 'generate_custom_taxonomy_conditions'));

        // Load multiselect options
        add_action(('wp_ajax_' . $this->get_plugin_prefix() . 'load_multiselect_options'), array($this, 'ajax_load_multiselect_options'));
    }

    /**
     * =================================================================================================================
     * EXTERNAL METHODS - CONDITION CHECKS
     * =================================================================================================================
     */

    /**
     * Check if conditions are matched
     *
     * Alias for internal method
     *
     * @access public
     * @param array $conditions
     * @param array $params
     * @return bool
     */
    public static function conditions_are_matched($conditions, $params = array())
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->conditions_are_matched_internal($conditions, $params);
    }

    /**
     * Check if single condition is matched
     *
     * Alias for internal method
     *
     * @access public
     * @param array $params
     * @return bool
     */
    public static function condition_is_matched($params = array())
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->condition_is_matched_internal($params);
    }

    /**
     * Check if object conditions are matched
     *
     * Alias for internal method
     *
     * @access public
     * @param array $object
     * @param array $params
     * @return bool
     */
    public static function object_conditions_are_matched($object, $params = array())
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->object_conditions_are_matched_internal($object, $params);
    }

    /**
     * Filter objects by conditions
     *
     * Alias for internal method
     *
     * @access public
     * @param array $objects
     * @param array $params
     * @return array
     */
    public static function filter_objects($objects, $params = array())
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->filter_objects_internal($objects, $params);
    }

    /**
     * =================================================================================================================
     * EXTERNAL METHODS - CONDITION CHECKS SPECIFIC TO CART ITEMS AND PRODUCTS
     * =================================================================================================================
     */

    /**
     * Check if cart item matches product conditions
     *
     * Alias for internal method
     *
     * @access public
     * @param array $cart_item
     * @param array $conditions
     * @return bool
     */
    public static function cart_item_matches_product_conditions($cart_item, $conditions)
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->cart_item_matches_product_conditions_internal($cart_item, $conditions);
    }

    /**
     * Get cart items filtered by product conditions
     *
     * Alias for internal method
     *
     * Leaves those cart items for which all product conditions match
     * Ignores non-product conditions
     *
     * @access public
     * @param array $conditions
     * @return array
     */
    public static function get_cart_items_by_product_conditions($conditions = array())
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->get_cart_items_by_product_conditions_internal($conditions);
    }

    /**
     * =================================================================================================================
     * EXTERNAL METHODS - BACKEND CONDITION MANAGEMENT
     * =================================================================================================================
     */

    /**
     * Display condition fields
     *
     * @access public
     * @param string $context
     * @param string $combined_condition_key
     * @param string $position
     * @param string $alias
     * @return void
     */
    public static function display_fields($context, $combined_condition_key, $position, $alias = 'condition')
    {

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Load condition
        if ($condition = $instance->get_item($combined_condition_key)) {

            // Get condition fields
            $condition_fields = $condition->get_fields();

            // Check if at least one field is defined
            if (!empty($condition_fields[$position])) {

                // Get total field count (all positions)
                $field_count = count($condition_fields, COUNT_RECURSIVE) - count($condition_fields);

                // Iterate over fields of current position
                foreach ($condition_fields[$position] as $field_key) {

                    // Load field
                    if ($field = $instance->get_condition_fields_controller()->get_item($field_key)) {

                        // Determine field size
                        if ($field_count === 2 || $position === 'before') {
                            $field_size = 'single';
                        }
                        else if ($field_count === 1) {
                            $field_size = 'double';
                        }
                        else {
                            $field_size = 'triple';
                        }

                        // Open container
                        echo '<div class="' . $instance->get_plugin_prefix() . $alias . '_setting_fields_' . $field_size . '">';

                        // Print field
                        $field->display($context, $alias);

                        // Close container
                        echo '</div>';
                    }
                }
            }
        }
    }

    /**
     * Return condition method options for specific condition
     *
     * Alias for internal method
     *
     * @access public
     * @param string $combined_condition_key
     * @return array
     */
    public static function get_condition_method_options_for_display($combined_condition_key)
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->get_condition_method_options_for_display_internal($combined_condition_key);
    }

    /**
     * Check if condition method option exists
     *
     * Alias for internal method
     *
     * @access public
     * @param string $combined_condition_key
     * @param string $option_key
     * @return bool
     */
    public static function condition_method_option_exists($combined_condition_key, $option_key)
    {

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Get method options
        $options = $instance->get_condition_method_options_for_display_internal($combined_condition_key);

        // Check if method option exists
        return isset($options[$option_key]);
    }

    /**
     * Get conditions multiselect field option labels
     *
     * @access public
     * @param array $conditions
     * @return array
     */
    public static function get_conditions_multiselect_field_option_labels($conditions)
    {

        $labels = array();

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Iterate over conditions
        foreach ($conditions as $condition_key => $condition_settings) {

            // Load condition object
            if ($condition = $instance->get_item($condition_settings['type'])) {

                // Iterate over condition fields
                foreach ($condition->get_fields() as $position => $fields) {
                    foreach ($fields as $condition_field_key) {

                        // Check if current field is not empty
                        if (!empty($condition_settings[$condition_field_key])) {

                            // Load condition field object
                            if ($condition_field = $instance->get_condition_fields_controller()->get_item($condition_field_key)) {

                                // Get multiselect option labels
                                if (method_exists($condition_field, 'get_multiselect_option_labels')) {
                                    $labels[$condition_key][$condition_field_key] = $condition_field->get_multiselect_option_labels($condition_settings[$condition_field_key]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $labels;
    }

    /**
     * Load multiselect options
     *
     * @access public
     * @return void
     */
    public function ajax_load_multiselect_options()
    {

        $options = array();

        // Check if required vars are set
        if (!empty($_POST['type']) && !empty($_POST['query'])) {

            // Load corresponding condition field
            if ($field = $this->get_condition_fields_controller()->get_item($_POST['type'])) {

                // Get options
                $options = $field->get_multiselect_options(array(
                    'query'     => $_POST['query'],
                    'selected'  => (!empty($_POST['selected']) && is_array($_POST['selected'])) ? $_POST['selected'] : array(),
                ));
            }
        }

        // Return data and exit
        echo RightPress_Help::json_encode_multiselect_options(array('results' => array_values($options)));
        exit;
    }

    /**
     * =================================================================================================================
     * EXTERNAL METHODS - BACKEND CONDITION VALIDATION
     * =================================================================================================================
     */

    /**
     * Validate conditions
     *
     * @access public
     * @param array $posted
     * @param string $alias
     * @return array
     */
    public static function validate_conditions($posted, $alias = 'conditions')
    {

        $conditions = array();

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Iterate over posted conditions
        if (!empty($posted[$alias]) && is_array($posted[$alias])) {
            foreach ($posted[$alias] as $condition) {

                // Validate and sanitize condition
                if ($processed_condition = $instance->validate_single_condition_internal($condition)) {
                    $conditions[] = $processed_condition;
                }
            }
        }

        return $conditions;
    }

    /**
     * Validate single condition
     *
     * Alias for internal method
     *
     * @access public
     * @param string $posted
     * @return array
     */
    public static function validate_single_condition($posted)
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->validate_single_condition_internal($posted);
    }

    /**
     * Validate single condition fields
     *
     * Alias for internal method
     *
     * @access public
     * @param string $condition_key
     * @param string $condition_method_key
     * @param array $posted
     * @return mixed
     */
    public static function validate_single_condition_fields($condition_key, $condition_method_key, $posted)
    {

        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);
        return $instance->validate_single_condition_fields_internal($condition_key, $condition_method_key, $posted);
    }

    /**
     * Flag disabled conditions
     *
     * @access public
     * @param array $conditions
     * @return void
     */
    public static function flag_disabled_conditions(&$conditions)
    {

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Iterate over conditions
        foreach ($conditions as $condition_key => $condition) {

            // Load condition object
            $condition_object = $instance->get_item($condition['type']);

            // Unable to load condition object
            if (!$condition_object) {

                // Custom taxonomy condition
                if ($instance->is_group($condition, 'custom_taxonomy')) {

                    // Taxonomy exists
                    if (taxonomy_exists(str_replace('custom_taxonomy__', '', $condition['type']))) {
                        $conditions[$condition_key]['_disabled_taxonomy'] = true;
                    }
                    // Taxonomy does not exist
                    else {
                        $conditions[$condition_key]['_non_existent_taxonomy'] = true;
                    }
                }
                // Regular condition
                else {
                    $conditions[$condition_key]['_non_existent'] = true;
                }
            }
            // Condition is disabled
            else if (!$condition_object->is_enabled()) {
                $conditions[$condition_key]['_disabled'] = true;
            }
        }
    }

    /**
     * Get all disabled condition flags
     *
     * @access public
     * @return array
     */
    public static function get_all_disabled_condition_flags()
    {

        return array(
            '_disabled',
            '_disabled_taxonomy',
            '_non_existent',
            '_non_existent_taxonomy',
        );
    }

    /**
     * =================================================================================================================
     * EXTERNAL METHODS - OTHER
     * =================================================================================================================
     */

    /**
     * Get condition params from product
     *
     * @access public
     * @param object $product
     * @return array
     */
    public static function get_condition_params_from_product($product)
    {

        return array(
            'item_id'               => $product->is_type('variation') ? $product->get_parent_id() : $product->get_id(),
            'child_id'              => $product->is_type('variation') ? $product->get_id() : null,
            'variation_attributes'  => $product->is_type('variation') ? $product->get_variation_attributes() : null,
        );
    }

    /**
     * Check if at least one object has at least one condition of specified type
     *
     * @access public
     * @param array $objects
     * @param string $condition_group_keys
     * @return bool
     */
    public static function objects_have_condition_groups($objects, $condition_group_keys)
    {

        // Get instance
        $instance = RightPress_Controller_Conditions::get_controller_instance(get_called_class(), __FUNCTION__);

        // Iterate over objects
        foreach ($objects as $object) {

            // Get conditions from object
            $conditions = $instance->get_conditions_from_object($object);

            // Iterate over conditions
            if (!empty($conditions)) {
                foreach ($conditions as $condition) {

                    // Condition matched
                    if (in_array(strtok($condition['type'], '__'), $condition_group_keys, true)) {
                        return true;
                    }
                }
            }
        }

        // No object has condition of specified types
        return false;
    }

    /**
     * Get list of all custom product taxonomies
     *
     * @access public
     * @param array $blacklist
     * @return void
     */
    public static function get_all_custom_taxonomies($blacklist = array())
    {

        $taxonomies = array();

        // Supported object types
        $object_types = apply_filters('rightpress_custom_condition_taxonomy_object_types', array('product', 'product_variation'));

        // Iterate over all taxonomies
        foreach (get_taxonomies(array(), 'objects') as $taxonomy_key => $taxonomy) {
            if (array_intersect($taxonomy->object_type, $object_types) && !in_array($taxonomy->name, $blacklist, true)) {
                $taxonomies[$taxonomy->name] = $taxonomy->name;
            }
        }

        return apply_filters('rightpress_custom_condition_taxonomies', $taxonomies);
    }

    /**
     * Check if condition is of given type(s)
     *
     * @access public
     * @param array $condition
     * @param string|array $types
     * @return bool
     */
    public static function is_type($condition, $types)
    {

        return (isset($condition['type']) && in_array($condition['type'], (array) $types, true));
    }

    /**
     * Check if condition belongs to given group(s)
     *
     * @access public
     * @param array $condition
     * @param string|array $groups
     * @return bool
     */
    public static function is_group($condition, $groups)
    {

        if (isset($condition['type'])) {
            foreach ((array) $groups as $group) {
                if (RightPress_Help::string_begins_with_substring($condition['type'], ($group . '__'))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate custom taxonomy conditions
     *
     * To be overriden by child classes which then call generate_custom_taxonomy_conditions_internal
     *
     * @access public
     * @param array $enabled
     * @return void
     */
    public function generate_custom_taxonomy_conditions()
    {

        return;
    }

    /**
     * Get controller instance
     *
     * @access public
     * @param string $called_class
     * @param string $called_function
     * @return object
     */
    public static function get_controller_instance($called_class, $called_function)
    {

        // Methods calling this method must not be called through the parent class
        if ($called_class === __CLASS__) {
            RightPress_Help::doing_it_wrong(($called_class . '::' . $called_function), 'Method must be called through a child class.', '1.0');
            exit;
        }

        // Get instance
        return $called_class::get_instance();
    }

    /**
     * =================================================================================================================
     * INTERNAL METHODS - CONDITION CHECKS
     * =================================================================================================================
     */

    /**
     * Check if conditions are matched
     *
     * @access public
     * @param array $conditions
     * @param array $params
     * @return bool
     */
    public function conditions_are_matched_internal($conditions, $params = array())
    {

        $product_conditions_for_cart = array();

        // Get cart item params if cart item is set
        if (!empty($params['cart_item'])) {
            $params = array_merge($params, $this->get_condition_params_from_cart_item($params['cart_item']));
        }

        // Regular condition handling
        foreach ($conditions as $condition) {

            // Handle product conditions for cart discount and checkout fee rules separately
            if (!empty($params['context']) && in_array($params['context'], array('cart_discounts', 'checkout_fees'), true)) {
                if ($this->is_group($condition, array('product', 'product_property', 'product_other', 'custom_taxonomy'))) {
                    $product_conditions_for_cart[] = $condition;
                    continue;
                }
            }

            // Add condition to params
            $params = array_merge($params, array('condition' => $condition));

            // Check single condition
            if (!$this->condition_is_matched_internal($params)) {
                return false;
            }
        }

        // Special handling of product conditions in cart discount and checkout fee rules
        if (!empty($product_conditions_for_cart)) {

            // Get cart items by product conditions
            $cart_items = $this->get_cart_items_by_product_conditions_internal($product_conditions_for_cart);

            // No cart items matched
            if (empty($cart_items)) {
                return false;
            }
        }

        // All conditions are matched
        return true;
    }

    /**
     * Check if single condition is matched
     *
     * @access public
     * @param array $params
     * @return bool
     */
    public function condition_is_matched_internal($params = array())
    {

        // Get condition object
        if ($condition = $this->get_item($params['condition']['type'])) {

            // Condition is disabled
            if (!$condition->is_enabled()) {
                return false;
            }

            // Maybe skip cart conditions
            if (!empty($params['skip_cart_conditions']) && $condition->is_cart()) {
                return false;
            }

            // Special handling for coupons applied
            if ($condition->get_combined_key() === 'cart__coupons') {
                $params['cache_coupons'] = RightPress_Help::get_wc_cart_applied_coupon_ids();
            }

            // Use caching on regular requests
            if (!is_ajax()) {

                // Get condition cache hash
                $cache_hash = $this->get_cache_hash($params);

                // Result not in cache
                if (!isset($this->condition_check_cache[$cache_hash])) {

                    // Check condition
                    $this->condition_check_cache[$cache_hash] = $condition->check($params);
                }

                // Return result from cache
                return $this->condition_check_cache[$cache_hash];
            }
            // Do not use caching
            else {

                // Check condition
                return $condition->check($params);
            }
        }

        return false;
    }

    /**
     * Check if object conditions are matched
     *
     * @access public
     * @param array $object
     * @param array $params
     * @return bool
     */
    public function object_conditions_are_matched_internal($object, $params = array())
    {

        // Get conditions
        if ($conditions = $this->get_conditions_from_object($object)) {

            // Check conditions
            return $this->conditions_are_matched_internal($conditions, $params);
        }

        // No conditions to check
        return true;
    }

    /**
     * Filter objects by conditions
     *
     * @access public
     * @param array $objects
     * @param array $params
     * @return array
     */
    public function filter_objects_internal($objects, $params = array())
    {

        $filtered = array();

        // Iterate over objects
        foreach ($objects as $object) {

            // Check object against conditions
            if ($this->object_conditions_are_matched_internal($object, $params)) {

                // Add object to filtered objects list
                $filtered[] = $object;
            }
        }

        return $filtered;
    }

    /**
     * =================================================================================================================
     * INTERNAL METHODS - CONDITION CHECKS SPECIFIC TO CART ITEMS AND PRODUCTS
     * =================================================================================================================
     */

    /**
     * Check if cart item matches product conditions
     *
     * @access public
     * @param array $cart_item
     * @param array $conditions
     * @return bool
     */
    public function cart_item_matches_product_conditions_internal($cart_item, $conditions)
    {

        // Iterate over conditions
        foreach ($conditions as $condition) {

            // Skip non-product conditions
            if (!$this->is_group($condition, array('product', 'product_property', 'product_other', 'custom_taxonomy'))) {
                continue;
            }

            // Get cart item params
            $params = $this->get_condition_params_from_cart_item($cart_item);
            $params['condition'] = $condition;
            $params['cart_item'] = $cart_item;

            // Check if condition is matched
            if (!$this->condition_is_matched_internal($params)) {

                // At least one product condition is not matched
                return false;
            }
        }

        // If we reached this point conditions are matched
        return true;
    }

    /**
     * Get cart items filtered by product conditions
     *
     * Leaves those cart items for which all product conditions match
     * Ignores non-product conditions
     *
     * @access public
     * @param array $conditions
     * @return array
     */
    public function get_cart_items_by_product_conditions_internal($conditions = array())
    {

        // Get cart items
        $cart_items = RightPress_Help::get_wc_cart_items();

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Check if cart item matches product conditions
            if (!$this->cart_item_matches_product_conditions_internal($cart_item, $conditions)) {

                // Cart item does not match product conditions - remove cart item from array
                unset($cart_items[$cart_item_key]);
            }
        }

        // Return remaining cart items
        return $cart_items;
    }

    /**
     * =================================================================================================================
     * INTERNAL METHODS - BACKEND CONDITION MANAGEMENT
     * =================================================================================================================
     */

    /**
     * Return condition method options for specific condition
     *
     * @access public
     * @param string $combined_condition_key
     * @return array
     */
    public function get_condition_method_options_for_display_internal($combined_condition_key)
    {

        // Get condition object
        if ($condition = $this->get_item($combined_condition_key)) {

            // Get condition method
            if ($method = $this->get_condition_methods_controller()->get_item($condition->get_method())) {

                // Return condition methods options
                return $method->get_options();
            }
        }

        // No methods found for this condition
        return array();
    }

    /**
     * =================================================================================================================
     * INTERNAL METHODS - BACKEND CONDITION VALIDATION
     * =================================================================================================================
     */

    /**
     * Validate single condition
     *
     * @access public
     * @param string $posted
     * @return array
     */
    public function validate_single_condition_internal($posted)
    {

        $single = array();

        // Unique identifier
        if (!empty($posted['uid'])) {
            $single['uid'] = $posted['uid'];
        }
        else {
            $single['uid'] = $this->get_plugin_prefix() . RightPress_Help::get_hash();
        }

        // Type
        if (isset($posted['type']) && $this->item_exists($posted['type'])) {
            $single['type'] = $posted['type'];
        }
        else {
            return false;
        }

        // Method
        if (isset($posted['method_option']) && $this->condition_method_option_exists($single['type'], $posted['method_option'])) {
            $single['method_option'] = $posted['method_option'];
        }
        else {
            return false;
        }

        // Other condition field values
        $field_values = $this->validate_single_condition_fields_internal($single['type'], $single['method_option'], $posted);

        // At least one field value exists
        if (!empty($field_values)) {
            $single = array_merge($single, $field_values);
        }
        // Validation error
        else if ($field_values === false) {
            return false;
        }

        return !empty($single) ? $single : false;
    }

    /**
     * Validate single condition fields
     *
     * @access public
     * @param string $condition_key
     * @param string $condition_method_key
     * @param array $posted
     * @return mixed
     */
    public function validate_single_condition_fields_internal($condition_key, $condition_method_key, $posted)
    {

        $single = array();

        // Get condition object
        if ($condition = $this->get_item($condition_key)) {

            // Iterate over condition fields
            foreach ($condition->get_fields() as $position => $fields) {
                foreach ($fields as $condition_field_key) {

                    // Get condition field object
                    if ($condition_field = $this->get_condition_fields_controller()->get_item($condition_field_key)) {

                        // Validate condition field
                        if ($condition_field->validate($posted, $condition, $condition_method_key)) {

                            // Sanitize condition field value
                            $single[$condition_field_key] = $condition_field->sanitize($posted, $condition, $condition_method_key);
                        }
                        else {

                            return false;
                        }
                    }
                }
            }
        }

        return $single;
    }

    /**
     * =================================================================================================================
     * INTERNAL METHODS - OTHER
     * =================================================================================================================
     */

    /**
     * Generate custom taxonomy conditions
     *
     * @access protected
     * @param array $enabled_taxonomies
     * @param string $condition_class
     * @param string $condition_field_class
     * @return void
     */
    protected function generate_custom_taxonomy_conditions_internal($enabled_taxonomies, $condition_class, $condition_field_class)
    {

        // Get available taxonomies
        $available = get_taxonomies(array(), 'objects');

        // Generate conditions for available enabled taxonomies
        if (!empty($enabled_taxonomies)) {
            foreach ($available as $taxonomy) {
                if (in_array($taxonomy->name, $enabled_taxonomies, true)) {

                    $condition_field_key = ('custom_taxonomy_' . $taxonomy->name);

                    // Instantiate condition
                    new $condition_class($taxonomy->name, array('after' => array($condition_field_key)), $taxonomy->name);

                    // Instantiate condition field
                    new $condition_field_class($condition_field_key, $taxonomy->name, $taxonomy->hierarchical);
                }
            }
        }
    }

    /**
     * Get conditions from object
     *
     * @access public
     * @param array|object $object
     * @return array|bool
     */
    public function get_conditions_from_object($object)
    {

        // Conditions inside array
        if (is_array($object) && !empty($object['conditions']) && is_array($object['conditions'])) {
            $conditions = $object['conditions'];
        }
        // Conditions inside object
        else if (is_object($object) && !empty($object->conditions) && is_array($object->conditions)) {
            $conditions = $object->conditions;
        }
        // No conditions found
        else {
            $conditions = false;
        }

        return $conditions;
    }

    /**
     * Get condition params from cart item
     *
     * Note: attempt to figure our product id and variation attributes from variation id is a fix for issue WCDPD #500
     *
     * @access public
     * @param array $cart_item
     * @return array
     */
    public function get_condition_params_from_cart_item($cart_item)
    {

        // Get variation id if present
        $variation_id = RightPress_Help::get_wc_variation_id_from_cart_item($cart_item);

        // Get variation attributes from cart item
        if (!empty($cart_item['variation'])) {
            $variation_attributes = $cart_item['variation'];
        }
        // Get variation attributes by variation id
        else if (isset($variation_id) && ($variation = wc_get_product($variation_id))) {
            $variation_attributes = $variation->get_variation_attributes();
        }
        // No variation attributes
        else {
            $variation_attributes = null;
        }

        // Get product id from cart item
        if (!empty($cart_item['product_id'])) {
            $product_id = $cart_item['product_id'];
        }
        // Get product id by variation id
        else if (isset($variation_id) && ($variation = wc_get_product($variation_id))) {
            $product_id = $variation->get_parent_id();
        }
        // No product id
        else {
            $product_id = null;
        }

        // Return array of params
        return array(
            'item_id'               => $product_id,
            'child_id'              => $variation_id,
            'variation_attributes'  => $variation_attributes,
        );
    }

    /**
     * Get condition cache hash for condition check
     *
     * @access public
     * @param array $params
     * @return string
     */
    public function get_cache_hash($params)
    {

        // Unset condition uid
        if (isset($params['condition']['uid'])) {
            unset($params['condition']['uid']);
        }

        // Unset cart item
        if (isset($params['cart_item'])) {
            unset($params['cart_item']);
        }

        // Do not cache any cart conditions when running pricing tests (we modify cart contents on the fly)
        if (class_exists('RightPress_Product_Price_Test') && RightPress_Product_Price_Test::is_running() && RightPress_Help::string_begins_with_substring($params['condition']['type'], 'cart_')) {
            $params['running_test'] = RightPress_Help::get_hash();
        }

        // Hash remaining condition check params
        return RightPress_Help::get_hash(true, $params);
    }

    /**
     * Get condition fields controller instance
     *
     * @access public
     * @return object
     */
    public function get_condition_fields_controller()
    {

        return $this->condition_fields_controller;
    }

    /**
     * Get condition methods controller instance
     *
     * @access public
     * @return object
     */
    public function get_condition_methods_controller()
    {

        return $this->condition_methods_controller;
    }

    /**
     * Check if amounts in conditions include tax
     *
     * @access public
     * @return bool
     */
    public static function amounts_include_tax()
    {

        return false;
    }





}
