<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC\Helper\Select\Options\Paginated;
use ACA\ACF\Search\Comparison;
use ACP\Helper\Select\Post\GroupFormatter\PostDate;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Image extends Comparison\Repeater
    implements SearchableValues
{

    public function __construct(string $meta_type, string $parent_key, string $sub_key)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($meta_type, $parent_key, $sub_key, $operators);
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? (new PostTitle())->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create(
            [
                's'              => $search,
                'paged'          => $page,
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ],
            new PostTitle(),
            new PostDate()
        );
    }

}