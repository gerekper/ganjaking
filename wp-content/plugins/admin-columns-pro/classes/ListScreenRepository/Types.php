<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC;

interface Types extends AC\ListScreenRepository\Types
{

    public const FILE = 'ac-file';
    public const TEMPLATE = 'ac-file-template';

}