<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class MetaPost extends Meta implements Comparison\SearchableValues
{

    private $post_type;

    public function __construct(string $meta_key, array $post_type = null)
    {
        parent::__construct(
            $meta_key,
            new Operators([
                Operators::EQ,
            ], false)
        );

        $this->post_type = $post_type;
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->get_label_formatter()->format_label($post)
            : '';
    }

    protected function get_label_formatter()
    {
        return new ACP\Helper\Select\Post\LabelFormatter\PostTitle();
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new ACP\Helper\Select\Post\PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->post_type ?: 'any',
        ], $this->get_label_formatter());
    }
}