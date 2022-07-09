<?php

/**
 * Description of A2W_FrontendShippingController
 *
 * @author MA_GROUP
 * @autoload: a2w_frontend_init
 */
if (!class_exists('A2W_FrontendShippingController')):

    class A2W_FrontendShippingController extends A2W_AbstractController
{

        private $woocommerce_model;

        private $cart_item_key = '';
        private $is_minicart = false;
        private $shipping_display_type = '';

        public function __construct()
    {
            parent::__construct(A2W()->plugin_path() . '/view/');

            if (a2w_get_setting('aliship_frontend') && A2W_Woocommerce::is_woocommerce_installed()) {

                $this->shipping_display_type = a2w_get_setting('aliship_selection_type');

                add_action('init', array($this, 'init'), 10, 1);
            }
        }

        public function init()
    {

            $this->woocommerce_model = new A2W_Woocommerce();

            add_action('wp_enqueue_scripts', array($this, 'assets'));

            //show shipping popup or drop-down on the product page
            if (a2w_get_setting('aliship_product_enable')) {

                if (a2w_get_setting('aliship_product_position') === 'before_cart') {
                    add_action('woocommerce_before_add_to_cart_button', array(
                        $this,
                        'render_shipping_on_product_page',
                    ));
                } else {
                    add_action('woocommerce_after_add_to_cart_button', array(
                        $this,
                        'render_shipping_on_product_page',
                    ));
                }
            }

            //save cart item key
            add_filter('woocommerce_cart_item_name', array($this, 'woocommerce_cart_item_name'), 10, 3);

            //show shipping popup or drop-down on the cart and checkout pages
            add_filter('woocommerce_get_item_data', array($this, 'woocommerce_get_item_data'), 10, 2);

            //Change allowed html tags to use for cart item shipping html
            add_action('woocommerce_before_template_part', array($this, 'woocommerce_before_template_part'));
            add_action('woocommerce_after_template_part', array($this, 'woocommerce_after_template_part'));

            //Do not show shipping info in mini cart
            add_action('woocommerce_before_mini_cart', array($this, 'woocommerce_before_mini_cart'));
            add_action('woocommerce_after_mini_cart', array($this, 'woocommerce_after_mini_cart'));

            //Do not expire cart if cart contains removed items because of unavailable Ali shipping
            add_filter('woocommerce_checkout_update_order_review_expired', array($this, 'woocommerce_checkout_update_order_review_expired'));

            //Update checkout when selecting an other shipping carrier or country
            add_action('woocommerce_checkout_update_order_review', array($this, 'woocommerce_checkout_update_order_review'), 99);

            //force billing country is set to shipping country (for checkout page)
            add_action('woocommerce_calculated_shipping', array($this, 'woocommerce_calculated_shipping'), 99);

            //save shipping data to the order
            add_action('woocommerce_checkout_create_order_line_item', array($this, 'woocommerce_checkout_create_order_line_item'), 10, 4);
        }

