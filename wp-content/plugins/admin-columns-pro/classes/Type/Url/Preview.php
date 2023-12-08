<?php

declare(strict_types=1);

namespace ACP\Type\Url;

use AC\Type\Uri;
use ACP\Nonce\PreviewNonce;

class Preview extends Uri
{

    public function __construct(Uri $url)
    {
        parent::__construct((string)$url);

        $nonce = new PreviewNonce();

        $this->add_arg($nonce->get_name(), $nonce->create());
        $this->add_arg('ac_action', 'acp-preview-mode');
        $this->add_arg('preview_method', 'activate');
    }

}