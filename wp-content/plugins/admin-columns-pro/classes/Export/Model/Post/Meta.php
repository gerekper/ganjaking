<?php

namespace ACP\Export\Model\Post;

use AC\MetaType;
use ACP\Export\Model;

class Meta extends Model\Meta
{

    public function __construct(string $meta_key)
    {
        parent::__construct(new MetaType(MetaType::POST), $meta_key);
    }

}