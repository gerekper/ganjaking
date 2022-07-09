<?php

/**
 * Description of A2W_WooCommerceOrderItem
 *
 * @author Mikhail
 */
if (!class_exists('A2W_WooCommerceOrderItem')) {

    class A2W_WooCommerceOrderItem
    {
        private $orderItem;
        private $has_changes; //use it for Woocommerce CRUD

        public function __construct($order_item)
        {
            if (is_numeric($order_item)) {
                $this->orderItem = new WC_Order_Item_Product($order_item);
            } else {
                $this->orderItem = $order_item;
            }
            $this->has_changes = false;
        }

        public function get_id()
        {
            if (get_class($this->orderItem) == 'WC_Order_Item_Product') {
                return $this->orderItem->get_id();
            } else {
                return $this->orderItem['id'];
            }
        }

        public function get_order_id()
        {
            $order = $this->get_order();
            return $order->get_id();
        }

        public function get_name()
        {
            if (get_class($this->orderItem) == 'WC_Order_Item_Product') {
                return $this->orderItem->get_name();
            } else {
                return $this->orderItem['name'];
            }
        }

        public function get_product_id()
        {
            if (get_class($this->orderItem) == 'WC_Order_Item_Product') {
                return $this->orderItem->get_product_id();
            } else {
                return $this->orderItem['product_id'];
            }
        }

        public function get_variation_id()
        {
            if (get_class($this->orderItem) == 'WC_Order_Item_Product') {
                return $this->orderItem->get_variation_id();
            } else {
                return $this->orderItem['variation_id'];
            }
        }

        public function get_quantity()
        {
            if (get_class($this->orderItem) == 'WC_Order_Item_Product') {
                return $this->orderItem->get_quantity();
            } else {
                return $this->orderItem['qty'];
            }
        }

        public static function check_is_delivered($status)
        {
            return in_array(strtolower(trim($status)), array('delivery successful', 'delivered', 'buyer_accept_goods'), true);
        }

        public function is_delivered()
        {
            $tracking_status = $this->get_tracking_status();
            if ($tracking_status) {
                return self::check_is_delivered($tracking_status);
            }
            return false;
        }

        public function is_shipped()
        {

            $tracking_status = $this->get_tracking_status();

            if ($tracking_status) {
                if (in_array(strtolower(trim($tracking_status)), array(
                    'seller_shipped',
                    'seller_send_goods'
                ), true)) {

                    return true;
                }
            }

            return false;
        }

        public function get_external_product_id()
        {
            $product_id = $this->get_product_id();
            return get_post_meta($product_id, A2W_Constants::product_external_meta(), true);
        }

        public function get_external_order_id()
        {
            $external_order_id = $this->orderItem->get_meta(A2W_Constants::order_item_external_order_meta());

            $external_order_id = is_array($external_order_id) ? $external_order_id[0] : '';

            return $external_order_id;
        }

        private function get_tracking_data()
        {
            $tracking_data = $this->orderItem->get_meta(A2W_Constants::order_item_tracking_data_meta());

            if (!$tracking_data) {
                $tracking_data = array();
                $tracking_data = array("tracking_codes" => array(), "carrier_name" => '', "carrier_url" => '', "tracking_status" => '');
            }

            if (!isset($tracking_data['tracking_codes'])) {
                $tracking_data['tracking_codes'] = array();
            }

            if (!isset($tracking_data['carrier_name'])) {
                $tracking_data['carrier_name'] = '';
            }

            if (!isset($tracking_data['carrier_url'])) {
                $tracking_data['carrier_url'] = '';
            }

            if (!isset($tracking_data['tracking_status'])) {
                $tracking_data['tracking_status'] = '';
            }

            return $tracking_data;
        }

        public function get_tracking_codes()
        {

            $tracking_data = $this->get_tracking_data();

            return ($tracking_data && isset($tracking_data['tracking_codes'])) ? $tracking_data['tracking_codes'] : array();

            return array();
        }

        public function get_tracking_status()
        {
            $tracking_data = $this->get_tracking_data();
            $tracking_status = $tracking_data['tracking_status'];
            return $tracking_status;
        }

        public function get_carrier_name()
        {
            $tracking_data = $this->get_tracking_data();
            if ($tracking_data && isset($tracking_data['tracking_codes'])) {
                return $tracking_data['carrier_name'];
            }
            return "";
        }

        public function get_ali_order_item_link()
        {
            $external_order_id = $this->get_external_order_id();

            if ($external_order_id) {
                $url = "https://trade.aliexpress.com/order_detail.htm?orderId={$external_order_id}";
                return "<a target='_blank' href='{$url}'>" . $external_order_id . "</a>";
            } else {
                return false;
            }

        }

        public function get_formated_carrier_link()
        {
            $tracking_data = $this->get_tracking_data();

            if ($tracking_data && isset($tracking_data['tracking_codes'])) {

                $tracking_url = $tracking_data['carrier_url'];
                $carrier_name = $tracking_data['carrier_name'];

                if ($tracking_url && $carrier_name) {
                    return "<a target='_blank' href='{$tracking_url}'>" . $carrier_name . "</a>";
                } else if ($carrier_name) {
                    return $carrier_name;
                }

            }

            return "";
        }

        public function get_formated_tracking_codes($plain = false)
        {

            $tracking_numbers = $this->get_tracking_codes();

            $tracking_numbers_formated = array();

            if (!$plain) {

                $tracking_url_template = "https://global.cainiao.com/detail.htm?mailNoList={tracking_number}";

                $tracking_url_template = apply_filters('a2w_get_tracking_url_template', $tracking_url_template);

                foreach ($tracking_numbers as $tracking_number) {
                    $tracking_url = str_replace('{tracking_number}', $tracking_number, $tracking_url_template);
                    $link_title = __('Click to see the tracking information', 'ali2woo');
                    $tracking_numbers_formated[] = "<a target='_blank' title='{$link_title}' href='{$tracking_url}'>" . $tracking_number . "</a>";
                }

            } else {
                $tracking_numbers_formated = $tracking_numbers;
            }

            return !empty($tracking_numbers_formated) ? implode(",", $tracking_numbers_formated) : "";

        }

        public function get_ali_shipping_code()
        {
            $shipping_code = '';

            $shipping_meta = $this->orderItem->get_meta(A2W_Shipping::get_order_item_shipping_meta_key());

            $legacy_shipping_meta = $this->orderItem->get_meta(A2W_Shipping::get_order_item_legacy_shipping_meta_key());

            if ($shipping_meta) {
                $shipping_info = json_decode($shipping_meta, true);
                $shipping_code = $shipping_info['service_name'];
            } else

            if ($legacy_shipping_meta) {
                $shipping_code = $legacy_shipping_meta;
            }

            return $shipping_code;
        }

        public function update_tracking_data($tracking_codes, $carrier_name, $carrier_url, $tracking_status, $force_save = false)
        {

            foreach ($tracking_codes as $tracking_number) {
                $order_id = $this->orderItem->get_order_id();
                //    $order_item_id =  $this->orderItem->get_id();
                do_action('wcae_after_add_tracking_code', $order_id, $tracking_number);
            }

            $this->orderItem->update_meta_data(A2W_Constants::order_item_tracking_data_meta(), array("tracking_codes" => $tracking_codes, "carrier_name" => $carrier_name, "carrier_url" => $carrier_url, "tracking_status" => $tracking_status));

            $this->has_changes = true;
            if ($force_save) {
                $this->save();
            }
        }

        public function update_tracking_codes($tracking_codes, $force_save = false)
        {

            foreach ($tracking_codes as $tracking_number) {
                $order_id = $this->orderItem->get_order_id();
                //    $order_item_id =  $this->orderItem->get_id();
                do_action('wcae_after_add_tracking_code', $order_id, $tracking_number);
            }

            $tracking_data = $this->get_tracking_data();

            $tracking_data["tracking_codes"] = $tracking_codes;

            $this->orderItem->update_meta_data(A2W_Constants::order_item_tracking_data_meta(), $tracking_data);

            $this->has_changes = true;
            if ($force_save) {
                $this->save();
            }
        }

        public function update_external_order($external_order_id, $force_save = false)
        {

            $this->orderItem->update_meta_data(A2W_Constants::order_item_external_order_meta(), $external_order_id ? array($external_order_id) : "");

            $this->has_changes = true;
            if ($force_save) {
                $this->save();
            }
        }

        public function save()
        {
            if ($this->has_changes && get_class($this->orderItem) == 'WC_Order_Item_Product') {
                $this->orderItem->save();
                return true;
            }

            return false;
        }

    }
}
