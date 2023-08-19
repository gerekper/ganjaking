<?php

declare(strict_types=1);

namespace ACP\ListScreenFactory;

use AC;
use ACP\ListScreen\Media;

class MediaFactory extends AC\ListScreenFactory\MediaFactory
{

    protected function create_list_screen(string $key): AC\ListScreen
    {
        return new Media();
    }

}