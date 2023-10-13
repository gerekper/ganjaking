<?php

/**
 * Description of A2W_AbstractConnector
 *
 * @author Ali2Woo Team
 */

if (!class_exists('A2W_AbstractConnector')) {

    abstract class A2W_AbstractConnector {
        private static $_instances = array();

        public static function getInstance()
        {
            $class = get_called_class();
            if (!isset(self::$_instances[$class])) {
                self::$_instances[$class] = new $class();
            }
            return self::$_instances[$class];
        }

        abstract public function load_product($product_id, $params = array());
        abstract public function load_products($filter, $page = 1, $per_page = 20, $params = array());
        abstract public function load_store_products($filter, $page = 1, $per_page = 20, $params = array());
        abstract public function load_reviews($product_id, $page, $page_size = 20, $params = array());
        abstract public function check_affiliate($product_id);
        abstract public function load_shipping_info($product_id, $quantity, $country_code, $country_code_from = 'CN', $min_price = '', $max_price = '', $province = '', $city = '');
        abstract public function place_order($data);
        abstract public function load_order($order_id);
        abstract static function get_images_from_description($product);
        protected function sort_variant_id_parts(array $parts) {
            if (count($parts) > 0){
                sort($parts);
            }
            return $parts;
        }
    }

}