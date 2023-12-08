<?php

namespace ACA\WC\Export\Product;

use ACA\WC\Column;
use ACP;

class Variation implements ACP\Export\Service
{

    private $column;

    public function __construct(Column\Product\Variation $column)
    {
        $this->column = $column;
    }

    public function get_value($id): string
    {
        return (string)count($this->column->get_raw_value($id));
    }

}