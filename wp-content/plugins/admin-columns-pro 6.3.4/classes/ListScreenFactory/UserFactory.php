<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC;
use AC\ListScreen;
use ACP\ListScreen\User;

class UserFactory extends AC\ListScreenFactory\UserFactory
{

    protected function create_list_screen(string $key): ListScreen
    {
        return new User();
    }

}