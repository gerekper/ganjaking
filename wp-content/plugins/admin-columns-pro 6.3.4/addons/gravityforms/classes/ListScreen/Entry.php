<?php

declare(strict_types=1);

namespace ACA\GravityForms\ListScreen;

use AC;
use AC\ColumnRepository;
use AC\Type\Uri;
use ACA\GravityForms;
use ACA\GravityForms\Column;
use ACA\GravityForms\Column\EntryConfigurator;
use ACA\GravityForms\ListTable;
use ACA\GravityForms\MetaTypes;
use ACP\Editing;
use ACP\Export;
use GF_Entry_List_Table;
use GFAPI;

class Entry extends AC\ListScreen implements Editing\ListScreen, Export\ListScreen, AC\ListScreen\ManageValue,
                                             AC\ListScreen\ListTable
{

    private $form_id;

    private $column_configurator;

    public function __construct(int $form_id, EntryConfigurator $column_configurator)
    {
        parent::__construct('gf_entry_' . $form_id, '_page_gf_entries');

        $this->form_id = $form_id;
        $this->column_configurator = $column_configurator;

        $this->group = 'gravity_forms';
        $this->set_meta_type(MetaTypes::GRAVITY_FORMS_ENTRY);
    }

    public function list_table(): AC\ListTable
    {
        return new ListTable($this->get_list_table());
    }

    public function manage_value(): AC\Table\ManageValue
    {
        return new GravityForms\Table\ManageValue\Entry(new ColumnRepository($this));
    }

    public function editing()
    {
        return new GravityForms\Editing\Strategy\Entry($this->get_list_table());
    }

    public function export()
    {
        return new GravityForms\Export\Strategy\Entry($this);
    }

    public function get_heading_hookname(): string
    {
        return 'gform_entry_list_columns';
    }

    public function get_label(): ?string
    {
        return GFAPI::get_form($this->get_form_id())['title'];
    }

    public function get_form_id(): int
    {
        return $this->form_id;
    }

    public function get_table_url(): Uri
    {
        $url = new AC\Type\Url\ListTable('admin.php');

        return $url->with_arg('id', (string)$this->form_id)
                   ->with_arg('page', 'gf_entries');
    }

    public function get_list_table(): GF_Entry_List_Table
    {
        return (new GravityForms\TableFactory())->create($this->get_screen_id(), $this->form_id);
    }

    public function register_column_types(): void
    {
        $this->column_configurator->register_entry_columns($this);

        $this->register_column_types_from_list([
            Column\Entry\Custom\User::class,
            Column\Entry\Original\DateCreated::class,
            Column\Entry\Original\DatePayment::class,
            Column\Entry\Original\EntryId::class,
            Column\Entry\Original\PaymentAmount::class,
            Column\Entry\Original\SourceUrl::class,
            Column\Entry\Original\Starred::class,
            Column\Entry\Original\TransactionId::class,
            Column\Entry\Original\User::class,
            Column\Entry\Original\UserIp::class,
        ]);
    }

}