<?php

declare(strict_types=1);

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Sorting;

class Path extends AC\Column\Post\Path
    implements Sorting\Sortable, Editing\Editable, ConditionalFormat\Formattable
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

}