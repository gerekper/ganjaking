<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;

class Currency extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_label('Currency')
             ->set_type('column-wc-order_currency')
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $value = $this->get_raw_value($id);

        return $value ?: $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        return get_post_meta($id, '_order_currency', true);
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this);
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta('_order_currency');
    }

    public function search()
    {
        return new Search\ShopOrder\Currency();
    }

}