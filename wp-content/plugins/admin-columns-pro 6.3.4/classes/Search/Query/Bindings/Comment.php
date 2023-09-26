<?php

namespace ACP\Search\Query\Bindings;

use ACP\Search\Query\Bindings;

class Comment extends Bindings
{

    protected $parent = 0;

    public function get_parent(): int
    {
        return $this->parent;
    }

    public function parent(int $id): self
    {
        $this->parent = $id;

        return $this;
    }

}