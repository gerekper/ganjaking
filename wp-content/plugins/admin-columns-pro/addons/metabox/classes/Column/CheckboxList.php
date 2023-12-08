<?php

namespace ACA\MetaBox\Column;

use AC\Settings\Column\NumberOfItems;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACA\MetaBox\Search;
use ACP;

class CheckboxList extends Column
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function get_raw_value($id)
    {
        $value = $this->get_meta_value($id, $this->get_meta_key(), false);

        return $value ?: false;
    }

    public function format_single_value($value, $id = null)
    {
        if ( ! $value) {
            return $this->get_empty_char();
        }

        // MetaBox sometimes stores extra empty options, not need to show them
        $value = array_filter($value);
        $values = [];
        foreach ($value as $key) {
            $values[] = $this->get_label_for_option($key);
        }

        $setting_limit = $this->get_setting('number_of_items');

        return ac_helper()->html->more($values, $setting_limit ? $setting_limit->get_value() : false);
    }

    protected function get_label_for_option($key)
    {
        $options = $this->get_field_options();

        return isset($options[$key]) ? $options[$key] : $key;
    }

    public function get_field_options(): array
    {
        $options = $this->get_field_setting('options');

        return $options
            ? (array)$options
            : [];
    }

    protected function register_settings()
    {
        $this->add_setting(new NumberOfItems($this));
    }

    public function editing()
    {
        return $this->is_clonable()
            ? false
            : new ACP\Editing\Service\Basic(
                (new ACP\Editing\View\CheckboxList($this->get_field_setting('options')))->set_clear_button(true),
                (new StorageFactory())->create($this, false)
            );
    }

    public function search()
    {
        return (new Search\Factory\CheckboxList())->create($this);
    }

}