<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Url extends AC\Column\User\Url
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\User\UserField('user_url');
    }

    public function editing()
    {
        return new Editing\Service\User\Url($this->get_label());
    }

    public function search()
    {
        return new Search\Comparison\User\Url();
    }

}