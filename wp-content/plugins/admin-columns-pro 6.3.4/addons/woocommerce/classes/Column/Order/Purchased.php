<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Sorting\Order\ItemsSold;
use ACP\Sorting\Sortable;

class Purchased extends AC\Column implements AC\Column\AjaxValue, Sortable
{

    public function __construct()
    {
        $this->set_type('column-order_purchased')
             ->set_label(__('Products Sold', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);
        $count = $order ? $order->get_item_count() : 0;

        if ( ! $count) {
            return $this->get_empty_char();
        }

        $count = sprintf(_n('%d item', '%d items', $count, 'codepress-admin-columns'), $count);

        return ac_helper()->html->get_ajax_modal_link($count, [
            'title'     => get_the_title($id),
            'edit_link' => get_edit_post_link($id),
            'class'     => "-nopadding",
        ]);
    }

    public function get_ajax_value($id)
    {
        $order = wc_get_order($id);

        if ( ! $order) {
            return false;
        }

        $order_items = $order->get_items();

        if (count($order_items) <= 0) {
            return false;
        }

        $view = new AC\View([
            'items' => $order_items,
        ]);

        echo $view->set_template('modal-value/purchased')->render();
        exit;
    }

    public function sorting()
    {
        return new ItemsSold();
    }

}