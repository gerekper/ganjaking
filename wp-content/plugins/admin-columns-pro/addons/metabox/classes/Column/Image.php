<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class Image extends Column implements ACP\Search\Searchable, ACP\Editing\Editable
{

    public function format_single_value($value, $id = null)
    {
        if (empty($value)) {
            return $this->get_empty_char();
        }

        $results = [];
        foreach (array_keys($value) as $attachment_id) {
            $results[] = $this->get_formatted_value($attachment_id);
        }

        $setting_limit = $this->get_setting('number_of_items');

        return ac_helper()->html->more($results, $setting_limit ? $setting_limit->get_value() : false, '');
    }

    public function is_multiple()
    {
        return true;
    }

    protected function register_settings()
    {
        parent::register_settings();
        $this->add_setting(new AC\Settings\Column\Image($this));
        $this->add_setting(new AC\Settings\Column\NumberOfItems($this));
    }

    public function search()
    {
        return (new Search\Factory\Meta)->create($this);
    }

    public function editing()
    {
        return $this->is_clonable()
            ? false
            : new ACP\Editing\Service\Basic(
                (new ACP\Editing\View\Image())->set_clear_button(true)->set_multiple(true),
                (new Editing\StorageFactory())->create($this, false)
            );
    }

}