<?php

namespace ACP\Column\Comment;

use AC;
use ACP;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Editing\Editable;

class Status extends AC\Column\Comment\Status
    implements Editable, ConditionalFormat\Formattable, ACP\Search\Searchable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function editing()
    {
        return new Editing\Service\Comment\Status();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Comment\Approved();
    }

}