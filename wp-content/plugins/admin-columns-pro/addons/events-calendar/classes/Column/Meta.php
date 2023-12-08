<?php

namespace ACA\EC\Column;

use ACA\EC\Service\ColumnGroups;
use ACP;

abstract class Meta extends ACP\Column\Meta implements ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_group(ColumnGroups::EVENTS_CALENDAR);
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Text(),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

}