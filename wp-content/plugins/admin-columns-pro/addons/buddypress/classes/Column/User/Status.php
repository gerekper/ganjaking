<?php

namespace ACA\BP\Column\User;

use AC;
use ACA\BP\Editing;
use ACA\BP\Search;
use ACP;

class Status extends AC\Column
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-buddypress_user_status')
             ->set_label(__('Status', 'buddypress'))
             ->set_group('buddypress');
    }

    public function get_value($id)
    {
        $user_status = $this->get_raw_value($id);

        return absint($user_status) === 1 ? __('Spammer', 'buddypress') : __('Active', 'buddypress');
    }

    public function get_raw_value($id)
    {
        return ac_helper()->user->get_user_field('user_status', $id);
    }

    public function search()
    {
        return new Search\User\Status();
    }

    public function editing()
    {
        return new Editing\Service\User\Status();
    }

}