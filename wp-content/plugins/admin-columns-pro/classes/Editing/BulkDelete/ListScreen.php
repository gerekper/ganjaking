<?php

namespace ACP\Editing\BulkDelete;

interface ListScreen
{

    public function deletable(): Deletable;

}