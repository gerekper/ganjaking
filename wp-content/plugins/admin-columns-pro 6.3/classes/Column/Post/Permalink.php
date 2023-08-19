<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Permalink extends AC\Column\Post\Permalink
    implements Sorting\Sortable, Editing\Editable, Export\Exportable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return is_post_type_hierarchical($this->get_post_type())
            ? new Sorting\Model\Post\Permalink($this->get_post_type())
            : new Sorting\Model\Post\PostField('post_name');
    }

    public function editing()
    {
        return new Editing\Service\Post\Slug();
    }

    public function export()
    {
        return new Export\Model\Post\Permalink();
    }

}