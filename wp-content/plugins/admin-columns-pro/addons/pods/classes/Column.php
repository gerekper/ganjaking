<?php

namespace ACA\Pods;

use AC;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

abstract class Column extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Filtering\FilterableDateSetting
{

    /**
     * @var array Pod settings
     */
    private $pod;

    /**
     * @return string
     */
    abstract protected function get_pod_name();

    public function __construct()
    {
        $this->set_type('column-pods');
        $this->set_label(__('Pods', 'codepress-admin-columns'));
        $this->set_group('pods');
    }

    public function get_filtering_date_setting(): ?string
    {
        return $this->options['filter_format'] ?? null;
    }

    public function get_meta_key()
    {
        return $this->get_setting('pods_field')->get_value();
    }

    public function get_value($id)
    {
        $value = $this->get_field()->get_value($id);

        if ($value instanceof AC\Collection) {
            $value = $value->filter()->implode($this->get_separator());
        }

        if (ac_helper()->string->is_empty($value)) {
            return $this->get_empty_char();
        }

        return $value;
    }

    public function get_raw_value($id)
    {
        return $this->get_field()->get_raw_value($id);
    }

    public function get_separator()
    {
        $separator = $this->get_field()->get_separator();

        if ($separator !== null) {
            return $separator;
        }

        return parent::get_separator();
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

    protected function register_settings()
    {
        $this->add_setting(new Setting\Field($this));
    }

    /**
     * Current Pod field settings
     * @return false|array Field settings
     */
    public function get_pod_field()
    {
        $fields = $this->get_pod_fields();

        return isset($fields[$this->get_meta_key()]) ? $fields[$this->get_meta_key()] : false;
    }

    /**
     * @param string $property
     *
     * @return mixed|false
     */
    public function get_pod_field_option($property)
    {
        $field = $this->get_pod_field();

        return isset($field[$property]) ? $field[$property] : false;
    }

    /**
     * @return string|false
     */
    public function get_field_type()
    {
        return $this->get_pod_field_option('type');
    }

    private function set_pod()
    {
        add_filter('pods_error_exception', '__return_true', 12); // otherwise pods_error() will throw an exit
        $pod = pods_api()->load_pod(['name' => $this->get_pod_name()]);

        remove_filter('pods_error_exception', '__return_true', 12);

        $this->pod = isset($pod['id']) ? $pod : false;
    }

    /**
     * @return array
     */
    public function get_pod()
    {
        if (null === $this->pod) {
            $this->set_pod();
        }

        return $this->pod;
    }

    /**
     * @return Field
     */
    public function get_field()
    {
        $pick_object = $this->get_pod_field_option('pick_object');

        return (new FieldFactory())->create($this->get_field_type(), $this, $pick_object ?: null);
    }

    /**
     * @return false|array Field settings
     */
    public function get_pod_fields()
    {
        $pod = $this->get_pod();

        return isset($pod['fields']) ? $pod['fields'] : false;
    }

}