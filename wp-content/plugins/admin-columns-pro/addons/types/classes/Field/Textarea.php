<?php

namespace ACA\Types\Field;

use AC;
use AC\MetaType;
use ACA\Types\Field;
use ACP\Editing;
use ACP\Search\Comparison;
use ACP\Sorting;

class Textarea extends Field
{

    public function get_value($id)
    {
        return $this->column->get_formatted_value($this->get_raw_value($id));
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\TextArea())->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function search()
    {
        return new Comparison\Meta\Text(
            $this->column->get_meta_key()
        );
    }

    public function get_dependent_settings()
    {
        return [new AC\Settings\Column\WordLimit($this->column)];
    }

}