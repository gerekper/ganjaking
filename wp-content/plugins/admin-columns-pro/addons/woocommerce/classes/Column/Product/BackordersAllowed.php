<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

class BackordersAllowed extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-wc-backorders_allowed')
             ->set_label(__('Backorders Allowed', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        switch ($this->get_backorders((int)$id)) {
            case 'no' :
                return ac_helper()->icon->no(__('No'));
            case 'yes' :
                return ac_helper()->icon->yes(__('Yes'));
            case 'notify' :
                $icon_email = ac_helper()->icon->dashicon(['icon' => 'email-alt']);

                return ac_helper()->html->tooltip(
                    ac_helper()->icon->yes() . $icon_email,
                    __('Yes, but notify customer', 'woocommerce')
                );
            default :
                return $this->get_empty_char();
        }
    }

    public function get_raw_value($id)
    {
        return $this->get_backorders((int)$id);
    }

    public function editing()
    {
        return new Editing\Product\BackordersAllowed();
    }

    public function sorting()
    {
        return new Sorting\Product\BackordersAllowed();
    }

    public function search()
    {
        return new Search\Product\BackordersAllowed();
    }

    public function get_backorders(int $id): string
    {
        return wc_get_product($id)->get_backorders();
    }

}