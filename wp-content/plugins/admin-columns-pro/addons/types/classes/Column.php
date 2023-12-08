<?php

namespace ACA\Types;

use AC;
use AC\Collection;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

abstract class Column extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Filtering\FilterableDateSetting
{

    /**
     * @return array
     */
    abstract public function get_fields();

    /**
     * @param int $id
     *
     * @return string
     */
    abstract public function get_render_value($id);

    public function __construct()
    {
        $this->set_type('column-types')
             ->set_label('Toolset Types')
             ->set_group('types');
    }

    public function get_meta_key()
    {
        return $this->get_type_field_option('meta_key');
    }

    public function get_value($id)
    {
        $value = $this->get_field()->get_value($id);

        if ($value instanceof Collection) {
            $value = $value->filter()->implode($this->get_separator());
        }

        if (ac_helper()->string->is_empty($value)) {
            return $this->get_empty_char();
        }

        return $value;
    }

    public function get_filtering_date_setting(): ?string
    {
        return $this->options['filter_format'] ?? null;
    }

    protected function get_type_name()
    {
        return 'wpcf-fields';
    }

    /**
     * @param string $property
     *
     * @return array|string|false
     */
    public function get_type_field_option($property)
    {
        $field = $this->get_type_field();

        return $field && isset($field[$property]) ? $field[$property] : false;
    }

    public function is_repeatable()
    {
        $data = $this->get_type_field_option('data');

        return isset($data['repetitive']) && '1' === $data['repetitive'];
    }

    public function editing()
    {
        return $this->get_field()->editing();
    }

    public function sorting()
    {
        return $this->get_field()->sorting();
    }

    public function export()
    {
        return $this->get_field()->export();
    }

    public function search()
    {
        return $this->get_field()->search();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return $this->get_field()->conditional_format();
    }

    public function is_serialized()
    {
        return $this->get_field()->is_serialized();
    }

    /**
     * Register settings
     */
    protected function register_settings()
    {
        $this->add_setting(new Settings\Field($this));
    }

    public function get_raw_value($id)
    {
        return $this->get_field()->get_raw_value($id);
    }

    /**
     * @return Field
     */
    public function get_field()
    {
        $field_type = $this->get_type_field_option('type');

        if ($this->is_repeatable()) {
            return (new FieldRepeatableFactory())->create($field_type, $this);
        }

        return (new FieldFactory())->create($field_type, $this);
    }

    /**
     * @return array|false
     */
    public function get_type_field()
    {
        $field = wpcf_admin_fields_get_field($this->get_type_field_id(), null, null, null, $this->get_type_name());

        if ( ! $field) {
            return false;
        }

        return $field;
    }

    public function get_type_field_id()
    {
        return (string)$this->get_setting('types_field')->get_value();
    }

}