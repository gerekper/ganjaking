<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP;

class Date extends AC\Column
    implements ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('order_date')
             ->set_original(true);
    }

    protected function register_settings()
    {
        $width = $this->get_setting('width');

        $width->set_default(120);
        $width->set_default('px', 'width_unit');
    }

    public function export()
    {
        return new ACP\Export\Model\Post\Date();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Post\Date\PostDate($this->get_post_type());
    }

}