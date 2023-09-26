<?php

namespace ACP\Editing\ApplyFilter;

use AC\Request;

class RowsPerIteration
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply_filters(int $rows_per_iteration): int
    {
        /**
         * @deprecated 5.8
         */
        $rows_per_iteration = (int)apply_filters('acp/editing/bulk/editable_rows_per_iteration', $rows_per_iteration);

        return (int)apply_filters('acp/editing/rows_per_iteration', $rows_per_iteration, $this->request);
    }

}