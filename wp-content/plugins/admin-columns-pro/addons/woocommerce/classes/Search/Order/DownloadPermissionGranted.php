<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;

class DownloadPermissionGranted extends OperationalDataField
{

    public function __construct()
    {
        parent::__construct(
            'download_permission_granted',
            new Operators([
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );
    }

}