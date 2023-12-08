<?php

declare(strict_types=1);

namespace ACP\Filtering\Service\Admin;

use AC;
use AC\Asset;
use AC\Registerable;

class Scripts implements Registerable
{

    private $location;

    public function __construct(Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_action('ac/admin_scripts', [$this, 'scripts']);
    }

    public function scripts($page): void
    {
        if ($page instanceof AC\Admin\Page\Columns) {
            $script = new Asset\Script(
                'acp-filtering-settings',
                $this->location->with_suffix('assets/filtering/js/settings.js'),
                ['jquery']
            );
            $script->enqueue();
        }
    }

}