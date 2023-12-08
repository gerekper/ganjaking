<?php

namespace ACP\Column;

use AC;
use AC\MetaType;
use ACP\Editing;
use ACP\Sorting;

abstract class Meta extends AC\Column\Meta
    implements Sorting\Sortable, Editing\Editable
{

    public function sorting()
    {
        return $this->get_meta_key() && $this->get_meta_type()
            ? (new Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key())
            : null;
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

}