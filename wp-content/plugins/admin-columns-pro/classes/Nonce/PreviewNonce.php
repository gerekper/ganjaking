<?php

declare(strict_types=1);

namespace ACP\Nonce;

use AC\Form\Nonce;

class PreviewNonce extends Nonce
{

    public function __construct()
    {
        parent::__construct('acp-preview-mode', 'acp_nonce');
    }
}