<?php

namespace ACP\Search\Comparison\Meta;

use AC\Meta\Query;
use ACP\Helper\Select;

class Image extends Attachment
{

    public function __construct(string $meta_key, Query $query)
    {
        parent::__construct($meta_key, $query, 'image');
    }

}