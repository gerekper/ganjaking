<?php

namespace ACA\Types\Export\Field;

use ACA\Types\Export;
use ACA\Types\Field;

class Checkboxes extends Export\Field
{

    public function get_value($id)
    {
        $field = $this->column->get_field();

        if ( ! $field instanceof Field\Checkboxes) {
            return false;
        }

        $labels = $field->get_values_as_labels($id);

        return $labels ? implode(', ', array_filter($labels)) : '';
    }

}