<?php

declare(strict_types=1);

function ACP(): ACP\AdminColumnsPro
{
    static $acp = null;

    if ($acp === null) {
        $acp = new ACP\AdminColumnsPro();
    }

    return $acp;
}

function acp_support_email(): string
{
    return 'support@admincolumns.com';
}

/**
 * @deprecated 6.0
 * @since      5.1
 */
function acp_sorting_show_all_results(): bool
{
    _deprecated_function(__FUNCTION__, '6.0');

    return true;
}