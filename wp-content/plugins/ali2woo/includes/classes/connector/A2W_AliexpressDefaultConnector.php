<?php

/**
 * Description of A2W_AliexpressDefaultConnector
 *
 * @author Ali2Woo Team
 */
if (!class_exists('A2W_AliexpressDefaultConnector')) {

    class A2W_AliexpressDefaultConnector extends A2W_AbstractConnector
    {
        //todo: currency apply code should be moved above (parent class)
        public function load_product($product_id, $params = array()){
            $session = $this->get_access_token();
            $params  = !isset($params['data']) ? array() : $params['data'];
            $data_v1 = $this->load_product_data_v1($product_id, $session, $params);
            $data_v2 = $this->load_product_data_v2($product_id, $session, $params); 

            if ($data_v1['state'] === 'error' && $data_v2['state'] === 'error'){
               if ($data_v1['state'] === 'error'){
                    return $data_v1;
                }
                /*
                if ($data_v2['state'] === 'error'){
                    $result = $data_v2;
                }*/
            } else if ($data_v1['state'] !== 'error' && $data_v2['state'] !== 'error') {
                $result = $this->get_product_combined_data($data_v1, $data_v2);
            } else if ($data_v1['state'] !== 'error' && $data_v2['state'] === 'error'){
                //if we have only data from api v1, then need to convert prices to the currency
                $current_currency = strtoupper(A2W_AliexpressLocalizator::getInstance()->currency);
                $currency_rates = A2W_CurrencyRates::get();
  
                if ($currency_rates['state'] == 'ok' && isset($currency_rates['rates']['USD_' . $current_currency])){
                    
                    foreach ( $data_v1['product']['sku_products']['variations'] as &$var) {
                        if ($var['currency'] === 'USD'){
                            $selected_exchange_rate = $currency_rates['rates']['USD_' . $current_currency];
                        } else {
                            //sometimes API V1 returns prices in other currency
                            $other_currency = strtoupper($var['currency']);
                            if (!isset($currency_rates['rates'][$other_currency . '_' . $current_currency])){
                                $currency_api_result = A2W_CurrencyRates::load_currency_rate($current_currency, $other_currency);
                                if ($currency_api_result['state'] !== 'error'){
                                    $currency_rates['rates'][$other_currency . '_' . $current_currency] = $currency_api_result['rate'];
                                } else {
                                    return $currency_api_result;    
                                }
                            }
                            $selected_exchange_rate = $currency_rates['rates'][$other_currency . '_' . $current_currency]; 
                        }  

                        $var['currency'] = $current_currency;
                        $var['regular_price'] = round($var['regular_price'] * $selected_exchange_rate, 2);
                        $var['price'] = round($var['price'] * $selected_exchange_rate, 2);
                        $var['bulk_price'] = round($var['bulk_price'] * $selected_exchange_rate, 2); 
                    }

                    $data_v1['product']['currency'] = $current_currency;

                    $result = $data_v1;
                } 
                else {
                    return $result = A2W_ResultBuilder::buildError($currency_rates['message']);     
                } 
            } else {
                $result = $data_v2;         
            }

            return  $result;
        }

        public function load_products($filter, $page = 1, $per_page = 20, $params = array()){
            $request_url = A2W_RequestHelper::build_request('get_products', array_merge(array('page' => $page, 'per_page' => $per_page), $filter));
            $request = a2w_remote_get($request_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else if (intval($request['response']['code']) != 200) {
                $result = A2W_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
            } else {
                $result = json_decode($request['body'], true);
            }

            return $result;
        }

        public function load_store_products($filter, $page = 1, $per_page = 20, $params = array()){
            $request_url = A2W_RequestHelper::build_request('get_store_products', array_merge(array('page' => $page, 'per_page' => $per_page), $filter));

            $request = a2w_remote_get($request_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else if (intval($request['response']['code']) != 200) {
                $result = A2W_ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
            } else {
                $result = json_decode($request['body'], true);
            }

            return $result;
        }

        public function load_reviews($product_id, $page, $page_size = 20, $params = array()){
            $request_url = A2W_RequestHelper::build_request('get_reviews', array('lang' => A2W_AliexpressLocalizator::getInstance()->language, 'product_id' => $product_id, 'page' => $page, 'page_size' => $page_size));
            $request = a2w_remote_get($request_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                $result = json_decode($request['body'], true);
            }

            return $result;
        }
        
        public function check_affiliate($product_id)
        {
            $request_url = A2W_RequestHelper::build_request('check_affiliate', array('product_id' => $product_id));
            $request = a2w_remote_get($request_url);
            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                $result = json_decode($request['body'], true);
            }
            return $result;
        }

        public function load_shipping_info($product_id, $quantity, $country_code, $country_code_from = 'CN', $min_price = '', $max_price = '', $province = '', $city = ''){

            $session = $this->get_access_token();

            $current_currency = strtoupper(A2W_AliexpressLocalizator::getInstance()->currency);
            $currency_rates = A2W_CurrencyRates::get();
            if ($currency_rates['state'] == 'ok' && isset($currency_rates['rates']['USD_' . $current_currency])){
                $param_aeop_freight_calculate_for_buyer_d_t_o = array(
                    'country_code' => A2W_Utils::filter_country($country_code), 
                    'product_id' => $product_id, 
                    'product_num' => $quantity, 
                    'send_goods_country_code' => $country_code_from
                );
    
                $payload = array(
                    'param_aeop_freight_calculate_for_buyer_d_t_o' => json_encode($param_aeop_freight_calculate_for_buyer_d_t_o),
                  );
      
                $params = array(
                      "session" => $session,
                      "method" => "aliexpress.logistics.buyer.freight.calculate",
                      "payload" => json_encode($payload),
                );
      
                $request_url = A2W_RequestHelper::build_request('sign', $params);
                $request = a2w_remote_get($request_url);

                    if (is_wp_error($request)) {
                        $result = A2W_ResultBuilder::buildError($request->get_error_message());
                    } else {
                        if (intval($request['response']['code']) == 200) {
                            $result = json_decode($request['body'], true);
                        } else {
                            $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                        }
                    }
      
                    if ($result['state'] == 'error') {
                        return $result = A2W_ResultBuilder::buildError($request['data']);
                    }
                    
                    $request = a2w_remote_post($result['request']['requestUrl'], $result['request']['apiParams']);
      
                    if (is_wp_error($request)) {
                        $result = A2W_ResultBuilder::buildError($request->get_error_message());
                    } else {
                        
                        if (intval($request['response']['code']) == 200) {
                            $body = json_decode($request['body'], true);
                            $shipping_data = $body['aliexpress_logistics_buyer_freight_calculate_response']['result'];
    
                            $hasShipping = isset($shipping_data['aeop_freight_calculate_result_for_buyer_d_t_o_list']) &&
                                            !empty($shipping_data['aeop_freight_calculate_result_for_buyer_d_t_o_list']['aeop_freight_calculate_result_for_buyer_dto'])
                                            && !isset($shipping_data['error_desc']);
                          
                            if ($hasShipping){    
                                    $current_currency = strtoupper(A2W_AliexpressLocalizator::getInstance()->currency);
                                    $items = $shipping_data['aeop_freight_calculate_result_for_buyer_d_t_o_list']['aeop_freight_calculate_result_for_buyer_dto'];
                                    
                                    $normalized_items = array();
                            
                                    foreach ($items as $item){
                                        $normalized_item = array(
                                            'serviceName' => $item['service_name'], 
                                            'company' => $item['service_name'],
                                            'time' => $item['estimated_delivery_time'],
                                            'freightAmount' => array()
                                        );
    
                                        $value = round(floatval($item['freight']['amount'])  *  floatval($currency_rates['rates']['USD_' . $current_currency]), 2);
    
                                        $normalized_item['freightAmount'] = array(
                                            'formatedAmount' => $value . ' ' . $current_currency,
                                            'value'=> $value
                                        );
    
                                        $normalized_items[] = $normalized_item;
                                    }
                                    $result = A2W_ResultBuilder::buildOk(array('items' => $normalized_items));
                            } else {
                                $result = A2W_ResultBuilder::buildError('can`t get shipping info');    
                            }
                        }
                    } 
            } else {
                $result = A2W_ResultBuilder::buildError($currency_rates['message']);    
            }

            return $result;
        }

        public function place_order($data)
        {
            $session = $this->get_access_token();

            $order = $data['order'];

            $wm = new A2W_Woocommerce();
            $customer_info = $wm->get_order_user_info($order);

            if (!$customer_info['phone']) {
                return A2W_ResultBuilder::buildError(__('Phone number is required', 'ali2woo'));
            } else if (!$customer_info['street'] && !$customer_info['address2']) {
                return A2W_ResultBuilder::buildError(__('Street is required', 'ali2woo'));
            } else if (!$customer_info['name']) {
                return A2W_ResultBuilder::buildError(__('Contact name is required', 'ali2woo'));
            } else if (!$customer_info['country']) {
                return A2W_ResultBuilder::buildError(__('Country is required', 'ali2woo'));
            } else if (!$customer_info['city'] && !$customer_info['state']) {
                return A2W_ResultBuilder::buildError(__('City/State/Province is required', 'ali2woo'));
            } else if (!$customer_info['postcode']) {
                return A2W_ResultBuilder::buildError(__('Zip/Postal code is required', 'ali2woo'));
            } else if ($customer_info['country'] === 'BR' && !$customer_info['cpf']) {
                return A2W_ResultBuilder::buildError(__('CPF is mandatory in Brazil', 'ali2woo'));
            } else if ($customer_info['country'] === 'CL' && !$customer_info['rutNo']) {
                return A2W_ResultBuilder::buildError(__('RUT number is mandatory for Chilean customers', 'ali2woo'));
            }

            $order_note = a2w_get_setting('fulfillment_custom_note', '');

            $product_items = array();
            $processing_order_items = array();
            $errors = array();
            foreach ($data['order_items'] as $order_item) {
                if (get_class($order_item) !== 'WC_Order_Item_Product') {
                    continue;
                }

                $order_item_id = $order_item->get_id();

                $quantity = $order_item->get_quantity() + $order->get_qty_refunded_for_item($order_item_id);

                if ($quantity == 0) {
                    continue;
                }

                $a2w_order_item = new A2W_WooCommerceOrderItem($order_item);

                $product_id = $order_item->get_product_id();
                $variation_id = $order_item->get_variation_id();

                $aliexpress_product_id = get_post_meta($product_id, '_a2w_external_id', true);
                if (!$aliexpress_product_id) {
                   $errors[] = array('order_item_id' => $order_item_id, 'message' => __('AliExpress product not found', 'ali2woo'));
                   continue;
                }

                if ($a2w_order_item->get_external_order_id()) {
                //    $errors[] = array('order_item_id' => $order_item_id, 'message' => __('Aliexpress order exists', 'ali2woo'));
                //    continue;
                }

                if ($variation_id) {
                    $sku_attr = get_post_meta($variation_id, '_aliexpress_sku_props', true);
                } else {
                    $sku_attr = get_post_meta($product_id, '_aliexpress_sku_props', true);
                }

                $shipping_company = a2w_get_setting('fulfillment_prefship', '');
                $shipping_meta = $order_item->get_meta(A2W_Shipping::get_order_item_shipping_meta_key());
                if ($shipping_meta) {
                    $shipping_meta = json_decode($shipping_meta, true);
                    $shipping_company = $shipping_meta['service_name'];
                }

                if (!$shipping_company) {
                    $errors[] = array('order_item_id' => $order_item_id, 'message' => __('Missing Shipping method', 'ali2woo'));
                    continue;
                }

                $product_items[] = array(
                    'product_count' => $quantity,
                    'product_id' => $aliexpress_product_id,
                    'sku_attr' => $sku_attr,
                    'logistics_service_name' => $shipping_company,
                    'order_memo' => $order_note,
                );

                $processing_order_items[] = $a2w_order_item;
            }

            if (!empty($errors)) {
                return A2W_ResultBuilder::buildError(__('Product error', 'ali2woo'), array('error_code' => 'product_error', 'errors' => $errors));
            }

            $logistics_address = array(
                'address' => $customer_info['street'],
                'city' => remove_accents($customer_info['city']),
                'contact_person' => $customer_info['name'],
                'country' => $customer_info['country'],
                'full_name' => $customer_info['name'],
                'mobile_no' => $customer_info['phone'],
                'phone_country' => $customer_info['phoneCountry'],
                'province' => remove_accents($customer_info['state']),
                // additional fields
                // 'locale' => 'en_US',
                // 'rut_no' => '',
                //'location_tree_address_id'=> '',
            );
            if (!empty($customer_info['cpf'])) {
                $logistics_address['cpf'] = $customer_info['cpf'];
            }
            if (!empty($customer_info['rutNo'])) {
                $logistics_address['rutNo'] = $customer_info['rutNo'];
            }
            if ($customer_info['postcode']) {
                $logistics_address['zip'] = $customer_info['postcode'];
            }
            if ($customer_info['address2']) {
                if ($customer_info['street']) {
                    $logistics_address['address2'] = remove_accents($customer_info['address2']);
                } else {
                    $logistics_address['address'] = remove_accents($customer_info['address2']);
                }
            }
            // additional fields
            if (!empty($customer_info['passport_no'])) {
                $logistics_address['passport_no'] = $customer_info['passport_no'];
            }
            if (!empty($customer_info['passport_no_date'])) {
                $logistics_address['passport_no_date'] = $customer_info['passport_no_date'];
            }
            if (!empty($customer_info['passport_organization'])) {
                $logistics_address['passport_organization'] = $customer_info['passport_organization'];
            }
            if (!empty($customer_info['tax_number'])) {
                $logistics_address['tax_number'] = $customer_info['tax_number'];
            }
            if (!empty($customer_info['foreigner_passport_no'])) {
                $logistics_address['foreigner_passport_no'] = $customer_info['foreigner_passport_no'];
            }
            if (!empty($customer_info['is_foreigner']) && $customer_info['is_foreigner']==='yes') {
                $logistics_address['is_foreigner'] = 'true';
            }
            if (!empty($customer_info['vat_no'])) {
                $logistics_address['vat_no'] = $customer_info['vat_no'];
            }
            if (!empty($customer_info['tax_company'])) {
                $logistics_address['tax_company'] = $customer_info['tax_company'];
            }

            $logistics_address = apply_filters('a2w_orders_logistics_address', $logistics_address, $customer_info);

            $logistics_address = $this->fix_shipping_address($logistics_address);

            $payload = array(
                'param_place_order_request4_open_api_d_t_o' => json_encode(
                    array(
                        'logistics_address' => $logistics_address,
                        'product_items' => $product_items,
                    )
                ),
            );

            $params = array(
                "session" => $session,
                "method" => "aliexpress.trade.buy.placeorder",
                "payload" => json_encode($payload),
            );

            $request_url = A2W_RequestHelper::build_request('sign', $params);
            $request = a2w_remote_get($request_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                if (intval($request['response']['code']) == 200) {
                    $result = json_decode($request['body'], true);
                } else {
                    $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                }
            }

            // $result = A2W_ResultBuilder::buildError('DEBUG STOP');
            if ($result['state'] == 'error') {
                return $result;
            }

            $request = a2w_remote_post($result['request']['requestUrl'], $result['request']['apiParams']);
            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                if (intval($request['response']['code']) == 200) {
                    $body = json_decode($request['body'], true);
                    if(isset($body['aliexpress_trade_buy_placeorder_response']['result'])){
                        $aliexpress_result = $body['aliexpress_trade_buy_placeorder_response']['result'];
                        if ($aliexpress_result['is_success'] && isset($aliexpress_result['order_list']['number'])) {
                            $aliexpress_order_ids = $aliexpress_result['order_list']['number'];
                            
                            foreach ($aliexpress_order_ids as $aliexpress_order_id) {
                                $result = $this->load_order($aliexpress_order_id);
                                if($result['state'] === 'error') {
                                    break;
                                } else {
                                    foreach($result['order']['child_order_list']['ae_child_order_info'] as $ae_product_info){
                                        foreach($processing_order_items as $order_item) {
                                            if($order_item->get_external_product_id() == $ae_product_info['product_id']){
                                                $order_item->update_external_order($aliexpress_order_id, true);
                                            }
                                        }
                                    }
                                }
                            }

                            $result = A2W_ResultBuilder::buildOk();

                            $placed_order_status = a2w_get_setting('placed_order_status');
                            if ($placed_order_status) {
                                $order->update_status($placed_order_status);
                            }
                        } else {
                            $result = A2W_ResultBuilder::buildError(A2W_AliexpressError::message($aliexpress_result));
                        }
                    } else{
                        a2w_error_log('place order error: '.print_r($body, true));
                        $result = A2W_ResultBuilder::buildError(A2W_AliexpressError::message($body));
                    }
                } else {
                    $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                }
            }

            return $result;
        }

        public function load_order($order_id)
        {
            $session = $this->get_access_token();

            $payload = array('order_id' => $order_id);
            $params = array(
                "session" => $session,
                "method" => "aliexpress.ds.trade.order.get",
                "payload" => json_encode($payload),
            );

            $request_url = A2W_RequestHelper::build_request('sign', $params);
            $request = a2w_remote_get($request_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                if (intval($request['response']['code']) == 200) {
                    $result = json_decode($request['body'], true);
                } else {
                    $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                }
            }

            // $result = A2W_ResultBuilder::buildError('DEBUG STOP');
            if ($result['state'] == 'error') {
                return $result;
            }

            $request = a2w_remote_post($result['request']['requestUrl'], $result['request']['apiParams']);
            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                $result = A2W_ResultBuilder::buildOk();
                if (intval($request['response']['code']) == 200) {
                    $body = json_decode($request['body'], true);
                    if(isset($body['error_response']['msg'])){
                        $result = A2W_ResultBuilder::buildError($body['error_response']['code'] . ' - ' . $body['error_response']['msg']);
                    } else if(intval($body['aliexpress_ds_trade_order_get_response']['rsp_code']) !== 200) {
                        $result = A2W_ResultBuilder::buildError($body['aliexpress_ds_trade_order_get_response']['rsp_msg'], array('error_code'=>$body['aliexpress_ds_trade_order_get_response']['rsp_code']));
                    } else {
                        $result = A2W_ResultBuilder::buildOk(array('order' => $body['aliexpress_ds_trade_order_get_response']['result']));
                    }
                } else {
                    $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                }
            }
            return $result;
        }

        public static function get_images_from_description($product)
        {
            $src_result = array();

            if ($product['description'] && class_exists('DOMDocument')) {
                $description = htmlspecialchars_decode(utf8_decode(htmlentities($product['description'], ENT_COMPAT, 'UTF-8', false)));

                if (function_exists('libxml_use_internal_errors')) {
                    libxml_use_internal_errors(true);
                }
                $dom = new DOMDocument();
                @$dom->loadHTML($description);
                $dom->formatOutput = true;
                $tags = $dom->getElementsByTagName('img');

                foreach ($tags as $tag) {
                    $src_result[md5($tag->getAttribute('src'))] = $tag->getAttribute('src');
                }
            }

            return $src_result;
        }

        private function fix_shipping_address($shipping_address)
        {
            if (a2w_check_defined('A2W_DEMO_MODE')){
                return $shipping_address;
            } 
            
            $json = json_encode($shipping_address);
    
            $args = [
                'headers' => array('Content-Type' => 'application/json'),
            ];
    
            $args = [];
    
            $request_url = A2W_RequestHelper::build_request('fix_shipping_address');
            $request = a2w_remote_post($request_url, $json, $args);
    
            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                if (intval($request['response']['code']) == 200) {
                    $result = json_decode($request['body'], true);
                } else {
                    $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                }
            }
    
            if ($result['state'] !== 'error' && isset($result['shipping_address'])) {
                $shipping_address = $result['shipping_address'];
            }
    
            return $shipping_address;
        }

        private function load_product_data_v2($product_id, $session, $params = array()){
              $payload = array(
                'product_id' => $product_id,
                'target_language' => isset($params['lang']) ? strtolower($params['lang']) : strtolower(A2W_AliexpressLocalizator::getInstance()->language),
                'target_currency' => isset($params['currency']) ? $params['currency'] : strtoupper(A2W_AliexpressLocalizator::getInstance()->currency),
                'ship_to_country' => A2W_Utils::filter_country(isset($params['lang']) ? strtoupper($params['lang']) : strtoupper(A2W_AliexpressLocalizator::getInstance()->language))
             );

             $api_us_id_fix = false;
             //todo: we can save in the product data that is us version id, and speed up the sync opertion in next time
             $original_params = $params;
             if (isset($params['api_us_id_fix']) && $params['api_us_id_fix']){
                //the api returns empty data for us version product ids if ship_to_country is set
                $api_us_id_fix = true;
                unset($payload['ship_to_country']);
             }

              $params = array(
                  "session" => $session,
                  "method" => "aliexpress.ds.product.get",
                  "payload" => json_encode($payload),
              );
  
              $request_url = A2W_RequestHelper::build_request('sign', $params);
              $request = a2w_remote_get($request_url);

                if (is_wp_error($request)) {
                    $result = A2W_ResultBuilder::buildError($request->get_error_message());
                } else {
                    if (intval($request['response']['code']) == 200) {
                        $result = json_decode($request['body'], true);
                    } else {
                        $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                    }
                }

                if ($result['state'] == 'error') {
                    return $result = A2W_ResultBuilder::buildError($request['data']);
                }
                
                $request = a2w_remote_post($result['request']['requestUrl'], $result['request']['apiParams']);

                if (is_wp_error($request)) {
                    $result = A2W_ResultBuilder::buildError($request->get_error_message());
                } else {
                    $result = A2W_ResultBuilder::buildOk();
                    if (intval($request['response']['code']) == 200) {

                        $lang = A2W_AliexpressLocalizator::getInstance()->language;
                      
                        $body = json_decode($request['body'], true);                     
                        if (isset($body['aliexpress_ds_product_get_response']) && !empty($body['aliexpress_ds_product_get_response']['result'])){
                            
                                $product_data = $body['aliexpress_ds_product_get_response']['result'];

                                $product = array();
            
                                $product['id'] = $product_id;
                                $product['seller_id'] = $product_data['ae_item_base_info_dto']['owner_member_seq_long'];

                                $product['import_lang'] = $lang;
                                if ($api_us_id_fix){
                                    $product['import_lang'] = 'en';    
                                }
                                
                                $product['sku'] = a2w_random_str(); //todo: we can make it as option
                                $product['url'] = "https://" . ($lang === 'en' ? "www" : $lang) . ".aliexpress.com/item/{$product_id}/{$product_id}.html";
                                $product['title'] = $product_data['ae_item_base_info_dto']['subject'];

                                $product['seller_url'] = "";
                                $product['store_id'] = "";
                                $product['seller_name'] = "Store";
                                if (isset($product_data['ae_store_info']['store_id'])){
                                    $product['store_id'] = $product_data['ae_store_info']['store_id'];
                                    $product['seller_url'] =  "https://" . ($lang === 'en' ? "www" : $lang) . ".aliexpress.com/store/" . $product['store_id'];   
                                    $product['seller_name'] = $product_data['ae_store_info']['store_name'];
                                }

                                $product['images'] = explode(';', $product_data['ae_multimedia_info_dto']['image_urls']);
                                $product['thumb'] = $product['images'][0];

                                
                                $product['video'] = array();

                                if (isset($product_data['ae_multimedia_info_dto']) && isset($product_data['ae_multimedia_info_dto']['ae_video_dtos']) && isset($product_data['ae_multimedia_info_dto']['ae_video_dtos']['ae_video_d_t_o'])){
                                    $sourceVideoData = $product_data['ae_multimedia_info_dto']['ae_video_dtos']['ae_video_d_t_o'];
                                    if (!empty($sourceVideoData) && is_array($sourceVideoData)) {
                                        $product['video'] = $sourceVideoData[0];
                                        $product['seller_id'] = $product['video']['ali_member_id'];
                                    }
                                }

                                if (!empty($product['video'])){
                                    //todo: add option to load video or not because it slow down the process
                                    $video_link = $this->get_valid_aliexpress_video_link( $product['video'] );
                                    if ( $video_link ) {
                                        $product['video']['url'] = $video_link;
                                    }
                                }

                                $product['dimensions']['length'] = $product_data['package_info_dto']['package_length'];
                                $product['dimensions']['width'] = $product_data['package_info_dto']['package_width'];
                                $product['dimensions']['height'] = $product_data['package_info_dto']['package_height'];
                                $product['dimensions']['weight'] = $product_data['package_info_dto']['gross_weight'];
                
                            // $product['baseUnit'] = $product_data['package_info_dto']['base_unit'];
                                $product['productUnit'] = $product_data['package_info_dto']['product_unit'];
                                $product['packageType'] = $product_data['package_info_dto']['package_type'];
                                $product['category_id'] = $product_data['ae_item_base_info_dto']['category_id'];
                                $product['category_name'] = '';

                                $product['description'] = $product_data['ae_item_base_info_dto']['detail'];
                                $product['ordersCount'] = 0;

                                $product['sku_products'] = array('attributes' => array(), 'variations' => array());
                        
                                if (isset($product_data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o']) 
                                    && is_array($product_data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o'])
                                    && $product_data['ae_item_base_info_dto']['product_status_type'] !== 'offline') 
                                {
                                    $attr_value_name_hash = array();
                                    $attributesWithPropertyIdAsKeys = array();

                                    //fetch attributes

                                    foreach ($product_data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o'] as $src_key_var => $src_var) {
                                        $attr_value_name_hash[$src_key_var] = array();
                                        if (isset($src_var['ae_sku_property_dtos'])){
                                            foreach ($src_var['ae_sku_property_dtos']['ae_sku_property_d_t_o'] as $src_attr){
                                                if (!isset($attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']])){
                                                    $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']] = array('id' => $src_attr['sku_property_id'], 'name' => $src_attr['sku_property_name'], 'value' => array());
                                                }
                                            
                                                $attr = $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']];
                
                                                $propertyValueId = $src_attr['property_value_id'];
                                                $value = array('id' => $attr['id'] . ':' . $propertyValueId, 'name' => isset($src_attr['property_value_definition_name']) ? $src_attr['property_value_definition_name'] : $src_attr['sku_property_value']);
                                                $value['thumb'] = isset( $src_attr['sku_image'] ) ? str_replace( array(
                                                    'ae02.alicdn.com',
                                                    'ae03.alicdn.com',
                                                    'ae04.alicdn.com',
                                                    'ae05.alicdn.com',
                                                ), 'ae01.alicdn.com', $src_attr['sku_image'] ) : '';
                                                $value['image'] = $value['thumb'];

                                                if ($value['image']){
                                                    //save image in src var for future use
                                                    $product_data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o'][$src_key_var]['sku_image'] = $value['thumb'];
                                                }

                                                $countryCode = $this->property_value_id_to_ship_from( $src_attr['sku_property_id'], $src_attr['property_value_id'] );
                                                if ($countryCode) {
                                                    $value['country_code'] = $countryCode;
                                                }
                
                                                // Fix value name dublicate
                                                if (empty($attr_value_name_hash[$src_key_var][$value['name']])) {
                                                    $attr_value_name_hash[$src_key_var][$value['name']] = 1;
                                                } else {
                                                    $attr_value_name_hash[$src_key_var][$value['name']] += 1;
                                                    $value['name'] = $value['name'] . "#" . $attr_value_name_hash[$src_key_var][$value['name']];
                                                }
                
                                                //$attr['value'][$value['id']] = $value;
                                                $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']]['value'][$value['id']] = $value;
                                            }
                                        }                        
                                    }

                                    $product['sku_products']['attributes'] = array_values($attributesWithPropertyIdAsKeys);

                                    //fetch variants
                                    $priceList = $product_data['ae_item_sku_info_dtos']['ae_item_sku_info_d_t_o'];
                                    foreach ($priceList as $src_var) {
                                        $aa = array();
                                        $aa_names = array();
                                        $country_code = ''; 
                                        if (isset($src_var['id']) && $src_var['id']) {
                                            $sky_attrs = explode(";", $src_var['id']);
                                            $sky_attrs = array_map(function($el){ $t = explode("#", $el); return $t[0]; }, $sky_attrs); //clean elements from # part

                                            foreach ($sky_attrs as $sky_attr) {
                                                $tmp_v = $sky_attr;

                                                if (count($product['sku_products']['attributes']) > 0){
                                                    $aa[] = $tmp_v;
                        
                                                    foreach ($product['sku_products']['attributes'] as $attr) {
                                                        if (isset($attr['value'][$tmp_v])) {
                                                            $aa_names[] = $attr['value'][$tmp_v]['name'];
                                                            if (isset($attr['value'][$tmp_v]['country_code'])) {
                                                                $country_code = $attr['value'][$tmp_v]['country_code'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        $quantity =  isset($src_var['sku_available_stock'] ) ? $src_var['sku_available_stock'] : ( isset( $src_var['ipm_sku_stock'] ) ? $src_var['ipm_sku_stock'] : 0 );
                                        $regular_price = round(floatval($src_var['sku_price']), 2);
                                        $price = isset($src_var['offer_sale_price'] ) ? round(floatval($src_var['offer_sale_price']), 2) : $regular_price;
                                        $currency = $src_var['currency_code'];
                                        $discount = 100 - 100 * $price /  $regular_price;
                                        $bulk_price = isset( $src_var['offer_bulk_sale_price'] ) ? round(floatval($src_var['offer_bulk_sale_price']), 2) : 0;

                                        $sorted_aa = $this->sort_variant_id_parts($aa);
            
                                        $variation = array(
                                            'id' => $product_id . '-' . ($sorted_aa ? implode('-', $sorted_aa) : (count($product['sku_products']['variations']) + 1)),
                                        /* 'skuId'=> $src_var['skuId'],
                                            'skuIdStr'=>$src_var['skuIdStr'],*/
                                            // 'sku' => $product_id . '-' . (count($product['sku_products']['variations']) + 1),
                                            'sku' => a2w_random_str(),
                                            'attributes' => $aa,
                                            'attributes_names' => $aa_names,
                                            'quantity' => $quantity,
                                            'currency' => strtoupper($currency),
                                            'regular_price' => $regular_price,
                                            'price' => $price,
                                            'bulk_price' => $bulk_price,
                                            'discount' => $discount,
                                        );
                    
                                        if ($country_code) {
                                            $variation['country_code'] = $country_code;
                                        }
                    
                                        if (isset($src_var['sku_image'])) {
                                            $variation['image'] = $src_var['sku_image'];
                                        }
                    
                                        $product['sku_products']['variations'][$variation['id']] = $variation;
                                    }
                                } else {
                                    return A2W_ResultBuilder::buildError(__('Product not available', 'ali2woo'), ['error_code' => 1005]);                               
                                }

                                //we don't need keys in array
                                if (count($product['sku_products']['variations']) > 0){
                                    $product['sku_products']['variations'] = array_values($product['sku_products']['variations']);
                                }
                                

                                $product['currency'] = $product_data['ae_item_base_info_dto']['currency_code'];

                                if (isset($product_data['ae_item_properties']['ae_item_property']) && is_array($product_data['ae_item_properties']['ae_item_property'])) {
                                    foreach ($product_data['ae_item_properties']['ae_item_property'] as $prop) {
                                        //todo: here value is returned as simple value not as array
                                        $product['attribute'][] = array('name' => $prop['attr_name'], 'value' => isset($prop['attr_value']) ? $prop['attr_value'] : '');
                                    }
                                }

                                //todo: check why it needs here?
                                $product['complete'] = true;

                                $result = A2W_ResultBuilder::buildOk(array('product' => $product));

                        }
                        else {
                            if (!isset($body['error_response'])){
                                if (!$api_us_id_fix){
                                    $original_params['api_us_id_fix'] = true;
                                    $result = $this->load_product_data_v2($product_id, $session, $original_params);
                                } else {
                                    $result = A2W_ResultBuilder::buildError(__('No such a product available',  'ali2woo'), ['error_code' => 1005]);   
                                }
                            } else {

                                $error_response = $body['error_response'];

                                if (isset($error_response['code']) && $error_response['code'] == '27'){
                                    $msg = sprintf(__('Refresh your AliExpress access token. <a target="_blank" href="%s">Please check our instruction</a>.', 'ali2woo'),
                                    'https://help.ali2woo.com/codex/how-to-get-access-token-from-aliexpress/'
                                    );
                                    $result = A2W_ResultBuilder::buildError($msg);
                                } else {
                                    $result = A2W_ResultBuilder::buildError(__('AliExpress get-product API error: "',  'ali2woo') . $error_response['msg'] . '", error code: ' . $error_response['code']);       
                                }
                            }
                        }
           
                    } else {
                        $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                    }
                }

                return $result;


        }

        private function load_product_data_v1($product_id, $session, $params = array()){
   
            $payload = array(
              'product_id' => $product_id,
              'local_country' => A2W_Utils::filter_country(isset($params['lang']) ? strtoupper($params['lang']) : strtoupper(A2W_AliexpressLocalizator::getInstance()->language)),
              'local_language' => isset($params['lang']) ? strtolower($params['lang']) : strtolower(A2W_AliexpressLocalizator::getInstance()->language),
            );

            $params = array(
                "session" => $session,
                "method" => "aliexpress.postproduct.redefining.findaeproductbyidfordropshipper",
                "payload" => json_encode($payload),
            );

            $request_url = A2W_RequestHelper::build_request('sign', $params);
            $request = a2w_remote_get($request_url);

              if (is_wp_error($request)) {
                  $result = A2W_ResultBuilder::buildError($request->get_error_message());
              } else {
                  if (intval($request['response']['code']) == 200) {
                      $result = json_decode($request['body'], true);
                  } else {
                      $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                  }
              }

              if ($result['state'] == 'error') {
                  return $result = A2W_ResultBuilder::buildError($request['data']);
              }
              
              $request = a2w_remote_post($result['request']['requestUrl'], $result['request']['apiParams']);

              if (is_wp_error($request)) {
                  $result = A2W_ResultBuilder::buildError($request->get_error_message());
              } else {
                  
                  if (intval($request['response']['code']) == 200) {

                    $body = json_decode($request['body'], true);

                    if (!isset($body['error_response'])){
                      
                        $product_data = $body['aliexpress_postproduct_redefining_findaeproductbyidfordropshipper_response']['result'];

                        if (isset($product_data['error_message']) || isset($product_data['error_code'])){
                            if (isset($product_data['error_code']) &&  $product_data['error_code'] == '-99999' ){
                                $result = A2W_ResultBuilder::buildError(__('Product not available', 'ali2woo'), ['error_code' => 1005]);  
                            } else {
                                $result = A2W_ResultBuilder::buildError(__('Product not found', 'ali2woo'), ['error_code' => 1004]); 
                            }
                        } else {

                                $lang = A2W_AliexpressLocalizator::getInstance()->language;
                                $product = array();
                                $product['id'] = $product_id;
                                $product['seller_id'] = '';
                        
                                $product['sku'] = a2w_random_str(); //todo: we can make it as option
                                $product['url'] = "https://" . ($lang === 'en' ? "www" : $lang) . ".aliexpress.com/item/{$product_id}/{$product_id}.html";
                                $product['title'] = $product_data['subject'];
                                
                                $product['seller_url'] = "";
                                $product['store_id'] = "";
                                $product['seller_name'] = "Store";
                                if (isset($product_data['store_info']['store_id'])){
                                    $product['store_id'] = $product_data['store_info']['store_id'];
                                    $product['seller_url'] =  "https://" . ($lang === 'en' ? "www" : $lang) . ".aliexpress.com/store/" . $product['store_id'];   
                                    $product['seller_name'] = $product_data['store_info']['store_name'];
                                }
                                
                                //  $product['local_seller_tag'] = // skip this
                                $product['ratings'] = isset($product_data['avg_evaluation_rating']) ? $product_data['avg_evaluation_rating'] : null;
                                $product['ratings_count'] = isset($product_data['evaluation_count']) ? $product_data['evaluation_count'] : null;
                    
                                $product['images'] = explode(';', $product_data['image_u_r_ls']);
                                $product['thumb'] = $product['images'][0];

                                $product['video'] = array();
                                if (isset($product_data['aeop_a_e_multimedia']) && isset($product_data['aeop_a_e_multimedia']['aeop_a_e_videos']) && isset($product_data['aeop_a_e_multimedia']['aeop_a_e_videos']['aeop_ae_video'])){
                                    $sourceVideoData = $product_data['aeop_a_e_multimedia']['aeop_a_e_videos']['aeop_ae_video'];
                                    if (!empty($sourceVideoData) && is_array($sourceVideoData)) {
                                        $product['video'] = $sourceVideoData[0];
                                        $product['seller_id'] = $product['video']['ali_member_id'];
                                    }
                                }

                                if (!empty($product['video'])){
                                    //todo: add option to load video or not because it slow down the process
                                    $video_link = $this->get_valid_aliexpress_video_link( $product['video'] );
                                    if ( $video_link ) {
                                        $product['video']['url'] = $video_link;
                                    }
                                }

                                $product['dimensions']['length'] = $product_data['package_length'];
                                $product['dimensions']['width'] = $product_data['package_width'];
                                $product['dimensions']['height'] = $product_data['package_height'];
                                $product['dimensions']['weight'] = $product_data['gross_weight'];
                
                                $product['baseUnit'] = isset($product_data['base_unit']) ? $product_data['base_unit'] : 0;
                                $product['productUnit'] = $product_data['product_unit'];
                                $product['packageType'] = $product_data['package_type'];
                                $product['category_id'] = $product_data['category_id'];
                                $product['category_name'] = '';

                                $product['description'] = $product_data['detail'];
                                $product['ordersCount'] = $product_data['order_count'];

                                $product['sku_products'] = array('attributes' => array(), 'variations' => array());

                                if (isset($product_data['aeop_ae_product_s_k_us']['aeop_ae_product_sku']) 
                                    && is_array($product_data['aeop_ae_product_s_k_us']['aeop_ae_product_sku'])
                                    && $product_data['product_status_type'] !== 'offline') 
                                {

                                    $attr_value_name_hash = array();
                                    $attributesWithPropertyIdAsKeys = array();

                                    //fetch attributes

                                    foreach ($product_data['aeop_ae_product_s_k_us']['aeop_ae_product_sku'] as $src_key_var => $src_var) {
                                            $attr_value_name_hash[$src_key_var] = array();
                                            if (isset($src_var['aeop_s_k_u_propertys'])){
                                            foreach ($src_var['aeop_s_k_u_propertys']['aeop_sku_property'] as $src_attr){
                                            
                                                if (!isset($attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']])){
                                                    $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']] = array('id' => $src_attr['sku_property_id'], 'name' => $src_attr['sku_property_name'], 'value' => array());
                                                }
                                                
                                                $attr = $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']];
                
                                                $propertyValueId = isset($src_attr['property_value_id_long']) ? $src_attr['property_value_id_long'] : '';
                                                $value = array('id' => $attr['id'] . ':' . $propertyValueId, 'name' => isset($src_attr['property_value_definition_name']) ? $src_attr['property_value_definition_name'] : $src_attr['sku_property_value']);
                                                $value['thumb'] = isset( $src_attr['sku_image'] ) ? str_replace( array(
                                                    'ae02.alicdn.com',
                                                    'ae03.alicdn.com',
                                                    'ae04.alicdn.com',
                                                    'ae05.alicdn.com',
                                                ), 'ae01.alicdn.com', $src_attr['sku_image'] ) : '';
                                                $value['image'] = $value['thumb'];
                
                                                if ($value['image']){
                                                    //save image in src var for future use
                                                    $product_data['aeop_ae_product_s_k_us']['aeop_ae_product_sku'][$src_key_var]['sku_image'] = $value['thumb'];
                                                }
                
                                                $countryCode = $this->property_value_id_to_ship_from( $src_attr['sku_property_id'], $propertyValueId );
                                                if ($countryCode) {
                                                    $value['country_code'] = $countryCode;
                                                }
                
                                                    // Fix value name dublicate
                                                    if (empty($attr_value_name_hash[$src_key_var][$value['name']])) {
                                                    $attr_value_name_hash[$src_key_var][$value['name']] = 1;
                                                } else {
                                                    $attr_value_name_hash[$src_key_var][$value['name']] += 1;
                                                    $value['name'] = $value['name'] . "#" . $attr_value_name_hash[$src_key_var][$value['name']];
                                                }
                
                                                //$attr['value'][$value['id']] = $value;
                                                $attributesWithPropertyIdAsKeys[$src_attr['sku_property_id']]['value'][$value['id']] = $value;
                                            }    
                                            }                    
                                    }

                                    $product['sku_products']['attributes'] = array_values($attributesWithPropertyIdAsKeys);
                                                
                                    //fetch variants
                                    $priceList = $product_data['aeop_ae_product_s_k_us']['aeop_ae_product_sku'];
                                    foreach ($priceList as $src_var) {
                                        $aa = array();
                                        $aa_names = array();
                                        $country_code = ''; 
                                        if (isset($src_var['id']) && $src_var['id']) {
                                            $sky_attrs = explode(";", $src_var['id']);
                                            $sky_attrs = array_map(function($el){ $t = explode("#", $el); return $t[0]; }, $sky_attrs); //clean elements from # part
                                            foreach ($sky_attrs as $sky_attr) {
                                                $tmp_v = $sky_attr;
                                                if (count($product['sku_products']['attributes']) > 0){
                                                    $aa[] = $tmp_v;
                        
                                                    foreach ($product['sku_products']['attributes'] as $attr) {
                                                        if (isset($attr['value'][$tmp_v])) {
                                                            $aa_names[] = $attr['value'][$tmp_v]['name'];
                                                            if (isset($attr['value'][$tmp_v]['country_code'])) {
                                                                $country_code = $attr['value'][$tmp_v]['country_code'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        $quantity =  isset($src_var['s_k_u_available_stock'] ) ? $src_var['s_k_u_available_stock'] : ( isset( $src_var['ipm_sku_stock'] ) ? $src_var['ipm_sku_stock'] : 0 );
                                        $regular_price = round(floatval($src_var['sku_price']), 2);
                                        $price = isset($src_var['offer_sale_price'] ) ? round(floatval($src_var['offer_sale_price']), 2) : $regular_price;
                                        $currency = $src_var['currency_code'];
                                        $discount = 100 -  round(100 * $price / $regular_price, 2);
                                        $bulk_price = isset( $src_var['offer_bulk_sale_price'] ) ? round(floatval($src_var['offer_bulk_sale_price']), 2) : 0;
                                        $min_bulk_order =  isset( $src_var['sku_bulk_order'] ) ? $src_var['sku_bulk_order'] : 0;

                                        $sorted_aa = $this->sort_variant_id_parts($aa);

                                        $variation = array(
                                            'id' => $product_id . '-' . ($sorted_aa ? implode('-', $sorted_aa) : (count($product['sku_products']['variations']) + 1)),
                                            /* 'skuId'=> $src_var['skuId'],
                                            'skuIdStr'=>$src_var['skuIdStr'],*/
                                            // 'sku' => $product_id . '-' . (count($product['sku_products']['variations']) + 1),
                                            'sku' => a2w_random_str(),
                                            'attributes' => $aa,
                                            'attributes_names' => $aa_names,
                                            'quantity' => $quantity,
                                            'currency' => strtoupper($currency),
                                            'regular_price' => $regular_price,
                                            'price' => $price,
                                            'bulk_price' => $bulk_price,
                                            'min_bulk_order' => $min_bulk_order,
                                            'discount' => $discount,
                                        );
                    
                                        if ($country_code) {
                                            $variation['country_code'] = $country_code;
                                        }
                    
                                        if (isset($src_var['sku_image'])) {
                                            $variation['image'] = $src_var['sku_image'];
                                        }
                    
                                        $product['sku_products']['variations'][$variation['id']] = $variation;
                                    }
                                }
                                else {
                                    return A2W_ResultBuilder::buildError(__('Product not available', 'ali2woo'), ['error_code' => 1005]);  
                                }

                                 //we don't need keys in array
                                 if (count($product['sku_products']['variations']) > 0){
                                    $product['sku_products']['variations'] = array_values($product['sku_products']['variations']);
                                }

                                $product['currency'] = $product_data['currency_code'];
                                if (isset($product_data['aeop_ae_product_propertys']['aeop_ae_product_property']) && is_array($product_data['aeop_ae_product_propertys']['aeop_ae_product_property'])) {
                                    foreach ($product_data['aeop_ae_product_propertys']['aeop_ae_product_property'] as $prop) {
                                        //todo: here value is returned as simple value not as array
                                        $product['attribute'][] = array('name' => $prop['attr_name'], 'value' => isset($prop['attr_value']) ? $prop['attr_value'] : '');
                                    }
                                }
                                //todo: check why it needs here?
                                $product['complete'] = true;

                                $result = A2W_ResultBuilder::buildOk(array('product' => $product));
                        }
                    } else {
                        $error_response = $body['error_response'];

                        if (isset($error_response['code']) && $error_response['code'] == '27'){
                            $msg = sprintf(__('Refresh your AliExpress access token. <a target="_blank" href="%s">Please check our instruction</a>.', 'ali2woo'),
                            'https://help.ali2woo.com/codex/how-to-get-access-token-from-aliexpress/'
                            );
                            $result = A2W_ResultBuilder::buildError($msg);
                        } else {
                            $result = A2W_ResultBuilder::buildError(__('AliExpress get-product API error: "',  'ali2woo') . $error_response['msg'] . '", error code: ' . $error_response['code']);       
                        }
                    }

                  } else {
                      $result = A2W_ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
                  }
              }

              return $result;
        }

        private function get_valid_aliexpress_video_link( $video ) {
            //todo: check how this method works with both API
            $link    = "https://cloud.video.taobao.com/play/u/{$video['ali_member_id']}/p/1/e/6/t/10301/{$video['media_id']}.mp4";
            $request = wp_safe_remote_get( $link );
            if ( wp_remote_retrieve_response_code( $request ) == 400 ) {
                $link    = "https://video.aliexpress-media.com/play/u/ae_sg_item/{$video['ali_member_id']}/p/1/e/6/t/10301/{$video['media_id']}.mp4";
                $request = wp_safe_remote_get( $link );
                if ( wp_remote_retrieve_response_code( $request ) == 400 ) {
                    $link = false;
                }
            }

            return $link;
        }

        private function property_value_id_to_ship_from( $property_id, $property_value_id ) {
            $ship_from = '';
            if ( $property_id == 200007763 ) {
                switch ( $property_value_id ) {
                    case 203372089:
                        $ship_from = 'PL';
                        break;
                    case 201336100:
                    case 201441035:
                        $ship_from = 'CN';
                        break;
                    case 201336103:
                        $ship_from = 'RU';
                        break;
                    case 100015076:
                        $ship_from = 'BE';
                        break;
                    case 201336104:
                        $ship_from = 'ES';
                        break;
                    case 201336342:
                        $ship_from = 'FR';
                        break;
                    case 201336106:
                        $ship_from = 'US';
                        break;
                    case 201336101:
                        $ship_from = 'DE';
                        break;
                    case 203124901:
                        $ship_from = 'UA';
                        break;
                    case 201336105:
                        $ship_from = 'UK';
                        break;
                    case 201336099:
                        $ship_from = 'AU';
                        break;
                    case 203287806:
                        $ship_from = 'CZ';
                        break;
                    case 201336343:
                        $ship_from = 'IT';
                        break;
                    case 203054831:
                        $ship_from = 'TR';
                        break;
                    case 203124902:
                        $ship_from = 'AE';
                        break;
                    case 100015009:
                        $ship_from = 'ZA';
                        break;
                    case 201336102:
                        $ship_from = 'ID';
                        break;
                    case 202724806:
                        $ship_from = 'CL';
                        break;
                    case 203054829:
                        $ship_from = 'BR';
                        break;
                    case 203124900:
                        $ship_from = 'VN';
                        break;
                    case 203124903:
                        $ship_from = 'IL';
                        break;
                    case 100015000:
                        $ship_from = 'SA';
                        break;
                    case 5581:
                        $ship_from = 'KR';
                        break;
                    default:
                }
            }
    
            return $ship_from;
        }

        private function get_product_combined_data($data_v1, $data_v2){
            $result = $data_v2;

            if ($data_v1['state'] !== 'error' &&  $result['state'] !== 'error') {
                foreach ( $result['product']['sku_products']['variations'] as $i => &$var) {
                    if (isset($data_v1['product']['sku_products']['variations'][$i])){
                        $var_v1 = $data_v1['product']['sku_products']['variations'][$i];

                        //take quantity from v1 if it's zero in v2
                        if ($var['quantity'] == 0){
                            $var['quantity'] = $var_v1['quantity'];
                        }

                        //take discount from v1
                        $discount = $var['discount'] = $var_v1['discount'];
                        if ($discount > 0){
                            $var['regular_price'] = round(floatval(100 * $var['price'] / (100 - $discount)), 2);
                        }
                        unset( $var['bulk_price']); //todo: we can calculate it and use if needed
                    } else {
                        //todo: make a logging of such products for which APIs returns different vars
                    }
                }

                if ($result['product']['import_lang'] !== A2W_AliexpressLocalizator::getInstance()->language){
                    $result['product']['title'] = $data_v1['product']['title'];
                    $result['product']['description'] = $data_v1['product']['description'];
                }

                $result['product']['ordersCount'] = $data_v1['product']['ordersCount'];
            }

            return $result;
        }

        private function get_access_token(){
            A2W_Utils::clear_system_error_messages();

            $token = A2W_AliexpressToken::getInstance()->defaultToken();
    
            if (!$token) {
                $msg = sprintf(__('AliExpress access token is not found. <a target="_blank" href="%s">Please check our instruction</a>.', 'ali2woo'),
                'https://help.ali2woo.com/codex/how-to-get-access-token-from-aliexpress/'
                );

                A2W_Utils::show_system_error_message($msg);

                //todo: add here a check whether token has expired 

                throw new Exception($msg);
            }

            return $token['access_token'];
        }
    }
}
