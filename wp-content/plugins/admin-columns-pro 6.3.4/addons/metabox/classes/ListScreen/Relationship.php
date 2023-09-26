<?php

namespace ACA\MetaBox\ListScreen;

use ACP;

class Relationship extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('mb-relationship');

        $this->group = 'metabox';
    }

}