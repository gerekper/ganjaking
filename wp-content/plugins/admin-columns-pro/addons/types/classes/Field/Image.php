<?php

namespace ACA\Types\Field;

use AC;
use AC\MetaType;
use ACA\Types\Editing\Storage;
use ACP\Editing;

class Image extends File
{

    public function get_value($id)
    {
        $ids = array_unique((array)$this->get_raw_value($id));
        $values = [];

        foreach ($ids as $url) {
            $values[] = $this->column->get_formatted_value($this->get_attachment_id_by_url($url));
        }

        return implode($values);
    }

    public function get_dependent_settings()
    {
        $image = new AC\Settings\Column\Image($this->column);
        $image->set_default('cpac-custom');

        return [$image];
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Image())->set_clear_button(true),
            new Storage\File($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

}