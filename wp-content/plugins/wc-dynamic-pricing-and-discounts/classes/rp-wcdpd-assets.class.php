<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to scripts and stylesheets
 *
 * @class RP_WCDPD_Assets
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Assets extends RightPress_Assets
{

    protected $plugin_prefix = RP_WCDPD_PLUGIN_PRIVATE_PREFIX;

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Construct parent
        parent::__construct();








/**
 * TODO: OLD CODE BELOW
 */

        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));

        // Enqueue backend assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_assets'), 20);

        // Enqueue Select2
        add_action('init', array($this, 'enqueue_select2'), 1);
    }













/**
 * TODO: OLD CODE BELOW
 */

    /**
     * Load frontend stylesheets
     *
     * @access public
     * @return void
     */
    public function enqueue_frontend_assets()
    {
        global $post;

        // Checkout scripts
        if (is_checkout()) {
            wp_enqueue_script('rp-wcdpd-checkout-scripts', RP_WCDPD_PLUGIN_URL . '/assets/js/checkout.js', array('jquery'), RP_WCDPD_VERSION);
        }
    }

    /**
     * Enqueue Select2
     *
     * @access public
     * @return void
     */
    public static function enqueue_select2()
    {
        // Load backend assets conditionally
        if (!RP_WCDPD_Settings::is_settings_page()) {
            return;
        }

        // Only load Select2 on WooCommerce 3.1 or lower
        // TODO: check if we can switch to SelectWoo
        //if (!RightPress_Help::wc_version_gte('3.2')) {

            // Enqueue Select2 related scripts and styles
            wp_enqueue_script('rp-wcdpd-select2-scripts', RP_WCDPD_PLUGIN_URL . '/assets/select2/js/select2.full.min.js', array('jquery'), '4.0.3');
            wp_enqueue_script('rp-wcdpd-select2-rp', RP_WCDPD_PLUGIN_URL . '/assets/js/rp-select2.js', array(), RP_WCDPD_VERSION);
            wp_enqueue_style('rp-wcdpd-select2-styles', RP_WCDPD_PLUGIN_URL . '/assets/select2/css/select2.min.css', array(), '4.0.3');

            // Print scripts before WordPress takes care of it automatically (helps load our version of Select2 before any other plugin does it)
            add_action('wp_print_scripts', array('RP_WCDPD_Assets', 'print_select2'));
        //}

        // Load Grouped Select2
        RightPress_Loader::load_jquery_plugin('rightpress-grouped-select2');
    }

    /**
     * Print Select2 scripts
     *
     * @access public
     * @return void
     */
    public static function print_select2()
    {
        remove_action('wp_print_scripts', array('RP_WCDPD_Assets', 'print_select2'));
        wp_print_scripts('rp-wcdpd-select2-scripts');
        wp_print_scripts('rp-wcdpd-select2-rp');
    }

    /**
     * Load backend assets conditionally
     *
     * @access public
     * @return void
     */
    public function enqueue_backend_assets()
    {
        // Load backend assets conditionally
        if (!RP_WCDPD_Settings::is_settings_page()) {
            return;
        }

        // Enqueue jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');

        // Prepare values for JS
        $current_tab = RP_WCDPD_Settings::get_tab();

        // jQuery UI Accordion
        wp_enqueue_script('jquery-ui-accordion');

        // jQuery UI Sortable
        wp_enqueue_script('jquery-ui-sortable');

        // Datetimepicker
        $this->load_asset_scripts('datetimepicker');
        $this->load_asset_styles('datetimepicker');

        // jQuery UI Tooltip
        wp_enqueue_script('jquery-ui-tooltip');

        // Rules page
        if (in_array($current_tab, array('product_pricing', 'cart_discounts', 'checkout_fees'), true)) {
            wp_enqueue_script('rp-wcdpd-rules-scripts', RP_WCDPD_PLUGIN_URL . '/assets/js/rules.js', array('jquery'), RP_WCDPD_VERSION);
            wp_enqueue_script('rp-wcdpd-rules-validation-scripts', RP_WCDPD_PLUGIN_URL . '/assets/js/rules-validation.js', array('jquery'), RP_WCDPD_VERSION);
        }

        // Settings page
        if (in_array($current_tab, array('promo', 'settings'), true)) {
            wp_enqueue_script('rp-wcdpd-settings-scripts', RP_WCDPD_PLUGIN_URL . '/assets/js/settings.js', array('jquery'), RP_WCDPD_VERSION);
        }

        // Use selectWoo starting from WooCommerce 3.2
        // TODO: check if we can switch to SelectWoo
        /*if (RightPress_Help::wc_version_gte('3.2')) {
            wp_enqueue_script('selectWoo');
            wp_enqueue_style('rp-wcdpd-select2-styles', RP_WCDPD_PLUGIN_URL . '/assets/select2/css/select2.min.css', array(), RP_WCDPD_VERSION);
        }*/

        // Backend styles
        wp_enqueue_style('rp-wcdpd-settings-styles', RP_WCDPD_PLUGIN_URL . '/assets/css/settings.css', array(), RP_WCDPD_VERSION);

        // jQuery UI styles
        RightPress_Help::enqueue_or_inject_stylesheet('rp-wcdpd-jquery-ui-styles', RP_WCDPD_PLUGIN_URL . '/assets/jquery-ui/jquery-ui.min.css', '1.12.1');

        // Get correct row note placeholder
        if ($current_tab === 'cart_discounts') {
            $row_note_placeholder = esc_html__('Cart Discount', 'rp_wcdpd');
        }
        else if ($current_tab === 'checkout_fees') {
            $row_note_placeholder = esc_html__('Checkout Fee', 'rp_wcdpd');
        }
        else {
            $row_note_placeholder = esc_html__('Pricing Rule', 'rp_wcdpd');
        }

        // Pass variables to settings JS UI
        wp_localize_script('rp-wcdpd-settings-scripts', 'rp_wcdpd', array(
            'ajaxurl'       => RP_WCDPD_Ajax::get_url(),
            'current_tab'   => $current_tab,
            'labels'        => array(
                'select2_placeholder_custom_product_taxonomies' => esc_html__('No taxonomies enabled', 'rp_wcdpd'),
                'select2_placeholder'                           => esc_html__('Select values', 'rp_wcdpd'),
                'select2_no_results'                            => esc_html__('No results found', 'rp_wcdpd'),
            ),
        ));

        // Pass variables to rules JS UI
        wp_localize_script('rp-wcdpd-rules-scripts', 'rp_wcdpd', array(
            'ajaxurl'           => RP_WCDPD_Ajax::get_url(),
            'current_tab'       => $current_tab,
            'price_format'                      => sprintf(get_woocommerce_price_format(), get_woocommerce_currency_symbol(), '{{value}}'),
            'price_decimals'                    => wc_get_price_decimals(),
            'product_pricing_method_titles'     => RP_WCDPD_Settings::get_product_pricing_methods_for_display(false),
            'title_format_bogo'                 => sprintf(esc_html__('Buy %s get %s', 'rp_wcdpd'), '{{x}}', '{{y}}'),
            'title_format_bogo_repeat'          => sprintf(esc_html__('Buy %s get %s - Repeating', 'rp_wcdpd'), '{{x}}', '{{y}}'),
            'labels'                            => array(
                'select2_placeholder'   => esc_html__('Select values', 'rp_wcdpd'),
                'select2_no_results'    => esc_html__('No results found', 'rp_wcdpd'),
                'row_note_placeholder'  => $row_note_placeholder,
                'per_item'              => esc_html__('per item', 'rp_wcdpd'),
                'per_cart_item'         => esc_html__('per cart item', 'rp_wcdpd'),
                'per_cart_line'         => esc_html__('per cart line', 'rp_wcdpd'),
                'per_group'             => esc_html__('per group', 'rp_wcdpd'),
            ),
            'error_messages'    => array(
                'generic_error'                     => esc_html__('Error: Please fix this element.', 'rp_wcdpd'),
                'required'                          => esc_html__('Value is required.', 'rp_wcdpd'),
                'number_natural'                    => esc_html__('Value must be positive.', 'rp_wcdpd'),
                'number_min_0'                      => esc_html__('Value must be positive.', 'rp_wcdpd'),
                'number_min_1'                      => esc_html__('Value must be greater than or equal to 1.', 'rp_wcdpd'),
                'number_whole'                      => esc_html__('Value must be a whole number.', 'rp_wcdpd'),
                'no_quantity_ranges'                => esc_html__('At least one quantity range is required for this pricing rule.', 'rp_wcdpd'),
                'no_group_products'                 => esc_html__('At least one product must be added to a group.', 'rp_wcdpd'),
                'no_conditions'                     => esc_html__('At least one condition is required for this rule.', 'rp_wcdpd'),
                'quantity_ranges_from_more_than_to' => esc_html__('Closing quantity must not be lower than opening quantity.', 'rp_wcdpd'),
                'quantity_ranges_last_to_open'      => esc_html__('Quantity range cannot be left open when adding subsequent quantity ranges.', 'rp_wcdpd'),
                'quantity_ranges_last_from_higher'  => esc_html__('Quantity range must start with a higher value than previous quantity range.', 'rp_wcdpd'),
                'quantity_ranges_overlap'           => esc_html__('Quantity ranges must not overlap.', 'rp_wcdpd'),
                'condition_non_existent'            => esc_html__('Rule must not contain conditions of non-existent type.', 'rp_wcdpd'),
                'condition_disabled'                => esc_html__('Rule must not contain disabled conditions.', 'rp_wcdpd'),
            ),
            'open_rule_uid' => !empty($_REQUEST['open_rule_uid']) ? $_REQUEST['open_rule_uid'] : null,
        ));
    }





}

RP_WCDPD_Assets::get_instance();