        public function assets()
    {

            if (is_cart() || is_checkout() || (is_product() && a2w_get_setting('aliship_product_enable'))) {

                if ($this->shipping_display_type == "popup") {

                    wp_enqueue_script('jquery-ui-dialog');
                    wp_enqueue_style('wp-jquery-ui-dialog');

                    wp_enqueue_script('a2w-aliexpress-shipping-script', A2W()->plugin_url() . '/assets/js/shipping_popup.js', array(), A2W()->version, true);

                }

                if ($this->shipping_display_type == "select") {

                    wp_enqueue_script('a2w-aliexpress-shipping-script', A2W()->plugin_url() . '/assets/js/shipping_select.js', array(), A2W()->version, true);

                }

                wp_enqueue_style('a2w-aliexpress-frontend-style', A2W()->plugin_url() . '/assets/css/frontend.css', array(), A2W()->version);

                $lang_data = array(
                    'apply' => esc_html__('Apply', 'ali2woo'),
                );

                $script_data = array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'lang' => $lang_data,
                );
                wp_localize_script('a2w-aliexpress-shipping-script', 'a2w_ali_ship_data', $script_data);
            }

        }

        public function render_shipping_on_product_page()
    {

            global $product;

            //do not show this html for external product type for;
            if ('external' == $product->get_type()) {
                return;
            }

            $external_id = get_post_meta($product->get_id(), '_a2w_external_id', true);

            if ($external_id) {

                $product_id = $product->get_id();

                $default_shipping_to_country = $this->get_default_shipping_country();

                $default_shipping_method = a2w_get_setting('fulfillment_prefship');

                $countries = array_merge(array('' => __('Select a Country', 'ali2woo')), A2W_Shipping::get_countries());

                $this->model_put("countries", $countries);

                $this->model_put("product_id", $product_id);

                $this->model_put("default_country", $default_shipping_to_country);

                $this->model_put("show_label", true);

                $data = $this->get_shipping_data_for_render($product, 1, $default_shipping_to_country, $default_shipping_method);

                $country_label = $countries[$default_shipping_to_country];

                $shipping_option_text = a2w_get_setting('aliship_shipping_option_text');

                if ($this->shipping_display_type == "popup") {

                    $method_label = $data['method_label'];

                    $default_shipping_method = $data['shipping_method'];

                    $min_price_html = $data['min_price_html'];

                    $delivery_time = $data['delivery_time'];

                    $this->model_put("min_price", $min_price_html);

                    $this->model_put("shipping_methods", $data['normalized_methods']);

                    $this->model_put("shipping_to_country_allowed", true);

                    if (empty($data['normalized_methods'])) {
                        $this->model_put("shipping_to_country_allowed", false);
                    }

                    $this->model_put("shipping_info_data", $data);

                    if ($default_shipping_method) {

                        $shipping_info = str_replace(array(
                            '{shipping_cost}',
                            '{shipping_company}',
                            '{delivery_time}',
                            '{country}',
                        ), array($min_price_html, $method_label, $delivery_time, $country_label), $shipping_option_text);

                        $this->model_put("shipping_info", $shipping_info);

                    } else {

                        if (a2w_get_setting('aliship_not_available_remove')) {

                            //if this product page and option is enabled we use a separate not available message
                            $this->model_put("shipping_info", str_replace('{country}', $country_label, a2w_get_setting('aliship_product_not_available_message')));
                        } else {

                            $this->model_put("shipping_info", A2W_Shipping::get_not_available_shipping_message($country_label));

                        }

                    }

                    if (!$default_shipping_method) {
                        $default_shipping_method = A2W_Shipping::get_fake_method_id();
                    }

                    $this->model_put("default_shipping_method", $default_shipping_method);

                    $this->include_view('shipping/shipping_popup.php');

                } else if ($this->shipping_display_type == "select") {

                $normalized_methods = $data['normalized_methods'];

                $default_shipping_method = $data['shipping_method'];

                $this->model_put("shipping_to_country_allowed", true);

                if (count($normalized_methods) == 1 && empty(array_values($normalized_methods)[0])) {
                    $this->model_put("shipping_to_country_allowed", false);
                    unset($normalized_methods[0]);

                    $default_shipping_method = A2W_Shipping::get_fake_method_id();

                    $normalized_methods[$default_shipping_method] = '';

                }

                if (a2w_get_setting('aliship_not_available_remove')) {

                    //if this product page and option is enabled we use a separate not available message
                    $this->model_put("shipping_info", str_replace('{country}', $country_label, a2w_get_setting('aliship_product_not_available_message')));
                } else {

                    $this->model_put("shipping_info", A2W_Shipping::get_not_available_shipping_message($country_label));

                }

                $this->model_put("shipping_methods", $normalized_methods);
                $this->model_put("default_shipping_method", $default_shipping_method);

                $this->include_view('shipping/shipping_select.php');

            }

        }

    }

    public function woocommerce_cart_item_name($name, $cart_item, $cart_item_key)
    {
        $this->cart_item_key = $cart_item_key;

        return $name;
    }

    public function woocommerce_get_item_data($item_data, $cart_item)
    {

        if ($this->is_minicart || !$this->cart_item_key) {
            return $item_data;
        }

        $product_id = $cart_item['product_id'];

        $external_id = get_post_meta($product_id, '_a2w_external_id', true);

        $cart_item_key = $this->cart_item_key;

        if ($external_id) {

            $default_shipping_to_country = $this->get_default_shipping_country();

            $default_shipping_method = a2w_get_setting('fulfillment_prefship');

            if (isset($cart_item['a2w_to_country']) && $cart_item['a2w_to_country'] !== $default_shipping_to_country) {
                //if country is changed, then we need to recheck the shipping method
                //and initially assume there is no shipping method available for that country
                //set the shipping method to fake
                $cart_item['a2w_shipping_method'] = A2W_Shipping::get_fake_method_id();
            }

            $default_shipping_method = isset($cart_item['a2w_shipping_method']) ? $cart_item['a2w_shipping_method'] : $default_shipping_method;

            $this->model_put("countries", array());
            $this->model_put("product_id", $product_id);
            $this->model_put("cart_item_key", $cart_item_key);
            $this->model_put("show_label", false);

            $normalized_methods = array();

            $countries = A2W_Shipping::get_countries();

            $data = $this->get_shipping_data_for_render($cart_item['data'], $cart_item['quantity'], $default_shipping_to_country, $default_shipping_method);

            $country_label = $countries[$default_shipping_to_country];

            $default_shipping_method = $data['shipping_method'];

            WC()->cart->cart_contents[$cart_item_key]['a2w_shipping_method'] =
            $default_shipping_method ? $default_shipping_method : A2W_Shipping::get_fake_method_id();

            WC()->cart->cart_contents[$cart_item_key]['a2w_to_country'] = $default_shipping_to_country;

            WC()->cart->set_session(); //remove this?

            $shipping_option_text = a2w_get_setting('aliship_shipping_option_text');

            if ($this->shipping_display_type == "popup") {

                $method_label = $data['method_label'];

                $delivery_time = $data['delivery_time'];

                $min_price_html = $data['min_price_html'];

                $this->model_put("min_price", $min_price_html);

                $this->model_put("shipping_methods", $data['normalized_methods']);
                $this->model_put("default_shipping_method", $default_shipping_method);

                $this->model_put("shipping_to_country_allowed", true);

                if (empty($data['normalized_methods'])) {
                    $this->model_put("shipping_to_country_allowed", false);
                }

                $this->model_put("shipping_info_data", $data);

                if ($default_shipping_method) {

                    $shipping_info = str_replace(array(
                        '{shipping_cost}',
                        '{shipping_company}',
                        '{delivery_time}',
                        '{country}',
                    ), array($min_price_html, $method_label, $delivery_time, $country_label), $shipping_option_text);

                    $this->model_put("shipping_info", $shipping_info);

                } else {

                    if (a2w_get_setting('aliship_not_available_remove')) {

                        //if this product page and option is enabled we use a separate not available message
                        $this->model_put("shipping_info", str_replace('{country}', $country_label, a2w_get_setting('aliship_product_not_available_message')));
                    } else {

                        $this->model_put("shipping_info", A2W_Shipping::get_not_available_shipping_message($country_label));

                    }

                }

                ob_start();
                $this->include_view('shipping/shipping_popup.php');
                $shipping_content = ob_get_clean();

            } else if ($this->shipping_display_type == "select") {

                $normalized_methods = $data['normalized_methods'];

                $this->model_put("shipping_methods", $normalized_methods);
                $this->model_put("default_shipping_method", $default_shipping_method);

                $this->model_put("shipping_to_country_allowed", true);

                if (count($normalized_methods) == 1 && empty(array_values($normalized_methods)[0])) {
                    $this->model_put("shipping_to_country_allowed", false);
                }

                if (a2w_get_setting('aliship_not_available_remove')) {

                    //if this product page and option is enabled we use a separate not available message
                    $this->model_put("shipping_info", str_replace('{country}', $country_label, a2w_get_setting('aliship_product_not_available_message')));
                } else {

                    $this->model_put("shipping_info", A2W_Shipping::get_not_available_shipping_message($country_label));

                }

                ob_start();
                $this->include_view('shipping/shipping_select.php');
                $shipping_content = ob_get_clean();

            }

            $item_data[] = array(
                'key' => esc_html__('Shipping', 'ali2woo'),
                'value' => $shipping_content,
            );

            $this->cart_item_key = '';

        }

        return $item_data;
    }

    public function woocommerce_before_template_part($name)
    {
        if ($name === 'cart/cart-item-data.php') {
            add_filter('wp_kses_allowed_html', array($this, 'wp_kses_allowed_html'), 10, 2);
        }
    }

    public function woocommerce_after_template_part($name)
    {
        if ($name === 'cart/cart-item-data.php') {
            remove_filter('wp_kses_allowed_html', array($this, 'wp_kses_allowed_html'), 10);
        }
    }

    public function woocommerce_before_mini_cart()
    {
        $this->is_minicart = true;
    }

    public function woocommerce_after_mini_cart()
    {
        $this->is_minicart = false;
    }

    public function wp_kses_allowed_html($allowedposttags, $context)
    {

        $allowedposttags['select'] = array('class' => true, 'id' => true, 'name' => true, 'data-placeholder' => true);
        $allowedposttags['option'] = array('class' => true, 'id' => true, 'value' => true, 'selected' => true);
        $allowedposttags['input'] = array('class' => true, 'id' => true, 'name' => true, 'type' => true, 'value' => true, 'checked' => true);
        $allowedposttags['span'] = array('class' => true, 'data-country' => true, 'data-id' => true);
        $allowedposttags['div'] = array('class' => true, 'id' => true, 'data-initial-shipping-info' => true);
        $allowedposttags['script'] = true;

        return $allowedposttags;
    }

    public function woocommerce_checkout_update_order_review_expired($expired)
    {
        $cart = WC()->cart;
        if (!empty($cart->removed_cart_contents)) {

            foreach ($cart->removed_cart_contents as $cart_item_key => $cart_item) {
                if (isset($cart_item['a2w_shipping_method']) && isset($cart_item['a2w_remove_no_shipping_item'])) {
                    $expired = false;
                    break;
                }
            }
        }

        return $expired;
    }

    public function woocommerce_checkout_update_order_review($data)
    {

        if (is_string($data)) {
            parse_str($data, $post_data);
        } else {
            $post_data = array();
        }
        $current_country = isset($_POST['country']) ? wc_clean(wp_unslash($_POST['country'])) : null;
        if (!wc_ship_to_billing_address_only() && isset($post_data['ship_to_different_address']) && $post_data['ship_to_different_address']) {
            $current_country = isset($_POST['s_country']) ? wc_clean(wp_unslash($_POST['s_country'])) : null;
        }

        $checked_cart_keys = array();
        $default_shipping_method = a2w_get_setting('fulfillment_prefship');

        $cart = WC()->cart;

        //1) Check previusly removed items, restore them if possible
        if (!empty($cart->removed_cart_contents)) {

            foreach ($cart->removed_cart_contents as $cart_item_key => $cart_item) {

                if (isset($cart_item['a2w_shipping_method']) && isset($cart_item['a2w_remove_no_shipping_item'])) {

                    //this is ignore countries array, if current country exists, then keep cart item removed
                    $countries = isset($cart_item['a2w_remove_no_shipping_countries']) ? $cart_item['a2w_remove_no_shipping_countries'] : array();

                    if (in_array($current_country, $countries)) {

                        continue;
                    }

                    $has_shipping = false;

                    $item_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

                    $_product = wc_get_product($item_id);

                    $result = A2W_Utils::get_product_shipping_info($_product, $cart_item['quantity'], $current_country, false);

                    if ($result) {

                        $shipping_methods = $result['items'];

                        if (!empty($shipping_methods)) {

                            foreach ($shipping_methods as $method) {

                                $local_method = A2W_Shipping::get_local_method_by_company($method['company']);

                                if ($local_method !== false) {

                                    unset(WC()->cart->removed_cart_contents[$cart_item_key]['a2w_remove_no_shipping_item']);
                                    WC()->cart->restore_cart_item($cart_item_key);
                                    $checked_cart_keys[$cart_item_key] = true;
                                    WC()->cart->cart_contents[$cart_item_key]['a2w_shipping_method'] = $default_shipping_method;
                                    $has_shipping = true;

                                    break;
                                }

                            }

                        }

                    }

                    if (!$has_shipping) {

                        $this->add_ignore_country_to_remove_cart_item($cart_item_key, $current_country);
                    }
                }
            }
        }

        //2). Check current cart items, remove items if shipping is not available
        if (!empty($cart->cart_contents)) {

            $remove_cart_item = a2w_get_setting('aliship_not_available_remove');

            foreach ($cart->cart_contents as $cart_item_key => $cart_item) {

                if (!isset($checked_cart_keys[$cart_item_key]) && isset($cart_item['a2w_shipping_method']) && isset($cart_item['a2w_to_country'])) {

                    //the country can be changed on the checkout page, then we need to recheck the item shipping
                    if ($cart_item['a2w_to_country'] !== $current_country) {

                        $has_shipping = false;

                        $item_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

                        $_product = wc_get_product($item_id);

                        $result = A2W_Utils::get_product_shipping_info($_product, $cart_item['quantity'], $current_country, false);

                        if ($result) {

                            $shipping_methods = $result['items'];

                            if (!empty($shipping_methods)) {

                                foreach ($shipping_methods as $method) {

                                    $local_method = A2W_Shipping::get_local_method_by_company($method['company']);

                                    if ($local_method !== false) {
                                        $has_shipping = true;

                                        break;
                                    }

                                }

                            }

                        }

                        if (!$has_shipping && $remove_cart_item) {

                            $this->remove_cart_item($cart_item_key, $cart_item, $current_country);
                        }

                    } else {

                        if ($cart_item['a2w_shipping_method'] === A2W_Shipping::get_fake_method_id() && $remove_cart_item) {

                            $this->remove_cart_item($cart_item_key, $cart_item, $current_country);
                        }

                    }
                }
            }

        }

    }

    public function woocommerce_calculated_shipping()
    {

        $shipping_country = WC()->customer->get_shipping_country();

        WC()->customer->set_billing_country($shipping_country);
        WC()->customer->save();
    }

    public function woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
    {
        if (isset($values['a2w_shipping_method'])) {
            $cost_added = false;
            switch (a2w_get_setting('aliship_shipping_type', 'new')) {
                case 'new':
                    $shipping_methods = $order->get_shipping_methods();
                    if (count($shipping_methods)) {
                        foreach ($shipping_methods as $shipping_method) {
                            if ('flat_rate' === $shipping_method->get_instance_id()) {
                                $cost_added = true;
                                break;
                            }
                        }
                    }
                    break;
                case 'new_only':
                case 'add':
                    $cost_added = true;
                    break;
                case 'none':
                default:
            }

            if (isset($values['a2w_shipping_info']) && !$values['a2w_shipping_info']['shipping_cost']) {
                $cost_added = false;
            }

            $shipping_info = isset($values['a2w_shipping_info']) ? $values['a2w_shipping_info'] : array();

            if ($values['a2w_shipping_method'] === A2W_Shipping::get_fake_method_id()) {
                if (!a2w_get_setting('aliship_not_available_remove')) {
                    $shipping_info['shipping_cost'] = a2w_get_setting('aliship_not_available_cost');
                    $shipping_info['delivery_time'] = a2w_get_setting('aliship_not_available_time_min') . '-' . a2w_get_setting('aliship_not_available_time_max');
                    $shipping_info['company'] = '';
                    $shipping_info['service_name'] = '';
                }
            }

            $item->update_meta_data(A2W_Shipping::get_order_item_shipping_meta_key(), json_encode(array(
                'company' => $shipping_info['company'],
                'service_name' => $shipping_info['service_name'],
                'delivery_time' => $shipping_info['delivery_time'],
                'shipping_cost' => $shipping_info['shipping_cost'],
                'quantity' => $item->get_quantity(),
                'cost_added' => $cost_added,
            )));
        }
    }

    private function get_default_shipping_country()
    {

        $country = WC()->customer->get_shipping_country();

        if (isset($_POST['woocommerce-shipping-calculator-nonce'])) {
            if (!empty($_POST['calc_shipping_country'])) {
                $country = sanitize_text_field($_POST['calc_shipping_country']);
            }
        }

        if (is_null($country) || empty($country)) {
            $country = a2w_get_setting('aliship_shipto');
        }

        return $country;
    }

    /**
     * Remove cart item if shipping is not available
     */
    private function remove_cart_item($cart_item_key, $cart_item, $country)
    {
        if ($cart_item) {
            if (WC()->cart->remove_cart_item($cart_item_key)) {

                WC()->cart->removed_cart_contents[$cart_item_key]['a2w_shipping_method'] = A2W_Shipping::get_fake_method_id();
                WC()->cart->removed_cart_contents[$cart_item_key]['a2w_to_country'] = $country;

                //this parameter makes a difference between items removed by user and programmatically removed
                WC()->cart->removed_cart_contents[$cart_item_key]['a2w_remove_no_shipping_item'] = time();

                $this->add_ignore_country_to_remove_cart_item($cart_item_key, $country);

                $product = wc_get_product($cart_item['product_id']);

                $item_removed_title = apply_filters('woocommerce_cart_item_removed_title', $product ? sprintf(_x('&ldquo;%s&rdquo;', 'Item name in quotes', 'ali2woo'), $product->get_name()) : __('Item', 'ali2woo'), $cart_item);
                $wc_countries = WC()->countries->get_countries();
                $removed_notice = isset($wc_countries[$country]) ? sprintf(__('%s removed because it can not be delivered to %s.', 'ali2woo'), $item_removed_title, $wc_countries[$country]) : sprintf(__('%s removed because it can not be delivered to your country.', 'ali2woo'), $item_removed_title);
                wc_add_notice($removed_notice, apply_filters('woocommerce_cart_item_removed_notice_type', 'error'));
            }
        }
    }

    private function add_ignore_country_to_remove_cart_item($cart_item_key, $country)
    {

        $cart_item = WC()->cart->removed_cart_contents[$cart_item_key];

        $countries = isset($cart_item['a2w_remove_no_shipping_countries']) ? $cart_item['a2w_remove_no_shipping_countries'] : array();
        $countries[] = $country;
        $countries = array_unique($countries);
        WC()->cart->removed_cart_contents[$cart_item_key]['a2w_remove_no_shipping_countries'] = $countries;

    }

    /**
     * This function prepare shipping data for rendering on  the product page page, cart & checkout
     * It compares the $default_shipping_method with other methods and find the method with minimal price among them
     * If $default_shipping_method doesn't exist at all, then the data of minimal method will be returned instead (label, price)
     * Also, the function retuns normalized label for all methods for the product
     */
    private function get_shipping_data_for_render($product, $quantity, $default_shipping_to_country, $default_shipping_method)
    {

        $res = array('min_price_html' => '', 'method_label' => '', 'delivery_time' => '', 'normalized_methods' => array(), 'shipping_method' => false);

        $result = A2W_Utils::get_product_shipping_info($product, $quantity, $default_shipping_to_country, false);

        $normalized_methods = array();
        $tmp_methods = array();

        if ($result) {

            $shipping_methods = $result['items'];

            if (!empty($shipping_methods)) {

                $search_tariff_code = $default_shipping_method;

                $min_method = false;

                foreach ($shipping_methods as $method) {

                    $normalized_method = A2W_Shipping::get_normalized($method, $default_shipping_to_country, "select", $product);

                    if (!$normalized_method) {
                        continue;
                    }

                    $tmp_methods[$method['serviceName']] = $normalized_method;

                    if (!$min_method || $normalized_method['price'] < $min_method['price']) {
                        $min_method = $normalized_method;
                    }

                    if ($this->shipping_display_type == "select") {
                        $normalized_methods[$method['serviceName']] = $normalized_method['label'];
                    }
                    if ($this->shipping_display_type == "popup") {
                        $normalized_methods[$method['serviceName']] = $normalized_method;
                    }
                }

                if (isset($tmp_methods[$search_tariff_code])) {
                    $min_price = $tmp_methods[$search_tariff_code]['price'];
                    $method_label = $tmp_methods[$search_tariff_code]['company'];
                    $delivery_time = $tmp_methods[$search_tariff_code]['formated_delivery_time'];

                } else {
                    $min_price = $min_method['price'];
                    $method_label = $min_method['company'];
                    $delivery_time = $min_method['formated_delivery_time'];

                    $default_shipping_method = $min_method['serviceName'];
                }

                $min_price = apply_filters('wcml_raw_price_amount', $min_price);
                $min_price_html = ($min_price ? strip_tags(wc_price($min_price)) : __('free', 'ali2woo'));

                $res['min_price_html'] = $min_price_html;
                $res['method_label'] = $method_label;
                $res['delivery_time'] = $delivery_time;

                if ($this->shipping_display_type == "select") {
                    if (empty($normalized_methods)) {
                        /**
                         * force generation of the shipping drop-down select
                         * by adding first empty element for woocommerce_form_field()
                         *
                         */

                        $normalized_methods = array('');
                    }
                }

            } else {

                //if can't deliver to the country

                if ($this->shipping_display_type == "select") {
                    /**
                     * force generation of the shipping drop-down select
                     * by adding first empty element for woocommerce_form_field()
                     *
                     */

                    $normalized_methods = array('');

                }

                if ($this->shipping_display_type == "popup") {

                    $default_shipping_method = false;

                }
            }

        } else {

            //todo: it looks like rudiment condition result can't be false
            //funtion can't come here, remove?

            if ($this->shipping_display_type == "select") {
                /**
                 * force generation of the shipping drop-down select
                 * by adding first empty element for woocommerce_form_field()
                 *
                 */

                $normalized_methods = array('');

            }

            if ($this->shipping_display_type == "popup") {

                $default_shipping_method = false;
            }

        }

        $res['normalized_methods'] = $normalized_methods;
        $res['shipping_method'] = $default_shipping_method;

        return $res;

    }

}

endif;
