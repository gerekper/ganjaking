<?php

/**
 * Description of AbstractConnector
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

abstract class AbstractConnector {
    private static $_instances = [];

    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }

    abstract public function load_product($product_id, $params = []);
    abstract public function load_products($filter, $page = 1, $per_page = 20, $params = []);
    abstract public function load_store_products($filter, $page = 1, $per_page = 20, $params = []);
    abstract public function load_reviews($product_id, $page, $page_size = 20, $params = []);
    abstract public function check_affiliate($product_id);
    abstract public function load_shipping_info($product_id, $quantity, $country_code, $country_code_from = 'CN', $min_price = '', $max_price = '', $province = '', $city = '');
    abstract public function place_order(array $productItems, array $logisticsAddress): array;
    abstract public function load_order(string $order_id): array;

    abstract static function get_images_from_description($data);
}
