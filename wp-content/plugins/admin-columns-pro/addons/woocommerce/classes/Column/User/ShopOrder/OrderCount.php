<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Helper;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class OrderCount extends AC\Column implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Export\Exportable,
                                              ACP\ConditionalFormat\Formattable
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

        $link = add_query_arg([
            'post_type'      => 'shop_order',
            '_customer_user' => $user_id,
        ], admin_url('edit.php'));

        return $count
            ? sprintf('<a href="%s">%s</a>', $link, $count)
            : $this->get_empty_char();
    }

    public function get_raw_value($user_id)
    {
        $order_ids = (new Helper\User())->get_shop_order_ids_by_user((int)$user_id, $this->get_order_status());

        return count($order_ids);
    }

    public function sorting()
    {
        return new Sorting\User\ShopOrder\OrderCount($this->get_order_status());
    }

    public function search()
    {
        return new Search\User\ShopOrder\OrderCount($this->get_order_status() ? $this->get_order_status() : []);
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

    public function register_settings(): void
    {
        $this->add_setting(new Settings\OrderStatuses($this));
    }

}