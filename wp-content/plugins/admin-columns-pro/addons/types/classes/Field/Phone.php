<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACP;
use ACP\Search\Comparison;
use ACP\Sorting;
use ACP\Sorting\Type\DataType;

class Phone extends Field
{

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true)->set_placeholder((string)$this->get('placeholder')),
            new ACP\Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            new DataType(DataType::NUMERIC)
        );
    }

    public function search()
    {
        return new Comparison\Meta\Text(
            $this->column->get_meta_key()
        );
    }

}