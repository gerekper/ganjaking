<?php

namespace ACP\Editing\Response;

use AC\Response\Json;

class QueryRows extends Json
{

    public function __construct(array $ids, $rows_per_iteration)
    {
        parent::__construct();

        $this->set_parameter('ids', $ids)
             ->set_parameter('rows_per_iteration', (int)$rows_per_iteration);
    }

}