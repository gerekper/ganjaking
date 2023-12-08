<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class DownloadPermissionGranted extends AC\Column implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable,
                                                             ACP\Sorting\Sortable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-order_download_permissions_granted')
             ->set_label(__('Download Permission Granted', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        return $this->get_raw_value($id)
            ? ac_helper()->icon->yes(true, __('Download Permission Granted', 'codepress-admin-columns'))
            : $this->get_empty_char();
    }

    public function get_raw_value($id)
    {
        $order = wc_get_order($id);

        return $order && $order->get_download_permissions_granted();
    }

    public function search()
    {
        return new Search\Order\DownloadPermissionGranted();
    }

    public function sorting()
    {
        return new Sorting\Order\OperationalData('download_permission_granted');
    }

}