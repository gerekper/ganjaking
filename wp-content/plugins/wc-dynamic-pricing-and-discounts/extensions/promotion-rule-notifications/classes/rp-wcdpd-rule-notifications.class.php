<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Rule Notifications
 *
 * Parent class
 *
 * @class RP_WCDPD_Rule_Notifications
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
abstract class RP_WCDPD_Rule_Notifications
{
    protected $current_register = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));
    }

    /**
     * Set up promotion tool
     *
     * @access public
     * @return void
     */
    public function set_up_promotion_tool()
    {
        // Do nothing on ajax requests to prevent duplicate notifications
        if (is_ajax()) {
            return;
        }

        // Check if notifications for this rule type is enabled
        if (RP_WCDPD_Settings::get('promo_rule_notifications_' . $this->context)) {
            add_action('rp_wcdpd_' . $this->context . '_rule_applied_to_cart', array($this, 'rule_applied'), 10, 2);
        }

        // Initialize register
        add_action(('rp_wcdpd_' . $this->context . '_nothing_to_apply'), array($this, 'initialize_register'));

        // Update register
        add_action('shutdown', array($this, 'update_register'), 1);
    }

    /**
     * Rule applied
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return void
     */
    public function rule_applied($identifier, $data)
    {
        // Get previously stored register
        $register = WC()->session->get(('rp_wcdpd_rule_notifications_register_' . $this->context), array());

        // Get message
        $message = $this->get_message($identifier, $data);

        // Get hash
        $hash = $this->get_hash($message, $identifier, $data);

        // Check if this notification was already displayed
        $displayed = (isset($register[$hash]) || (is_array($this->current_register) && isset($this->current_register[$hash])));

        // Add to register
        $this->add_to_register($hash, $data);

        // Display if not already displayed
        if (apply_filters(('rp_wcdpd_promotion_rule_notifications_display_' . $this->context), !$displayed, $message, $identifier, $data)) {
            wc_add_notice($message);
        }
    }

    /**
     * Get hash
     *
     * @access public
     * @param string $message
     * @param string $identifier
     * @param array $data
     * @return void
     */
    public function get_hash($message, $identifier, $data)
    {
        return RightPress_Help::get_hash(false, array($message, $identifier));
    }

    /**
     * Get message
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function get_message($identifier, $data)
    {
        // Get message
        $raw_message = RP_WCDPD_Settings::get('promo_rule_notifications_' . $this->context . '_message');

        // Replace new lines with <br> instances
        $message = nl2br($raw_message);

        // Replace macros
        $message = $this->replace_macros($message, $identifier, $data);

        // Remove double line breaks
        $message = preg_replace('/<br[^>]*>(\s*<br[^>]*>)+/', '<br>', $message);

        // Allow developers to override
        $message = apply_filters(('rp_wcdpd_promotion_rule_notifications_message_' . $this->context), $message, $identifier, $data, $raw_message);

        // Return message
        return $message;
    }

    /**
     * Replace macros
     *
     * @access public
     * @param string $message
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function replace_macros($message, $identifier, $data)
    {
        // Search
        $search = array(
            '{{title}}',
            '{{description}}'
        );

        // Replace
        $replace = array(
            apply_filters(('rp_wcdpd_promotion_rule_notifications_title_' . $this->context), $this->get_title_value($identifier, $data), $message, $identifier, $data),
            apply_filters(('rp_wcdpd_promotion_rule_notifications_description_' . $this->context), $this->get_description_value($identifier, $data), $message, $identifier, $data),
        );

        // Replace macros and return
        return str_replace($search, $replace, $message);
    }

    /**
     * Get title value
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function get_title_value($identifier, $data)
    {
        return null;
    }

    /**
     * Get description value
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function get_description_value($identifier, $data)
    {
        // Try to get public description by identifier
        if ($descriptions = RP_WCDPD_Rules::get_public_descriptions($this->context, $identifier)) {
            if (isset($descriptions[$identifier])) {
                return $descriptions[$identifier];
            }
        }

        return null;
    }

    /**
     * Add to register
     *
     * @access public
     * @param string $hash
     * @param array $data
     * @return void
     */
    public function add_to_register($hash, $data)
    {
        $this->initialize_register();

        // Note: Currently we are not adding $data in order to save space in
        // session data, however, we may add some properties later on if needed
        $this->current_register[$hash] = array();
    }

    /**
     * Initialize register
     *
     * @access public
     * @return void
     */
    public function initialize_register()
    {
        if ($this->current_register === null) {
            $this->current_register = array();
        }
    }

    /**
     * Update register
     *
     * @access public
     * @return void
     */
    public function update_register()
    {
        if (WC()->session !== null && $this->current_register !== null) {
            WC()->session->set(('rp_wcdpd_rule_notifications_register_' . $this->context), $this->current_register);
        }
    }



}
