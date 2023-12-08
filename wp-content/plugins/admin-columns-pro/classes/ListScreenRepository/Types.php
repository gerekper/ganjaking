<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC;

// TODO David redo 'types'? The 'extend' is weird?
interface Types extends AC\ListScreenRepository\Types
{

    public const FILE = 'ac-file';
    public const PRESET = 'ac-file-preset';

}