<?php

declare(strict_types=1);

namespace ACP\Editing\Storage\Taxonomy;

use AC\MetaType;
use ACP;

class Meta extends ACP\Editing\Storage\Meta
{

    public function __construct(string $meta_key)
    {
        parent::__construct($meta_key, new MetaType(MetaType::TERM));
    }

}