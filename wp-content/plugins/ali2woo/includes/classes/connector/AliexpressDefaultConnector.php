<?php

/**
 * Description of AliexpressDefaultConnector
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class AliexpressDefaultConnector extends AbstractConnector
{
    //todo: fix product description loading
    public function load_product($product_id, $params = [])
    {
        $params['product_id'] = $product_id;
        $request_url = RequestHelper::build_request('get_product', $params);
        $request = a2w_remote_get($request_url);

        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else if (intval($request['response']['code']) != 200) {
            $result = ResultBuilder::buildError(
                $request['response']['code'] . " " . $request['response']['message']
            );
        } else {
            $result = json_decode($request['body'], true);
        }

        return  $result;
    }

    public function load_products($filter, $page = 1, $per_page = 20, $params = [])
    {
        $request_url = RequestHelper::build_request(
            'get_products',
            array_merge(['page' => $page, 'per_page' => $per_page], $filter)
        );
        $request = a2w_remote_get($request_url);

        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else if (intval($request['response']['code']) != 200) {
            $result = ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
        } else {
            $result = json_decode($request['body'], true);
        }

        return $result;
    }

    public function load_store_products($filter, $page = 1, $per_page = 20, $params = [])
    {
        $request_url = RequestHelper::build_request(
            'get_store_products',
            array_merge(['page' => $page, 'per_page' => $per_page], $filter)
        );
        $request = a2w_remote_get($request_url);

        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else if (intval($request['response']['code']) != 200) {
            $result = ResultBuilder::buildError($request['response']['code'] . " " . $request['response']['message']);
        } else {
            $result = json_decode($request['body'], true);
        }

        return $result;
    }
    
    public function load_reviews($product_id, $page, $page_size = 20, $params = [])
    {
        $request_url = RequestHelper::build_request('get_reviews',
           [
               'lang' => AliexpressLocalizator::getInstance()->language,
               'product_id' => $product_id,
               'page' => $page,
               'page_size' => $page_size
           ]
        );
        $request = a2w_remote_get($request_url);

        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else {
            $result = json_decode($request['body'], true);
        }

        return $result;
    }
    
    public function check_affiliate($product_id)
    {
        $request_url = RequestHelper::build_request('check_affiliate', array('product_id' => $product_id));
        $request = a2w_remote_get($request_url);
        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else {
            $result = json_decode($request['body'], true);
        }
        return $result;
    }

    public function load_shipping_info(
        $product_id, $quantity, $country_code, $country_code_from = 'CN',
        $min_price = '', $max_price = '', $province = '', $city = '', $extra_data = '', $sku_id = ''
    ) {
        $country_code = ProductShippingMeta::normalize_country($country_code);
        $params = [
            'product_id' => $product_id,
            'sku_id' => $sku_id,
            'quantity' => $quantity,
            'country_code' => $country_code,
            'extra_data' => $extra_data,
        ];
        if (!empty($country_code_from)) {
            $params['country_code_from'] = ProductShippingMeta::normalize_country($country_code_from);
        }

        $request_url = RequestHelper::build_request('get_shipping_info',$params);
        $request = a2w_remote_get($request_url);
        if (is_wp_error($request)) {
            $result = ResultBuilder::buildError($request->get_error_message());
        } else {
            if (intval($request['response']['code']) == 200) {
                $result = json_decode($request['body'], true);
                if ($result['state'] != 'error') {
                    $result = ResultBuilder::buildOk([
                        'items' => $result['items'],
                        'from_cach' => $result['from_cach']
                    ]);
                } else {
                    $result = ResultBuilder::buildError($result['message']);
                }
            } else {
                $result = ResultBuilder::buildError(
                    $request['response']['code'] . ' - ' . $request['response']['message']
                );
            }
        }

        return $result;
    }

    public function place_order(array $productItems, array $logisticsAddress): array
    {
        try {
            $this->get_access_token();
        } catch (\Exception $Exception) {
            return ResultBuilder::buildError($Exception->getMessage());
        }

        $params = [
            'logistics_address' => $logisticsAddress,
            'product_items' => $productItems,
        ];

        $json = json_encode($params);

        $args = [
            'headers' => ['Content-Type' => 'application/json'],
        ];

        $request_url = RequestHelper::build_request('place_order');
        $request = a2w_remote_post($request_url, $json, $args);
        $result = $this->handleRequestResult($request);

        if ($result['state'] !== 'error') {
            $result = ResultBuilder::buildOk([
                'orders' => $result['orders'],
            ]);
        }

        return $result;
    }

    public function load_order(string $order_id): array
    {
        try {
            $this->get_access_token();
        } catch (\Exception $Exception) {
            return ResultBuilder::buildError($Exception->getMessage());
        }

        $params = [
            'order_id' => $order_id,
        ];

        $request_url = RequestHelper::build_request('load_order',$params);
        $request = a2w_remote_get($request_url);
        $result = $this->handleRequestResult($request);

        if ($result['state'] !== 'error') {
            $result = ResultBuilder::buildOk([
                'order' => $result['order'],
            ]);
        }

        return $result;
    }

    public static function get_images_from_description($product)
    {
        $src_result = array();

        if (isset($product['desc_meta']) && isset($product['desc_meta']['images']) && is_array($product['desc_meta']['images'])) {
            foreach ($product['desc_meta']['images'] as $image_src) {
                $image_key = md5($image_src);
                $src_result[$image_key] = $image_src;
            }
        }

        return $src_result;
    }

    /**
     * @throws \Exception
     */
    private function get_access_token()
    {
        Utils::clear_system_error_messages();

        $token = AliexpressToken::getInstance()->defaultToken();

        if (!$token) {
            $msg = sprintf(
                __(
                    'AliExpress access token is not found. <a target="_blank" href="%s">Please check our instruction</a>.',
                    'ali2woo'
                ),
            'https://help.ali2woo.com/codex/how-to-get-access-token-from-aliexpress/'
            );

            Utils::show_system_error_message($msg);

            //todo: add here a check whether token has expired 

            throw new \Exception($msg);
        }

        return $token['access_token'];
    }

    private function handleRequestResult($request): array
    {
        if (is_wp_error($request)) {
           return ResultBuilder::buildError($request->get_error_message());
        }

        if (intval($request['response']['code']) !== 200) {
            return ResultBuilder::buildError(
                $request['response']['code'] . ' - ' . $request['response']['message']
            );
        }

        $result = json_decode($request['body'], true);

        if ($result['state'] === 'error') {
            return ResultBuilder::buildError($result['message']);
        }

        return $result;
    }
}
