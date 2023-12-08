<?php

namespace ACA\WC\Column\Order;

use ACP;

trait FilterableDateTrait
{

    public function get_filtering_date_setting(): ?string
    {
        return $this->get_filter_setting()->get_filter_format();
    }

    protected function get_filter_setting(): ACP\Filtering\Settings\Date
    {
        $setting = $this->get_setting('filter');

        if ( ! $setting instanceof ACP\Filtering\Settings\Date) {
            throw new \LogicException('Missing filter setting');
        }

        return $setting;
    }
}