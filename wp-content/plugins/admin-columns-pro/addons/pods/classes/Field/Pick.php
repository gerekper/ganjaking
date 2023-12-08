<?php

namespace ACA\Pods\Field;

use ACA\Pods\Field;
use ACP\Export;
use PodsField_Pick;

class Pick extends Field
{

    public function get_value($id)
    {
        return pods_field_display($this->get_pod(), $id, $this->get_field_name());
    }

    public function get_options()
    {
        return [];
    }

    protected function get_pick_field()
    {
        return new PodsField_Pick();
    }

    protected function is_multiple()
    {
        return 'multi' === $this->get_option('pick_format_type');
    }

    public function export()
    {
        return new Export\Model\StrippedValue($this->column());
    }

    protected function get_ids_from_array($array, $id_name = 'ID')
    {
        $ids = [];

        if ( ! is_array($array)) {
            return false;
        }

        if (isset($array[0])) {
            $ids = wp_list_pluck($array, $id_name);
        }

        if (array_key_exists($id_name, $array)) {
            $ids = [$array[$id_name]];
        }

        return $ids;
    }

}