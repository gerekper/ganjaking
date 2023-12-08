<?php

namespace ACP\Search\Comparison\Comment;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;
use ACP\Helper\Select\Post\LabelFormatter;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;

class Post extends Field
    implements SearchableValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function get_field(): string
    {
        return 'comment_post_ID';
    }

    private function formatter(): LabelFormatter\PostTitle
    {
        return new LabelFormatter\PostTitle();
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? $this->formatter()->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        // Only search in posts that have comments
        add_filter('posts_join', [$this, 'callback_join']);

        return (new Select\Post\PaginatedFactory())->create([
            's'              => $search,
            'paged'          => $page,
            'post_type'      => get_post_types_by_support('comments'),
            'posts_per_page' => 100,
        ],
            $this->formatter()
        );
    }

    public function callback_join($join): string
    {
        global $wpdb;

        $join .= "\nINNER JOIN $wpdb->comments AS cc ON $wpdb->posts.ID = cc.comment_post_ID";

        return $join;
    }

}