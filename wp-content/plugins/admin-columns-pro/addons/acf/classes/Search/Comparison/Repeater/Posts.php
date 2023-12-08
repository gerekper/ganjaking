<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC\Helper\Select\Options\Paginated;
use ACA\ACF\Search\Comparison;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Posts extends Comparison\Repeater
    implements SearchableValues
{

    /**
     * @var array
     */
    private $post_type;

    public function __construct(
        string $meta_type,
        string $parent_key,
        string $sub_key,
        array $post_types = null,
        bool $multiple = false
    ) {
        if (null === $post_types) {
            $post_types = ['any'];
        }

        $this->post_type = $post_types;

        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($meta_type, $parent_key, $sub_key, $operators, null, $multiple);
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->formatter()->format_label($post)
            : '';
    }

    private function formatter(): PostTitle
    {
        return new PostTitle();
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'             => $search,
            'paged'         => $page,
            'post_type'     => $this->post_type,
            'search_fields' => ['post_title', 'ID'],
        ], $this->formatter());
    }

}