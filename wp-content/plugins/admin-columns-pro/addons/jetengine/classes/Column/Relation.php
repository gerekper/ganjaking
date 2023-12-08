<?php

namespace ACA\JetEngine\Column;

use AC;
use ACA\JetEngine\Service\ColumnGroups;
use ACP;
use Jet_Engine\Relations\Relation as JetEngineRelation;

abstract class Relation extends AC\Column implements ACP\Export\Exportable, ACP\Editing\Editable, ACP\Search\Searchable
{

    /**
     * @var JetEngineRelation
     */
    protected $relation;

    public function __construct()
    {
        $this->set_group(ColumnGroups::JET_ENGINE_RELATION)
             ->set_label(__('JetEngine Relation', 'codepress-admin-columns'));
    }

    public function set_config(JetEngineRelation $relation)
    {
        $this->relation = $relation;
    }

    public function get_raw_value($id)
    {
        if ($this->is_relation_parent()) {
            $items = wp_list_pluck($this->relation->get_children($id), 'child_object_id');
        } else {
            $items = wp_list_pluck($this->relation->get_parents($id), 'parent_object_id');
        }

        return new AC\Collection($items);
    }

    public function has_many()
    {
        switch ($this->relation->get_args('type')) {
            case 'one_to_many':
                return $this->is_relation_parent();
            case 'many_to_many':
                return true;
            default:
                return false;
        }
    }

    protected function get_related_object(): string
    {
        return $this->is_relation_parent()
            ? (string)explode('::', $this->relation->get_args('child_object'))[1]
            : (string)explode('::', $this->relation->get_args('parent_object'))[1];
    }

    public function is_relation_parent(): bool
    {
        $list_screen = $this->list_screen;

        switch (true) {
            case $list_screen instanceof AC\ListScreen\User:
                return $this->relation->is_parent('mix', 'users');
            case $list_screen instanceof AC\ListScreen\Post:
                return $this->relation->is_parent('posts', $this->get_post_type());
            case $list_screen instanceof ACP\ListScreen\Taxonomy:
                return $this->relation->is_parent('terms', $this->get_taxonomy());
        }

        return false;
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

}