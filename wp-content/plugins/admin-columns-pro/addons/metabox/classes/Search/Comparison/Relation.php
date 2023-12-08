<?php

namespace ACA\MetaBox\Search\Comparison;

use ACA\MetaBox\Entity;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;
use MB_Relationships_API;
use WP_Post;
use WP_Term;
use WP_User;

abstract class Relation extends ACP\Search\Comparison implements SearchableValues
{

    protected $relation;

    public function __construct(Entity\Relation $relation)
    {
        $this->relation = $relation;

        parent::__construct(
            new Operators(
                [Operators::EQ]
            )
        );
    }

    public function get_related_object_id($item)
    {
        switch ($item) {
            case $item instanceof WP_Post:
            case $item instanceof WP_User:
                return (int)$item->ID;
            case $item instanceof WP_Term:
                return (int)$item->term_id;
            default:
                return 0;
        }
    }

    /**
     * @param array $objects
     *
     * @return int[]
     */
    private function pluck_ids(array $objects)
    {
        $ids = array_filter(array_unique(array_map([$this, 'get_related_object_id'], $objects)));

        return ! empty($ids) ? $ids : [0];
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $type = $this->relation->get_related_type();

        $results = MB_Relationships_API::get_connected([
            'id'  => $this->relation->get_id(),
            $type => $value->get_value(),
        ]);

        switch ($this->relation->get_meta_type()) {
            case 'post':
                $ids = $this->pluck_ids($results);
                if ($ids) {
                    $bindings->where($wpdb->posts . '.ID IN( ' . implode(',', $ids) . ' )');
                }
                break;
            case 'user':
                $ids = $this->pluck_ids($results);
                if ($ids) {
                    $bindings->where($wpdb->users . '.ID IN( ' . implode(',', $ids) . ' )');
                }
                break;
            case 'term':
                $ids = $this->pluck_ids($results);
                if ($ids) {
                    $bindings->where('t.term_id IN( ' . implode(',', $ids) . ' )');
                }
                break;
        }

        return $bindings;
    }

}