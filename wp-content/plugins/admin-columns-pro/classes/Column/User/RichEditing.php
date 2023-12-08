<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class RichEditing extends AC\Column\User\RichEditing
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function editing()
    {
        return new Editing\Service\User\RichEditing();
    }

    public function sorting()
    {
        return new Sorting\Model\User\Meta('rich_editing');
    }

    public function search()
    {
        return new Search\Comparison\User\TrueFalse('rich_editing');
    }

}