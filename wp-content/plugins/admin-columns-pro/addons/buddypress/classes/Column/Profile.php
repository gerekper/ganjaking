<?php

namespace ACA\BP\Column;

use AC;
use ACA\BP\Field;
use ACA\BP\Settings;
use ACP;
use BP_XProfile_Field;

class Profile extends AC\Column
    implements ACP\Editing\Editable, ACP\Filtering\FilterableDateSetting,
               ACP\Sorting\Sortable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-buddypress')
             ->set_label(__('Profile Fields', 'buddypress'))
             ->set_group('buddypress');
    }

    public function get_value($id)
    {
        $value = $this->get_field()->get_value($id);

        if ( ! $value) {
            $value = $this->get_empty_char();
        }

        return $value;
    }

    public function is_valid()
    {
        return bp_is_active('xprofile');
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

    protected function register_settings()
    {
        $this->add_setting(new Settings\Profile($this));
    }

    public function get_raw_value($id)
    {
        return $this->get_field()->get_raw_value($id);
    }

    /**
     * @return false|Field\Profile
     */
    public function get_field()
    {
        switch (strtolower($this->get_buddypress_field_option('type'))) {
            case 'checkbox':
            case 'multiselectbox':
                return new Field\Profile\Checkbox($this);
            case 'datebox':
                return new Field\Profile\Datebox($this);
            case 'number':
                return new Field\Profile\Number($this);
            case 'radio':
            case 'selectbox':
                return new Field\Profile\Radio($this);
            case 'telephone':
                return new Field\Profile\Telephone($this);
            case 'textarea':
                return new Field\Profile\Textarea($this);
            case 'textbox':
                return new Field\Profile\Textbox($this);
            case 'url':
                return new Field\Profile\URL($this);
            default:
                return new Field\Profile($this);
        }
    }

    public function get_buddypress_field()
    {
        return new BP_XProfile_Field($this->get_buddypress_field_id());
    }

    public function get_buddypress_field_id()
    {
        return (string)$this->get_setting('profile_field')->get_value();
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function get_buddypress_field_option($property)
    {
        $field = $this->get_buddypress_field();

        if ( ! isset($field->$property)) {
            return false;
        }

        return $field->$property;
    }

    public function get_filtering_date_setting(): ?string
    {
        return $this->get_field()->get_filtering_date_setting();
    }

}