<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;

class StatusIcon extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-order_status_icon')
             ->set_label('Status Icon')
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        return wc_get_order($id)->get_status();
    }

    public function get_value($id)
    {
        $label = $this->get_status_label(
            $this->get_raw_value($id)
        );

        return sprintf(
            '<mark %s class="%s" style="display: none;">%s</mark>',
            ac_helper()->html->get_tooltip_attr($label),
            $this->get_raw_value($id),
            $label
        );
    }

    public function register_settings()
    {
        $width = $this->get_setting('width');

        $width->set_default(35);
        $width->set_default('px', 'width_unit');

        $label = $this->get_setting('label');
        if ( ! $label->get_value()) {
            $label->set_default('<span class="status_head">Status</span>');
        }
    }

    private function get_status_label($key)
    {
        $key = 'wc-' . $key;
        $statuses = wc_get_order_statuses();

        return $statuses[$key] ?? $key;
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\PostField('post_status');
    }

    public function editing()
    {
        return new Editing\ShopOrder\Status();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\Status($this->get_post_type());
    }

}