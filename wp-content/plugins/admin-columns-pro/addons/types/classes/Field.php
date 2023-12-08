<?php

namespace ACA\Types;

use ACA\Types\ConditionalFormatting\FormattableConfigFactory;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class Field implements ACP\ConditionalFormat\Formattable
{

    protected $column;

    public function __construct(Column $column)
    {
        $this->set_column($column);
    }

    public function get_value($id)
    {
        $this->is_required();

        return $this->column->get_render_value($id);
    }

    public function get_raw_value($id)
    {
        $value = get_metadata($this->column->get_meta_type(), $id, $this->column->get_meta_key());

        if ( ! $this->column->is_repeatable()) {
            $value = isset($value[0]) ? $value[0] : '';
        }

        return $value;
    }

    public function get_meta_type()
    {
        return $this->column->get_meta_type();
    }

    public function get_dependent_settings()
    {
        return [];
    }

    public function sorting()
    {
        return null;
    }

    public function editing()
    {
        return false;
    }

    public function search()
    {
        return false;
    }

    public function export()
    {
        return new Export\Field($this->column);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return (new FormattableConfigFactory())->create($this);
    }

    public function is_serialized()
    {
        return false;
    }

    public function is_required()
    {
        $validate = $this->get('validate');

        return isset($validate['required']) && 1 === (int)$validate['required']['active'];
    }

    public function get($key)
    {
        $data = $this->column->get_type_field_option('data');

        return isset($data[$key]) ? $data[$key] : false;
    }

    public function set_column(Column $column)
    {
        $this->column = $column;
    }

    public function get_repeatable_value($id)
    {
        return ac_helper()->html->small_block(explode(', ', $this->column->get_render_value($id)));
    }

    public function get_meta_key()
    {
        return $this->column->get_meta_key();
    }

}