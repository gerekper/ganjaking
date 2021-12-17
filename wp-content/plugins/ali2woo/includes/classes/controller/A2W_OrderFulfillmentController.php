<?php

/**
 * Description of A2W_OrderFulfillmentController
 *
 * @author MA_GROUP
 *
 * @autoload: a2w_admin_init
 *
 * @ajax: true
 */
if (!class_exists('A2W_OrderFulfillmentController')) {

    class A2W_OrderFulfillmentController extends A2W_AbstractController
    {

        public function __construct()
        {
            parent::__construct(A2W()->plugin_path() . '/view/');
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : (isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : "");
            if (is_admin() && $post_type == "shop_order") {
                add_action('admin_enqueue_scripts', array($this, 'assets'));
                add_action('admin_footer', array($this, 'place_orders_bulk_popup'));
            }

            add_filter('a2w_wcol_bulk_actions_init', array($this, 'bulk_actions'));
            add_action('wp_ajax_a2w_get_aliexpress_order_data', array($this, 'get_aliexpress_order_data'));
        }

        public function assets()
        {
            wp_enqueue_script('a2w-ali-orderfulfill-js', A2W()->plugin_url() . '/assets/js/orderfulfill.js', array(), A2W()->version, true);

            wp_enqueue_script('a2w-sprintf-script', A2W()->plugin_url() . '/assets/js/sprintf.js', array(), A2W()->version);

            $lang_data = array(
                'placing_orders_d_of_d' => _x('Placing orders %d/%d...', 'Status', 'ali2woo'),
                'please_wait_data_loads' => _x('Please wait, data loads..', 'Status', 'ali2woo'),

                'process_update_d_of_d_erros_d' => _x('Process update %d of %d. Errors: %d.', 'Status', 'ali2woo'),
                'complete_result_updated_d_erros_d' => _x('Complete! Result updated: %d; errors: %d.', 'Status', 'ali2woo'),
                'install_chrome_ext' => _x('Please install and connect to your website the Ali2Woo chrome extension to use this feature.', 'Status', 'ali2woo'),
                'please_connect_chrome_extension_check_d' => _x('Please connect the Chrome extension to your store and then continue. Need help? Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),
                'we_found_old_order' => _x('We found an old order fulfillment process and removed it. Press the "Continue" button.', 'Status', 'ali2woo'),
                'login_into_aliexpress_account' => _x('Please switch to AliExpress tab and login into your AliExpress account.', 'Status', 'ali2woo'),
                'detected_old_aliexpress_interface' => _x('Detected old AliExpress interface. Please contact Ali2Woo support.', 'Status', 'ali2woo'),
                'your_customer_address_entered' => _x('Your customer address is entered. Wait...', 'Status', 'ali2woo'),
                'product_is_added_to_cart' => _x('Product (%d) is added to the cart. Wait...', 'Status', 'ali2woo'),
                'all_products_are_added' => _x('All products are added to the cart. Wait...', 'Status', 'ali2woo'),
                'cart_is_cleared' => _x('The previous cart data is cleared. Wait...', 'Status', 'ali2woo'),
                'get_no_responces_from_chrome_ext_d' => _x('Get no responces from the chrome extension for 30s. Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),
                'fill_order_note' => _x('Filling order notes...', 'Status', 'ali2woo'),
                'cant_add_product_to_cart_d' => _x('Can`t add this product to the cart. Switch to AliExpress and choose another one or add a similar product from another supplier manually. Then continue. Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),
                'please_type_customer_address' => _x('Please switch to AliExpress tab, type the address or skip this order.', 'Status', 'ali2woo'),
                'please_input_captcha' => _x('Please switch to AliExpress and input the Captcha code manually or wait for your Captcha solver to do the job...', 'Status', 'ali2woo'),
                'order_is_placed' => _x('The order is placed. Wait...', 'Status', 'ali2woo'),
                'internal_aliexpress_error' => _x('Internal AliExpress error. Please continue to try again or skip this order.', 'Status', 'ali2woo'),
                'all_orders_are_placed' => _x('All orders are placed! Click "Orders List" to be directed to the orders list on the AliExpress website.', 'Status', 'ali2woo'),
                'cant_process_your_orders' => _x('We can`t process your orders. Check out the "Status Page" for more details.', 'Status', 'ali2woo'),
                'cant_get_order_id' => _x('Can`t get the external order ID, please copy it manually to your WC order. Then continue.', 'Status', 'ali2woo'),
                'payment_is_failed' => _x('The payment is failed, please finish this order manually. Then continue.', 'Status', 'ali2woo'),
                'done_pay_manually' => _x('Please switch to AliExpress and pay for the order.', 'Status', 'ali2woo'),
                'choose_payment_method' => _x('Please switch to AliExpress and choose payment method.', 'Status', 'ali2woo'),

                'please_activate_right_store_apikey_in_chrome' => _x('This website is not connected to the Ali2Woo chrome extension. Please check that you choose right API key.', 'Status', 'ali2woo'),

                'bad_product_id' => _x('Can`t find the WC order with a given ID.', 'Status', 'ali2woo'),
                'no_variable_data' => _x('This order has a variable product but doesn`t contain the variable data for some reason. Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),
                'no_product_url' => _x('This order doesn`t contain the `product_url` field for some reason. Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),
                'no_ali_products' => _x('No AliExpress products in the current order. Check out <a href="%s">the instruction</a>', 'Status', 'ali2woo'),

                'unknown_error' => _x('Unknown error occured. Please contact support.', 'Status', 'ali2woo'),
                'server_error' => _x('Server error. Continue to try again.', 'Status', 'ali2woo'),
            );

            wp_localize_script('a2w-ali-orderfulfill-js', 'a2w_ali_orderfulfill_js', array('lang' => $lang_data));

        }

        public function bulk_actions($params)
        {
            $params[0][] = 'a2w_order_place_bulk';
            $params[1]['a2w_order_place_bulk'] = __("Place on AliExpress", 'ali2woo');

            return $params;
        }

        public function place_orders_bulk_popup()
        {
            $this->include_view('includes/place_orders_bulk_popup.php');
        }

        public function get_aliexpress_order_data()
        {

            $result = array("state" => "ok", "data" => "", "action" => "");

            $post_id = isset($_POST['id']) ? $_POST['id'] : false;

            if (!$post_id) {
                $result['state'] = 'error';
                $result['error_code'] = -1;
                echo json_encode($result);
                wp_die();
            }

            $order = new WC_Order($post_id);

            $def_prefship = a2w_get_setting('fulfillment_prefship');
            $def_customer_note = a2w_get_setting('fulfillment_custom_note');
            $def_phone_number = a2w_get_setting('fulfillment_phone_number');
            $def_phone_code = a2w_get_setting('fulfillment_phone_code');
            $a2w_order_autopay = a2w_get_setting('order_autopay');
            $a2w_order_awaiting_payment = a2w_get_setting('order_awaiting_payment');

            $content = array('id' => $post_id,
                'defaultShipping' => $def_prefship,
                'note' => $def_customer_note !== "" ? $def_customer_note : $this->get_customer_note($order),
                'products' => array(),
                'countryRegion' => $this->get_country_region($order),
                'region' => strtolower($this->get_region($order)),
                'city' => $this->get_city($order),
                'contactName' => $this->get_contactName($order),
                'address1' => $this->get_address1($order),
                'address2' => $this->get_address2($order),
                'mobile' => $def_phone_number !== "" ? $def_phone_number : $this->get_phone($order),
                'mobile_code' => $def_phone_code !== "" ? $def_phone_code : '',
                'zip' => $this->get_zip($order),
                'autopay' => $a2w_order_autopay,
                'awaitingpay' => $a2w_order_awaiting_payment,
                'cpf' => $this->get_cpf($order),
                'storeurl' => get_site_url(),
                'currency' => $this->get_currency($order),
            );

            $items = $order->get_items();

            $k = 0;
            $total = 0;
            foreach ($items as $item) {

                $normalized_item = new A2W_WooCommerceOrderItem($item);
                $product_id = $normalized_item->getProductID();
                $variation_id = $normalized_item->getVariationID();
                $quantity = $normalized_item->getQuantity();

                $external_id = get_post_meta($product_id, '_a2w_external_id', true);

                if ($external_id) {

                    $skuArray = $this->getSkuArray($normalized_item);

                    if (empty($skuArray) && $variation_id && $variation_id > 0) {
                        $result['error_code'] = -2;
                        $result['state'] = 'error';
                        echo json_encode($result);
                        wp_die();
                    };

                    $original_url = get_post_meta($product_id, '_a2w_product_url', true);

                    if (empty($original_url)) {
                        $result['error_code'] = -3;
                        $result['state'] = 'error';
                        echo json_encode($result);
                        wp_die();
                    }

                    //try to use shipping method that user choose on the product page, cart or checkout
                    //if it returns empty, then keep it
                    //because chrome extension chosoe default shipping method in this case
                    $shipping_service_name = $normalized_item->get_A2W_ShippingCode();

                    //todo: make an ability to change the shipping method
                    //before place order on AliExpress

                    $content['products'][$k] = array(
                        'url' => $original_url,
                        'productId' => $external_id,
                        'originalId' => $product_id,
                        'qty' => $quantity,
                        'sku' => $skuArray,
                        'shipping' => $shipping_service_name,
                    );

                    $k++;
                }

                $total++;
            }

            if ($k < 1) {
                $result['error_code'] = -4;
                $result['state'] = 'error';
                echo json_encode($result);
                wp_die();
            }

            if ($k == $total) {
                $result['action'] = 'upd_ord_status';
            }

            $result['data'] = array('content' => $content, 'id' => $post_id);

            echo json_encode($result);
            wp_die();
        }

        private function format_field($str)
        {
            $str = trim($str);

            if (!empty($str)) {
                $str = ucwords(strtolower($str));
            }

            return $str;
        }

        private function get_currency($order)
        {
            return strtolower($order->get_currency());
        }

        private function get_cpf($order)
        {

            $b_cpf = $order->get_meta('_billing_cpf');
            $s_cpf = $order->get_meta('_shipping_cpf');

            $cpf = $b_cpf ? $b_cpf : ($s_cpf ? $s_cpf : '');

            $cpf = $cpf ? preg_replace("/[^0-9]/", "", $cpf) : '';

            return $cpf;

        }

        private function get_phone($order)
        {
            if (WC()->version < '3.0.0') {
                $result = $order->billing_phone ? $order->billing_phone : $order->shipping_phone;
            } else {
                $result = $order->get_billing_phone();
            }

            $result = preg_replace('/[^0-9]+/', '', $result);

            return $result;
        }

        private function get_customer_note($order)
        {
            if (WC()->version < '3.0.0') {
                $result = $order->customer_note;
            } else {
                $result = $order->get_customer_note();
            }

            return $this->translitirate($result);
        }

        private function get_country_region($order)
        {
            if (WC()->version < '3.0.0') {
                $result = $order->shipping_country ? $this->format_field_country($order->shipping_country) : $this->format_field_country($order->billing_country);
            } else {
                $result = $order->get_shipping_country() ? $this->format_field_country($order->get_shipping_country()) : $this->format_field_country($order->get_billing_country());
            }

            return $this->translitirate($result);
        }

        private function get_region($order)
        {
            if (WC()->version < '3.0.0') {
                $result = $order->shipping_state ? $this->format_field_state($order->shipping_country, $order->shipping_state) : $this->format_field_state($order->billing_country, $order->billing_state);
            } else {
                $result = $order->get_shipping_state() ? $this->format_field_state($order->get_shipping_country(), $order->get_shipping_state()) : $this->format_field_state($order->get_billing_country(), $order->get_billing_state());
            }

            return $this->translitirate($result);
        }

        private function get_city($order)
        {

            if (WC()->version < '3.0.0') {
                $result = $order->shipping_city ? $this->format_field($order->shipping_city) : $this->format_field($order->billing_city);
            } else {
                $result = $order->get_shipping_city() ? $this->format_field($order->get_shipping_city()) : $this->format_field($order->get_billing_city());
            }

            return $this->translitirate($result);
        }

        private function get_contactName($order)
        {

            if (WC()->version < '3.0.0') {

                if ($order->shipping_first_name) {
                    $result = $order->shipping_first_name . ' ' . $order->shipping_last_name;

                    if (isset($this->shipping_third_name)) {
                        $result .= ' ' . $order->shipping_third_name;
                    }
                } else {
                    $result = $order->billing_first_name . ' ' . $order->billing_last_name;

                    if (isset($this->billing_third_name)) {
                        $result .= ' ' . $order->billing_third_name;
                    }
                }

            } else {
                $result = $order->get_shipping_first_name() ? $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() . ' ' . $order->get_meta('_shipping_third_name') : $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . ' ' . $order->get_meta('_billing_third_name');
            }

            return $this->translitirate($result);
        }

        private function get_address_number($order)
        {

            $b_number = $order->get_meta('_billing_number');
            $s_number = $order->get_meta('_shipping_number');

            $number = $b_number ? $b_number : ($s_number ? $s_number : '');

            $number = $number ? preg_replace("/[^0-9]/", "", $number) : '';

            return $number;

        }

        private function get_address1($order)
        {

            if (WC()->version < '3.0.0') {
                $result = $order->shipping_address_1 ? $order->shipping_address_1 : $order->billing_address_1;
            } else {
                $result = $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1();
            }

            //Add street number if it's available
            $result = $result . " " . $this->get_address_number($order);

            return $this->translitirate($result);
        }

        private function get_address2($order)
        {

            if (WC()->version < '3.0.0') {
                $result = $order->shipping_address_2 ? $order->shipping_address_2 : $order->billing_address_2;
            } else {
                $result = $order->get_shipping_address_2() ? $order->get_shipping_address_2() : $order->get_billing_address_2();
            }

            return $this->translitirate($result);
        }

        private function get_zip($order)
        {

            if (WC()->version < '3.0.0') {
                $result = $order->shipping_postcode ? $order->shipping_postcode : $order->billing_postcode;
            } else {
                $result = $order->get_shipping_postcode() ? $order->get_shipping_postcode() : $order->get_billing_postcode();
            }

            return $result;
        }

        private function format_field_country($str)
        {
            $str = trim($str);

            if (!empty($str)) {
                $str = strtoupper($str);
            }

            if ($str === "GB") {
                $str = "UK";
            }

            if ($str == "RS") {
                $str = "SRB";
            }

            if ($str == "ME") {
                $str = "MNE";
            }

            return $str;
        }

        private function format_field_state($country_code, $state_code)
        {
            if (isset(WC()->countries->states[$country_code]) && isset(WC()->countries->states[$country_code][$state_code])) {
                $result = $this->format_field(WC()->countries->states[$country_code][$state_code]);
            } else {
                $result = $state_code;
            }

            //WooCommerce translation file has html entities
            $result = html_entity_decode((string) $result, ENT_QUOTES, 'UTF-8');

            return $result;

        }

        private function getSkuArray($item)
        {

            $sku = array();

            if ($item->getVariationID() !== 0) {

                $variation_id = $item->getVariationID();
                $sku = $this->getSkuArrayByVariationID($variation_id);

            } else {
                $product_id = $item->getProductID();
                $sku = $this->getSkuArrayByVariationID($product_id);

                // if (empty($sku)){
                //     // Backward-compatible code to get sku data for Simple type product
                //     $handle=new WC_Product_Variable($product_id);
                //     if ($handle){
                //         $variations_ids=$handle->get_children();
                //         if ($variations_ids && count($variations_ids) > 0){
                //             $first_variation_id = $variations_ids[0];
                //             $sku = $this->getSkuArrayByVariationID($first_variation_id);
                //         }
                //     }
                // }
            }
            return $sku;
        }

        private function getSkuArrayByVariationID($variation_id)
        {

            $sku = array();

            $external_var_data = get_post_meta($variation_id, '_aliexpress_sku_props', true);

            if (empty($external_var_data)) {
                return $sku;
            }

            if ($external_var_data) {
                $items = explode(';', $external_var_data);

                foreach ($items as $item) {
                    list(, $sku[]) = explode(':', $item);
                }
            }

            return $sku;
        }

        private function translitirate($result)
        {
            if (a2w_get_setting('order_translitirate')) {
                $result = A2W_Utils::safeTransliterate($result);
            }

            return $result;
        }

    }

}
