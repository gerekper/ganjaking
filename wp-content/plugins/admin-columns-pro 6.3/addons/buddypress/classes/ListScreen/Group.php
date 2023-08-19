<?php

declare(strict_types=1);

namespace ACA\BP\ListScreen;

use AC;
use AC\ColumnRepository;
use AC\Type\Uri;
use ACA\BP\Column;
use ACA\BP\Editing;
use ACA\BP\ListTable;
use ACA\BP\Table;
use ACP;
use BP_Groups_List_Table;

class Group extends AC\ListScreen
    implements ACP\Editing\ListScreen, AC\ListScreen\ManageValue, AC\ListScreen\ListTable
{

    public function __construct()
    {
        parent::__construct('bp-groups', 'toplevel_page_bp-groups');

        $this->label = __('Groups', 'codepress-admin-columns');
        $this->group = 'buddypress';
    }

    public function get_heading_hookname(): string
    {
        return 'bp_groups_list_table_get_columns';
    }

    public function list_table(): AC\ListTable
    {
        // Hook suffix is required when using the list screen, mainly in Ajax
        if ( ! isset($GLOBALS['hook_suffix'])) {
            $GLOBALS['hook_suffix'] = $this->get_screen_id();
        }

        return new ListTable\Group(new BP_Groups_List_Table());
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new Table\ManageValue\Group(new ColumnRepository($this));
    }

    public function get_table_url(): Uri
    {
        $url = new AC\Type\Url\ListTable(
            'admin.php',
            $this->has_id() ? $this->get_id() : null
        );

        return $url->with_arg('page', 'bp-groups');
    }

    protected function register_column_types(): void
    {
        $this->register_column_types_from_list([
            Column\Group\Avatar::class,
            Column\Group\Creator::class,
            Column\Group\Description::class,
            Column\Group\Id::class,
            Column\Group\Name::class,
            Column\Group\NameOnly::class,
            Column\Group\Status::class,
        ]);
    }

    public function get_table_attr_id(): string
    {
        return '#bp-groups-form';
    }

    public function editing()
    {
        return new Editing\Strategy\Group();
    }

}