<?php

/**
 * Description of Aliexpress
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class Aliexpress
{

    private $product_import_model;
    private $connector;
    private $account;

    public function __construct()
    {
        $this->product_import_model = new ProductImport();
        $this->connector = AliexpressDefaultConnector::getInstance();
        $this->account = Account::getInstance();
    }

    public function load_products($filter, $page = 1, $per_page = 20, $params = [])
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $products_in_import = $this->product_import_model->get_product_id_list();

        $result = $this->connector->load_products($filter, $page, $per_page, $params);

        if (isset($result['state']) && $result['state'] !== 'error') {
            $default_type = get_setting('default_product_type');
            $default_status = get_setting('default_product_status');

            $tmp_urls = [];

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
                } catch (\Throwable $e) {
                    a2w_print_throwable($e);
                    foreach ($result['products'] as &$product) {
                        $product['affiliate_url'] = $product['url'];
                    }
                }
            }
        }

        return $result;
    }

    public function load_store_products($filter, $page = 1, $per_page = 20, $params = [])
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $products_in_import = $this->product_import_model->get_product_id_list();

        $result = $this->connector->load_store_products($filter, $page, $per_page, $params);

        if (isset($result['state']) && $result['state'] !== 'error') {
            $default_type = get_setting('default_product_type');
            $default_status = get_setting('default_product_status');

            $tmp_urls = [];

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
                } catch (\Throwable $e) {
                    a2w_print_throwable($e);
                    foreach ($result['products'] as &$product) {
                        $product['affiliate_url'] = $product['url'];
                    }
                } catch (\Exception $e) {
                    a2w_print_throwable($e);
                    foreach ($result['products'] as &$product) {
                        $product['affiliate_url'] = $product['url'];
                    }
                }
            }
        }

        return $result;
    }

    public function load_reviews($product_id, $page, $page_size = 20, $params = [])
    {
        $result = $this->connector->load_reviews($product_id, $page, $page_size, $params);
        if ($result['state'] !== 'error') {
            $result = ResultBuilder::buildOk(
                [
                    'reviews' => $result['reviews']['evaViewList'] ?? [],
                    'totalNum' => $result['reviews']['totalNum'] ?? 0
                ]
            );
        }

        return $result;
    }

    public function load_product($product_id, $params = [])
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $products_in_import = $this->product_import_model->get_product_id_list();

        try {
            $params['skip_desc'] = get_setting('not_import_description');
            $result = $this->connector->load_product($product_id, $params);
        } catch (\Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        if ($result['state'] !== 'error') {
            $result['product']['post_id'] = $wpdb->get_var($wpdb->prepare(
                "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_a2w_external_id' AND meta_value='%s' LIMIT 1",
                $result['product']['id']
            ));
            $result['product']['import_id'] = in_array($result['product']['id'], $products_in_import) ? $result['product']['id'] : 0;
            $result['product']['import_lang'] = AliexpressLocalizator::getInstance()->language;

            $result['product'] = $this->calculateProductPricesFromVariants($result['product']);

            if ($this->account->custom_account) {
                try {
                    $promotionUrls = $this->get_affiliate_urls($result['product']['url']);
                    if (!empty($promotionUrls) && is_array($promotionUrls)) {
                        $result['product']['affiliate_url'] = $promotionUrls[0]['promotionUrl'];
                    }
                } catch (\Throwable $e) {
                    a2w_print_throwable($e);
                    $result['product']['affiliate_url'] = $result['product']['url'];
                }
            }

            if (get_setting('remove_ship_from')) {
                $default_ship_from = get_setting('default_ship_from');
                $result['product'] = Utils::remove_ship_from($result['product'], $default_ship_from);
            }

            $country_from = get_setting('aliship_shipfrom', 'CN');
            $country_to = get_setting('aliship_shipto');
            $result['product'] = Utils::update_product_shipping(
                $result['product'],
                $country_from,
                $country_to,
                'import',
                get_setting('add_shipping_to_price')
            );

            if (($convert_attr_casea = get_setting('convert_attr_case')) != 'original') {
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

            if (get_setting('use_random_stock')) {
                $result['product']['disable_var_quantity_change'] = true;
                foreach ($result['product']['sku_products']['variations'] as &$variation) {
                    $variation['original_quantity'] = intval($variation['quantity']);
                    $tmp_quantity = rand(
                        intval(get_setting('use_random_stock_min')),
                        intval(get_setting('use_random_stock_max'))
                    );
                    $tmp_quantity = ($tmp_quantity > $variation['original_quantity']) ?
                        $variation['original_quantity'] :
                        $tmp_quantity;
                    $variation['quantity'] = $tmp_quantity;
                }
            }

            if (isset($result['product']['attribute']) && is_array($result['product']['attribute'])) {
                $convertedAttributes = [];
                $split_attribute_values = get_setting('split_attribute_values');
                $attribute_values_separator = get_setting('attribute_values_separator');
                foreach ($result['product']['attribute'] as $attr) {
                    $el = ['name' => $attr['name'], 'value' => []];
                    if (!empty($attr['value'])) {
                        if ($split_attribute_values) {
                            $el['value'] = array_map('Ali2Woo\phrase_apply_filter_to_text', array_map('trim', explode($attribute_values_separator, $attr['value'])));
                        } else {
                            $el['value'] = [phrase_apply_filter_to_text(trim($attr['value']))];
                        }
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
                        $convertedDescription .= '<tr><th>' . $attribute['name'] . '</th><td><p>' .
                            (is_array($attribute['value']) ?
                                implode(", ", $attribute['value']) :
                                $attribute['value']) . "</p></td></tr>";
                    }
                    $convertedDescription .= '</tbody></table>';
                }
                $result['product']['description'] = $convertedDescription;
            }

            if (!get_setting('not_import_description')) {
                $result['product']['description'] .= $this->clean_description($sourceDescription);
            }

            $result['product']['description'] = PhraseFilter::apply_filter_to_text($result['product']['description']);

            $tmp_all_images = Utils::get_all_images_from_product($result['product']);

            $not_import_gallery_images = false;
            $not_import_variant_images = false;
            $not_import_description_images = get_setting('not_import_description_images');

            $result['product']['skip_images'] = [];
            foreach ($tmp_all_images as $img_id => $img) {
                $shouldSkipImage = !in_array($img_id, $result['product']['skip_images']) &&
                    (($not_import_gallery_images && $img['type'] === 'gallery') ||
                        ($not_import_variant_images && $img['type'] === 'variant') ||
                        ($not_import_description_images && $img['type'] === 'description')
                    );
                if ($shouldSkipImage) {
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

        $products = [];
        $notAvailableProducts = [];

        foreach ($product_ids as $product_id) {

            $product_id_parts = explode(';', $product_id);
            $params['lang'] = $product_id_parts[1];

            try {
                $result = $this->connector->load_product($product_id_parts[0], $params);
            } catch (\Throwable $e) {
                a2w_print_throwable($e);
                $result = ResultBuilder::buildError($e->getMessage());
            }

            if ($result['state'] !== 'error') {
                $products[] = $result['product'];
            } else {
                if (isset($result['error_code']) && in_array($result['error_code'], [1004,1005])){
                    $notAvailableProducts[] = $this->createNotAvailableProduct($product_id_parts[0]);
                }
                //$result = ResultBuilder::buildError($request->get_error_message());
            }
        }

        $result = ResultBuilder::buildOk(array('products' => $products));

        $use_random_stock = get_setting('use_random_stock');
        if ($use_random_stock) {
            $random_stock_min = intval(get_setting('use_random_stock_min'));
            $random_stock_max = intval(get_setting('use_random_stock_max'));

            foreach ($result['products'] as &$product) {
                foreach ($product['sku_products']['variations'] as &$variation) {
                    $variation['original_quantity'] = intval($variation['quantity']);
                    $tmp_quantity = rand($random_stock_min, $random_stock_max);
                    $tmp_quantity = ($tmp_quantity > $variation['original_quantity']) ? $variation['original_quantity'] : $tmp_quantity;
                    $variation['quantity'] = $tmp_quantity;
                }
            }
        }

        $sync_default_shipping_cost = isset($params['manual_update']) && $params['manual_update'] 
                                        && a2w_check_defined('A2W_SYNC_PRODUCT_SHIPPING')
                                        &&  get_setting('add_shipping_to_price');

        if ($sync_default_shipping_cost) {
            /*
                This feature enables the synchronization of the shipping cost assigned to a product. 
                It attempts to apply the cost of the default shipping method if it is available for the default shipping country. 
                If the default shipping method is not available, it selects the cheapest shipping option.
            */

            $country_from = get_setting('aliship_shipfrom', 'CN');
            $country_to = get_setting('aliship_shipto');

            foreach ($result['products'] as $key => $product) {
                $product = $this->calculateProductPricesFromVariants($product);
                $result['products'][$key] = Utils::update_product_shipping($product, $country_from, $country_to, 'import', true);
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
            } catch (\Throwable $e) {
                a2w_print_throwable($e);
                foreach ($result['products'] as &$product) {
                    $product['affiliate_url'] = ''; //set empty to disable update!
                }
            } catch (\Exception $e) {
                a2w_print_throwable($e);
                foreach ($result['products'] as &$product) {
                    $product['affiliate_url'] = ''; //set empty to disable update!
                }
            }

        }

        //we don't want to update description by default
        foreach ($result["products"] as &$product) {

            if (isset($product['description'])){
                $product['source_description'] = $product['description'];
                $product['description'] = '';
            }
        }

        if (isset($params['manual_update']) && $params['manual_update'] && a2w_check_defined('A2W_FIX_RELOAD_DESCRIPTION') && !get_setting('not_import_description')) {

            foreach ($result["products"] as &$product) {
                if (isset($product['description'])){
                    $source_description = $product['source_description'];
                    $product['description'] = $this->clean_description($source_description);
                    $product['description'] = PhraseFilter::apply_filter_to_text($product['description']);
                }
            }
        }

        /*
        $request_url = RequestHelper::build_request('sync_products', $request_params);

        if (empty($params['data'])) {
            $request = a2w_remote_get($request_url);
        } else {
            $request = a2w_remote_post($request_url, $params['data']);
        }*/

        //add not available products to result
        array_push($result['products'], ...$notAvailableProducts);

        return $result;
    }

    public function load_shipping_info(
        $product_id, $quantity, $country_code, $country_code_from = 'CN', $min_price = '',
        $max_price = '', $province = '', $city = '', $extra_data = '', $sku_id = ''
    ) {

        return $this->connector->load_shipping_info(
            $product_id,
            $quantity,
            $country_code,
            $country_code_from,
            $min_price,
            $max_price,
            $province,
            $city,
            $extra_data,
            $sku_id
        );
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

        if ($html && class_exists('\DOMDocument')) {
            $dom = new \DOMDocument();
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
                    $e->setAttribute('src', Utils::clear_image_url($e->getAttribute('src')));
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

    private function get_affiliate_urls($urls): array
    {
        if ($this->account->account_type == 'admitad') {
            return AdmitadAccount::getInstance()->getDeeplink($urls);
        } else if ($this->account->account_type == 'epn') {
            return EpnAccount::getInstance()->getDeeplink($urls);
        } else {
            return AliexpressAccount::getInstance()->getDeeplink($urls);
        }
    }

    public function place_order($data)
    {
        /**
         * @var \WC_ORDER $WoocommerceOrder
         */
        $WoocommerceOrder = $data['order'];

        $Order = new Order($WoocommerceOrder);
        $userInfo = $Order->getUserInfo();

        if (!$userInfo['phone']) {
            return ResultBuilder::buildError(__('Phone number is required', 'ali2woo'));
        } else if (!$userInfo['street'] && !$userInfo['address2']) {
            return ResultBuilder::buildError(__('Street is required', 'ali2woo'));
        } else if (!$userInfo['name']) {
            return ResultBuilder::buildError(__('Contact name is required', 'ali2woo'));
        } else if (!$userInfo['country']) {
            return ResultBuilder::buildError(__('Country is required', 'ali2woo'));
        } else if (!$userInfo['city'] && !$userInfo['state']) {
            return ResultBuilder::buildError(__('City/State/Province is required', 'ali2woo'));
        } else if (!$userInfo['postcode']) {
            return ResultBuilder::buildError(__('Zip/Postal code is required', 'ali2woo'));
        } else if ($userInfo['country'] === 'BR' && !$userInfo['cpf']) {
            return ResultBuilder::buildError(__('CPF is mandatory in Brazil', 'ali2woo'));
        } else if ($userInfo['country'] === 'CL' && !$userInfo['rutNo']) {
            return ResultBuilder::buildError(__('RUT number is mandatory for Chilean customers', 'ali2woo'));
        }

        $order_note = get_setting('fulfillment_custom_note', '');

        $product_items = [];
        $processing_order_items = [];
        $errors = [];
        foreach ($data['order_items'] as $order_item) {
            if (get_class($order_item) !== 'WC_Order_Item_Product') {
                continue;
            }

            $order_item_id = $order_item->get_id();

            $quantity = $order_item->get_quantity() + $WoocommerceOrder->get_qty_refunded_for_item($order_item_id);

            if ($quantity == 0) {
                continue;
            }

            $a2w_order_item = new WooCommerceOrderItem($order_item);

            $product_id = $order_item->get_product_id();
            $variation_id = $order_item->get_variation_id();

            $aliexpress_product_id = get_post_meta($product_id, '_a2w_external_id', true);
            if (!$aliexpress_product_id) {
                $errors[] = [
                    'order_item_id' => $order_item_id,
                    'message' => __('AliExpress product not found', 'ali2woo')
                ];
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

            $shipping_company = get_setting('fulfillment_prefship', '');
            $shipping_meta = $order_item->get_meta(Shipping::get_order_item_shipping_meta_key());
            if ($shipping_meta) {
                $shipping_meta = json_decode($shipping_meta, true);
                $shipping_company = $shipping_meta['service_name'];
            }

            if (!$shipping_company) {
                $errors[] = [
                    'order_item_id' => $order_item_id,
                    'message' => __('Missing Shipping method', 'ali2woo')
                ];
                continue;
            }

            $product_items[] = [
                'product_count' => $quantity,
                'product_id' => $aliexpress_product_id,
                'sku_attr' => $sku_attr,
                'logistics_service_name' => $shipping_company,
                'order_memo' => $order_note,
            ];

            $processing_order_items[] = $a2w_order_item;
        }

        if (!empty($errors)) {
            return ResultBuilder::buildError(
                __('Product error', 'ali2woo'),
                ['error_code' => 'product_error', 'errors' => $errors]
            );
        }

        $logisticsAddress = $this->buildLogisticsAddressFromUserInfo($userInfo);

        try {
            $apiResult = $this->connector->place_order($product_items, $logisticsAddress);

            if ($apiResult['state'] !== 'ok') {
                return $apiResult;
            }

            $aliexpressOrders = $apiResult['orders']['list'];

            foreach ($aliexpressOrders as $aliexpressOrder) {
                foreach ($aliexpressOrder['child_order_list']['ae_child_order_info'] as $ae_product_info) {
                    foreach ($processing_order_items as $order_item) {
                        if ($order_item->get_external_product_id() == $ae_product_info['product_id']) {
                            $order_item->update_external_order($aliexpressOrder['order_id'], true);
                        }
                    }
                }
            }

            $result = ResultBuilder::buildOk();

            $placed_order_status = get_setting('placed_order_status');
            if ($placed_order_status) {
                $WoocommerceOrder->update_status($placed_order_status);
            }

                //$this->log_aliexpress_error($aliexpress_result, $payload);


        } catch (\Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        return $result;
    }

    public function load_order(string $order_id): array
    {
        try {
            $result = $this->connector->load_order($order_id);

            if($result['state'] !== 'ok') {
                return $result;
            }

            $tracking_codes = [];
            $courier_name = "";
            $tracking_status = $result['order']['order_status'];
            if (isset($result['order']['logistics_info_list']['ae_order_logistics_info'])) {
                foreach ($result['order']['logistics_info_list']['ae_order_logistics_info'] as $item) {
                    $tracking_codes[] = $item['logistics_no'];
                    $courier_name = $item['logistics_service'];
                }
            }

            $result['order'] = [
                'tracking_codes' => $tracking_codes,
                'courier_name' => $courier_name,
                'tracking_status' => $tracking_status
            ];

        } catch (\Throwable $e) {
            a2w_print_throwable($e);
            $result = ResultBuilder::buildError($e->getMessage());
        }

        return $result;
    }

    private function buildLogisticsAddressFromUserInfo(array $userInfo): array
    {
        $logisticsAddress = [
            'address' => $userInfo['street'],
            'city' => remove_accents($userInfo['city']),
            'contact_person' => $userInfo['name'],
            'country' => $userInfo['country'],
            'full_name' => $userInfo['name'],
            'mobile_no' => $userInfo['phone'],
            'phone_country' => $userInfo['phoneCountry'],
            'province' => remove_accents($userInfo['state']),
            // 'locale' => 'en_US',
            //'location_tree_address_id'=> '',
        ];

        if (!empty($userInfo['cpf'])) {
            $logisticsAddress['cpf'] = $userInfo['cpf'];
        }

        if (!empty($userInfo['rutNo'])) {
            $logisticsAddress['rut_no'] = $userInfo['rutNo'];
        }
        if ($userInfo['postcode']) {
            $logisticsAddress['zip'] = $userInfo['postcode'];
        }
        if ($userInfo['address2']) {
            if ($logisticsAddress['address']) {
                $logisticsAddress['address2'] = remove_accents($userInfo['address2']);
            } else {
                $logisticsAddress['address'] = remove_accents($userInfo['address2']);
            }
        }

        if ($userInfo['street_number']){
            $logisticsAddress['address'] = $logisticsAddress['address'] . ', ' . remove_accents($userInfo['street_number']);
        }

        if ($userInfo['shipping_neighborhood']){
            if ($logisticsAddress['address2']) {
                $logisticsAddress['address2'] = $logisticsAddress['address2'] . ', ' . remove_accents($userInfo['shipping_neighborhood']);
            } else {
                $logisticsAddress['address'] = $logisticsAddress['address'] . ', ' . remove_accents($userInfo['shipping_neighborhood']);
            }
        }

        if (!empty($userInfo['passport_no'])) {
            $logisticsAddress['passport_no'] = $userInfo['passport_no'];
        }
        if (!empty($userInfo['passport_no_date'])) {
            $logisticsAddress['passport_no_date'] = $userInfo['passport_no_date'];
        }
        if (!empty($userInfo['passport_organization'])) {
            $logisticsAddress['passport_organization'] = $userInfo['passport_organization'];
        }
        if (!empty($userInfo['tax_number'])) {
            $logisticsAddress['tax_number'] = $userInfo['tax_number'];
        }
        if (!empty($userInfo['foreigner_passport_no'])) {
            $logisticsAddress['foreigner_passport_no'] = $userInfo['foreigner_passport_no'];
        }
        if (!empty($userInfo['is_foreigner']) && $userInfo['is_foreigner'] === 'yes') {
            $logisticsAddress['is_foreigner'] = 'true';
        }
        if (!empty($userInfo['vat_no'])) {
            $logisticsAddress['vat_no'] = $userInfo['vat_no'];
        }
        if (!empty($userInfo['tax_company'])) {
            $logisticsAddress['tax_company'] = $userInfo['tax_company'];
        }

        $logisticsAddress = apply_filters('a2w_orders_logistics_address', $logisticsAddress, $userInfo);

        //todo: add woocommerce order id to this log to identify it later
        return $this->fix_shipping_address($logisticsAddress);
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

        $request_url = RequestHelper::build_request('fix_shipping_address');
        $request = a2w_remote_post($request_url, $json, $args);

        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else {
            if (intval($request['response']['code']) == 200) {
                $result = json_decode($request['body'], true);
            } else {
                $result = ResultBuilder::buildError($request['response']['code'] . ' - ' . $request['response']['message']);
            }
        }

        if ($result['state'] !== 'error' && isset($result['shipping_address'])) {
            $shipping_address = $result['shipping_address'];

            //clear accents characters after fix shipping address
            $shipping_address['province'] = remove_accents($shipping_address['province']);
            $shipping_address['city'] = remove_accents($shipping_address['city']);
            $shipping_address['address'] = remove_accents($shipping_address['address']);
        }

        return $shipping_address;
    }


}
