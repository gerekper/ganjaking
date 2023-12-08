<?php

namespace ACA\EC\Column\Event\Field;

use ACA\EC\Column\Event;
use ACA\EC\Search;
use ACP\Editing;

/**
 * @since 1.1.2
 */
class Dropdown extends Event\Field
{

    public function editing()
    {
        return new Editing\Service\Basic(
            new Editing\View\Select($this->get_field_options()),
            new Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Event\Field\Options($this->get_meta_key(), $this->get_field_options());
    }

    /**
     * @return array
     */
    private function get_field_options()
    {
        $options = explode("\r\n", $this->get('values'));

        return array_combine($options, $options);
    }

}