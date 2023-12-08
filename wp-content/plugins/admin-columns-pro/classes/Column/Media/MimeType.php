<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class MimeType extends AC\Column\Media\MimeType
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Media\MimeType();
    }

    public function editing()
    {
        return new Editing\Service\Media\MimeType();
    }

    public function search()
    {
        return new Search\Comparison\Media\MimeType();
    }

}