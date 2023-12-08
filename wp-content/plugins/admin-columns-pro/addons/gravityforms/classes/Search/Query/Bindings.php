<?php

namespace ACA\GravityForms\Search\Query;

use ACP\Query;
use GFFormsModel;

class Bindings extends Query\Bindings
{

    /**
     * @return string
     */
    public function get_entry_meta_table_name()
    {
        return GFFormsModel::get_entry_meta_table_name();
    }

    /**
     * @return string
     */
    public function get_entry_meta_table_name_alias()
    {
        return $this->get_unique_alias($this->get_entry_meta_table_name());
    }

    /**
     * @return string
     */
    public function get_entry_table()
    {
        return GFFormsModel::get_entry_table_name();
    }

    public function join_entry_meta_table($join_alias, $meta_key, $join_type = null)
    {
        global $wpdb;

        if ('LEFT' !== $join_type) {
            $join_type = 'INNER';
        }

        $this->join = sprintf(
            ' %s JOIN %s AS %3$s ON `%4$s`.form_id = %3$s.form_id AND `%4$s`.id = %3$s.entry_id AND %3$s.meta_key = %5$s',
            $join_type,
            $this->get_entry_meta_table_name(),
            $join_alias,
            't1',
            $wpdb->prepare("%s", $meta_key)
        );

        return $this;
    }

}