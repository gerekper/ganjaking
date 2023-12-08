<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;

class Color extends Field
{

    use Editing\DefaultServiceTrait;
    use Sorting\DefaultSortingTrait;

    public function get_value($id)
    {
        return ac_helper()->string->get_color_block($this->get_raw_value($id));
    }

}