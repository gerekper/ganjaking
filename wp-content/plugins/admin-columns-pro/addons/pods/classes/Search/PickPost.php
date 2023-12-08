<?php

namespace ACA\Pods\Search;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP\Helper\Select\Post\GroupFormatter\PostType;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PickPost extends Meta
    implements SearchableValues
{

    private $post_type;

    protected $query;

    public function __construct($meta_key, array $post_type, Query $query, string $value_type = null)
    {
        $this->post_type = $post_type;
        $this->query = $query;

        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
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
        return (new PaginatedFactory())->create([
            'post__in'  => $this->get_used_post_ids(),
            'paged'     => $page,
            's'         => $search,
            'post_type' => $this->post_type,
        ], null, new PostType());
    }

    public function get_used_post_ids(): array
    {
        return array_filter($this->query->get(), 'is_numeric');
    }

}