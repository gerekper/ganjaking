<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACP\Editing;
use ACP\Search\Comparison;
use ACP\Sorting;
use ACP\Sorting\Type\DataType;

class Number extends Field
{

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            new DataType(DataType::NUMERIC)
        );
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Number())->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function search()
    {
        return new Comparison\Meta\Number($this->column->get_meta_key());
    }

}