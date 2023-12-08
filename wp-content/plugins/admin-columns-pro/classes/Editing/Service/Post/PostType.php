<?php

namespace ACP\Editing\Service\Post;

use AC\Helper\Select\Options;
use ACP\Editing\RemoteOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;

class PostType extends BasicStorage implements RemoteOptions
{

    public function __construct()
    {
        parent::__construct(new Storage\Post\PostType());
    }

    public function get_view(string $context): ?View
    {
        return new View\RemoteSelect();
    }

    public function get_remote_options(int $id = null): Options
    {
        return (new Select\OptionsFactory\PostType())->create();
    }

}