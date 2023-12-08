<?php

namespace ACA\BP\Column\User;

use AC;
use AC\Collection;
use ACA\BP\Search;
use ACA\BP\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class Groups extends AC\Column
    implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-buddypress_user_groups')
             ->set_label(__('Groups', 'buddypress'))
             ->set_group('buddypress');
    }

    public function get_value($id)
    {
        $values = $this->get_formatted_value(new Collection($this->get_raw_value($id)));
        
        return implode($this->get_separator(), $values->all());
    }

    public function get_raw_value($id)
    {
        $group_ids = groups_get_user_groups($id);

        return $group_ids['groups'];
    }

    protected function register_settings()
    {
        $this->add_setting(new Settings\Group($this));
    }

    public function is_valid()
    {
        return bp_is_active('groups');
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\User\Groups();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(
            new ACP\ConditionalFormat\Formatter\FilterHtmlFormatter(
                new ACP\ConditionalFormat\Formatter\IntegerFormatter()
            )
        );
    }

}