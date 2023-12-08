<?php

declare(strict_types=1);

namespace ACP\Filtering\View;

use AC\View;

class FilterContainer extends View
{

    public function __construct(string $column_name)
    {
        parent::__construct(['column_name' => $column_name]);

        $this->set_template('table/filter-container');
    }
}