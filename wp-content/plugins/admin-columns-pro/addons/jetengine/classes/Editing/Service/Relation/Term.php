<?php

namespace ACA\JetEngine\Editing\Service\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Editing;
use ACP\Editing\Storage;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;

class Term extends Editing\Service\Relationship
{

    private $taxonomy;

    public function __construct(Storage $storage, bool $multiple, string $taxonomy)
    {
        $this->taxonomy = $taxonomy;

        parent::__construct($storage, $multiple);
    }

    public function get_value($id)
    {
        $value = [];
        $term_ids = parent::get_value($id);

        foreach ($term_ids as $term_id) {
            $value[$term_id] = ac_helper()->taxonomy->get_term_display_name(get_term($term_id));
        }

        return $value;
    }

    public function get_paginated_options(string $search, int $page, int $id = null): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ]);
    }

}