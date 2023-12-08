<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class FirstName extends AC\Column\User\FirstName
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\User\Meta($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Text())->set_clear_button(true),
            new Editing\Storage\User\Meta($this->get_meta_key())
        );
    }

    public function search()
    {
        return new Search\Comparison\Meta\Text($this->get_meta_key());
    }

}