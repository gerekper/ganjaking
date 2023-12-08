<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class ShippingMethod extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable,
                                                  ACP\Sorting\Sortable, ACP\Search\Searchable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_shipping_method')
             ->set_label(__('Shipping Method', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        if ( ! $order) {
            return $this->get_empty_char();
        }

        $values = [];
        $display_type = $this->get_method_property();

        foreach ($order->get_shipping_methods() as $method) {
            $values[] = $display_type === Settings\ShopOrder\ShippingMethodType::METHOD_ID
                ? $method->get_method_id()
                : $method->get_method_title();
        }

        return empty($values)
            ? $this->get_empty_char()
            : implode($this->get_separator(), $values);
    }

    private function get_method_property()
    {
        $setting = $this->get_setting(Settings\ShopOrder\ShippingMethodType::NAME);

        return $setting instanceof Settings\ShopOrder\ShippingMethodType
            ? $setting->get_shipping_method_type()
            : Settings\ShopOrder\ShippingMethodType::METHOD_TITLE;
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Settings\ShopOrder\ShippingMethodType($this));
    }

    public function export()
    {
        return new ACP\Export\Model\Value($this);
    }

    public function sorting()
    {
        return Settings\ShopOrder\ShippingMethodType::METHOD_TITLE === $this->get_method_property()
            ? new Sorting\Order\ShippingMethodLabel()
            : new Sorting\Order\ShippingMethod();
    }

    public function search()
    {
        return Settings\ShopOrder\ShippingMethodType::METHOD_TITLE === $this->get_method_property()
            ? new Search\Order\ShippingMethodLabel()
            : new Search\Order\ShippingMethod();
    }

}