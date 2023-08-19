<?php

namespace ACP\Sorting;

interface Sortable
{

    /**
     * @return AbstractModel|null
     */
    public function sorting();

}