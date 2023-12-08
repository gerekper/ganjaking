<?php

namespace ACP\Editing\HideOnScreen;

use ACP;

class BulkDelete extends ACP\Settings\ListScreen\HideOnScreen
{

    public function __construct()
    {
        parent::__construct('hide_bulk_delete', __('Bulk Delete', 'codepress-admin-columns'));
    }

}