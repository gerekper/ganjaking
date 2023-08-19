<?php

declare(strict_types=1);

namespace ACA\WC\Column\User;

use AC\Column;
use ACA\WC\ConditionalFormat;
use ACA\WC\Helper\User;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class TotalSales extends Column implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable,
                                           ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-user-total-sales')
             ->set_label(__('Total Sales', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($user_id)
    {
        $spent = (new User())->get_total_sales($user_id);

        return $spent
            ? wc_price($spent)
            : $this->get_empty_char();
    }

    public function get_raw_value($user_id)
    {
        return (new User())->get_total_sales($user_id);
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new ConditionalFormat\Formatter\User\TotalSalesFormatter());
    }

    public function search()
    {
        return new Search\User\TotalSales();
    }

    public function sorting()
    {
        return new Sorting\User\TotalSales();
    }

}