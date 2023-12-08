<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat;
use ACA\WC\Helper;
use ACA\WC\Search;
use ACA\WC\Settings\OrderStatuses;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class TotalSales extends AC\Column implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable,
                                              ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-user-total-sales')
             ->set_label(__('Total Sales', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new ConditionalFormat\Formatter\User\TotalSalesFormatter());
    }

    private function get_order_statuses(): array
    {
        $setting = $this->get_setting(OrderStatuses::NAME);

        return $setting instanceof OrderStatuses
            ? $setting->get_value()
            : ['wc-completed', 'wc-processing'];
    }

    public function get_value($user_id)
    {
        $values = [];

        foreach ($this->get_raw_value($user_id) as $total) {
            if ($total) {
                $values[] = wc_price($total);
            }
        }

        if ( ! $values) {
            return $this->get_empty_char();
        }

        return implode(' | ', $values);
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new OrderStatuses($this, ['wc-completed', 'wc-processing']));
    }

    public function get_raw_value($user_id)
    {
        return (new Helper\User())->get_shop_order_totals_for_user((int)$user_id, $this->get_order_statuses());
    }

    public function sorting()
    {
        return new Sorting\User\ShopOrder\TotalSales($this->get_order_statuses());
    }

    public function search()
    {
        return new Search\User\ShopOrder\TotalSales($this->get_order_statuses());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}