<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;
use ACP;

class Total extends AC\Column implements ACP\Search\Searchable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_type('order_total')
             ->set_original(true);
    }

    public function get_value($id)
    {
        return null;
    }

    protected function register_settings()
    {
        $width = $this->get_setting('width');

        $width->set_default(90);
        $width->set_default('px', 'width_unit');
    }

    public function export()
    {
        return new ACP\Export\Model\Post\Meta('_order_total');
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Decimal('_order_total');
    }

}