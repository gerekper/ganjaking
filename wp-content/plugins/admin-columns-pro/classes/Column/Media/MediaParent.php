<?php

namespace ACP\Column\Media;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Search;

class MediaParent extends AC\Column\Media\MediaParent
    implements Export\Exportable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function export()
    {
        return new Export\Model\Post\PostParent();
    }

    public function search()
    {
        return new Search\Comparison\Post\PostParent('attachment');
    }

}