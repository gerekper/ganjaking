<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to Settings Migration
 *
 * @class RP_WCDPD_Settings_Migration
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Settings_Migration
{

    /**
     * Migrate settings
     *
     * @access protected
     * @param array $stored
     * @return array
     */
    public static function migrate($stored)
    {
        // Migrate from versions 1.x
        if (empty($stored)) {

            // Get options in previous format
            $options = get_option('rp_wcdpd_options');

            // Check flag
            $already_migrated = get_option('rp_wcdpd_settings_migrated');

            // Check if 1.x options were loaded
            if (!empty($options) && !empty($options[1]) && !$already_migrated) {

                $options = $options[1];
                $stored = array();

                // Get cart discount title
                $cart_discount_title = !empty($options['settings']['cart_discount_title']) ? (string) $options['settings']['cart_discount_title'] : 'Discount';

                // Set combined cart discount title
                $stored['cart_discounts_combined_title'] = $cart_discount_title;

                // Set cart discounts to be merged into one (default behaviour in 1.x)
                $stored['cart_discounts_if_multiple_applicable'] = 'combined';

                // Display pricing table
                if (!empty($options['settings']['display_table']) && $options['settings']['display_table'] !== 'hide') {

                    // Set flag
                    $stored['promo_volume_pricing_table'] = '1';

                    // Pricing table name
                    if (isset($options['localization']['quantity_discounts'])) {
                        $stored['promo_volume_pricing_table_title'] = (string) $options['localization']['quantity_discounts'];
                    }

                    // Position
                    if (!empty($options['settings']['display_position'])) {
                        $stored['promo_volume_pricing_table_position'] = $options['settings']['display_position'];
                    }

                    // Layout
                    $orientation = !empty($options['settings']['pricing_table_style']) ? $options['settings']['pricing_table_style'] : 'horizontal';
                    $stored['promo_volume_pricing_table_layout'] = $options['settings']['display_table'] . '-' . $orientation;
                }

                // Product pricing rule selection method
                if (!empty($options['pricing']['apply_multiple'])) {
                    if (in_array($options['pricing']['apply_multiple'], array('first', 'all'), true)) {
                        $stored['product_pricing_rule_selection_method'] = $options['pricing']['apply_multiple'];
                    }
                    else {
                        $stored['product_pricing_rule_selection_method'] = 'smaller_price';
                    }
                }

                $quantities_based_on_mapping = array(
                    'exclusive_product'         => 'individual__product',
                    'exclusive_variation'       => 'individual__variation',
                    'exclusive_configuration'   => 'individual__configuration',
                    'cumulative_categories'     => 'cumulative__categories',
                    'cumulative_all'            => 'cumulative__all',
                );

                // Product pricing
                if (!empty($options['pricing']['sets']) && is_array($options['pricing']['sets'])) {
                    foreach ($options['pricing']['sets'] as $rule) {

                        // Only empty set is present
                        if (count($options['pricing']['sets']) == 1 && (empty($rule['method']) || ($rule['method'] === 'quantity' && empty($rule['pricing'][1]['min'])))) {
                            continue;
                        }

                        $current_rule = array();

                        // UID
                        $current_rule['uid'] = 'rp_wcdpd_' . RightPress_Help::get_hash();

                        // Exclusivity
                        $current_rule['exclusivity'] = (!empty($rule['if_matched']) && in_array($rule['if_matched'], array('all', 'this', 'other'), true)) ? $rule['if_matched'] : 'all';

                        // Note
                        $current_rule['note'] = !empty($rule['description']) ? (string) $rule['description'] : '';

                        // Date from
                        if (!empty($rule['valid_from'])) {

                            // If date is not valid, add date far in the future to block condition
                            $condition_date = RightPress_Help::is_date((string) $rule['valid_from'], 'Y-m-d') ? (string) $rule['valid_from'] : '2100-01-01';

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'time__date',
                                'method_option' => 'from',
                                'date'          => $condition_date,
                            );
                        }

                        // Date to
                        if (!empty($rule['valid_until'])) {

                            // If date is not valid, add date far in the past to block condition
                            $condition_date = RightPress_Help::is_date((string) $rule['valid_until'], 'Y-m-d') ? (string) $rule['valid_until'] : '1999-12-31';

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'time__date',
                                'method_option' => 'to',
                                'date'          => $condition_date,
                            );
                        }

                        // Quantities based on
                        if (!empty($rule['method']) && in_array($rule['method'], array('quantity', 'special'), true)) {
                            if (!empty($rule['quantities_based_on']) && isset($quantities_based_on_mapping[$rule['quantities_based_on']])) {
                                $current_rule['quantities_based_on'] = $quantities_based_on_mapping[$rule['quantities_based_on']];
                            }
                        }

                        // Product conditions
                        if (!empty($rule['selection_method']) && $rule['selection_method'] === 'products_include') {
                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'product__product',
                                'method_option' => 'in_list',
                                'products'      => (!empty($rule['products']) && is_array($rule['products'])) ? $rule['products'] : array('0'),
                            );
                        }
                        else if (!empty($rule['selection_method']) && $rule['selection_method'] === 'products_exclude') {
                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'product__product',
                                'method_option' => 'not_in_list',
                                'products'      => (!empty($rule['products']) && is_array($rule['products'])) ? $rule['products'] : array('0'),
                            );
                        }
                        else if (!empty($rule['selection_method']) && $rule['selection_method'] === 'categories_include') {
                            $current_rule['conditions'][] = array(
                                'uid'                   => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'                  => 'product__category',
                                'method_option'         => 'in_list',
                                'product_categories'    => (!empty($rule['categories']) && is_array($rule['categories'])) ? $rule['categories'] : array('0'),
                            );
                        }
                        else if (!empty($rule['selection_method']) && $rule['selection_method'] === 'categories_exclude') {
                            $current_rule['conditions'][] = array(
                                'uid'                   => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'                  => 'product__category',
                                'method_option'         => 'not_in_list',
                                'product_categories'    => (!empty($rule['categories']) && is_array($rule['categories'])) ? $rule['categories'] : array('0'),
                            );
                        }

                        // Customer conditions
                        if (!empty($rule['user_method']) && $rule['user_method'] === 'roles_include') {
                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__role',
                                'method_option' => 'in_list',
                                'roles'         => (!empty($rule['roles']) && is_array($rule['roles'])) ? $rule['roles'] : array('administrator'),
                            );
                        }
                        else if (!empty($rule['user_method']) && $rule['user_method'] === 'roles_exclude') {

                            $roles = (!empty($rule['roles']) && is_array($rule['roles'])) ? $rule['roles'] : null;

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__role',
                                'method_option' => $roles !== null ? 'not_in_list' : 'in_list',
                                'roles'         => $roles !== null ? $roles : array('administrator'),
                            );
                        }
                        else if (!empty($rule['user_method']) && $rule['user_method'] === 'capabilities_include') {
                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__capability',
                                'method_option' => 'in_list',
                                'capabilities'  => (!empty($rule['capabilities']) && is_array($rule['capabilities'])) ? $rule['capabilities'] : array('manage_woocommerce'),
                            );
                        }
                        else if (!empty($rule['user_method']) && $rule['user_method'] === 'capabilities_exclude') {

                            $capabilities = (!empty($rule['capabilities']) && is_array($rule['capabilities'])) ? $rule['capabilities'] : null;

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__capability',
                                'method_option' => $capabilities !== null ? 'not_in_list' : 'in_list',
                                'capabilities'  => $capabilities !== null ? $capabilities : array('manage_woocommerce'),
                            );
                        }
                        else if (!empty($rule['user_method']) && $rule['user_method'] === 'users_include') {
                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__customer',
                                'method_option' => 'in_list',
                                'users'         => (!empty($rule['users']) && is_array($rule['users'])) ? $rule['users'] : array('0'),
                            );
                        }
                        else if (!empty($rule['user_method']) && $rule['user_method'] === 'users_exclude') {

                            $users = (!empty($rule['users']) && is_array($rule['users'])) ? $rule['users'] : null;

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'customer__customer',
                                'method_option' => $users !== null ? 'not_in_list' : 'in_list',
                                'users'         => $users !== null ? $users : array('0'),
                            );
                        }

                        // Bulk rule settings
                        if (!empty($rule['method']) && $rule['method'] === 'quantity') {

                            $current_rule['method'] = 'bulk';

                            // Products to adjust no longer supported for quantity rules
                            if (!empty($rule['quantity_products_to_adjust']) && $rule['quantity_products_to_adjust'] !== 'matched') {

                                $differ = false;

                                // Check if products to adjust differ from products to count
                                if (empty($rule['selection_method']) || in_array($rule['selection_method'], array('all', 'categories_exclude', 'products_exclude'), true)) {
                                    $differ = true;
                                }
                                else if ($rule['selection_method'] === 'categories_include' && !empty($rule['categories'])) {
                                    if ($rule['quantity_products_to_adjust'] === 'other_products') {
                                        $differ = true;
                                    }
                                    else {

                                        $categories_to_count = array_values($rule['categories']);
                                        $categories_to_count = array_map('strval', $categories_to_count);
                                        $categories_to_count = array_unique($categories_to_count);
                                        sort($categories_to_count);

                                        $categories_to_adjust = !empty($rule['quantity_categories']) ? array_values($rule['quantity_categories']) : array();
                                        $categories_to_adjust = array_map('strval', $categories_to_adjust);
                                        $categories_to_adjust = array_unique($categories_to_adjust);
                                        sort($categories_to_adjust);

                                        if ($categories_to_count != $categories_to_adjust) {
                                            $differ = true;
                                        }
                                    }
                                }
                                else if ($rule['selection_method'] === 'products_include' && !empty($rule['products'])) {
                                    if ($rule['quantity_products_to_adjust'] === 'other_categories') {
                                        $differ = true;
                                    }
                                    else {

                                        $products_to_count = array_values($rule['products']);
                                        $products_to_count = array_map('strval', $products_to_count);
                                        $products_to_count = array_unique($products_to_count);
                                        sort($products_to_count);

                                        $products_to_adjust = !empty($rule['quantity_products']) ? array_values($rule['quantity_products']) : array();
                                        $products_to_adjust = array_map('strval', $products_to_adjust);
                                        $products_to_adjust = array_unique($products_to_adjust);
                                        sort($products_to_adjust);

                                        if ($products_to_count != $products_to_adjust) {
                                            $differ = true;
                                        }
                                    }
                                }

                                // If products to adjust do not differ from products to count, we simply drop that setting
                                // If they differ, we add admin notice and special condition to block this rule until it is fixed
                                if ($differ) {

                                    // Add condition that would prevent these rules from running
                                    $current_rule['conditions'][] = array(
                                        'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                        'type'          => 'customer__role',
                                        'method_option' => 'in_list',
                                        'roles'         => array('administrator'),
                                    );

                                    // Add admin notice
                                    $html = '<p><strong>WooCommerce Dynamic Pricing & Discounts</strong> no longer supports quantity discount rules that have a separate set of "Products to adjust". Starting from current version, the plugin discounts the same products that are counted.</p> <p>If you simply need a pricing rule that would discount a group of products if a product from another group is present in cart (without really counting quantities), you can simply add the "Products in cart" condition.</p> <p>Please review all product pricing rules and fix any potential issues in configuration. We added a "role = administrator" condition to all affected pricing rules to make sure they are not applied to real orders while the configuration is being reviewed.</p><p>If you have a real need to count one set of products and adjust another set of products based on different pricing tiers, please get in touch with us to discuss.</p>';
                                    update_option('rp_wcdpd_migration_notice_products_to_adjust', $html);
                                }
                            }

                            // Migrate quantity ranges
                            $current_rule['quantity_ranges'] = RP_WCDPD_Settings_Migration::migrate_quantity_ranges($rule);
                        }
                        // BOGO
                        else if (!empty($rule['method']) && $rule['method'] === 'special') {

                            $current_rule['method'] = 'bogo' . (!empty($rule['special_repeat']) ? '_repeat' : '');

                            // Amount to purchase
                            if (!empty($rule['special_purchase']) && is_numeric($rule['special_purchase'])) {
                                $current_rule['bogo_purchase_quantity'] = (int) $rule['special_purchase'];
                            }
                            else {
                                $current_rule['bogo_purchase_quantity'] = PHP_INT_MAX;
                            }

                            // Amount to adjust
                            if (!empty($rule['special_adjust']) && is_numeric($rule['special_adjust'])) {
                                $current_rule['bogo_receive_quantity'] = (int) $rule['special_adjust'];
                            }
                            else {
                                $current_rule['bogo_receive_quantity'] = 0;
                            }

                            // Pricing method
                            if (!empty($rule['special_type']) && $rule['special_type'] === 'price') {
                                $current_rule['bogo_pricing_method'] = 'discount__amount';
                            }
                            else if (!empty($rule['special_type']) && $rule['special_type'] === 'fixed') {
                                $current_rule['bogo_pricing_method'] = 'fixed__price';
                            }
                            else {
                                $current_rule['bogo_pricing_method'] = 'discount__percentage';
                            }

                            // Pricing value
                            if (!empty($rule['special_value']) && is_numeric($rule['special_value'])) {
                                $current_rule['bogo_pricing_value'] = (float) $rule['special_value'];
                            }
                            else {
                                $current_rule['bogo_pricing_value'] = 0.0;
                            }

                            // Products to adjust
                            if (!empty($rule['special_products_to_adjust']) && $rule['special_products_to_adjust'] === 'other_products') {

                                $current_rule['bogo_receive_products'] = 'product__product';

                                if (!empty($rule['special_products']) && is_array($rule['special_products'])) {
                                    $current_rule['bogo_products'] = $rule['special_products'];
                                }
                                else {
                                    $current_rule['bogo_products'] = array('0');
                                }
                            }
                            else if (!empty($rule['special_products_to_adjust']) && $rule['special_products_to_adjust'] === 'other_categories') {

                                $current_rule['bogo_receive_products'] = 'product__category';

                                if (!empty($rule['special_categories']) && is_array($rule['special_categories'])) {
                                    $current_rule['bogo_product_categories'] = $rule['special_categories'];
                                }
                                else {
                                    $current_rule['bogo_product_categories'] = array('0');
                                }
                            }
                            else {
                                $current_rule['bogo_receive_products'] = 'matched';
                            }
                        }
                        // Exclude
                        else if (!empty($rule['method']) && $rule['method'] === 'exclude') {
                            $current_rule['method'] = 'exclude';
                        }
                        else {
                            continue;
                        }

                        // Add to main array
                        $stored['product_pricing'][] = $current_rule;
                    }
                }

                // Cart discount rule selection method
                if (!empty($options['discounts']['apply_multiple'])) {
                    if (in_array($options['discounts']['apply_multiple'], array('first', 'all'), true)) {
                        $stored['cart_discounts_rule_selection_method'] = $options['discounts']['apply_multiple'];
                    }
                    else {
                        $stored['cart_discounts_rule_selection_method'] = 'bigger_discount';
                    }
                }

                // Cart discounts
                if (!empty($options['discounts']['sets']) && is_array($options['discounts']['sets'])) {
                    foreach ($options['discounts']['sets'] as $cart_discount) {

                        // Only empty discount is present
                        if (count($options['discounts']['sets']) == 1 && empty($cart_discount['value'])) {
                            continue;
                        }

                        $current_rule = array();

                        // UID
                        $current_rule['uid'] = 'rp_wcdpd_' . RightPress_Help::get_hash();

                        // Default exclusivity
                        $current_rule['exclusivity'] = 'all';

                        // Title
                        $current_rule['title'] = $cart_discount_title;

                        // Private note
                        $current_rule['note'] = isset($cart_discount['description']) ? (string) $cart_discount['description'] : '';

                        // Pricing method
                        if (isset($cart_discount['type']) && $cart_discount['type'] === 'price') {
                            $current_rule['pricing_method'] = 'discount__amount';
                        }
                        else {
                            $current_rule['pricing_method'] = 'discount__percentage';
                        }

                        // Pricing value
                        $current_rule['pricing_value'] = (!empty($cart_discount['value']) && is_numeric($cart_discount['value'])) ? (float) $cart_discount['value'] : 0.0;

                        // Date from
                        if (!empty($cart_discount['valid_from'])) {

                            // If date is not valid, add date far in the future to block condition
                            $condition_date = RightPress_Help::is_date((string) $cart_discount['valid_from'], 'Y-m-d') ? (string) $cart_discount['valid_from'] : '2100-01-01';

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'time__date',
                                'method_option' => 'from',
                                'date'          => $condition_date,
                            );
                        }

                        // Date to
                        if (!empty($cart_discount['valid_until'])) {

                            // If date is not valid, add date far in the past to block condition
                            $condition_date = RightPress_Help::is_date((string) $cart_discount['valid_until'], 'Y-m-d') ? (string) $cart_discount['valid_until'] : '1999-12-31';

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'time__date',
                                'method_option' => 'to',
                                'date'          => $condition_date,
                            );
                        }

                        // Only if pricing not adjusted
                        if (!empty($cart_discount['only_if_pricing_not_adjusted'])) {

                            $current_rule['conditions'][] = array(
                                'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                'type'          => 'other__pricing_rules_applied',
                                'method_option' => 'no',
                            );
                        }

                        // Conditions
                        if (!empty($cart_discount['conditions']) && is_array($cart_discount['conditions'])) {
                            foreach ($cart_discount['conditions'] as $condition) {

                                // Condition key must be set
                                if (empty($condition['key'])) {
                                    continue;
                                }

                                // Proceed depending on condition key
                                switch ($condition['key']) {

                                    // Subtotal from
                                    case 'subtotal_bottom':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__subtotal',
                                            'method_option' => 'more_than',
                                            'decimal'       => (!empty($condition['value']) && is_numeric($condition['value'])) ? (float) $condition['value'] : 0.0,
                                        );
                                        break;

                                    // Subtotal to
                                    case 'subtotal_top':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__subtotal',
                                            'method_option' => 'not_more_than',
                                            'decimal'       => (!empty($condition['value']) && is_numeric($condition['value'])) ? (float) $condition['value'] : 0.0,
                                        );
                                        break;

                                    // Item count from
                                    case 'item_count_bottom':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__count',
                                            'method_option' => 'more_than',
                                            'number'        => (!empty($condition['value']) && is_numeric($condition['value'])) ? (int) $condition['value'] : 0,
                                        );
                                        break;

                                    // Item count to
                                    case 'item_count_top':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__count',
                                            'method_option' => 'not_more_than',
                                            'number'        => (!empty($condition['value']) && is_numeric($condition['value'])) ? (int) $condition['value'] : 0,
                                        );
                                        break;

                                    // Cart quantity from
                                    case 'quantity_bottom':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__quantity',
                                            'method_option' => 'more_than',
                                            'decimal'       => (!empty($condition['value']) && is_numeric($condition['value'])) ? (float) $condition['value'] : 0.0,
                                        );
                                        break;

                                    // Cart quantity to
                                    case 'quantity_top':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart__quantity',
                                            'method_option' => 'not_more_than',
                                            'decimal'       => (!empty($condition['value']) && is_numeric($condition['value'])) ? (float) $condition['value'] : 0.0,
                                        );
                                        break;

                                    // Products in cart
                                    case 'products':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart_items__products',
                                            'method_option' => 'at_least_one',
                                            'products'      => (!empty($condition['products']) && is_array($condition['products'])) ? $condition['products'] : array('0'),
                                        );
                                        break;

                                    // Products not in cart
                                    case 'products_not':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'cart_items__products',
                                            'method_option' => 'none',
                                            'products'      => (!empty($condition['products']) && is_array($condition['products'])) ? $condition['products'] : array('0'),
                                        );
                                        break;

                                    // Categories in cart
                                    case 'categories':
                                        $current_rule['conditions'][] = array(
                                            'uid'                   => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'                  => 'cart_items__product_categories',
                                            'method_option'         => 'at_least_one',
                                            'product_categories'    => (!empty($condition['categories']) && is_array($condition['categories'])) ? $condition['categories'] : array('0'),
                                        );
                                        break;

                                    // Categories not in cart
                                    case 'categories_not':
                                        $current_rule['conditions'][] = array(
                                            'uid'                   => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'                  => 'cart_items__product_categories',
                                            'method_option'         => 'none',
                                            'product_categories'    => (!empty($condition['categories']) && is_array($condition['categories'])) ? $condition['categories'] : array('0'),
                                        );
                                        break;

                                    // Specific customer
                                    case 'users':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'customer__customer',
                                            'method_option' => 'in_list',
                                            'users'         => (!empty($condition['users']) && is_array($condition['users'])) ? $condition['users'] : array('0'),
                                        );
                                        break;

                                    // Role
                                    case 'roles':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'customer__role',
                                            'method_option' => 'in_list',
                                            'roles'         => (!empty($condition['roles']) && is_array($condition['roles'])) ? $condition['roles'] : array('administrator'),
                                        );
                                        break;

                                    // Capabilities
                                    case 'capabilities':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'customer__capability',
                                            'method_option' => 'in_list',
                                            'capabilities'  => (!empty($condition['capabilities']) && is_array($condition['capabilities'])) ? $condition['capabilities'] : array('manage_woocommerce'),
                                        );
                                        break;

                                    // Order count
                                    case 'history_count':
                                        $current_rule['conditions'][] = array(
                                            'uid'               => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'              => 'customer_value__order_count',
                                            'method_option'     => 'at_least',
                                            'timeframe_span'    => 'all_time',
                                            'number'            => (!empty($condition['value']) && is_numeric($condition['value'])) ? (int) $condition['value'] : 0,
                                        );
                                        break;

                                    // Order amount
                                    case 'history_amount':
                                        $current_rule['conditions'][] = array(
                                            'uid'               => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'              => 'customer_value__amount_spent',
                                            'method_option'     => 'at_least',
                                            'timeframe_span'    => 'all_time',
                                            'decimal'           => (!empty($condition['value']) && is_numeric($condition['value'])) ? (float) $condition['value'] : 0.0,
                                        );
                                        break;

                                    // Shipping country
                                    case 'shipping_countries':
                                        $current_rule['conditions'][] = array(
                                            'uid'           => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                                            'type'          => 'shipping__country',
                                            'method_option' => 'in_list',
                                            'countries'     => (!empty($condition['shipping_countries']) && is_array($condition['shipping_countries'])) ? $condition['shipping_countries'] : array(),
                                        );
                                        break;

                                    default:
                                        break;
                                }
                            }
                        }

                        // Add to main array
                        $stored['cart_discounts'][] = $current_rule;
                    }
                }

                // Store settings
                $stored = array(
                    '1' => $stored,
                );

                // Store data
                update_option('rp_wcdpd_settings', $stored);

                // Add flag
                update_option('rp_wcdpd_settings_migrated', 1, 'no');

                // Add admin notice
                $html = sprintf('<p><strong>WooCommerce Dynamic Pricing & Discounts</strong> was updated to version <strong>%s</strong> which differs a lot from the one that you were using.</p><p>Your settings were migrated automatically but we ask that you <a href="%s">double check them</a> as well as make sure that pricing adjustments and cart discounts are being applied and are correct.</p><p>If you customized functionality of this extension in any way, you must check if your customizations are still working as expected.</p>', RP_WCDPD_VERSION, admin_url('/admin.php?page=rp_wcdpd_settings'));
                update_option('rp_wcdpd_migration_notice', $html);
           }
        }

        return $stored;
    }

    /**
     * Migrate quantity ranges
     *
     * @access public
     * @param array $rule
     * @return array
     */
    public static function migrate_quantity_ranges($rule)
    {
        $ranges = array();

        if (!empty($rule['pricing']) && is_array($rule['pricing'])) {

            if ($normalized = RP_WCDPD_Settings_Migration::normalize_quantity_pricing_table($rule['pricing'])) {
                foreach ($normalized as $quantity_range) {

                    // Pricing method
                    if (!empty($quantity_range['type']) && $quantity_range['type'] === 'price') {
                        $pricing_method = 'discount__amount';
                    }
                    else if (!empty($quantity_range['type']) && $quantity_range['type'] === 'fixed') {
                        $pricing_method = 'fixed__price';
                    }
                    else {
                        $pricing_method = 'discount__percentage';
                    }

                    $ranges[] = array(
                        'uid'               => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                        'from'              => (int) $quantity_range['min'],
                        'to'                => $quantity_range['max'] !== null ? (int) $quantity_range['max'] : null,
                        'pricing_method'    => $pricing_method,
                        'pricing_value'     => (!empty($quantity_range['value']) && is_numeric($quantity_range['value'])) ? (float) $quantity_range['value'] : 0.0,
                    );
                }
            }
        }

        // Add one range to block rule if no ranges were added
        if (empty($ranges)) {
            $ranges[] = array(
                'uid'               => 'rp_wcdpd_' . RightPress_Help::get_hash(),
                'from'              => PHP_INT_MAX,
                'to'                => PHP_INT_MAX,
                'pricing_method'    => 'discount__percentage',
                'pricing_value'     => 0.0,
            );
        }

        return $ranges;
    }

    /**
     * Normalize quantity pricing table
     *
     * Method adapted from the old version
     *
     * @access public
     * @param array $original_table
     * @return array|bool
     */
    public static function normalize_quantity_pricing_table($original_table)
    {
        if (empty($original_table) || !is_array($original_table)) {
            return false;
        }

        $table = array();

        // Track ranges to make sure we don't have overlaps
        $used_ranges = array();

        // Iterate over original elements
        foreach ($original_table as $current_row) {

            $row = $current_row;

            // Min quantity
            if (!is_numeric($row['min']) || ($row['min'] < 0)) {
                if ($row['min'] == '*') {
                    $row['min'] = 1;
                }
                else {
                    return false;
                }
            }

            // Max quantity
            if (!is_numeric($row['max']) || ($row['max'] < 0)) {
                if ($row['max'] == '*') {
                    $row['max'] = PHP_INT_MAX;
                }
                else {
                    return false;
                }
            }

            // Min must be smaller than max
            if ($row['min'] > $row['max']) {
                return false;
            }

            // Range must not overlap with existing ranges
            foreach ($used_ranges as $range) {
                if ($row['min'] == $range['min']) {
                    return false;
                }
                else if ($row['min'] < $range['min']) {
                    if ($row['max'] >= $range['min']) {
                        return false;
                    }
                }
                else if ($row['min'] > $range['min']) {
                    if ($row['min'] <= $range['max'] || $row['max'] <= $range['max']) {
                        return false;
                    }
                }
            }

            $used_ranges[] = array('min' => $row['min'], 'max' => $row['max']);

            // Adjustment type
            if (!isset($row['type']) || !in_array($row['type'], array('percentage', 'price', 'fixed'))) {
                return false;
            }

            // Value
            if (!is_numeric($row['value'])) {
                return false;
            }
            else if ($row['type'] == 'percentage' && ($row['value'] < 0 || $row['value'] > 100)) {
                return false;
            }
            else if (in_array($row['type'], array('price', 'fixed')) && $row['value'] < 0) {
                return false;
            }

            $table[] = $row;
        }

        if (empty($table)) {
            return false;
        }

        // Sort table ascending
        uasort($table, array('RP_WCDPD_Settings_Migration', 'sort_pricing_table_method_asc'));

        // Fix last to
        $all_keys = array_keys($table);
        $last_key = array_pop($all_keys);

        if (isset($table[$last_key]) && $table[$last_key]['max'] == PHP_INT_MAX) {
            $table[$last_key]['max'] = null;
        }

        return $table;
    }

    /**
     * Sort pricing table - ascending
     *
     * @access public
     * @param mixed $first
     * @param mixed $second
     * @return bool
     */
    public static function sort_pricing_table_method_asc($first, $second)
    {
        return ($first['min'] < $second['min']) ? -1 : 1;
    }



}
