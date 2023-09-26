<?php

namespace ACP\Export\Model\User;

use ACP\Export\Service;

class FullName implements Service
{

    public function get_value($id)
    {
        return ac_helper()->user->get_display_name((int)$id, 'full_name') ?: '';
    }

}