<?php

namespace ACA\Pods\Field;

use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Search;

class Password extends Field
{

    use Editing\DefaultServiceTrait;
    use Sorting\DefaultSortingTrait;

    public function get_value($id)
    {
        return $this->column->get_formatted_value(parent::get_value($id));
    }

    public function get_dependent_settings()
    {
        return [
            new Settings\Column\Password($this->column),
        ];
    }

}