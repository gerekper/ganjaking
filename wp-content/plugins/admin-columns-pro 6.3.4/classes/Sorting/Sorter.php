<?php

namespace ACP\Sorting;

use ACP\Sorting\Type\DataType;

/**
 * Sorts an array ascending, maintains index association and returns keys
 */
class Sorter
{

    /**
     * @param array         $values [ (int) $id => (string|int|bool) $value ]
     * @param DataType|null $data_type
     *
     * @return int[]
     */
    public function sort(array $values, DataType $data_type = null): array
    {
        switch ((string)$data_type) {
            case DataType::NUMERIC :
                $values = array_filter($values, 'is_numeric');

                asort($values, SORT_NUMERIC);

                return array_keys($values);
            default :
                $values = array_filter($values, 'is_scalar');
                $values = array_filter($values, [$this, 'is_not_empty']);
                $values = array_map([$this, 'truncate'], $values);

                natcasesort($values);

                return array_keys($values);
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function truncate($value)
    {
        return $value && is_string($value)
            ? substr($value, 0, 100)
            : $value;
    }

    /**
     * Allow zero values as nonempty. Allows them to be sorted.
     *
     * @param string|int|bool $value
     *
     * @return bool
     */
    private function is_not_empty($value)
    {
        return $value || 0 === $value || '0' === $value;
    }

}