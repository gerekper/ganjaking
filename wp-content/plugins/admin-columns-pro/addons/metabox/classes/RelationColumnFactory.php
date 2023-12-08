<?php

namespace ACA\MetaBox;

use ACA\MetaBox;
use ACA\MetaBox\Entity\Relation;

final class RelationColumnFactory
{

    public function create(Relation $relation): ?MetaBox\Column\Relation
    {
        switch ($relation->get_related_meta_type()) {
            case 'term':
                $column = new MetaBox\Column\Relation\Term();

                break;
            case 'user':
                $column = new MetaBox\Column\Relation\User();

                break;
            case 'post':
                $column = new MetaBox\Column\Relation\Post();

                break;
            default:
                return null;
        }

        $column->set_label($relation->get_title());
        $column->set_type($relation->get_type() . '__' . $relation->get_id());
        $column->set_relation($relation);

        return $column;
    }

}