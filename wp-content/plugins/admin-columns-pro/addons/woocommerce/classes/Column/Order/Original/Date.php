<?php

namespace ACA\WC\Column\Order\Original;

use AC;
use ACA\WC;
use ACP;

class Date extends AC\Column implements ACP\Search\Searchable, ACP\Filtering\FilterableDateSetting
{

    use WC\Column\Order\FilterableDateTrait;

    public function __construct()
    {
        $this->set_type('order_date')
             ->set_original(true);
    }

    public function register_settings()
    {
        $this->add_setting(new ACP\Filtering\Settings\Date($this));
    }

    public function search()
    {
        return new WC\Search\Order\Date\CreatedDate();
    }

}