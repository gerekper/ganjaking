<?php

namespace ACP\Sorting;

interface ListScreen
{

    public function sorting(AbstractModel $model): Strategy;

}