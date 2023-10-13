<?php

/**
 * Description of A2W_Aliexpress
 *
 * @author Ali2Woo Team
 */
if (!class_exists('A2W_Aliexpress')) {

    class A2W_Aliexpress
    {

        private $product_import_model;
        private $connector;
        private $account;

        public function __construct()
        {
            $this->product_import_model = new A2W_ProductImport();
            $this->connector = A2W_AliexpressDefaultConnector::getInstance();
            $this->account = A2W_Account::getInstance();
        }

        public function load_products($filter, $page = 1, $per_page = 20, $params = array())
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $products_in_import = $this->product_import_model->get_product_id_list();

            $result = $this->connector->load_products($filter, $page, $per_page, $params);

            if (isset($result['state']) && $result['state'] !== 'error') {
                $default_type = a2w_get_setting('default_product_type');
                $default_status = a2w_get_setting('default_product_status');

                $tmp_urls = array();

                foreach ($result['products'] as &$product) {
                    $product['post_id'] = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_a2w_external_id' AND meta_value='%s' LIMIT 1", $product['id']));
                    $product['import_id'] = in_array($product['id'], $products_in_import) ? $product['id'] : 0;
                    $product['product_type'] = $default_type;
                    $product['product_status'] = $default_status;
                    $product['is_affiliate'] = true;

                    if (isset($filter['country']) && $filter['country']) {
                        $product['shipping_to_country'] = $filter['country'];
                    }

                    $tmp_urls[] = $product['url'];
                }

                if ($this->account->custom_account) {
                    try {
                        $promotionUrls = $this->get_affiliate_urls($tmp_urls);
                        if (!empty($promotionUrls) && is_array($promotionUrls)) {
                            foreach ($result["products"] as $i => $product) {
                                foreach ($promotionUrls as $pu) {
                                    if ($pu['url'] == $product['url']) {
                                        $result["products"][$i]['affiliate_url'] = $pu['promotionUrl'];
                                        break;
                                    }
                                }
                            }
                        }
                    } catch (Throwable $e) {
                        a2w_print_throwable($e);
                        foreach ($result['products'] as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    } catch (Exception $e) {
                        a2w_print_throwable($e);
                        foreach ($result['products'] as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    }
                }
            }
            
            return $result;
        }

        public function load_store_products($filter, $page = 1, $per_page = 20, $params = array())
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $products_in_import = $this->product_import_model->get_product_id_list();

            $result = $this->connector->load_store_products($filter, $page, $per_page, $params);

            if (isset($result['state']) && $result['state'] !== 'error') {
                $default_type = a2w_get_setting('default_product_type');
                $default_status = a2w_get_setting('default_product_status');

                $tmp_urls = array();

                foreach ($result['products'] as &$product) {
                    $product['post_id'] = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_a2w_external_id' AND meta_value='%s' LIMIT 1", $product['id']));
                    $product['import_id'] = in_array($product['id'], $products_in_import) ? $product['id'] : 0;
                    $product['product_type'] = $default_type;
                    $product['product_status'] = $default_status;
                    $product['is_affiliate'] = true;

                    if (isset($filter['country']) && $filter['country']) {
                        $product['shipping_to_country'] = $filter['country'];
                    }

                    $tmp_urls[] = $product['url'];
                }

                if ($this->account->custom_account) {
                    try {
                        $promotionUrls = $this->get_affiliate_urls($tmp_urls);
                        if (!empty($promotionUrls) && is_array($promotionUrls)) {
                            foreach ($result["products"] as $i => $product) {
                                foreach ($promotionUrls as $pu) {
                                    if ($pu['url'] == $product['url']) {
                                        $result["products"][$i]['affiliate_url'] = $pu['promotionUrl'];
                                        break;
                                    }
                                }
                            }
                        }
                    } catch (Throwable $e) {
                        a2w_print_throwable($e);
                        foreach ($result['products'] as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    } catch (Exception $e) {
                        a2w_print_throwable($e);
                        foreach ($result['products'] as &$product) {
                            $product['affiliate_url'] = $product['url'];
                        }
                    }
                }
            }
            
            return $result;
        }

        public function load_reviews($product_id, $page, $page_size = 20, $params = array())
        {
            $result = $this->connector->load_reviews($product_id, $page, $page_size, $params);

            if ($result['state'] !== 'error') {
                $result = A2W_ResultBuilder::buildOk(array('reviews' => isset($result['reviews']['evaViewList']) ? $result['reviews']['evaViewList'] : array(), 'totalNum' => isset($result['reviews']['totalNum']) ? $result['reviews']['totalNum'] : 0));
            }

            return $result;
        }

        public function load_product($product_id, $params = array())
        {
            /** @var wpdb $wpdb */
            global $wpdb;
            $products_in_import = $this->product_import_model->get_product_id_list();

            try {
                $result = $this->connector->load_product($product_id, $params);
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }
        
            if ($result['state'] !== 'error') {
                $result['product']['post_id'] = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_a2w_external_id' AND meta_value='%s' LIMIT 1", $result['product']['id']));
                $result['product']['import_id'] = in_array($result['product']['id'], $products_in_import) ? $result['product']['id'] : 0;
                $result['product']['import_lang'] = A2W_AliexpressLocalizator::getInstance()->language;

                $result['product'] = $this->calculateProductPricesFromVariants($result['product']);

                if ($this->account->custom_account) {
                    try {
                        $promotionUrls = $this->get_affiliate_urls($result['product']['url']);
                        if (!empty($promotionUrls) && is_array($promotionUrls)) {
                            $result['product']['affiliate_url'] = $promotionUrls[0]['promotionUrl'];
                        }
                    } catch (Throwable $e) {
                        a2w_print_throwable($e);
                        $result['product']['affiliate_url'] = $result['product']['url'];
                    } catch (Exception $e) {
                        a2w_print_throwable($e);
                        $result['product']['affiliate_url'] = $result['product']['url'];
                    }
                } else {
                    try {
                        $promotionUrls = $this->get_default_affiliate_urls($result['product']['url']);
                        if (is_array($promotionUrls)) {
                            $result['product']['affiliate_url'] = $promotionUrls[0]['promotionUrl'];
                        }
                    } catch (Exception $e) {}
    
                }

                if (a2w_get_setting('remove_ship_from')) {
                    $default_ship_from = a2w_get_setting('default_ship_from');
                    $result['product'] = A2W_Utils::remove_ship_from($result['product'], $default_ship_from);
                }

                $country_from = a2w_get_setting('aliship_shipfrom', 'CN');
                $country_to = a2w_get_setting('aliship_shipto');
                $result['product'] = A2W_Utils::update_product_shipping($result['product'], $country_from, $country_to, 'import', a2w_get_setting('add_shipping_to_price'));

                if (($convert_attr_casea = a2w_get_setting('convert_attr_case')) != 'original') {
                    $convert_func = false;
                    switch ($convert_attr_casea) {
                        case 'lower':
                            $convert_func = function ($v) {return strtolower($v);};
                            break;
                        case 'sentence':
                            $convert_func = function ($v) {return ucfirst(strtolower($v));};
                            break;
                    }

                    if ($convert_func) {
                        foreach ($result['product']['sku_products']['attributes'] as &$product_attr) {
                            if (!isset($product_attr['original_name'])) {
                                $product_attr['original_name'] = $product_attr['name'];
                            }

                            $product_attr['name'] = $convert_func($product_attr['name']);

                            foreach ($product_attr['value'] as &$product_attr_val) {
                                $product_attr_val['name'] = $convert_func($product_attr_val['name']);
                            }
                        }

                        foreach ($result['product']['sku_products']['variations'] as &$product_var) {
                            $product_var['attributes_names'] = array_map($convert_func, $product_var['attributes_names']);
                        }
                    }
                }

                if (a2w_get_setting('use_random_stock')) {
                    $result['product']['disable_var_quantity_change'] = true;
                    foreach ($result['product']['sku_products']['variations'] as &$variation) {
                        $variation['original_quantity'] = intval($variation['quantity']);
                        $tmp_quantity = rand(intval(a2w_get_setting('use_random_stock_min')), intval(a2w_get_setting('use_random_stock_max')));
                        $tmp_quantity = ($tmp_quantity > $variation['original_quantity']) ? $variation['original_quantity'] : $tmp_quantity;
                        $variation['quantity'] = $tmp_quantity;
                    }
                }

                if (isset($result['product']['attribute']) && is_array($result['product']['attribute'])) {
                    $convertedAttributes = array();
                    $split_attribute_values = a2w_get_setting('split_attribute_values');
                    $attribute_values_separator = a2w_get_setting('attribute_values_separator');
                    foreach ($result['product']['attribute'] as $attr) {
                        $el = array('name' => $attr['name']);
                        if ($split_attribute_values) {
                            $el['value'] = array_map('a2w_phrase_apply_filter_to_text', array_map('trim', explode($attribute_values_separator, $attr['value'])));
                        } else {
                            $el['value'] = array(a2w_phrase_apply_filter_to_text(trim($attr['value'])));
                        }
                        $convertedAttributes[] = $el;
                    }
                    $result['product']['attribute'] = $convertedAttributes;
                }

                $sourceDescription = $result['product']['description'];
                $result['product']['description'] = '';
                if (a2w_check_defined('A2W_SAVE_ATTRIBUTE_AS_DESCRIPTION')) {
                    $convertedDescription = '';
                    if ($result['product']['attribute'] && count($result['product']['attribute']) > 0) {
                        $convertedDescription .= '<table class="shop_attributes"><tbody>';
                        foreach ($result['product']['attribute'] as $attribute) {
                            $convertedDescription .= '<tr><th>' . $attribute['name'] . '</th><td><p>' . (is_array($attribute['value']) ? implode(", ", $attribute['value']) : $attribute['value']) . "</p></td></tr>";
                        }
                        $convertedDescription .= '</tbody></table>';
                    }
                    $result['product']['description'] = $convertedDescription;
                }

                if (!a2w_get_setting('not_import_description')) {
                    $result['product']['description'] .= $this->clean_description($sourceDescription);
                }

                $result['product']['description'] = A2W_PhraseFilter::apply_filter_to_text($result['product']['description']);

                $tmp_all_images = A2W_Utils::get_all_images_from_product($result['product']);

                $not_import_gallery_images = false;
                $not_import_variant_images = false;
                $not_import_description_images = a2w_get_setting('not_import_description_images');

                $result['product']['skip_images'] = array();
                foreach ($tmp_all_images as $img_id => $img) {
                    if (!in_array($img_id, $result['product']['skip_images']) && (($not_import_gallery_images && $img['type'] === 'gallery') || ($not_import_variant_images && $img['type'] === 'variant') || ($not_import_description_images && $img['type'] === 'description'))) {
                        $result['product']['skip_images'][] = $img_id;
                    }
                }
            }

            return $result;
        }


        public function check_affiliate($product_id)
        {
            //todo: fix this method
            $result = $this->connector->check_affiliate($product_id);

            return $result;
        }

        private function calculateProductPricesFromVariants($product){

            $product['regular_price_min'] =  
            $product['regular_price_max'] =  
            $product['price_min'] =  
            $product['price_max'] = 0.00;
            
            foreach ($product['sku_products']['variations'] as $var) {
                $product['currency'] = $var['currency'];
    
                if (!$product['price_min'] || !$product['price_max']) {
                    $product['price_min'] = $product['price_max'] = $var['price'];
                    $product['regular_price_min'] = $product['regular_price_max'] = $var['regular_price'];
                }
    
                if ($product['price_min'] > $var['price']) {
                    $product['price_min'] = $var['price'];
                    $product['regular_price_min'] = $var['regular_price'];
                }
                if ($product['price_max'] < $var['price']) {
                    $product['price_max'] = $var['price'];
                    $product['regular_price_max'] = $var['regular_price'];
                }
            }
    
            return $product;
        }    

        private function createNotAvailableProduct($id){
            return 
                [
                    'id' => $id,
                    'sku_products' => [
                        'attributes' => [], 
                        'variations' => []
                ]];
        }

        public function sync_products($product_ids, $params = array())
        {
            //todo: check what to do with pc param
            //also check what to do when one of the product is not updated
            $product_ids = is_array($product_ids) ? $product_ids : array($product_ids);

            $request_params = array('product_id' => implode(',', $product_ids));
            if (!empty($params['manual_update'])) {
                $request_params['manual_update'] = 1;
            }
            if (!empty($params['pc'])) {
                $request_params['pc'] = $params['pc'];
            }

            $products = array();

            foreach($product_ids as $product_id){

                $product_id_parts = explode(';', $product_id);
                $params['lang'] = $product_id_parts[1];
           
                try {
                    $result = $this->connector->load_product($product_id_parts[0], $params);
                } catch (Throwable $e) {
                    a2w_print_throwable($e);
                    $result = A2W_ResultBuilder::buildError($e->getMessage());
                }

                if ( $result['state'] !== 'error'){
                    $products[] = $result['product'];
                } else {
                    if (isset($result['error_code']) && in_array($result['error_code'], [ 1004,1005])){
                        $products[] = $this->createNotAvailableProduct( $product_id_parts[0] );   
                    }
                    //$result = A2W_ResultBuilder::buildError($request->get_error_message());
                }
            }

            $result = A2W_ResultBuilder::buildOk(array('products' => $products));

            $use_random_stock = a2w_get_setting('use_random_stock');
            if ($use_random_stock) {
                $random_stock_min = intval(a2w_get_setting('use_random_stock_min'));
                $random_stock_max = intval(a2w_get_setting('use_random_stock_max'));

                foreach ($result['products'] as &$product) {
                    foreach ($product['sku_products']['variations'] as &$variation) {
                        $variation['original_quantity'] = intval($variation['quantity']);
                        $tmp_quantity = rand($random_stock_min, $random_stock_max);
                        $tmp_quantity = ($tmp_quantity > $variation['original_quantity']) ? $variation['original_quantity'] : $tmp_quantity;
                        $variation['quantity'] = $tmp_quantity;
                    }
                }
            }

            if ($this->account->custom_account && isset($result['products'])) {
                $tmp_urls = array();

                foreach ($result['products'] as $product) {
                    if (isset($product['url']) && !empty($product['url'])) {
                        $tmp_urls[] = $product['url'];
                    }
                }

                try {
                    $promotionUrls = $this->get_affiliate_urls($tmp_urls);
                    if (!empty($promotionUrls) && is_array($promotionUrls)) {
                        foreach ($result["products"] as &$product) {
                            foreach ($promotionUrls as $pu) {
                                if (!empty($pu) && $pu['url'] == $product['url']) {
                                    $product['affiliate_url'] = $pu['promotionUrl'];
                                    break;
                                }
                            }
                        }
                    }
                } catch (Throwable $e) {
                    a2w_print_throwable($e);
                    foreach ($result['products'] as &$product) {
                        $product['affiliate_url'] = ''; //set empty to disable update!
                    }
                } catch (Exception $e) {
                    a2w_print_throwable($e);
                    foreach ($result['products'] as &$product) {
                        $product['affiliate_url'] = ''; //set empty to disable update!
                    }
                }

            }else {
                try {
                    foreach ($result["products"] as $product) {
                        $promotionUrls = $this->get_default_affiliate_urls($product['url']);
                        if (is_array($promotionUrls)) {
                            $product['affiliate_url'] = $promotionUrls[0]['promotionUrl'];
                        }
                    }
             
                } catch (Exception $e) {}

            }

            //we don't want to update description by default
            foreach ($result["products"] as &$product) {

                if (isset($product['description'])){
                    $product['source_description'] = $product['description'];
                    $product['description'] = '';
                }
            }

            if (isset($params['manual_update']) && $params['manual_update'] && a2w_check_defined('A2W_FIX_RELOAD_DESCRIPTION') && !a2w_get_setting('not_import_description')) {

                foreach ($result["products"] as &$product) {
                    if (isset($product['description'])){
                        $source_description = $product['source_description'];
                        $product['description'] = $this->clean_description($source_description);
                        $product['description'] = A2W_PhraseFilter::apply_filter_to_text($product['description']);
                    }
                }
            }

            /*
            $request_url = A2W_RequestHelper::build_request('sync_products', $request_params);

            if (empty($params['data'])) {
                $request = a2w_remote_get($request_url);
            } else {
                $request = a2w_remote_post($request_url, $params['data']);
            }*/

            return $result;
        }

        public function load_shipping_info($product_id, $quantity, $country_code, $country_code_from = 'CN', $min_price = '', $max_price = '', $province = '', $city = '' ){

            $result = $this->connector->load_shipping_info($product_id, $quantity, $country_code, $country_code_from, $min_price, $max_price, $province, $city);

            return $result;
        }

        private function clean_description($description)
        {
            $html = $description;

            if (function_exists('mb_convert_encoding')) {
                $html = trim(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            } else {
                $html = htmlspecialchars_decode(utf8_decode(htmlentities($html, ENT_COMPAT, 'UTF-8', false)));
            }

            if (function_exists('libxml_use_internal_errors')) {
                libxml_use_internal_errors(true);
            }

            if ($html && class_exists('DOMDocument')) {
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $dom->formatOutput = true;

                $tags = apply_filters('a2w_clean_description_tags', array('script', 'head', 'meta', 'style', 'map', 'noscript', 'object', 'iframe'));

                foreach ($tags as $tag) {
                    $elements = $dom->getElementsByTagName($tag);
                    for ($i = $elements->length; --$i >= 0;) {
                        $e = $elements->item($i);
                        if ($tag == 'a') {
                            while ($e->hasChildNodes()) {
                                $child = $e->removeChild($e->firstChild);
                                $e->parentNode->insertBefore($child, $e);
                            }
                            $e->parentNode->removeChild($e);
                        } else {
                            $e->parentNode->removeChild($e);
                        }
                    }
                }

                if (!in_array('img', $tags)) {
                    $elements = $dom->getElementsByTagName('img');
                    for ($i = $elements->length; --$i >= 0;) {
                        $e = $elements->item($i);
                        $e->setAttribute('src', A2W_Utils::clear_image_url($e->getAttribute('src')));
                    }
                }

                $html = $dom->saveHTML();
            }

            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $html);

            $html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) height=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) alt=".*?"/i', '$1', $html);
            $html = preg_replace('/^<!DOCTYPE.+?>/', '$1', str_replace(array('<html>', '</html>', '<body>', '</body>'), '', $html));
            $html = preg_replace("/<\/?div[^>]*\>/i", "", $html);

            $html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '', $html);
            $html = preg_replace('/<a[^>]*><\/a>/iU', '', $html); //delete empty A tags
            $html = preg_replace("/<\/?h1[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?strong[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?span[^>]*\>/i", "", $html);

            //$html = str_replace(' &nbsp; ', '', $html);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = str_replace('\t', ' ', $html);
            $html = str_replace('  ', ' ', $html);

            $html = preg_replace("/http:\/\/g(\d+)\.a\./i", "https://ae$1.", $html);

            $html = preg_replace("/<[^\/>]*[^td]>([\s]?|&nbsp;)*<\/[^>]*[^td]>/", '', $html); //delete ALL empty tags
            $html = preg_replace('/<td[^>]*><\/td>/iU', '', $html); //delete empty TD tags

            $html = str_replace(array('<img', '<table'), array('<img class="img-responsive"', '<table class="table table-bordered'), $html);
            $html = force_balance_tags($html);

            return html_entity_decode($html, ENT_COMPAT, 'UTF-8');
        }

        private function get_affiliate_urls($urls)
        {
            if ($this->account->account_type == 'admitad') {
                return A2W_AdmitadAccount::getInstance()->getDeeplink($urls);
            } else if ($this->account->account_type == 'epn') {
                return A2W_EpnAccount::getInstance()->getDeeplink($urls);
            } else {
                return A2W_AliexpressAccount::getInstance()->getDeeplink($urls);
            }
        }

        private function get_default_affiliate_urls($urls)
        {
            $cashback_url = 'https://alitems.co/g/1e8d114494507e24cafe16525dc3e8/';

            if (!is_array($urls)) {
                $urls = array(strval($urls));
            }

            $result = array();

            foreach ($urls as $url) {
                $result[] = array('url' => $url, 'promotionUrl' => $cashback_url . '?ulp=' . urlencode($url));
            }

            return $result;

        }

        public function place_order($data)
        {
            try {
                $result = $this->connector->place_order($data);
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }

            return $result;
        }

        public function load_order($order_id)
        {
            try {
                $result = $this->connector->load_order($order_id);
            } catch (Throwable $e) {
                a2w_print_throwable($e);
                $result = A2W_ResultBuilder::buildError($e->getMessage());
            }

            return $result;
        }

    }

}
