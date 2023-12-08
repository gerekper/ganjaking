<?php

namespace ACA\MetaBox\Search\Comparison\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\MetaBox\Search;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;

class Post extends Search\Comparison\Relation
{

    private function get_label_formatter(): PostTitle
    {
        return new PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->get_label_formatter()->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $related = $this->relation->get_related_field_settings();

        $post_type = isset($related['post_type']) && is_string($related['post_type'])
            ? $related['post_type']
            : null;

        return (new PaginatedFactory())->create([
            'paged'     => $page,
            's'         => $search,
            'post_type' => $post_type,
        ], $this->get_label_formatter());
    }

}