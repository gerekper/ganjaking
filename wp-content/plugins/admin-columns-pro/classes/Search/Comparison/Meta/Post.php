<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options\Paginated;
use AC\Meta\Query;
use ACP\Helper\Select;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

class Post extends Meta
    implements SearchableValues
{

    protected $post_types;

    /**
     * @var WP_Term[]
     */
    protected $terms;

    protected $query;

    public function __construct(
        string $meta_key,
        array $post_types = [],
        array $terms = [],
        Labels $labels = null,
        Query $query = null
    ) {
        $this->post_types = $post_types;
        $this->terms = $terms;
        $this->query = $query;

        parent::__construct($this->get_meta_operators(), $meta_key, Value::STRING, $labels);
    }

    protected function get_meta_operators(): Operators
    {
        return new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);
    }

    private function formatter(): PostTitle
    {
        return new PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->formatter()->format_label($post)
            : '';
    }

    protected function get_post__in(): array
    {
        return $this->query->get();
    }

    public function get_values(string $search, int $page): Paginated
    {
        $args = [
            's'             => $search,
            'paged'         => $page,
            'post_type'     => $this->post_types ?: ['any'],
            'tax_query'     => $this->get_tax_query(),
            'search_fields' => ['post_title', 'ID'],
        ];

        if ($this->query) {
            $args['post__in'] = $this->get_post__in();
        }

        return (new PaginatedFactory())->create(
            $args,
            $this->formatter()
        );
    }

    protected function get_tax_query(): array
    {
        $tax_query = [];

        foreach ($this->terms as $term) {
            $tax_query[] = [
                'taxonomy' => $term->taxonomy,
                'field'    => 'slug',
                'terms'    => $term->slug,
            ];
        }

        return $tax_query;
    }

}