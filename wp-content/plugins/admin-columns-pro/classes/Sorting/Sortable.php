<?php

namespace ACP\Sorting;

use ACP\Sorting\Model\QueryBindings;

interface Sortable
{

    /**
     * @return QueryBindings|null
     */
    public function sorting();

}