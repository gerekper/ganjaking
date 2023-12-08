<?php

namespace ACP\Search\Comparison;

use AC\Meta\Query;
use AC\Meta\QueryMetaFactory;
use ACP\Search\Comparison\Meta\DateTime\ISO;

class MetaFactory
{

    private $query_meta_factory;

    public function __construct()
    {
        $this->query_meta_factory = new QueryMetaFactory();
    }

    public function create_datetime_iso(string $meta_key, string $meta_type = null, string $post_type = null): ISO
    {
        return new ISO($meta_key, $this->get_meta_query($meta_key, $meta_type, $post_type));
    }

    private function get_meta_query(string $meta_key, string $meta_type = null, string $post_type = null): Query
    {
        return $post_type
            ? $this->query_meta_factory->create_with_post_type($meta_key, $post_type)
            : $this->query_meta_factory->create($meta_key, $meta_type);
    }

}