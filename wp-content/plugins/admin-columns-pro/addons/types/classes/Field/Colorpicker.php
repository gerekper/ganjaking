<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACP;
use ACP\Search\Comparison;

class Colorpicker extends Field
{

    public function get_value($id)
    {
        return ac_helper()->string->get_color_block($this->get_raw_value($id));
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Color())->set_clear_button(true),
            new ACP\Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function search()
    {
        return new Comparison\Meta\Text(
            $this->column->get_meta_key()
        );
    }

}