<?php

namespace ACA\GravityForms\ListScreenFactory;

use AC\ListScreen;
use AC\ListScreenFactory;
use ACA\GravityForms;
use ACA\GravityForms\Column\EntryConfigurator;
use ACA\GravityForms\FieldFactory;
use ACA\GravityForms\ListScreen\Entry;
use GFForms;
use GFFormsModel;
use WP_Screen;

class EntryFactory extends ListScreenFactory\BaseFactory
{

    public function can_create(string $key): bool
    {
        return null !== $this->get_form_id_from_list_key($key);
    }

    protected function create_list_screen(string $key): ListScreen
    {
        $form_id = $this->get_form_id_from_list_key($key);

        return new Entry($form_id, $this->create_entry_configurator($this->get_form_id_from_list_key($key)));
    }

    private function get_form_id_from_list_key(string $key): ?int
    {
        if ( ! ac_helper()->string->starts_with($key, 'gf_entry_')) {
            return null;
        }

        $entry_id = ac_helper()->string->remove_prefix($key, 'gf_entry_');

        return is_numeric($entry_id)
            ? (int)$entry_id
            : null;
    }

    public function can_create_from_wp_screen(WP_Screen $screen): bool
    {
        return strpos($screen->id, '_page_gf_entries') !== false &&
               strpos($screen->base, '_page_gf_entries') !== false &&
               $this->has_form_id();
    }

    private function has_form_id(): bool
    {
        return $this->get_current_form_id() > 0;
    }

    protected function create_list_screen_from_wp_screen(WP_Screen $screen): ListScreen
    {
        $form_id = $this->get_current_form_id();

        return new Entry($form_id, $this->create_entry_configurator($form_id));
    }

    private function get_current_form_id(): int
    {
        $form_id = GFForms::get('id');

        if ( ! $form_id) {
            $forms = GFFormsModel::get_forms();

            if ($forms) {
                $form_id = $forms[0]->id;
            }
        }

        return (int)$form_id;
    }

    private function create_entry_configurator(int $form_id): EntryConfigurator
    {
        $fieldFactory = new FieldFactory();
        $columnFactory = new GravityForms\Column\EntryFactory($fieldFactory);

        $entry_configurator = new EntryConfigurator($form_id, $columnFactory, $fieldFactory);
        $entry_configurator->register();

        return $entry_configurator;
    }

}