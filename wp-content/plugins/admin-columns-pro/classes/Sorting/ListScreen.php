<?php

namespace ACP\Sorting;

/**
 * @deprecated NEWVERSION
 */
interface ListScreen
{

    public function sorting(AbstractModel $model): Strategy;

}