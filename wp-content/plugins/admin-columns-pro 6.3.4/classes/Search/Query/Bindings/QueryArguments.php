<?php

namespace ACP\Search\Query\Bindings;

use ACP;

class QueryArguments extends ACP\Search\Query\Bindings
{

    protected $query_arguments = [];

    public function query_arguments(array $args): self
    {
        $this->query_arguments = $args;

        return $this;
    }

    public function get_query_arguments(): array
    {
        return $this->query_arguments;
    }

}