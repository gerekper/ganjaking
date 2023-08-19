<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

class Excerpt extends PostField
{

    public function __construct()
    {
        parent::__construct('post_excerpt');
    }

}