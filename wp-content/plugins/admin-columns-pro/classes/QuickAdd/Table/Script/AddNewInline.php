<?php

namespace ACP\QuickAdd\Table\Script;

use AC\Asset\Location;
use AC\Asset\Script;

class AddNewInline extends Script
{

    /**
     * @var string
     */
    private $label;

    public function __construct(string $label, string $handle, Location $location = null, array $dependencies = [])
    {
        parent::__construct($handle, $location, $dependencies);

        $this->label = $label;
    }

    public function register(): void
    {
        parent::register();

        $this->localize(
            'acp_quick_add_i18n',
            new Script\Localize\Translation([
                'add_new' => $this->label,
            ])
        );
    }

}