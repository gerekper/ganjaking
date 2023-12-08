<?php

namespace ACA\Types\Column\Post;

use AC;
use ACA\Types;
use ACA\Types\Search;
use ACA\Types\Settings;
use ACP;
use ACP\Export\Model\StrippedValue;
use IToolset_Relationship_Definition;

class Relationship extends AC\Column
    implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-types_relationship')
             ->set_label('Toolset Types - Relationship')
             ->set_group('types');
    }

    public function get_value($id)
    {
        $raw_value = $this->get_raw_value($id);

        if ( ! $raw_value) {
            return $this->get_empty_char();
        }

        return parent::get_value($id);
    }

    public function get_raw_value($id)
    {
        $relationship = $this->get_relationship_setting()->get_relationship_object();

        if ( ! $relationship) {
            return false;
        }

        return new AC\Collection(
            toolset_get_related_posts(
                $id,
                $this->get_relationship_setting()->get_relationship(),
                $this->get_relationship_type()
            )
        );
    }

    public function is_valid()
    {
        return apply_filters('toolset_is_m2m_enabled', false) && $this->has_relationships();
    }

    private function has_relationships()
    {
        $relationships = toolset_get_related_post_types(
                             'parent',
                             $this->get_post_type()
                         ) + toolset_get_related_post_types('child', $this->get_post_type());

        return ! empty($relationships);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Relationship($this));
    }

    /**
     * @return IToolset_Relationship_Definition|null
     */
    private function get_relationship_object()
    {
        return $this->get_relationship_setting()->get_relationship_object();
    }

    /**
     * @param IToolset_Relationship_Definition $relationship
     *
     * @return bool|string
     */
    private function get_relationship_type()
    {
        $parent_type = $this->get_relationship_object()->get_parent_type();

        if ( ! in_array($this->get_post_type(), $parent_type->get_types())) {
            return 'child';
        }

        return 'parent';
    }

    /**
     * @return null|Settings\Relationship
     */
    public function get_relationship_setting()
    {
        $setting = $this->get_setting('relationship');

        return $setting instanceof Settings\Relationship
            ? $setting
            : null;
    }

    /**
     * @return string
     */
    private function get_related_post_type()
    {
        return 'parent' === $this->get_relationship_type()
            ? $this->get_relationship_setting()->get_relationship_object()->get_child_type()->get_types()[0]
            : $this->get_relationship_setting()->get_relationship_object()->get_parent_type()->get_types()[0];
    }

    public function export()
    {
        return new StrippedValue($this);
    }

    public function editing()
    {
        if (null === $this->get_relationship_object()) {
            return false;
        }

        $setting = $this->get_relationship_setting();
        $relationship = $setting ? $setting->get_value() : '';

        $storage = 'parent' === $this->get_relationship_type()
            ? new Types\Editing\Storage\Relationship\ParentRelation($this, $relationship)
            : new Types\Editing\Storage\Relationship\ChildRelation($this, $relationship);

        return new Types\Editing\Service\Relationship($storage, $this->get_related_post_type());
    }

    public function search()
    {
        $setting = $this->get_relationship_setting();

        if ( ! $setting) {
            return false;
        }

        $relationship_object = $setting->get_relationship_object();

        if ( ! $relationship_object) {
            return false;
        }

        return new Search\Post\Relationship(
            $relationship_object->get_slug(),
            $this->get_related_post_type(),
            'parent' === $this->get_relationship_type() ? 'child' : 'parent',
            $this->get_relationship_type()
        );
    }

}