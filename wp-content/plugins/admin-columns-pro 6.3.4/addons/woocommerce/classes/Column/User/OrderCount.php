<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACA\WC\Type\OrderTableUrl;
use ACP;

class OrderCount extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable,
                                              ACP\Sorting\Sortable, ACP\Search\Searchable
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-order_count')
             ->set_label(__('Number of Orders', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($user_id)
    {
        $count = $this->get_raw_value($user_id);

        $url = (new OrderTableUrl())->with_arg('_customer_user', $user_id);

        return $count
            ? sprintf('<a href="%s">%s</a>', $url, $count)
            : $this->get_empty_char();
    }

    public function get_raw_value($user_id)
    {
        $ids = wc_get_orders([
            'customer_id' => $user_id,
            'status'      => $this->get_order_status(),
            'return'      => 'ids',
        ]);

        return count($ids);
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this);
    }

    private function get_order_status(): array
    {
        $setting = $this->get_setting(Settings\OrderStatuses::NAME);

        return $setting instanceof Settings\OrderStatuses
            ? $setting->get_order_status()
            : ['any'];
    }

    public function sorting()
    {
        return new Sorting\User\OrderCount($this->get_order_status());
    }

    public function search()
    {
        return new Search\User\OrderCount();
    }

    public function register_settings(): void
    {
        $this->add_setting(new Settings\OrderStatuses($this));
    }

}