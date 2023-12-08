<?php

namespace ACA\EC\Column\Event\Field;

use ACA\EC\Column\Event;
use ACA\EC\Editing;
use ACA\EC\Search;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

/**
 * @since 1.1.2
 */
class Checkbox extends Event\Field
{

    public function editing()
    {
        return new Basic(
            (new View\CheckboxList($this->get_field_options()))->set_clear_button(true),
            new Editing\Storage\Field\Checkbox($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Event\Field\MultipleOptions($this->get_meta_key(), $this->get_field_options());
    }

    private function get_field_options()
    {
        $options = explode("\r\n", $this->get('values'));

        return array_combine($options, $options);
    }

}