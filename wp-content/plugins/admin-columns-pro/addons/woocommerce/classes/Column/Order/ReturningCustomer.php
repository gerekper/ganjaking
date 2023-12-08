<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class ReturningCustomer extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable,
                                                     ACP\ConditionalFormat\Formattable, ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_returning_customer')
             ->set_label(__('Returning Customer', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        return ac_helper()->icon->yes_or_no($this->get_raw_value($id));
    }

    public function get_raw_value($id)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT returning_customer FROM {$wpdb->prefix}wc_order_stats WHERE order_id = %d", $id);

        return $wpdb->get_var($sql);
    }

    public function search()
    {
        return new Search\Order\ReturningCustomer();
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this);
    }

    public function sorting()
    {
        return new Sorting\Order\Stats(
            'returning_customer',
            new ACP\Sorting\Type\DataType(ACP\Sorting\Type\DataType::NUMERIC)
        );
    }

}