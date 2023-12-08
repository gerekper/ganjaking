<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC\Helper\Select\Options\Paginated;
use ACA\ACF\Search\Comparison;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Media extends Comparison\Repeater
    implements SearchableValues
{

    private $mime_type;

    public function __construct(string $meta_type, string $parent_key, string $sub_key, string $mime_type = null)
    {
        parent::__construct(
            $meta_type,
            $parent_key,
            $sub_key,
            new Operators([
                Operators::EQ,
            ])
        );

        $this->mime_type = $mime_type;
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
        return (new PaginatedFactory())->create_media(
            [
                's'              => $search,
                'paged'          => $page,
                'post_mime_type' => $this->mime_type,
            ],
            new PostTitle()
        );
    }

}