<?php

namespace ACA\ACF\Sorting\ModelFactory;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Sorting;
use ACP;

class Taxonomy implements Sorting\SortingModelFactory
{

    public function create(Field $field, string $meta_key, Column $column)
    {
        if ( ! $field instanceof Field\Type\Taxonomy) {
            return null;
        }

        if ($field->uses_native_term_relation()) {
            return null;
        }

        return (new ACP\Sorting\Model\MetaFormatFactory())->create(
            $column->get_meta_type(),
            $meta_key,
            new Sorting\FormatValue\Taxonomy(),
            null,
            [
                'taxonomy' => $column->get_taxonomy(),
                'post_type' => $column->get_post_type(),
            ]
        );
    }

}