<?php

declare(strict_types=1);

namespace ACP\Storage;

use AC\Storage\Option;

final class PluginsDataFactory
{

    public function create(): Option
    {
        return new Option('acp_update_plugins_data');
    }

}