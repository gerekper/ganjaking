<?php

namespace ACA\MetaBox\ListScreen;

use ACP;

class Taxonomy extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('mb-taxonomy');

        $this->group = 'metabox';
    }

}