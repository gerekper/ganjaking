<?php

namespace ACP\Filtering;

use ACP\Search\Comparison;

/**
 * @depecated NEWVERSION
 */
interface Filterable
{

    /**
     * @return Comparison|null
     */
    public function filtering();

}