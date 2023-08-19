<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Sorting;

class Depth extends AC\Column\Post\Depth
    implements Sorting\Sortable, ConditionalFormat\Formattable
{

    use ConditionalFormat\IntegerFormattableTrait;

    public function sorting()
    {
        return new Sorting\Model\Post\Depth($this->get_post_type());
    }

}