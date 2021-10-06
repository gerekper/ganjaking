<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Conditions Helper
 *
 * @class RightPress_Conditions
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Conditions
{

    /**
     * Get all hierarchical taxonomy terms
     *
     * @access public
     * @param string $taxonomy
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_hierarchical_taxonomy_terms($taxonomy, $ids = array(), $query = '')
    {

        $items = array();

        // Get terms
        $terms = get_terms(array($taxonomy), array('hide_empty' => 0));
        $term_count = count($terms);

        // Iterate over terms
        foreach ($terms as $term_key => $term) {

            // Get term name
            $term_name = $term->name;

            // Term has parent
            if ($term->parent) {

                $parent_id = $term->parent;
                $has_parent = true;

                // Make sure we don't have an infinite loop here (happens with some kind of "ghost" terms)
                $found = false;
                $i = 0;

                while ($has_parent && ($i < $term_count || $found)) {

                    // Reset each time
                    $found = false;
                    $i = 0;

                    // Iterate over terms again
                    foreach ($terms as $parent_term_key => $parent_term) {

                        $i++;

                        if ($parent_term->term_id == $parent_id) {

                            $term_name = $parent_term->name . ' → ' . $term_name;
                            $found = true;

                            if ($parent_term->parent) {
                                $parent_id = $parent_term->parent;
                            }
                            else {
                                $has_parent = false;
                            }

                            break;
                        }
                    }
                }
            }

            // Get term id
            $term_id = (string) $term->term_id;

            // Skip this item if we don't need it
            if (!empty($ids) && !in_array($term_id, $ids, true)) {
                continue;
            }

            // Add item
            $items[] = array(
                'id'    => $term_id,
                'text'  => $term_name
            );
        }

        return $items;
    }

    /**
     * Get all non-hierarchical taxonomy terms
     *
     * @access public
     * @param string $taxonomy
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_non_hierarchical_taxonomy_terms($taxonomy, $ids = array(), $query = '')
    {

        $items = array();

        // Get terms
        $terms = get_terms(array($taxonomy), array('hide_empty' => 0));

        // Iterate over terms
        foreach ($terms as $term_key => $term) {

            // Get term id
            $term_id = (string) $term->term_id;

            // Skip this item if we don't need it
            if (!empty($ids) && !in_array($term_id, $ids, true)) {
                continue;
            }

            // Add item
            $items[] = array(
                'id'    => $term_id,
                'text'  => $term->name,
            );
        }

        return $items;
    }

    /**
     * Get all capabilities based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @param bool $include_custom_user_caps
     * @return array
     */
    public static function get_all_capabilities($ids = array(), $query = '', $include_custom_user_caps = false)
    {

        global $wpdb;
        global $wp_roles;

        $items = array();

        // Groups plugin active?
        if (class_exists('Groups_User') && class_exists('Groups_Wordpress') && function_exists('_groups_get_tablename')) {

            $capability_table = _groups_get_tablename('capability');
            $all_capabilities = $wpdb->get_results('SELECT capability FROM ' . $capability_table);

            if ($all_capabilities) {
                foreach ($all_capabilities as $capability) {

                    $capability = (string) $capability->capability;

                    // Skip this item if we don't need it
                    if (!empty($ids) && !in_array($capability, $ids, true)) {
                        continue;
                    }

                    // Add item
                    $items[] = array(
                        'id'    => $capability,
                        'text'  => $capability
                    );
                }
            }
        }

        // Get standard WP capabilities
        else {

            if (!isset($wp_roles)) {
                get_role('administrator');
            }

            $roles = $wp_roles->roles;

            $already_added = array();

            if (is_array($roles)) {

                // Iterate over roles
                foreach ($roles as $rolename => $atts) {
                    if (isset($atts['capabilities']) && is_array($atts['capabilities'])) {
                        foreach ($atts['capabilities'] as $capability => $value) {

                            $capability = (string) $capability;

                            if (!in_array($capability, $already_added, true)) {

                                // Skip this item if we don't need it
                                if (!empty($ids) && !in_array($capability, $ids, true)) {
                                    continue;
                                }

                                // Add item
                                $items[] = array(
                                    'id'    => $capability,
                                    'text'  => $capability
                                );
                                $already_added[] = $capability;
                            }
                        }
                    }
                }

                // Get custom capabilities assigned to individual users
                if ($include_custom_user_caps) {

                    // Get role names
                    $role_names = array_keys($roles);

                    // Get raw capability data for all users
                    $raw_capabilities = $wpdb->get_col("SELECT DISTINCT `meta_value` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE '{$wpdb->prefix}capabilities'");

                    // Add custom user capabilities to the list
                    foreach ($raw_capabilities as $raw_capability) {

                        // Unserialize raw capability entry
                        $unserialized = maybe_unserialize($raw_capability);

                        // Iterate over capabilities of current entry
                        if (is_array($unserialized)) {
                            foreach ($unserialized as $capability => $value) {

                                $capability = (string) $capability;

                                // Check if it's not a role name and was not already added
                                if (!in_array($capability, $role_names, true) && !in_array($capability, $already_added, true)) {

                                    // Skip this item if we don't need it
                                    if (!empty($ids) && !in_array($capability, $ids, true)) {
                                        continue;
                                    }

                                    // Add item
                                    $items[] = array(
                                        'id'    => $capability,
                                        'text'  => $capability
                                    );
                                    $already_added[] = $capability;
                                }
                            }
                        }
                    }
                }

            }
        }

        // Note: callbacks must return array of arrays with id/text properties set
        return apply_filters('rightpress_all_user_capabilities', $items);
    }

    /**
     * Get all countries based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_countries($ids = array(), $query = '')
    {

        $items = array();

        $countries = new WC_Countries();

        // Iterate over all countries
        if ($countries && is_array($countries->countries)) {
            foreach ($countries->countries as $country_code => $country_name) {

                // Add item
                $items[] = array(
                    'id'    => (string) $country_code,
                    'text'  => $country_name,
                );
            }
        }

        return $items;
    }

    /**
     * Get all coupons based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_coupons($ids = array(), $query = '')
    {

        $items = array();

        // Get all coupon ids
        $args = array(
            'posts_per_page'    => -1,
            'post_type'         => 'shop_coupon',
            'post_status'       => array('publish'),
            'fields'            => 'ids',
        );

        // Specific coupons requested
        if (!empty($ids)) {
            $args['post__in'] = $ids;
        }

        // WC31: As of WC 3.4 there are no coupon query methods and coupons are still treated as posts in WooCommerce core
        $posts_raw = get_posts($args);

        // Format results array
        foreach ($posts_raw as $post_id) {
            $items[] = array(
                'id'    => (string) $post_id,
                'text'  => get_the_title($post_id)
            );
        }

        return $items;
    }

    /**
     * Get all weekdays based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_weekdays($ids = array(), $query = '')
    {

        $items = array();

        // Get weekdays
        foreach (RightPress_Help::get_weekdays() as $weekday_key => $weekday) {

            // Add weekday
            $items[] = array(
                'id'    => (string) $weekday_key,
                'text'  => $weekday
            );
        }

        return $items;
    }

    /**
     * Get all product attributes based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_product_attributes($ids = array(), $query = '')
    {

        global $wc_product_attributes;

        $items = array();

        // Iterate over product attributes
        foreach ($wc_product_attributes as $attribute_key => $attribute) {

            // Get attribute name
            $attribute_name = !empty($attribute->attribute_label) ? $attribute->attribute_label : $attribute->attribute_name;

            // Get terms for this attribute
            $terms = RightPress_Conditions::get_all_hierarchical_taxonomy_terms($attribute_key, $ids, $query);

            // Iterate over subitems and make a list of item/subitem pairs
            foreach ($terms as $term) {
                $items[] = array(
                    'id'    => $term['id'],
                    'text'  => $attribute_name . ': ' . $term['text'],
                );
            }
        }

        return $items;
    }

    /**
     * Get all product categories based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_product_categories($ids = array(), $query = '')
    {

        // WC31: As of WC 3.4 product categories are still WP taxonomy terms
        return RightPress_Conditions::get_all_hierarchical_taxonomy_terms('product_cat', $ids, $query);
    }

    /**
     * Get all product tags based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_product_tags($ids = array(), $query = '')
    {

        // WC31: As of WC 3.4 product tags are still WP taxonomy terms
        return RightPress_Conditions::get_all_non_hierarchical_taxonomy_terms('product_tag', $ids, $query);
    }

    /**
     * Get all product variations based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_product_variations($ids = array(), $query = '')
    {

        $items = array();

        // Search product variations by query
        if ($query !== '') {

            // Load data stores
            $data_store = WC_Data_Store::load('product');

            // Search for variations
            // Note: search will also return other product types, we will filter them out later
            $variation_ids = $data_store->search_products($query, '', true, true);
        }
        // Get variations by ids or all variations if ids were not provided
        else {

            $variation_ids = wc_get_products(array(
                'include'   => $ids,
                'type'      => 'variation',
                'orderby'   => 'title',
                'order'     => 'ASC',
                'return'    => 'ids',
                'limit'     => -1,
            ));
        }

        // Format items
        foreach ($variation_ids as $variation_id) {

            // Load variation
            if ($variation = wc_get_product($variation_id)) {

                // Filter out other product types (search in data store may return mixed results)
                if ($variation->is_type('variation')) {

                    // Get list of variation attributes
                    $attributes = $variation->get_variation_attributes();

                    // Change empty values
                    foreach ($attributes as $attribute_key => $attribute) {
                        if ($attribute === '') {
                            $attributes[$attribute_key] = sprintf(strtolower(esc_html__('Any %s', 'woocommerce')), wc_attribute_label(str_replace('attribute_', '', $attribute_key)));
                        }
                    }

                    // Join attributes
                    $attributes = join(', ', $attributes);
                    $attributes = RightPress_Help::shorten_text($attributes, 25);

                    // Get variation identifier
                    if ($variation->get_sku()) {
                        $identifier = $variation->get_sku();
                    } else {
                        $identifier = '#' . $variation->get_id();
                    }

                    // Format variation title for display
                    $variation_title = $variation->get_title() . ' - ' . $attributes . ' (' . $identifier . ')';

                    // Add variation
                    $items[] = array(
                        'id'    => (string) $variation->get_id(),
                        'text'  => rawurldecode($variation_title),
                    );
                }
            }
        }

        return $items;
    }

    /**
     * Get all products based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_products($ids = array(), $query = '')
    {

        $items = array();

        // Search products by query
        if ($query !== '') {

            // Get product with all statuses except trash
            add_filter('woocommerce_search_products_post_statuses', array('RightPress_Conditions', 'wc_search_products_post_statuses'));

            // Load data stores
            $data_store = WC_Data_Store::load('product');

            // Search for products
            $product_ids = $data_store->search_products($query, '', false, false);

            // Remove filter
            remove_filter('woocommerce_search_products_post_statuses', array('RightPress_Conditions', 'wc_search_products_post_statuses'));
        }
        // Get products by ids or all products if ids were not provided
        else {

            $product_ids = wc_get_products(array(
                'include'   => $ids,
                'orderby'   => 'title',
                'order'     => 'ASC',
                'return'    => 'ids',
                'limit'     => -1,
            ));
        }

        // Format items
        foreach ($product_ids as $product_id) {

            // Load product
            if ($product = wc_get_product($product_id)) {

                // Add to items array
                $items[] = array(
                    'id'    => (string) $product->get_id(),
                    'text'  => rawurldecode($product->get_formatted_name()),
                );
            }
        }

        return $items;
    }

    /**
     * Product search post status filter to get all products but trashed
     *
     * @access public
     * @param array $statuses
     * @return array
     */
    public static function wc_search_products_post_statuses($statuses)
    {

        return array('private', 'publish', 'draft', 'future', 'pending');
    }

    /**
     * Get all product types based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_product_types($ids = array(), $query = '')
    {

        $items = array();

        // Fetch data
        foreach (wc_get_product_types() as $type_key => $type) {

            // Skip this item if we don't need it
            if (!empty($ids) && !in_array($type_key, $ids, true)) {
                continue;
            }

            // Add item
            $items[] = array(
                'id'    => $type_key,
                'text'  => $type . ' (' . $type_key . ')',
            );
        }

        return $items;
    }

    /**
     * Get all roles based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_roles($ids = array(), $query = '')
    {

        $items = array();

        // Get roles
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Iterate over roles and format results array
        foreach ($wp_roles->get_names() as $role_key => $role) {

            $role_key = (string) $role_key;

            // Skip this item if we don't need it
            if (!empty($ids) && !in_array($role_key, $ids, true)) {
                continue;
            }

            // Add item
            $items[] = array(
                'id'    => $role_key,
                'text'  => $role . ' (' . $role_key . ')',
            );
        }

        return $items;
    }

    /**
     * Get all shipping classes based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_shipping_classes($ids = array(), $query = '')
    {

        $items = array();

        // Iterate over shipping classes
        foreach (WC()->shipping()->get_shipping_classes() as $shipping_class) {

            // Get term id
            $shipping_class_id = (string) $shipping_class->term_id;

            // Skip this item if we don't need it
            if (!empty($ids) && !in_array($shipping_class_id, $ids, true)) {
                continue;
            }

            // Add item
            $items[] = array(
                'id'    => $shipping_class_id,
                'text'  => $shipping_class->name,
            );
        }

        return $items;
    }

    /**
     * Get all shipping zones based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_shipping_zones($ids = array(), $query = '')
    {

        $items = array();

        // Iterate over shipping zones
        foreach (WC_Shipping_Zones::get_zones() as $shipping_zone) {

            // Add item
            $items[] = array(
                'id'    => (string) $shipping_zone['zone_id'],
                'text'  => $shipping_zone['zone_name'],
            );
        }

        // Get Rest of the World shipping zone
        $shipping_zone = WC_Shipping_Zones::get_zone(0);

        // Add Rest of the World shipping zone
        $items = array_merge(array(array(
            'id'    => (string) $shipping_zone->get_id(),
            'text'  => $shipping_zone->get_zone_name(),
        )), $items);

        return $items;
    }

    /**
     * Get all states based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_states($ids = array(), $query = '')
    {

        $items = array();

        $countries = new WC_Countries();
        $all_states = $countries->get_states();

        // Iterate over all countries
        if ($countries && is_array($countries->countries) && is_array($all_states)) {
            foreach ($all_states as $country_key => $states) {
                if (is_array($states) && !empty($states)) {

                    // Get country name
                    $country_name = !empty($countries->countries[$country_key]) ? $countries->countries[$country_key] : $country_key;

                    // Iterate over all states
                    foreach ($states as $state_key => $state) {

                        // Add item
                        $items[] = array(
                            'id'    => $country_key . '_' . $state_key,
                            'text'  => $country_name . ': ' . $state,
                        );
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Get all users based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_users($ids = array(), $query = '')
    {

        $items = array();

        // Get users
        $users = get_users(array(
            'fields' => array('ID', 'user_login', 'user_email'),
        ));

        // Iterate over users
        foreach ($users as $user) {

            // Add item
            $items[] = array(
                'id'    => (string) $user->ID,
                'text'  => '#' . $user->ID . ' ' . $user->user_login . ' (' . $user->user_email . ')',
            );
        }

        return $items;
    }

    /**
     * Get all payment methods based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_payment_methods($ids = array(), $query = '')
    {

        $items = array();

        // Iterate over all payment gateways
        foreach (WC()->payment_gateways()->payment_gateways() as $gateway_key => $gateway) {

            // Get method title
            $method_title = $gateway->get_method_title();

            // Get custom title
            if (!empty($gateway->title) && is_string($gateway->title) && $gateway->title !== $method_title) {
                $method_title .= ' (' . $gateway->title . ')';
            }

            // Add item
            $items[] = array(
                'id'    => (string) $gateway_key,
                'text'  => $method_title,
            );
        }

        return $items;
    }

    /**
     * Get all shipping methods based on criteria
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public static function get_all_shipping_methods($ids = array(), $query = '')
    {

        $items = array();

        // Get shipping zone ids
        $shipping_zone_ids = array_merge(wp_list_pluck(WC_Shipping_Zones::get_zones(), 'zone_id'), array(0));

        // Get instances of shipping methods
        $shipping_method_instances = array();

        // Iterate over shipping zone ids
        foreach ($shipping_zone_ids as $shipping_zone_id) {

            // Load shipping zone
            $shipping_zone = WC_Shipping_Zones::get_zone($shipping_zone_id);

            // Get shipping zone name
            $shipping_zone_name = ($shipping_zone_id ? $shipping_zone->get_zone_name() : esc_html__('Other locations', 'rightpress'));

            // Get instances of shipping methods from current shipping zone
            foreach ($shipping_zone->get_shipping_methods() as $shipping_method_instance) {

                // Add to array
                $shipping_method_instances[$shipping_method_instance->id][] = array(
                    'combined_id'       => $shipping_method_instance->id . ':' . $shipping_method_instance->get_instance_id(),
                    'combined_title'    => $shipping_method_instance->get_title() . ' - ' . $shipping_zone_name,
                );
            }
        }

        // Load shipping methods
        WC()->shipping->load_shipping_methods();

        // Get shipping methods
        $shipping_methods = WC()->shipping->get_shipping_methods();

        // Iterate over shipping methods
        if (is_array($shipping_methods) && !empty($shipping_methods)) {
            foreach ($shipping_methods as $shipping_method) {

                // Add parent shipping method
                $items[] = array(
                    'id'    => (string) $shipping_method->id,
                    'text'  => $shipping_method->method_title . ' - ' . esc_html__('All zones', 'rightpress'),
                );

                // Add instances from zones
                if (!empty($shipping_method_instances[$shipping_method->id])) {
                    foreach ($shipping_method_instances[$shipping_method->id] as $instance_data) {

                        // Add shipping method instance
                        $items[] = array(
                            'id'    => $instance_data['combined_id'],
                            'text'  => $instance_data['combined_title'],
                        );
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Get order ids
     *
     * @access public
     * @param array $params
     * @return array
     */
    public static function get_order_ids($params = array())
    {

        $order_ids = array();
        $config = array();

        // Get date object
        if (isset($params['date'])) {
            $config['date'] = $params['date'];
        }

        // Only paid orders are counted
        $config['status'] = RightPress_Help::get_wc_order_is_paid_statuses(true);

        // Get customer id
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : (RightPress_Help::is_request('frontend') ? get_current_user_id() : null);

        // Get customer billing email
        if ($customer_id) {
            $customer = new WC_Customer($customer_id);
            $billing_email = $customer->get_billing_email();
        }
        else {
            $billing_email = RightPress_Conditions::get_checkout_billing_email();
        }

        // Get order ids by customer id
        if ($customer_id) {
            $order_ids = RightPress_Help::get_wc_order_ids(array_merge($config, array('customer_id' => $customer_id)));
        }

        // Get order ids by billing email
        if ($billing_email) {
            $order_ids = array_merge($order_ids, RightPress_Help::get_wc_order_ids(array_merge($config, array('billing_email' => $billing_email))));
        }

        // Return order ids
        return array_unique($order_ids);
    }

    /**
     * Get billing email from checkout data
     *
     * @access public
     * @return string|bool
     */
    public static function get_checkout_billing_email()
    {

        // Check for specific ajax requests
        if (!empty($_GET['wc-ajax']) && in_array($_GET['wc-ajax'], array('update_order_review', 'checkout'), true)) {

            $billing_email = null;

            // Check if request contains billing email
            if (!empty($_POST['billing_email'])) {
                $billing_email = $_POST['billing_email'];
            }
            else if (!empty($_POST['post_data'])) {

                parse_str($_POST['post_data'], $checkout_data);

                if (!empty($checkout_data['billing_email'])) {
                    $billing_email = $checkout_data['billing_email'];
                }
            }

            // Validate billing email format
            if (filter_var($billing_email, FILTER_VALIDATE_EMAIL)) {
                return $billing_email;
            }
        }

        return null;
    }

    /**
     * Check postcode
     *
     * @access public
     * @param string $value
     * @param string $condition_value
     * @return bool
     */
    public static function check_postcode($value, $condition_value)
    {

        // Neither can be empty
        if (RightPress_Help::is_empty($value) || RightPress_Help::is_empty($condition_value)) {
            return false;
        }

        // Break up condition postcode string
        $postcodes = explode(',', $condition_value);

        // Iterate over postcodes
        foreach ($postcodes as $postcode) {

            // Clean value
            $postcode = trim($postcode);

            // Postcode is empty
            if (RightPress_Help::is_empty($postcode)) {
                continue;
            }

            // Postcode with wildcards
            if (strpos($postcode, '*') !== false) {

                // Prepare regex string
                $regex = '/^' . str_replace('\*', '.', preg_quote($postcode)) . '$/i';

                // Compare
                if (preg_match($regex, $value) === 1) {
                    return true;
                }
            }
            // Postcode range
            else if (strpos($postcode, '-') !== false) {

                // Split range
                $ranges = explode('-', $postcode);
                $ranges[0] = trim($ranges[0]);
                $ranges[1] = trim($ranges[1]);

                // Check if ranges are valid
                if (count($ranges) !== 2 || (empty($ranges[0]) && $ranges[0] !== '0') || (empty($ranges[1]) && $ranges[1] !== '0') || !is_numeric($ranges[0]) || !is_numeric($ranges[1]) || $ranges[0] >= $ranges[1]) {
                    continue;
                }

                // Check if post code is within ranges
                if ($ranges[0] <= $value && $value <= $ranges[1]) {
                    return true;
                }
            }
            // Full postcode
            else if ($postcode === $value) {
                return true;
            }
        }

        // Postcode doesn't match
        return false;
    }

    /**
     * Get order total for use in conditions
     *
     * Attempts to get order total in base currency if another currency
     * was used for an order
     *
     * @access public
     * @param mixed $order
     * @return float
     */
    public static function order_get_total($order)
    {

        // Load order object
        if (!is_a($order, 'WC_Order')) {
            $order = wc_get_order($order);
        }

        // Order has different currency than base currency
        if ($order_total = RightPress_Help::get_wc_order_total_in_base_currency($order)) {
            return (float) $order_total;
        }
        // Get total in a regular way
        else {
            return (float) $order->get_total();
        }
    }





}
