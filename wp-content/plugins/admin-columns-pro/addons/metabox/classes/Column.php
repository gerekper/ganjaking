<?php

namespace ACA\MetaBox;

use AC;
use ACP;
use Metabox\CustomTable;

class Column extends AC\Column\Meta
    implements ACP\Export\Exportable, StorageAware
{

    /**
     * @var array
     */
    private $field_settings;

    public function __construct()
    {
        $this->set_group('metabox');
        $this->set_label('MetaBox');
    }

    public function get_value($id)
    {
        if ($this->is_clonable()) {
            return $this->get_multiple_values($id);
        }

        return $this->format_single_value(
            rwmb_get_value($this->get_meta_key(), ['object_type' => $this->get_meta_type()], $id),
            $id
        );
    }

    public function format_single_value($value, $id = null)
    {
        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $this->get_formatted_value($value, $id);
    }

    public function get_storage()
    {
        if ($this->get_field_setting('storage') instanceof CustomTable\Storage) {
            return self::CUSTOM_TABLE;
        }

        return self::META_BOX;
    }

    /**
     * @return string|false
     */
    public function get_storage_table()
    {
        return $this->get_field_setting('ac_storage_table');
    }

    public function get_clone_divider(): string
    {
        return '<div class="ac-mb-divider"></div>';
    }

    public function get_multiple_values($id)
    {
        $value = rwmb_get_value($this->get_meta_key(), ['object_type' => $this->get_meta_type()], $id);

        if ( ! $value) {
            return $this->get_empty_char();
        }

        $collection = new AC\Collection(
            (array)rwmb_get_value($this->get_meta_key(), ['object_type' => $this->get_meta_type()], $id)
        );
        $result = [];

        foreach ($collection as $value) {
            $result[] = $this->format_single_value($value, $id);
        }

        return implode($this->get_clone_divider(), $result);
    }

    public function get_field_setting($key)
    {
        $settings = $this->get_field_settings();

        if ( ! isset($settings[$key])) {
            return false;
        }

        return $settings[$key];
    }

    public function get_field_settings()
    {
        if ( ! empty($this->field_settings)) {
            return $this->field_settings;
        }

        $fields = rwmb_get_field_settings($this->get_meta_key(), ['object_type' => $this->get_meta_type()]);

        if ($fields) {
            $this->field_settings = $fields;
        } else {
            $this->field_settings = $this->get_meta_type_field_settings()[$this->get_type()];
        }

        return $this->field_settings;
    }

    /**
     * @return array
     */
    public function get_meta_type_field_settings()
    {
        switch ($this->get_meta_type()) {
            case 'user':
                return rwmb_get_object_fields('user', 'user');
            case 'post':
                return rwmb_get_object_fields($this->get_post_type());
            case 'term':
                return rwmb_get_object_fields($this->get_list_screen()->get_taxonomy(), 'term');
            default:
                return [];
        }
    }

    /**
     * @return bool
     */
    public function is_multiple()
    {
        $setting = $this->get_field_setting('multiple');

        return in_array($setting, [true, 'true', 1], true);
    }

    /**
     * @return bool
     */
    public function is_clonable()
    {
        $setting = $this->get_field_setting('clone');

        return in_array($setting, [true, 'true', 1], true);
    }

    public function get_meta_key()
    {
        return $this->get_type();
    }

    public function export()
    {
        return (new Export\Factory())->create($this);
    }

}