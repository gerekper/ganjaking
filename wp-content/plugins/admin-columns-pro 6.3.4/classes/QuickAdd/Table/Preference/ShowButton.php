<?php

namespace ACP\QuickAdd\Table\Preference;

use AC\Preferences\Site;

class ShowButton extends Site
{

    public function __construct($user_id = null)
    {
        parent::__construct('show_new_inline_button', $user_id);
    }

    public function is_active($key): bool
    {
        return in_array($this->get($key), [true, null], true);
    }

}