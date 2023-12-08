<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;

class PaymentMethod extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-payment_method')
             ->set_label(__('Payment Method', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $order = wc_get_order($id);

        return $order->get_payment_method_title() ?: $this->get_empty_char();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta('_payment_method_title');
    }

    public function search()
    {
        return new Search\ShopOrder\PaymentMethod();
    }

    public function editing()
    {
        return new Editing\ShopOrder\PaymentMethod();
    }

}