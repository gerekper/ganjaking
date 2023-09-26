<?php

namespace ACA\GravityForms\Column;

use AC;
use ACA\GravityForms\Column;
use ACA\GravityForms\FieldFactory;
use ACA\GravityForms\ListScreen;

final class EntryConfigurator implements AC\Registerable
{

    private $form_id;

    private $column_factory;

    private $field_factory;

    public function __construct($form_id, EntryFactory $column_factory, FieldFactory $field_factory)
    {
        $this->form_id = (int)$form_id;
        $this->column_factory = $column_factory;
        $this->field_factory = $field_factory;
    }

    public function register(): void
    {
        add_action('ac/list_screen/column_created', [$this, 'configure_column'], 10, 2);
    }

    public function configure_column(AC\Column $column, AC\ListScreen $list_screen): void
    {
        if ( ! $column instanceof Column\Entry) {
            return;
        }

        if ( ! $list_screen instanceof ListScreen\Entry) {
            return;
        }

        if ($list_screen->get_form_id() !== $this->form_id) {
            return;
        }

        $column->set_field(
            $this->field_factory->create($this->get_field_id_by_type($column->get_type()), $this->form_id)
        );
    }

    private function get_field_id_by_type($type)
    {
        return str_replace('field_id-', '', $type);
    }

    public function register_entry_columns(ListScreen\Entry $list_screen): void
    {
        foreach ((new AC\DefaultColumnsRepository())->get($list_screen->get_key()) as $type => $label) {
            $field_id = $this->get_field_id_by_type($type);
            $form_id = $list_screen->get_form_id();

            if ( ! $this->column_factory->has_field($field_id, $form_id)) {
                continue;
            }

            $column = $this->column_factory->create($field_id, $form_id);
            $column->set_type($type)
                   ->set_label($label)
                   ->set_list_screen($list_screen);

            $this->configure_column($column, $list_screen);

            $list_screen->register_column_type($column);
        }
    }

}