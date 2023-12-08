<?php

namespace ACA\BP\Column\User;

use AC;
use ACP;

class Friends extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-buddypress_user_friends')
             ->set_label(__('Friends', 'buddypress'))
             ->set_group('buddypress');
    }

    public function get_meta_key()
    {
        return 'total_friend_count';
    }

    public function is_valid()
    {
        return bp_is_active('friends');
    }

    public function get_raw_value($id)
    {
        return bp_get_total_friend_count($id);
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\User\Meta($this->get_meta_key());
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Number($this->get_meta_key());
    }

}