<?php

namespace ACA\JetEngine\Sorting;

use ACA\JetEngine\Sorting;

trait SortableTrait
{

    public function sorting()
    {
        return (new Sorting\ModelFactory())->create(
            $this->field,
            $this->get_meta_type(),
            ['post_type' => $this->get_post_type(), 'taxonomy' => $this->get_taxonomy()]
        );
    }

}