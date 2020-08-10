<?php

/**
 * WooChimp MailChimp API Wrapper Class
 * Partly based on official MailChimp API Wrapper for PHP
 *
 * @class WooChimp_Mailchimp
 * @package WooChimp
 * @author RightPress
 */

if (!class_exists('WooChimp_Mailchimp')) {

    class WooChimp_Mailchimp
    {
        /**
         * API Key
         */
        public $apikey;
        public $ch;
        public $root = 'https://api.mailchimp.com/3.0';

        /**
         * Constructor class
         *
         * @access public
         * @param string $apikey
         * @return void
         */
        public function __construct($apikey) {

            // Set up API Key
            if (!$apikey) {
                throw new Exception('You must provide a MailChimp API key');
            }

            $this->apikey = $apikey;

            // Set up host to connect to
            $dc = 'us1';

            if (strstr($this->apikey, '-')){
                list($key, $dc) = explode('-', $this->apikey, 2);

                if (!$dc) {
                    $dc = 'us1';
                }
            }

            $this->root = str_replace('https://api', 'https://' . $dc . '.api', $this->root);
            $this->root = rtrim($this->root, '/') . '/';

            // Initialize Curl
            $this->ch = curl_init();

            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/vnd.api+json',
                'Content-Type: application/vnd.api+json',
                'Authorization: apikey ' . $this->apikey
            ));
            curl_setopt($this->ch, CURLOPT_HEADER, false);
            curl_setopt($this->ch, CURLOPT_USERAGENT, 'MailChimp-API/3.0');

            curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($this->ch, CURLOPT_ENCODING, '');
            curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);

            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 600);
        }

        /**
         * Destructor class
         *
         * @access public
         * @return void
         */
        public function __destruct() {
            curl_close($this->ch);
        }

        /**
         * Make call to MailChimp
         *
         * @param type $http_verb
         * @param type $url
         * @param type $params
         */
        public function call($http_verb, $url, $params = array())
        {
            $request_url = $this->root . $url;
            $params_query = !empty($params) ? '?' . http_build_query($params) : '';
            $params_encoded = json_encode($params);

            $ch = $this->ch;
            curl_setopt($ch, CURLOPT_URL, $request_url);

            switch ($http_verb) {
                case 'post':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_encoded);
                    break;

                case 'get':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_URL, $request_url . $params_query);
                    curl_setopt($ch, CURLOPT_POST, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, false);
                    break;

                case 'delete':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;

                case 'patch':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_encoded);
                    break;

                case 'put':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_encoded);
                    break;
            }

            $start = microtime(true);

            $response_body = curl_exec($ch);
            $info = curl_getinfo($ch);
            $time = microtime(true) - $start;

            // Check for curl error
            $curl_error = curl_error($ch) ? ('API call to ' . $url . ' failed: ' . curl_error($ch)) : false;

            // Decode result
            $result = json_decode($response_body, true);

            // Remove unneeded '_links' arrays from response
            $result = $this->remove_links_in_response($result);

            // Check for errors
            $response_error = (floor($info['http_code'] / 100) >= 4) ? true : false;

            // Prepare the data
            $call_data = array(
              'message'    => '',
              'request_to' => $http_verb . ' ' . $url,
              'request'    => $params,
              'response'   => $result,
            );

            // Process the errors
            if ($curl_error !== false) {

                // cURL error message
                $call_data['message'] = $curl_error;
                throw new Exception(maybe_serialize($call_data));
            }
            else if ($response_error === true) {

                if (!isset($result['status'])) {

                    // Unknown error without a status
                    $call_data['message'] = __('We received an unexpected error', 'woochimp');
                    throw new Exception(maybe_serialize($call_data));
                }

                // Regular error
                $call_data['message'] = $result['title'] . '(' . $result['status'] . ')';
                throw new Exception(maybe_serialize($call_data));
            }

            return $result;
        }

        /**
         * Get account details (calling root)
         *
         * @access public
         * @param array $params
         * @return mixed
         */
        public function get_account_details($params = array())
        {
            return $this->call('get', '', $params);
        }

        /**
         * Get lists
         *
         * @access public
         * @param array $params
         * @return mixed
         */
        public function get_lists($params = array('count'  => 100))
        {
            return $this->call('get', 'lists', $params);
        }

        /**
         * Get list
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function get_list($list_id, $params = array())
        {
            return $this->call('get', 'lists/' . $list_id, $params);
        }

        /**
         * Get merge fields
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function get_merge_fields($list_id, $params = array('count'  => 100))
        {
            return $this->call('get', 'lists/' . $list_id . '/merge-fields', $params);
        }

        /**
         * Get interest categories
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function get_interest_categories($list_id, $params = array('count'  => 100))
        {
            return $this->call('get', 'lists/' . $list_id . '/interest-categories', $params);
        }

        /**
         * Get interests
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function get_interests($list_id, $category_id, $params = array('count'  => 100))
        {
            return $this->call('get', 'lists/' . $list_id . '/interest-categories/' . $category_id . '/interests', $params);
        }

        /**
         * Get member
         *
         * @access public
         * @param string $list_id
         * @param string $email
         * @param string $params
         * @return mixed
         */
        public function get_member($list_id, $email, $params = array())
        {
            $hash = self::member_hash($email);
            return $this->call('get', 'lists/' . $list_id . '/members/' . $hash, $params);
        }

        /**
         * Subscribe member
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function post_member($list_id, $params)
        {
            return $this->call('post', 'lists/' . $list_id . '/members', $params);
        }

        /**
         * Subscribe or update member
         *
         * @access public
         * @param string $list_id
         * @param array $params
         * @return mixed
         */
        public function put_member($list_id, $params)
        {
            $hash = self::member_hash($params['email_address']);
            return $this->call('put', 'lists/' . $list_id . '/members/' . $hash, $params);
        }

        /**
         * Delete member
         *
         * @access public
         * @param string $list_id
         * @param string $email
         * @return mixed
         */
        public function delete_member($list_id, $email)
        {
            $hash = self::member_hash($email);
            return $this->call('delete', 'lists/' . $list_id . '/members/' . $hash);
        }

        /**
         * Get stores
         *
         * @access public
         * @return mixed
         */
        public function get_stores($params = array('count'  => 100))
        {
            return $this->call('get', 'ecommerce/stores/', $params);
        }

        /**
         * Create store
         *
         * @access public
         * @param array $params
         * @return mixed
         */
        public function create_store($params)
        {
            return $this->call('post', 'ecommerce/stores/', $params);
        }

        /**
         * Get customers (not used now)
         *
         * @access public
         * @param string $store_id
         * @param array $params
         * @return mixed
         */
        public function get_customers($store_id, $params = array('count'  => 1000))
        {
            return $this->call('get', 'ecommerce/stores/' . $store_id . '/customers', $params);
        }

        /**
         * Get products (not used now)
         *
         * @access public
         * @param string $store_id
         * @param array $params
         * @return mixed
         */
        public function get_products($store_id, $params = array('count'  => 1000))
        {
            return $this->call('get', 'ecommerce/stores/' . $store_id . '/products', $params);
        }

        /**
         * Get product
         *
         * @access public
         * @param string $store_id
         * @param string $product_id
         * @param array $params
         * @return mixed
         */
        public function get_product($store_id, $product_id, $params = array())
        {
            return $this->call('get', 'ecommerce/stores/' . $store_id . '/products/' . $product_id, $params);
        }

        /**
         * Create product
         *
         * @access public
         * @param string $store_id
         * @param array $params
         * @return mixed
         */
        public function create_product($store_id, $params)
        {
            return $this->call('post', 'ecommerce/stores/' . $store_id . '/products/', $params);
        }

        /**
         * Create product variant
         *
         * @access public
         * @param string $store_id
         * @param string $product_id
         * @param array $params
         * @return mixed
         */
        public function create_variant($store_id, $product_id, $params)
        {
            return $this->call('post', 'ecommerce/stores/' . $store_id . '/products/' . $product_id . '/variants/', $params);
        }

        /**
         * Get order
         *
         * @access public
         * @param string $store_id
         * @param string $order_id
         * @param array $params
         * @return mixed
         */
        public function get_order($store_id, $order_id, $params = array())
        {
            return $this->call('get', 'ecommerce/stores/' . $store_id . '/orders/' . $order_id, $params);
        }

        /**
         * Create order
         *
         * @access public
         * @param string $store_id
         * @param array $params
         * @return mixed
         */
        public function create_order($store_id, $params)
        {
            return $this->call('post', 'ecommerce/stores/' . $store_id . '/orders/', $params);
        }

        /**
         * Update order
         *
         * @access public
         * @param string $store_id
         * @param string $order_id
         * @param array $params
         * @return mixed
         */
        public function update_order($store_id, $order_id, $params)
        {
            return $this->call('patch', 'ecommerce/stores/' . $store_id . '/orders/' . $order_id, $params);
        }

        /**
         * Delete order
         *
         * @access public
         * @param string $store_id
         * @param string $order_id
         * @return mixed
         */
        public function delete_order($store_id, $order_id)
        {
            return $this->call('delete', 'ecommerce/stores/' . $store_id . '/orders/' . $order_id);
        }

        /**
         * Get member_hash
         *
         * @access public
         * @param string $email
         * @return mixed
         */
        public static function member_hash($email = '') {
            return md5(strtolower($email));
        }

        /**
         * Remove all huge '_links' arrays from response
         *
         * @access public
         * @return void
         */
        private function remove_links_in_response($result)
        {
            if (is_array($result)) {

                // Sometimes links are in root
                if (isset($result['_links'])) {
                    unset($result['_links']);
                }

                // main_key is 'lists', 'merge_fields' or 'total_items'
                foreach ($result as $main_key => $main_array) {

                    // Check is it's array of items, not plain field (like 'total_items')
                    if (is_array($main_array)) {

                        // Could be here
                        if (isset($main_array['_links'])) {
                            unset($result[$main_key]['_links']);
                        }

                        // [0] => array of lists/interests/etc
                        foreach ($main_array as $item_key => $item_array) {

                            // Each item can have links array
                            if (is_array($item_array) && isset($item_array['_links'])) {
                                unset($result[$main_key][$item_key]['_links']);
                            }
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Get customer
         *
         * @access public
         * @param string $store_id
         * @param string $customer_id
         * @param array $params
         * @return mixed
         */
        public function get_customer($store_id, $customer_id, $params = array())
        {
            return $this->call('get', 'ecommerce/stores/' . $store_id . '/customers/' . $customer_id, $params);
        }

    }
}
