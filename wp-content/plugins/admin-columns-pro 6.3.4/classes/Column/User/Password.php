<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;

class Password extends AC\Column implements Editing\Editable
{

    public function __construct()
    {
        $this->set_type('column-user_password')
             ->set_label(__('Password', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        return $this->get_option('edit') === 'on'
            ? __('Set new password', 'codepress-admin-columns')
            : __('Enable Inline Edit to change password', 'codepress-admin-columns');
    }

    public function editing()
    {
        return new Editing\Service\User\Password();
    }

}