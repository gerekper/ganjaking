<?php

namespace ACA\WC\Export\UserSubscription;

use ACP;

class ActiveSubscriber implements ACP\Export\Service
{

    public function get_value($id)
    {
        return wcs_user_has_subscription((int)$id, '', 'active')
            ? '1'
            : '-';
    }

}