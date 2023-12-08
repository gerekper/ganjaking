<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Post;

class PostParent extends PostField
    implements SearchableValues
{

    private $current_post_type;

    private $searchable_post_types;

    public function __construct(string $current_post_type, array $searchable_post_types = [])
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            null,
            new Labels([
                Operators::IS_EMPTY     => __('Has No Parent', 'codepress-admin-columns'),
                Operators::NOT_IS_EMPTY => __('Has Parent', 'codepress-admin-columns'),
            ])
        );

        $this->current_post_type = $current_post_type;
        $this->searchable_post_types = $searchable_post_types ?: ['any'];
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        if (Operators::IS_EMPTY === $operator) {
            $operator = Operators::EQ;
            $value = new Value(0, $value->get_type());
        }

        if (Operators::IS_EMPTY === $operator) {
            $operator = Operators::NEQ;
            $value = new Value(0, $value->get_type());
        }

        return parent::create_query_bindings($operator, $value);
    }

    protected function get_field(): string
    {
        return 'post_parent';
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
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->searchable_post_types,
            'post__in'  => $this->get_parent_ids(),
        ], $this->formatter());
    }

    private function get_parent_ids(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT DISTINCT(post_parent) FROM $wpdb->posts WHERE post_type = %s",
            $this->current_post_type
        );

        return array_filter($wpdb->get_col($sql));
    }

}