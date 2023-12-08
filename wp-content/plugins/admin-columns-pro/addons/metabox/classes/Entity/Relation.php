<?php

namespace ACA\MetaBox\Entity;

use MB_Relationships_API;
use MBR_Relationship;
use WP_Post;
use WP_Term;
use WP_User;

class Relation
{

    /**
     * @var MBR_Relationship
     */
    private $relation;

    /**
     * @var string
     */
    private $type;

    /**
     * @param MBR_Relationship $relation
     * @param 'from'|'to'      $relation
     */
    public function __construct(MBR_Relationship $relation, string $type)
    {
        $this->relation = $relation;
        $this->type = $type;
    }

    public function get_id()
    {
        return $this->relation->id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_related_type()
    {
        return $this->type === 'from'
            ? 'to'
            : 'from';
    }

    public function get_meta_type()
    {
        return $this->relation->get_object_type($this->type);
    }

    public function get_related_meta_type()
    {
        return $this->relation->get_object_type($this->get_related_type());
    }

    public function get_title()
    {
        if ($this->relation->menu_title) {
            return $this->relation->menu_title;
        }

        switch ($this->get_related_meta_type()) {
            case 'user':
                return sprintf('User (%s)', $this->relation->id);

            case 'post':
                $field = $this->get_related_field_settings();

                if (empty($field['post_type'])) {
                    break;
                }

                $post_type_object = get_post_type_object($field['post_type']);

                return $post_type_object->label ?? $field['post_type'];

            case 'term':
                $field = $this->get_related_field_settings();

                if (empty($field['taxonomy'])) {
                    break;
                }

                $taxonomy = get_taxonomy($field['taxonomy']);

                return $taxonomy->label ?? $field['taxonomy'];
        }

        return empty($this->relation->menu_title)
            ? $this->relation->id
            : $this->relation->menu_title;
    }

    /**
     * @return array
     */

    public function get_related_field_settings()
    {
        return 'to' === $this->get_related_type()
            ? $this->relation->to['field']
            : $this->relation->from['field'];
    }

    /**
     * @param $object_id
     *
     * @return int[]
     */
    public function get_related_ids($object_id)
    {
        $ids = [];
        $items = MB_Relationships_API::get_connected([
            'id'        => $this->get_id(),
            $this->type => $object_id,
        ]);

        foreach ($items as $item) {
            switch (true) {
                case $item instanceof WP_Term:
                    $ids[] = $item->term_id;
                    break;
                case $item instanceof WP_Post:
                case $item instanceof WP_User:
                    $ids[] = $item->ID;
                    break;
            }
        }

        return $ids;
    }

    public function add_relation($from_id, $to_id)
    {
        return MB_Relationships_API::add(
            $this->type === 'from' ? $from_id : $to_id,
            $this->type === 'from' ? $to_id : $from_id,
            $this->get_id()
        );
    }

    public function delete_relation($from_id, $to_id)
    {
        return MB_Relationships_API::delete(
            $this->type === 'from' ? $from_id : $to_id,
            $this->type === 'from' ? $to_id : $from_id,
            $this->get_id()
        );
    }

}