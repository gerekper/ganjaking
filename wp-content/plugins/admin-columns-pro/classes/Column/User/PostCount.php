<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Sorting;

class PostCount extends AC\Column\User\PostCount
    implements Sorting\Sortable, Export\Exportable, ConditionalFormat\Formattable
{

    use ConditionalFormat\IntegerFormattableTrait;

    public function sorting()
    {
        return new Sorting\Model\User\PostCount(
            $this->get_selected_post_types(),
            $this->get_selected_post_status()
        );
    }

    public function export()
    {
        return new Export\Model\StrippedValue($this);
    }

}