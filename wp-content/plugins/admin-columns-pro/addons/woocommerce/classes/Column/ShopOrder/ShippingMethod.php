<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC;
use ACA\WC\Settings\ShopOrder\ShippingMethodType;
use ACP;
use WC_Shipping_Method;

class ShippingMethod extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable,
               ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_group('woocommerce')
             ->set_type('column-wc-order_shipping_method')
             ->set_label(__('Shipping Method', 'woocommerce'));
    }

    public function get_value($id)
    {
        $value = $this->get_raw_value($id);
        $formatted_values = array_filter(array_map([$this, 'get_formatted_method'], $value));

        return empty($formatted_values)
            ? $this->get_empty_char()
            : implode(', ', $formatted_values);
    }

    private function get_method_property(): string
    {
        $setting = $this->get_setting(ShippingMethodType::NAME);

        if ($setting instanceof ShippingMethodType) {
            return $setting->get_shipping_method_type();
        }

        return 'method_title';
    }

    public function get_raw_value($order_id)
    {
        $order = wc_get_order($order_id);

        $value = array_map(function ($shipping_method) {
            return $shipping_method->get_data();
        }, $order->get_shipping_methods());

        return $value;
    }

    private function get_method_title($data)
    {
        return $data[ShippingMethodType::METHOD_TITLE] ?? null;
    }

    private function get_method_type_label($data)
    {
        /**
         * @var WC_Shipping_Method[] $shipping_methods
         */
        $shipping_methods = WC()->shipping()->load_shipping_methods();
        $method_id = $data[ShippingMethodType::METHOD_ID] ?? null;

        if (isset($shipping_methods[$method_id])) {
            return $shipping_methods[$method_id]->get_method_title();
        }

        return $method_id;
    }

    private function get_formatted_method($data)
    {
        switch ($this->get_method_property()) {
            case ShippingMethodType::METHOD_TITLE:
                return $this->get_method_title($data);
            default:
                return $this->get_method_type_label($data);
        }
    }

    public function sorting()
    {
        switch ($this->get_method_property()) {
            case ShippingMethodType::METHOD_ID:
                return new WC\Sorting\ShopOrder\ShippingMethod();
            case ShippingMethodType::METHOD_TITLE:
            default:
                return new WC\Sorting\ShopOrder\ShippingMethodLabel();
        }
    }

    public function search()
    {
        switch ($this->get_method_property()) {
            case ShippingMethodType::METHOD_TITLE:
                return new WC\Search\ShopOrder\ShippingMethodLabel();
            default:
                return new WC\Search\ShopOrder\ShippingMethod();
        }
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new ShippingMethodType($this));
    }

}