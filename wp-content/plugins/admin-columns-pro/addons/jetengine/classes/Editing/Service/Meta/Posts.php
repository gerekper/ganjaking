<?php

namespace ACA\JetEngine\Editing\Service\Meta;

use ACP;

class Posts extends ACP\Editing\Service\Posts
{

    protected function sanitize_ids(array $ids): array
    {
        return array_map('strval', array_unique(array_filter($ids)));
    }
}