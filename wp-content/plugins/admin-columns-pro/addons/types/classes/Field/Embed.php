<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACA\Types\Search;
use ACP;
use ACP\Editing;

class Embed extends Field
{

    public function get_value($id)
    {
        return $this->get_raw_value($id);
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Url())->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function search()
    {
        return new Search\File($this->column->get_meta_key());
    }

}