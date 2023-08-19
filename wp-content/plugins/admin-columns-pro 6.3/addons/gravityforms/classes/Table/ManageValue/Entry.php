<?php

declare(strict_types=1);

namespace ACA\GravityForms\Table\ManageValue;

use AC\Table\ManageValue;

class Entry extends ManageValue
{

    public function register(): void
    {
        add_filter('gform_entries_field_value', [$this, 'render_value'], 10, 4);
    }

    public function render_value($original_value, $form_id, $field_id, $entry)
    {
        $value = $this->render_cell((string)$field_id, (int)$entry['id'], (string)$original_value);

        if ( ! $value) {
            $value = $this->render_cell('field_id-' . $field_id, (int)$entry['id'], (string)$original_value);
        }

        return $value ?: $original_value;
    }

}