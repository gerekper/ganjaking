<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;

class Boolean extends Field
{

    use Sorting\DefaultSortingTrait;
    use Editing\DefaultServiceTrait;

    public function get_value($id)
    {
        return ac_helper()->icon->yes_or_no('1' === $this->get_raw_value($id));
    }

}