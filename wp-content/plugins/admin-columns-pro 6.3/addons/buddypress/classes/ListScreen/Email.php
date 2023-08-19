<?php

declare(strict_types=1);

namespace ACA\BP\ListScreen;

use ACP;

class Email extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('bp-email');

        $this->group = 'buddypress';
    }

}