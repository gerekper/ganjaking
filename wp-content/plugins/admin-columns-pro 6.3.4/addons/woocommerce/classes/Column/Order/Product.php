<?php

namespace ACA\WC\Column\Order;

use AC;
use AC\Settings\Column\NumberOfItems;
use AC\Settings\Column\Separator;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACP;
use WC_Order;
use WC_Order_Item_Product;

class Product extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_product')
             ->set_label(__('Products', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        if ( ! $order) {
            return $this->get_empty_char();
        }

        $values = [];

        foreach ($this->get_products($order) as $product_id) {
            $values[] = $this->get_formatted_value($product_id, $product_id);
        }

        $values = array_filter($values);

        if (empty($values)) {
            return $this->get_empty_char();
        }

        return ac_helper()->html->more($values, $this->get_items_limit(), $this->get_separator());
    }

    public function get_separator(): string
    {
        $setting = $this->get_setting('separator');

        return $setting instanceof Separator
            ? $setting->get_separator_formatted()
            : parent::get_separator();
    }

    private function get_items_limit(): int
    {
        $setting_limit = $this->get_setting(NumberOfItems::NAME);

        return $setting_limit instanceof NumberOfItems
            ? (int)$setting_limit->get_number_of_items()
            : 0;
    }

    private function get_products(WC_Order $order): array
    {
        $product_ids = [];

        foreach ($order->get_items() as $item) {
            if ($item instanceof WC_Order_Item_Product && $item->get_quantity() > 0) {
                $product_ids[] = $item->get_variation_id() ?: $item->get_product_id();
            }
        }

        return $product_ids;
    }

    public function register_settings(): void
    {
        $this->add_setting(new Settings\ShopOrder\Product($this))
             ->add_setting(new AC\Settings\Column\NumberOfItems($this))
             ->add_setting(new AC\Settings\Column\Separator($this));
    }

    public function search()
    {
        return new Search\Order\Product();
    }

}