<?php

namespace ACA\EC\ListScreen;

use ACA\EC\API;
use ACA\EC\Column;
use ACA\EC\Export\Strategy;
use ACP;

class Event extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('tribe_events');

        $this->group = 'events-calendar';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\Event\AllDayEvent::class,
            Column\Event\Categories::class,
            Column\Event\Costs::class,
            Column\Event\DisplayDate::class,
            Column\Event\Duration::class,
            Column\Event\EndDate::class,
            Column\Event\Featured::class,
            Column\Event\Field::class,
            Column\Event\HideFromUpcoming::class,
            Column\Event\Organizer::class,
            Column\Event\ParentEvent::class,
            Column\Event\Recurring::class,
            Column\Event\StartDate::class,
            Column\Event\Sticky::class,
            Column\Event\Venue::class,
            Column\Event\Website::class,
        ]);

        if (API::is_pro()) {
            $fields = API::get_additional_fields();

            foreach ($fields as $field) {
                $column = $this->get_column_by_field_type($field['type']);

                if ( ! $column) {
                    continue;
                }

                $column->set_label($field['label'])
                       ->set_type('column' . $field['name']);

                $this->register_column_type($column);
            }
        }
    }

    public function get_column_by_field_type(string $type): ?Column\Event\Field
    {
        $mapping = [
            'checkbox' => Column\Event\Field\Checkbox::class,
            'dropdown' => Column\Event\Field\Dropdown::class,
            'radio'    => Column\Event\Field\Radio::class,
            'text'     => Column\Event\Field\Text::class,
            'textarea' => Column\Event\Field\Textarea::class,
            'url'      => Column\Event\Field\Url::class,
        ];

        return array_key_exists($type, $mapping)
            ? new $mapping[$type]()
            : null;
    }

    public function export()
    {
        return new Strategy\Event($this);
    }

}