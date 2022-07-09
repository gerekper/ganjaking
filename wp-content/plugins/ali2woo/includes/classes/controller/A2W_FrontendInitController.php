<?php

/**
 * Description of A2W_FrontendInitController
 *
 * @author Mikhail
 *
 * @autoload: init
 *
 * @ajax: true
 */
if (!class_exists('A2W_FrontendInitController')) {

    class A2W_FrontendInitController
    {
        public function __construct()
        {

            add_action('wp_ajax_a2w_frontend_load_shipping_info', array($this, 'ajax_frontend_load_shipping_info'));
            add_action('wp_ajax_nopriv_a2w_frontend_load_shipping_info', array($this, 'ajax_frontend_load_shipping_info'));

            add_filter( 'wcml_multi_currency_ajax_actions', 'add_action_to_multi_currency_ajax', 10, 1 );

            add_action('wp_ajax_a2w_frontend_update_shipping_list', array($this, 'ajax_frontend_update_shipping_list'));
            add_action('wp_ajax_nopriv_a2w_frontend_update_shipping_list', array($this, 'ajax_frontend_update_shipping_list'));

            if (a2w_get_setting('aliship_frontend')) {

                add_action('wp_ajax_a2w_update_shipping_method_in_cart_item', array($this, 'ajax_frontend_update_shipping_method_in_cart_item'));
                add_action('wp_ajax_nopriv_a2w_update_shipping_method_in_cart_item', array($this, 'ajax_frontend_update_shipping_method_in_cart_item'));

                if (a2w_get_setting('aliship_product_enable')) {
                    add_action('woocommerce_add_to_cart_validation', array($this, 'product_shipping_fields_validation'), 10, 3);
                    add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 2);

                    //set country to the last added (to cart) item
                    add_action('woocommerce_add_to_cart', array($this, 'set_default_cart_country'), 10, 6);
                }

            }

            if (a2w_get_setting('aliship_frontend')) {

                //calculate shipping total in the cart and checkout page
                add_filter('woocommerce_package_rates', array($this, 'woocommerce_package_rates'), 10, 2);
            }

            //this hook is fired on frontend and backend.
            //show shipping information on the order edit page (admin) and do not show on frontend for user (complete order page)
            //also do not show in the customers emails
            add_filter('woocommerce_order_item_get_formatted_meta_data', array($this, 'woocommerce_order_item_get_formatted_meta_data'), 10, 2);

        }

        function add_action_to_multi_currency_ajax( $ajax_actions ) {
            $ajax_actions[] = 'a2w_frontend_load_shipping_info'; // Add a AJAX action to the array            
            return $ajax_actions;
        }

        public function ajax_frontend_load_shipping_info()
        {
            if (isset($_POST['id']) && intval($_POST['id']) > 0) {

                $shipping_to_country = isset($_POST['country']) ? wc_clean(wp_unslash($_POST['country'])) : "";

                if (!$shipping_to_country) {
                    echo json_encode(A2W_ResultBuilder::buildError("load_product_shipping_info: country is required."));
                    wp_die();
                }

                $_product = wc_get_product(intval($_POST['id']));

                if (!$_product) {
                    echo json_encode(A2W_ResultBuilder::buildError("load_product_shipping_info: bad product ID."));
                    wp_die();

                }

                $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

                $result = A2W_Utils::get_product_shipping_info($_product, $quantity, $shipping_to_country, false);

                $countries = A2W_Shipping::get_countries();

                $country_label = $countries[$shipping_to_country];

                $page = "cart";

                if (isset($_POST['page'])) {
                    if ($_POST['page'] == "product") {
                        $page = "product";
                    }
                }

                if ($page == "product" && a2w_get_setting('aliship_not_available_remove')) {

                    //if this product page and option is enabled we use a separate not available message
                    $shipping_info = str_replace('{country}', $country_label, a2w_get_setting('aliship_product_not_available_message'));

                } else {
                    $shipping_info = A2W_Shipping::get_not_available_shipping_message($country_label);
                }

                $normalized_methods = array();

                $type = "select";

                if (isset($_POST['type'])) {
                    if ($_POST['type'] == "popup") {
                        $type = "popup";
                    }
                }

                foreach ($result['items'] as $method) {

                    $normalized_method = A2W_Shipping::get_normalized($method, $shipping_to_country, $type);

                    if (!$normalized_method) {
                        continue;
                    }

                    $normalized_methods[] = $normalized_method;

                }

                if ($normalized_methods) {

                }

                $result['items'] = $normalized_methods;

                echo json_encode(A2W_ResultBuilder::buildOk(array('products' => $result, 'shipping_info' => $shipping_info)));
            } else {
                echo json_encode(A2W_ResultBuilder::buildError("load_product_shipping_info: waiting for ID..."));
            }
            wp_die();
        }

        public function ajax_frontend_update_shipping_list()
        {
            if (isset($_POST['items'])) {
                foreach ($_POST['items'] as $ship_way) {
                    if (empty($ship_way['company']) || empty($ship_way['serviceName'])) {
                        continue;
                    }

                    $item = A2W_ShippingPostType::get_item($ship_way['company']);

                    // skip disabled items
                    if ($item === false) {
                        continue;
                    }

                    // if no such item yet, let`s add it and then get it
                    if (!$item) {
                        A2W_ShippingPostType::add_item($ship_way['company'], $ship_way['serviceName']);
                    }
                }
            }
            echo json_encode(A2W_ResultBuilder::buildOk());
            wp_die();
        }

        public function ajax_frontend_update_shipping_method_in_cart_item()
        {

            $cart_item_key = $_POST['id'];
            $tariff_code = $_POST['value'];

            if (isset(WC()->cart->cart_contents[$cart_item_key])) {

                WC()->cart->cart_contents[$cart_item_key]['a2w_shipping_method'] = $tariff_code;

            } else {

                $result = A2W_ResultBuilder::buildError('No cart item with give key in the cart!');
                echo json_encode($result);
                wp_die();

            }

            //reset shiping post code field
            //todo: remove?
            if (!isset($_POST['calc_shipping_postcode'])) {
                $_POST['calc_shipping_postcode'] = '';
            }

            // Update country in user meta.
            //todo: remove?
            $customer_id = apply_filters('woocommerce_checkout_customer_id', get_current_user_id());
            if ($customer_id && !empty($_POST['calc_shipping_country'])) {
                $customer = new WC_Customer($customer_id);
                $customer->set_shipping_country(strval($_POST['calc_shipping_country']));
                $customer->save();
            }

            //Update shipping & totals in the cart ( and in checkout?)

            //reset shipping rates
            $packages = WC()->cart->get_shipping_packages();
            foreach ($packages as $package_key => $package) {
                WC()->session->set('shipping_for_package_' . $package_key, false);
            }

            WC()->cart->calculate_totals();

            WC()->cart->calculate_shipping();

            $result = A2W_ResultBuilder::buildOk();
            echo json_encode($result);
            wp_die();

        }

        public function product_shipping_fields_validation($passed, $product_id, $quantity)
        {

            $external_id = get_post_meta($product_id, '_a2w_external_id', true);

            if ($external_id) {

                if (!isset($_REQUEST['a2w_shipping_method_field']) || empty($_REQUEST['a2w_shipping_method_field'])) {
                    wc_add_notice(__('Please select the shipping method', 'woocommerce'), 'error');
                    $passed = false;
                }

                if (!isset($_REQUEST['a2w_to_country_field']) || empty($_REQUEST['a2w_to_country_field'])) {
                    wc_add_notice(__('Please select the country where you would like your items to be delivered', 'woocommerce'), 'error');

                    $passed = false;
                }

            }

            return $passed;
        }

        public function add_cart_item_data($cart_item_meta, $product_id)
        {

            if (isset($_REQUEST['a2w_shipping_method_field'])) {
                $cart_item_meta['a2w_shipping_method'] = $_REQUEST['a2w_shipping_method_field'];
            }

            if (isset($_REQUEST['a2w_to_country_field'])) {
                $cart_item_meta['a2w_to_country'] = $_REQUEST['a2w_to_country_field'];
            }

            return $cart_item_meta;
        }

        public function set_default_cart_country($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
        {

            if (isset($cart_item_data['a2w_to_country'])) {
                $country = $cart_item_data['a2w_to_country'];

                WC()->customer->set_billing_country($country);
                WC()->customer->set_shipping_country($country);

                //it forces the shipping calculator to update its country field
                WC()->customer->set_calculated_shipping(true);
            }

        }

        public function woocommerce_package_rates($methods, $package)
        {

            if (!empty($package['contents'])) {

                $ali_shipping_type = a2w_get_setting('aliship_shipping_type', 'new');

                $default_shipping_to_country = $package["destination"]["country"];

                $default_tariff_code = a2w_get_setting('fulfillment_prefship', 'EMS_ZX_ZX_US'); //ePacket

                $ali_total_shipping = 0;
                $ali_shipping = false;
                $items_in_package = array();

                $remove_cart_item = a2w_get_setting('aliship_not_available_remove');

                $not_available_cost = a2w_get_setting('aliship_not_available_cost');

                foreach ($package['contents'] as $cart_item_key => $cart_item) {

                    $product_id = $cart_item['product_id'];

                    $external_id = get_post_meta($product_id, '_a2w_external_id', true);

                    if ($external_id) {
                        //we must check for $external_id here, because we can't rely on $cart_item['a2w_shipping_method']
                        //because if the shipping selection is not enabled on the product page
                        //$cart_item['a2w_shipping_method'] is unset

                        $ali_shipping = true;

                        $_product = $cart_item['data'];

                        $shipping_info = A2W_Utils::get_product_shipping_info($_product, $cart_item['quantity'], $default_shipping_to_country, false);

                        $normalized_methods = array();

                        if ($shipping_info) {

                            $shipping_methods = $shipping_info['items'];

                            if (!empty($shipping_methods)) {

                                $search_tariff_code = isset($cart_item['a2w_shipping_method']) ? $cart_item['a2w_shipping_method'] : $default_tariff_code;

                                $min_method = false;

                                foreach ($shipping_methods as $method) {

                                    $normalized_method = A2W_Shipping::get_normalized($method, $default_shipping_to_country);

                                    if (!$normalized_method) {
                                        continue;
                                    }

                                    $normalized_methods[$method['serviceName']] = $normalized_method;

                                    if (!$min_method || $normalized_method['price'] < $min_method['price']) {
                                        $min_method = $normalized_method;
                                    }
                                }

                                if (isset($normalized_methods[$search_tariff_code])) {
                                    $chosen_method = $normalized_methods[$search_tariff_code];
                                    $ali_total_shipping += $chosen_method['price'];
                                } else {
                                    $chosen_method = $min_method;
                                    $ali_total_shipping += $chosen_method['price'];
                                }

                                //save method cost to show in the future order
                                WC()->cart->cart_contents[$cart_item_key]['a2w_shipping_info'] = array(
                                    'company' => $chosen_method['company'],
                                    'service_name' => $chosen_method['serviceName'],
                                    'delivery_time' => $chosen_method['time'],
                                    'shipping_cost' => $chosen_method['price']);

                                $items_in_package[] = $_product->get_name() . ' &times; ' . $cart_item['quantity'];

                            } else {

                                //if product can't be delivered to the country
                                if (!$remove_cart_item && $not_available_cost) {

                                    $ali_total_shipping += $not_available_cost;
                                }
                            }

                        } else {

                            //todo: it looks like rudiment condition shipping_info can't be false
                            //funtion can't come here, remove?

                            if (!$remove_cart_item && $not_available_cost) {
                                $ali_total_shipping += $not_available_cost;
                            }

                        }

                    }
                }

                if ($ali_shipping && $ali_shipping_type !== 'none') {
                    if ($ali_total_shipping) {
                        $id = 'flat_rate';
                        $label = a2w_get_setting('aliship_shipping_label');
                        if (!$label) {
                            $label = esc_html__('Shipping', 'ali2woo');
                        }
                    } else {
                        $id = 'free_shipping';
                        $label = a2w_get_setting('aliship_free_shipping_label');
                        if (!$label) {
                            $label = esc_html__('Free Shipping', 'ali2woo');
                        }
                    }

                    switch ($ali_shipping_type) {
                        case 'new':
                            /*Create a new shipping method but still show other available shipping methods*/
                            $taxes = WC_Tax::calc_shipping_tax($ali_total_shipping, WC_Tax::get_shipping_tax_rates());
                            $methods[$id] = new WC_Shipping_Rate($id, $label, $ali_total_shipping, $taxes, $id, '');
                            if (count($items_in_package)) {
                                $methods[$id]->add_meta_data(__('Items', 'woocommerce'), implode(', ', $items_in_package));
                            }
                            break;
                        case 'new_only':
                            /*Create a new shipping method and make it the only available shipping method*/
                            $taxes = WC_Tax::calc_shipping_tax($ali_total_shipping, WC_Tax::get_shipping_tax_rates());
                            $methods = array($id => new WC_Shipping_Rate($id, $label, $ali_total_shipping, $taxes, $id, ''));
                            if (count($items_in_package)) {
                                $methods[$id]->add_meta_data(__('Items', 'woocommerce'), implode(', ', $items_in_package));
                            }
                            break;
                        case 'add':
                            /*Add shipping cost to all available shipping methods*/
                            if ($ali_total_shipping) {
                                foreach ($methods as $rate_k => $rate) {
                                    if (is_a($rate, 'WC_Shipping_Rate') && $rate && $rate->get_method_id() !== 'free_shipping') {
                                        $cost = $rate->get_cost() + $ali_total_shipping;
                                        $taxes = WC_Tax::calc_shipping_tax($cost, WC_Tax::get_shipping_tax_rates());
                                        $methods[$rate_k]->set_cost($cost);
                                        $methods[$rate_k]->set_taxes($taxes);
                                    }
                                }
                            }
                            break;
                        default:
                    }

                } else {
                    //if shipping calculation is none, do not calculate shipping
                    // just keep shipping information saved in the cart item meta (earlier)

                    if (!count($methods)) {

                        //we need at least one method if use doesn't create any method in woocommerce

                        $id = 'free_shipping';
                        $label = a2w_get_setting('aliship_free_shipping_label');
                        if (!$label) {
                            $label = esc_html__('Free Shipping', 'ali2woo');
                        }

                        $taxes = WC_Tax::calc_shipping_tax(0, WC_Tax::get_shipping_tax_rates());
                        $methods[$id] = new WC_Shipping_Rate('free_shipping', $label, 0, $taxes, $id, '');
                    }

                }

            }

            return $methods;
        }

        public function woocommerce_order_item_get_formatted_meta_data($formatted_meta, $item)
        {

            if (!is_admin() || isset($_POST['a2w_email_template_check'])) {

                $user_formatted_meta = array();

                foreach ($formatted_meta as $formatted_item) {
                    if ($formatted_item->key === A2W_Shipping::get_order_item_shipping_meta_key()) {
                        continue;
                    }

                    $user_formatted_meta[$formatted_item->key] = $formatted_item;
                }

                if (!empty($user_formatted_meta)) {
                    $formatted_meta = $user_formatted_meta;
                }

            }

            return $formatted_meta;
        }

    }
}
