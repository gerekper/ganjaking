<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Method controller
 *
 * @class RP_WCDPD_Controller_Methods
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Controller_Methods extends RightPress_Item_Controller
{

    protected $plugin_prefix        = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;
    protected $item_key             = 'method';
    protected $items_are_grouped    = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Getter
     *
     * @access public
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'applicable_adjustments') {
            return $this->applicable_adjustments;
        }
    }

    /**
     * Get rule method from rule
     *
     * @access public
     * @param array $rule
     * @return mixed
     */
    public function get_method_from_rule($rule = array())
    {
        // Get key from rule
        $key = isset($rule['method']) ? $rule['method'] : 'simple';

        // Attempt to find method by key
        foreach (self::get_items() as $method_group) {
            foreach ($method_group['children'] as $method_key => $method) {
                if ($method_key === $key) {
                    return $method;
                }
            }
        }

        // No such method
        return false;
    }

    /**
     * Get rule method key by rule
     *
     * @access public
     * @param array $rule
     * @return mixed
     */
    public function get_method_key($rule)
    {
        if ($method = $this->get_method_from_rule($rule)) {
            return $method->get_key();
        }

        return false;
    }

    /**
     * Get applicable adjustments
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public function get_applicable_adjustments($cart_items = null)
    {
        $adjustments = array();

        // Load all rules by context
        $rules = RP_WCDPD_Rules::get($this->context, array(
            'cart_items' => $cart_items,
        ));

        // Iterate over all rules
        foreach ($rules as $rule) {

            // Get method
            if ($method = $this->get_method_from_rule($rule)) {

                // Get adjustments for current method/rule
                $current_adjustments = $method->get_adjustments($rule, $cart_items);

                // Add adjustments to the main array
                foreach ($current_adjustments as $cart_item_key => $adjustment) {

                    // Add extra data and split by cart item if needed
                    if ($cart_items !== null) {

                        // Define extra data
                        $extra_data = array();

                        // Add to main array
                        $adjustments[$cart_item_key][$rule['uid']] = array_merge($extra_data, $adjustment);
                    }
                    else {
                        $adjustments[$rule['uid']] = $adjustment;
                    }
                }
            }
        }

        return $adjustments;
    }

    /**
     * Apply rules
     *
     * Note: This method is for Cart Discounts and Checkout Fees - other methods must override it
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function apply($cart = null)
    {
        $simple_combined = false;
        $applied = false;

        // Iterate over applicable adjustments
        foreach ($this->applicable_adjustments as $rule_uid => $adjustment) {

            // Get method
            if ($method = $this->get_method_from_rule($adjustment['rule'])) {

                // Handle combined simple cart discounts
                if ($method->get_key() === 'simple') {

                    // Already combined
                    if ($simple_combined) {
                        continue;
                    }
                    // Not yet combined but need to be combined
                    else if ($this->combine_simple()) {

                        $adjustment = array(
                            'rule'              => $this->get_combined_simple_rule(),
                            'adjustment_amount' => $this->get_combined_simple_rule_amount(),
                        );

                        $simple_combined = true;
                    }
                }

                // Rule applied - trigger action
                do_action('rp_wcdpd_' . $this->context . '_rule_applied_to_cart', $adjustment['rule']['uid'], $adjustment);
                $applied = true;

                // Apply adjustment
                $method->apply_adjustment($adjustment, $cart);
            }
        }

        // Nothing to apply - trigger action
        if (!$applied) {
            do_action('rp_wcdpd_' . $this->context . '_nothing_to_apply');
        }
    }

    /**
     * Get combined simple rule amount
     *
     * @access public
     * @return float
     */
    public function get_combined_simple_rule_amount()
    {
        $combined_amount = 0;

        // Iterate over applicable adjustments
        foreach ($this->applicable_adjustments as $rule_uid => $adjustment) {

            // Get method
            if ($method = $this->get_method_from_rule($adjustment['rule'])) {
                if ($method->get_key() === 'simple' && !empty($adjustment['adjustment_amount'])) {
                    $combined_amount += $adjustment['adjustment_amount'];
                }
            }
        }

        // Return combined amount
        return (float) $combined_amount;
    }

    /**
     * Maybe wrap string with public description span
     *
     * @access public
     * @param string $html
     * @param array $rule_uids
     * @return string
     */
    public function maybe_add_public_description($html, $rule_uids)
    {
        // Get descriptions for rules
        if ($descriptions = RP_WCDPD_Rules::get_public_descriptions($this->context, $rule_uids)) {

            // Wrap html in descriptions span
            $html = '<span data-rp-wcdpd-public-descriptions-' . str_replace('_', '-', $this->context) . '="' . htmlspecialchars(json_encode($descriptions), ENT_QUOTES, 'UTF-8') . '">' . $html . '</span>';

            // Enqueue assets
            if (!defined('RP_WCDPD_PUBLIC_DESCRIPTION_ASSETS_ENQUEUED')) {
                wp_enqueue_script('rp-wcdpd-public-descriptions-scripts', RP_WCDPD_PLUGIN_URL . '/assets/js/public-descriptions.js', array('jquery'), RP_WCDPD_VERSION);
                RightPress_Help::enqueue_or_inject_stylesheet('rp-wcdpd-public-descriptions-scripts', RP_WCDPD_PLUGIN_URL . '/assets/css/public-descriptions.css', RP_WCDPD_VERSION);
                define('RP_WCDPD_PUBLIC_DESCRIPTION_ASSETS_ENQUEUED', true);
            }
        }

        // Return html
        return $html;
    }



}
