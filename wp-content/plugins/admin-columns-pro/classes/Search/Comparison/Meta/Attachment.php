<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP\Helper\Select;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;
use WP_Post;

class Attachment extends Meta
    implements Comparison\SearchableValues
{

    private $query;

    private $mime_type;

    public function __construct(string $meta_key, Query $query, string $mime_type = null)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $this->query = $query;
        $this->mime_type = $mime_type;

        parent::__construct($operators, $meta_key);
    }

    private function formatter(): PostTitle
    {
        return new PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post instanceof WP_Post
            ? $this->formatter()->format_label($post)
            : (string)$value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create_media(
            [
                's'              => $search,
                'paged'          => $page,
                'post_mime_type' => $this->mime_type,
                'post__in'       => $this->query->get(),
            ],
            $this->formatter()
        );
    }

}