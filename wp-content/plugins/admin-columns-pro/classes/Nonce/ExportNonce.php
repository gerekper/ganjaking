<?php

declare(strict_types=1);

namespace ACP\Nonce;

use AC\Form\Nonce;

class ExportNonce extends Nonce
{

    public function __construct()
    {
        parent::__construct('acp-export', 'acp_export_nonce');
    }
}