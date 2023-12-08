<?php

namespace ACA\JetEngine\Column;

use AC;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Service\ColumnGroups;
use ACA\JetEngine\Utils\Api;
use ACA\JetEngine\Utils\Relations;
use ACP;

class RelationLegacy extends AC\Column
    implements ACP\Search\Searchable, ACP\Editing\Editable
{

    /**
     * @var array
     */
    protected $relation_information;

    public function __construct()
    {
        $this->set_group(ColumnGroups::JET_ENGINE_RELATION)
             ->set_label(__('JetEngine RelationLegacy', 'codepress-admin-columns'));
    }

    public function get_relation_key(): string
    {
        return $this->get_type();
    }

    public function set_config($relationInfo): void
    {
        $this->relation_information = $relationInfo;
    }

    protected function get_related_post_type(): ?string
    {
        return Relations::get_related_post_type($this->relation_information, $this->get_post_type());
    }

    public function get_value($id)
    {
        $raw_value = $this->get_raw_value($id);

        if (empty($raw_value)) {
            return $this->get_empty_char();
        }

        $formattedPosts = array_map(function ($postId) {
            return $this->get_formatted_value($postId, $postId);
        }, is_array($raw_value) ? $raw_value : [$raw_value]);

        return implode(', ', $formattedPosts);
    }

    public function get_raw_value($id)
    {
        return Api::Relations()->get_related_posts([
            'hash'    => $this->get_relation_key(),
            'current' => $this->get_post_type(),
            'post_id' => $id,
        ]);
    }

    protected function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\Post($this));
    }

    public function search()
    {
        $post_type = $this->get_related_post_type();

        return new ACP\Search\Comparison\Meta\Post($this->get_relation_key(), $post_type ? (array)$post_type : []);
    }

    public function editing()
    {
        $related_post_type = $this->get_related_post_type();

        return $related_post_type !== null
            ? new Editing\Service\RelationshipLegacy(
                $this->get_relation_key(),
                $this->get_post_type(),
                $related_post_type,
                Relations::has_multiple_relations($this->relation_information, $this->get_post_type())
            )
            : null;
    }

}