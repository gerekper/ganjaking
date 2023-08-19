<?php

namespace ACA\MetaBox;

use AC;
use ACA\MetaBox\Entity\Relation;
use ACP;
use MB_Relationships_API;
use MBR_Relationship;

class RelationshipRepository
{

    /**
     * @param AC\ListScreen $list_screen
     *
     * @return Relation[]
     */
    public function get_by_list_screen(AC\ListScreen $list_screen): array
    {
        $results = [];

        /**
         * @var MBR_Relationship[] $relationships
         */
        $relationships = MB_Relationships_API::get_all_relationships();

        foreach ($relationships as $relation) {
            if ($this->is_relation_type_for_list_screen($relation->from, $list_screen)) {
                $results[] = new Relation($relation, 'from');
            }
            if ($this->is_relation_type_for_list_screen($relation->to, $list_screen)) {
                $results[] = new Relation($relation, 'to');
            }
        }

        return $results;
    }

    public function get_by_column(Column\Relation $column): ?Relation
    {
        $column_information = explode('__', $column->get_type());
        $type = array_shift($column_information);
        $relation_id = implode('', $column_information);
        $relation = MB_Relationships_API::get_relationship($relation_id);

        return $relation
            ? new Relation($relation, $type)
            : null;
    }

    private function is_relation_type_for_list_screen($type, AC\ListScreen $list_screen)
    {
        if ( ! isset($type['object_type']) || $list_screen->get_meta_type() !== $type['object_type']) {
            return false;
        }

        switch (true) {
            case $list_screen instanceof AC\ListScreen\Media:
            case $list_screen instanceof AC\ListScreen\Post:
                return 'post' === $type['object_type'] && $list_screen->get_post_type() === $type['field']['post_type'];
            case $list_screen instanceof AC\ListScreen\User;
                return 'user' === $type['object_type'];
            case $list_screen instanceof ACP\ListScreen\Taxonomy;
                return 'term' === $type['object_type'];
            default:
                return false;
        }
    }

}