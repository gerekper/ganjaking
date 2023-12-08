<?php

namespace ACA\ACF\Search;

use ACP\Helper\Select;

trait UnserializedValuesTrait
{

    private function get_unserialized_values(array $meta_values, array $values = []): array
    {
        foreach ($meta_values as $value) {
            if (is_serialized($value)) {
                $values = $this->get_unserialized_values(unserialize($value, ['allowed_classes' => false]), $values);

                continue;
            }

            if (is_numeric($value)) {
                $values[] = (int)$value;
            }
        }

        return $values;
    }

}