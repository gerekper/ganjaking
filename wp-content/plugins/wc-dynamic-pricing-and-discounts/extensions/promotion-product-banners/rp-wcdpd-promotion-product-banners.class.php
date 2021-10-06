<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Product Banners
 *
 * @class RP_WCDPD_Promotion_Product_Banners
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Promotion_Product_Banners
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 160);

        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));

        // Listen for Ajax calls
        add_action('wp_ajax_rp_wcdpd_load_product_banner', array($this, 'load_product_banner'));
        add_action('wp_ajax_nopriv_rp_wcdpd_load_product_banner', array($this, 'load_product_banner'));
    }

    /**
     * Register settings structure
     *
     * @access public
     * @param array $settings
     * @return array
     */
    public function register_settings_structure($settings)
    {

        $settings['promo']['children']['product_banners'] = array(
            'title' => esc_html__('Product Banners', 'rp_wcdpd'),
            'info'  => esc_html__('Displays banners on product pages when product pricing rules are applicable. Banners can be customized using CSS styling, template overrides or filters.', 'rp_wcdpd'),
            'children' => array(
                'promo_product_banners' => array(
                    'title'     => esc_html__('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_product_banners_position' => array(
                    'title'     => esc_html__('Position', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'woocommerce_before_add_to_cart_form',
                    'class'     => 'if_rp_wcdpd_promo_product_banners',
                    'required'  => true,
                    'options'   => array(
                        'woocommerce_before_add_to_cart_form'       => esc_html__('Add to cart - Before', 'rp_wcdpd'),
                        'woocommerce_after_add_to_cart_form'        => esc_html__('Add to cart - After', 'rp_wcdpd'),
                        'woocommerce_product_meta_start'            => esc_html__('Product meta - Before', 'rp_wcdpd'),
                        'woocommerce_product_meta_end'              => esc_html__('Product meta - After', 'rp_wcdpd'),
                        'woocommerce_single_product_summary'        => esc_html__('Product summary - Before', 'rp_wcdpd'),
                        'woocommerce_after_single_product_summary'  => esc_html__('Product summary - After', 'rp_wcdpd'),
                    ),
                ),
                'promo_product_banners_title' => array(
                    'title'     => esc_html__('Title', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => esc_html__('Promotion', 'rp_wcdpd'),
                    'class'     => 'if_rp_wcdpd_promo_product_banners',
                    'required'  => false,
                ),
                'promo_product_banners_message' => array(
                    'title'     => esc_html__('Message', 'rp_wcdpd'),
                    'type'      => 'textarea',
                    'required'  => true,
                    'class'     => 'if_rp_wcdpd_promo_product_banners',
                    'default'   => esc_html__('Add this product to cart now to get a special price.', 'rp_wcdpd'),
                    'hint'      => esc_html__('Available macros: {{price}} , {{original_price}} , {{description}} . The latter displays public description as set in pricing rule.', 'rp_wcdpd'),
                ),
            ),
        );

        return $settings;
    }

    /**
     * Set up promotion tool
     *
     * @access public
     * @return void
     */
    public function set_up_promotion_tool()
    {

        // Check this promotion tool is active
        if (!RP_WCDPD_Settings::get('promo_product_banners')) {
            return;
        }

        // Add hook
        add_action(RP_WCDPD_Settings::get('promo_product_banners_position'), array($this, 'print_container'));
    }

    /**
     * Print container
     *
     * @access public
     * @return void
     */
    public function print_container()
    {

        // Do not print container during Ajax requests
        if (is_ajax()) {
            return;
        }

        // Print container
        echo '<div id="rp_wcdpd_promotion_product_banners_container" style="display: none;"></div>';

        // Load jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');
        RightPress_Loader::load_jquery_plugin('rightpress-live-product-update');

        // Inject styles
        RightPress_Help::inject_stylesheet('rp-wcdpd-promotion-product-banners-styles', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-product-banners/assets/styles.css', RP_WCDPD_VERSION);

        // Enqueue main script
        wp_enqueue_script('rp-wcdpd-promotion-product-banners-scripts', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-product-banners/assets/scripts.js', array('jquery'), RP_WCDPD_VERSION);

        // Pass variables
        wp_localize_script('rp-wcdpd-promotion-product-banners-scripts', 'rp_wcdpd_promotion_product_banners_scripts_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php?rightpress_ajax=1'),
        ));
    }

    /**
     * Load product banner
     *
     * @access public
     * @return void
     */
    public function load_product_banner()
    {

        try {

            // Get price data
            $price_data = RightPress_Product_Price_Test::get_price_data_for_ajax_tools();

            // Check if any pricing rules are applicable
            if ($price_data && !empty($price_data['all_changes']['rp_wcdpd'])) {

                $banners = array();

                // Extract rules
                $rules = array();

                foreach ($price_data['all_changes']['rp_wcdpd'] as $change) {
                    $rules[$change['rule']['uid']] = $change['rule'];
                }

                // Iterate over applicable rules
                foreach ($rules as $rule_uid => $rule) {

                    // Get banner html
                    if ($banner_html = RP_WCDPD_Promotion_Product_Banners::get_banner_html($rule, $price_data)) {

                        // Get banner html hash
                        $html_hash = md5($banner_html);

                        // Ensure only unique banners are displayed
                        if (!isset($banners[$html_hash])) {

                            // Add to banners array
                            $banners[$html_hash] = array(
                                'rule_uid'  => $rule_uid,
                                'html'      => $banner_html,
                            );
                        }
                    }
                }

                // Allow developers to override
                $banners = apply_filters('rp_wcdpd_promotion_product_banners_banners', $banners, $price_data);
            }

            // Check if we have anything to display
            if (!empty($banners)) {

                // Send response
                echo json_encode(array(
                    'result'        => 'success',
                    'display'       => 1,
                    'banners'       => $banners,
                    'banners_hash'  => md5(json_encode($banners)),
                ));
            }
            // Nothing to display
            else {

                // Send response
                echo json_encode(array(
                    'result'    => ($price_data === 'false' ? 'error' : 'success'),
                    'display'   => 0,
                ));
            }
        }
        catch (Exception $e) {

            // Send error response
            echo json_encode(array(
                'result'    => 'error',
                'message'   => $e->getMessage(),
            ));
        }

        exit;
    }

    /**
     * Get banner html
     *
     * @access public
     * @param array $rule
     * @param array $price_data
     * @return string
     */
    public static function get_banner_html($rule, $price_data)
    {

        $html = '';

        // Allow developers to abort
        if (!apply_filters('rp_wcdpd_promotion_product_banners_display', true, $rule, $price_data)) {
            return $html;
        }

        // Include template
        ob_start();
        RightPress_Help::include_extension_template('promotion-product-banners', 'banner', RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array(
            'title'     => RP_WCDPD_Promotion_Product_Banners::get_title($rule, $price_data),
            'message'   => RP_WCDPD_Promotion_Product_Banners::get_message($rule, $price_data),
        ));
        $html = ob_get_clean();

        // Allow developers to override and return
        return apply_filters('rp_wcdpd_promotion_product_banners_banner_html', $html, $rule, $price_data);
    }

    /**
     * Get title
     *
     * @access public
     * @param array $rule
     * @param array $price_data
     * @return string
     */
    public static function get_title($rule, $price_data)
    {

        // Get title
        $raw_title = RP_WCDPD_Settings::get('promo_product_banners_title', '');

        // Allow developers to override
        $raw_title = apply_filters('rp_wcdpd_promo_product_banners_raw_title', $raw_title, $rule, $price_data);

        // No modifications at the moment
        $title = $raw_title;

        // Replace macros
        $title = RP_WCDPD_Promotion_Product_Banners::replace_macros($title, $rule, $price_data);

        // Allow developers to override
        $title = apply_filters('rp_wcdpd_promo_product_banners_title', $title, $raw_title, $rule, $price_data);

        // Return title
        return $title;
    }

    /**
     * Get message
     *
     * @access public
     * @param array $rule
     * @param array $price_data
     * @return string
     */
    public static function get_message($rule, $price_data)
    {

        // Get message
        $raw_message = RP_WCDPD_Settings::get('promo_product_banners_message', '');

        // Allow developers to override
        $raw_message = apply_filters('rp_wcdpd_promo_product_banners_raw_message', $raw_message, $rule, $price_data);

        // Replace new lines with <br> instances
        $message = nl2br($raw_message);

        // Replace macros
        $message = RP_WCDPD_Promotion_Product_Banners::replace_macros($message, $rule, $price_data);

        // Remove double line breaks
        $message = preg_replace('/<br[^>]*>(\s*<br[^>]*>)+/', '<br>', $message);

        // Allow developers to override
        $message = apply_filters('rp_wcdpd_promo_product_banners_message', $message, $raw_message, $rule, $price_data);

        // Return message
        return $message;
    }

    /**
     * Replace macros
     *
     * @access public
     * @param string $text
     * @param array $rule
     * @param array $price_data
     * @return string
     */
    public static function replace_macros($text, $rule, $price_data)
    {

        // Search
        $search = array(
            '{{description}}',
            '{{price}}',
            '{{original_price}}',
        );

        // Get raw macro values
        $raw_description    = $rule['public_note'];
        $raw_price          = $price_data['price'];
        $raw_original_price = $price_data['original_price'];

        // Replace
        $replace = array(
            apply_filters('rp_wcdpd_promotion_product_banners_macro_description_value', $raw_description, $raw_description, $rule, $price_data),
            apply_filters('rp_wcdpd_promotion_product_banners_macro_price_value', wc_price($raw_price), $raw_price, $rule, $price_data),
            apply_filters('rp_wcdpd_promotion_product_banners_macro_original_price_value', wc_price($raw_original_price), $raw_original_price, $rule, $price_data),
        );

        // Replace macros and return
        return str_replace($search, $replace, $text);
    }





}

RP_WCDPD_Promotion_Product_Banners::get_instance();
