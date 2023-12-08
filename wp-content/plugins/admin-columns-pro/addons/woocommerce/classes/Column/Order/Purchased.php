<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP\Search\Searchable;
use ACP\Sorting\Sortable;
use WC_Order_Item_Product;

class Purchased extends AC\Column implements AC\Column\AjaxValue, Sortable, Searchable
{

    use OrderTitle;

    public function __construct()
    {
        $this->set_type('column-order_purchased')
             ->set_label(__('Products Count', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $count = $order ? $order->get_item_count() : 0;

        if ( ! $count) {
            return $this->get_empty_char();
        }

        $count = sprintf(_n('%d product', '%d products', $count, 'codepress-admin-columns'), $count);

        return ac_helper()->html->get_ajax_modal_link(
            $count,
            [
                'title'     => $this->get_order_title($order),
                'edit_link' => $order->get_edit_order_url(),
                'class'     => "-nopadding -w-large",
            ]
        );
    }

    public function get_ajax_value($id)
    {
        $order = wc_get_order($id);

        if ( ! $order) {
            return false;
        }

        $order_items = $order->get_items();

        if (count($order_items) < 1) {
            return false;
        }

        $view = new AC\View([
            'items' => $this->get_ordered_items($order_items),
        ]);

        return $view->set_template('modal-value/purchased')->render();
    }

    private function get_ordered_items(array $order_items): array
    {
        $items = [];

        $sku_enabled = wc_product_sku_enabled();

        foreach ($order_items as $item) {
            if ( ! $item instanceof WC_Order_Item_Product) {
                continue;
            }

            $sku = null;
            $name = $item->get_name();
            $qty = $item->get_quantity();
            $total = $item->get_total();
            $tax = $item->get_total_tax();

            $product = $item->get_product();

            if ($product && 'trash' !== $product->get_status()) {
                $link = get_edit_post_link($product->get_id());

                if ($link) {
                    $name = sprintf('<a href="%s">%s</a>', $link, $name);
                }

                if ($sku_enabled) {
                    $sku = $product->get_sku();
                }
            }

            $meta = [];

            foreach ($item->get_formatted_meta_data() as $meta_item) {
                $meta[] = sprintf('<strong>%s:</strong> %s', $meta_item->display_key, $meta_item->value);
            }

            $items[] = [
                'qty'   => $qty > 0 ? $qty : '-',
                'name'  => $name,
                'sku'   => $sku,
                'tax'   => $tax > 0 ? wc_price($tax) : '-',
                'total' => $total > 0 ? wc_price($total) : '-',
                'meta'  => implode('<br>', $meta),
            ];
        }

        return $items;
    }

    public function search()
    {
        return new Search\Order\Product();
    }

    public function sorting()
    {
        return new Sorting\Order\ItemsSold();
    }

}