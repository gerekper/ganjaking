<?php

namespace ACA\Pods\Field;

use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Search;

class Text extends Field
{

    use Editing\DefaultServiceTrait;
    use Sorting\DefaultSortingTrait;

    public function get_dependent_settings()
    {
        return [
            new Settings\Column\CharacterLimit($this->column),
        ];
    }

}