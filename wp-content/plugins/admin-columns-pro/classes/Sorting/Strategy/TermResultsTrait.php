<?php

namespace ACP\Sorting\Strategy;

use WP_Term_Query;

trait TermResultsTrait
{

    protected $taxonomy;

    /**
     * For backwards compatibility we need the method `get_results()`
     * @depecated NEWVSERSION
     */
    public function get_results(array $args = []): array
    {
        _deprecated_function(__METHOD__, 'NEWVERSION');

        $defaults = [
            'fields'     => 'ids',
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ];

        $args = array_merge($defaults, $args);

        $query = new WP_Term_Query($args);

        return (array)$query->get_terms();
    }

}