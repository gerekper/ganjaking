<?php

namespace ACA\Pods;

use AC;
use ACA\Pods\ConditionalFormatting\FormattableConfigFactory;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class Field
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    /**
     * @var Column
     */
    protected $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function editing()
    {
        return false;
    }

    public function sorting()
    {
        return null;
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this->column);
    }

    public function search()
    {
        return (new Search\ComparisonFactory())->create($this, $this->column);
    }

    public function conditional_format(): ?FormattableConfig
    {
        return (new FormattableConfigFactory())->create($this);
    }

    /**
     * @return AC\Settings\Column[]
     */
    public function get_dependent_settings()
    {
        return [];
    }

    /**
     * @return Column
     */
    protected function column()
    {
        return $this->column;
    }

    public function get_value($id)
    {
        return $this->column->get_formatted_value($this->get_raw_value($id));
    }

    /**
     * @return string
     */
    public function get_pod()
    {
        return $this->get('pod');
    }

    /**
     * @return string
     */
    public function get_field_name()
    {
        return $this->get_meta_key();
    }

    public function get_raw_value($id)
    {
        return pods_field_raw($this->get_pod(), $id, $this->get_field_name(), true);
    }

    public function get_meta_type()
    {
        return $this->column->get_meta_type();
    }

    /**
     * Get the raw DB value
     *
     * @param int $id
     *
     * @return array|false
     */
    protected function get_db_value($id)
    {
        return (new Value\DbRaw($this->get_meta_key(), $this->get_meta_type()))->get_value($id);
    }

    public function get_separator()
    {
        return null;
    }

    /**
     * @param string $key
     *
     * @return mixed|false
     */
    public function get($key)
    {
        return $this->column->get_pod_field_option($key);
    }

    /**
     * @param string $key
     *
     * @return mixed|false
     */
    public function get_option($key)
    {
        $options = $this->get('options');

        return isset($options[$key]) ? $options[$key] : false;
    }

    protected function get_meta_key()
    {
        return $this->column->get_meta_key();
    }

}