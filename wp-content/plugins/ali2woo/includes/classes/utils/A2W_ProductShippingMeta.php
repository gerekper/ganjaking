<?php
/**
 * Description of A2W_ProductShippingMeta
 *
 * @author MA_GROUP
 *
 */

if (!class_exists('A2W_ProductShippingMeta')) {

    class A2W_ProductShippingMeta
    {
        private $data = array();
        private $product_id;

        public function __construct($product_id)
        {
            $this->product_id = $product_id;
            $meta_data = get_post_meta($this->product_id, '_a2w_shipping_data', true);
            $this->data = $meta_data ? $meta_data : $this->data;
        }

        public static function meta_key($from_country, $to_country)
        {
            return A2W_ProductShippingMeta::normalize_country($from_country) . A2W_ProductShippingMeta::normalize_country($to_country);
        }

        public function get_items($quantity, $from_country, $to_country)
        {
            $meta_key = A2W_ProductShippingMeta::meta_key($from_country, $to_country);
            if (isset($this->data[$meta_key][$quantity])) {
                return $this->data[$meta_key][$quantity];
            }
            return false;
        }

        public function get_cost()
        {
            return isset($this->data['cost']) ? $this->data['cost'] : '';
        }

        public function get_country_to()
        {
            return isset($this->data['country_to']) ? $this->data['country_to'] : '';
        }

        public function get_country_from()
        {
            return isset($this->data['country_from']) ? $this->data['country_from'] : '';
        }

        public function get_method()
        {
            return isset($this->data['method']) ? $this->data['method'] : '';
        }

        // mutations
        public function save_data($data, $force_save = true)
        {
            $this->data = $data;
            if ($force_save) {
                $this->save();
            }
        }
        public function save_items($quantity, $from_country, $to_country, $items, $force_save = true)
        {
            $meta_key = $this->meta_key($from_country, $to_country);
            if (!isset($this->data[$meta_key])) {
                $this->data[$meta_key] = array();
            }

            $this->data[$meta_key][$quantity] = $items;

            if ($force_save) {
                $this->save();
            }

        }

        public function save_cost($cost, $force_save = true)
        {
            $this->data['cost'] = $cost;
            if ($force_save) {
                $this->save();
            }

        }

        public function save_country_to($country_to, $force_save = true)
        {
            $this->data['country_to'] = A2W_ProductShippingMeta::normalize_country($country_to);
            if ($force_save) {
                $this->save();
            }

        }

        public function save_country_from($country_from, $force_save = true)
        {
            $this->data['country_from'] = A2W_ProductShippingMeta::normalize_country($country_from);
            if ($force_save) {
                $this->save();
            }

        }

        public function save_method($method, $force_save = true)
        {
            $this->data['method'] = $method;
            if ($force_save) {
                $this->save();
            }

        }

        public function save()
        {
            update_post_meta($this->product_id, '_a2w_shipping_data', $this->data);
        }

        public static function clear_in_all_product()
        {
            global $wpdb;

            $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_a2w_shipping_data'");
        }

        public static function get_country_from_list($product_id)
        {
            global $wpdb;
            $query = "SELECT DISTINCT pm.meta_value FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON (pm.post_id=p.ID AND pm.meta_key='_a2w_country_code') WHERE p.post_parent=%d AND p.post_type='product_variation'";
            $country_from_list = $wpdb->get_col($wpdb->prepare($query, $product_id));

            if (empty($country_from_list)) {
                $query = "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm WHERE pm.post_id=%d AND pm.meta_key='_a2w_country_code'";
                $country_from_list = $wpdb->get_col($wpdb->prepare($query, $product_id));
            }
            return $country_from_list;
        }

        /**
         * Convert WooCommerce country code to AliExpress country code
         *
         * @param $country
         *
         * @return string
         */
        public static function normalize_country($country)
        {
            switch ($country) {
                case 'AQ':
                case 'BV':
                case 'IO':
                case 'CU':
                case 'TF':
                case 'HM':
                case 'IR':
                case 'IM':
                case 'SH':
                case 'PN':
                case 'SD':
                case 'SJ':
                case 'SY':
                case 'TK':
                case 'UM':
                case 'EH':
                    $country = 'OTHER';
                    break;
                case 'AX':
                    $country = 'ALA';
                    break;
                    // case 'CN':
                    //     $country = 'HK';
                    break;
                case 'CD':
                    $country = 'ZR';
                    break;
                case 'GG':
                    $country = 'GGY';
                    break;
                case 'JE':
                    $country = 'JEY';
                    break;
                case 'ME':
                    $country = 'MNE';
                    break;
                case 'KP':
                    $country = 'KR';
                    break;
                case 'BL':
                    $country = 'BLM';
                    break;
                case 'MF':
                    $country = 'MAF';
                    break;
                case 'RS':
                    $country = 'SRB';
                    break;
                case 'GS':
                    $country = 'SGS';
                    break;
                case 'TL':
                    $country = 'TLS';
                    break;
                case 'GB':
                    $country = 'UK';
                    break;
                default:
            }

            return $country;
        }
    }
}
