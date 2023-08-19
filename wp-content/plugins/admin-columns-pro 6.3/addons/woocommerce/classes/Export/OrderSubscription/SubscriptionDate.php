<?php

namespace ACA\WC\Export\OrderSubscription;

use ACP;
use LogicException;

class SubscriptionDate implements ACP\Export\Service
{

    private $date_type;

    public function __construct(string $date_type)
    {
        $this->date_type = $date_type;
        $this->validate();
    }

    private function validate()
    {
        if ( ! in_array(
            $this->date_type,
            ['start', 'date_created', 'trial_end', 'next_payment', 'last_order_date_created', 'end']
        )) {
            throw new LogicException(sprintf('Date type "%s" not supported', $this->date_type));
        }
    }

    public function get_value($id)
    {
        return wcs_get_subscription($id)->get_date($this->date_type) ?: '';
    }

}