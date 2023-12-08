<?php

namespace ACA\WC\Export\ShopSubscription;

use ACA\WC\Column;
use ACA\WC\Field;
use ACP;
use DateTime;

class SubscriptionDate implements ACP\Export\Service
{

    protected $column;

    public function __construct(Column\ShopSubscription\SubscriptionDate $column)
    {
        $this->column = $column;
    }

    public function get_value($id)
    {
        $field = $this->column->get_field();

        if ( ! $field instanceof Field) {
            return '';
        }

        $date = $field->get_date(wcs_get_subscription($id));

        if ( ! $date instanceof DateTime) {
            return '';
        }

        return $date->format('Y-m-d H:i');
    }

}