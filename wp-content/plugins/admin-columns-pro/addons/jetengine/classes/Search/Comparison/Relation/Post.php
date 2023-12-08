<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;
use Jet_Engine\Relations\Relation as JetEngineRelation;
use WP_Post;

class Post extends Relation
{

    private $post_type;

    public function __construct(JetEngineRelation $relation, bool $is_parent, string $post_type)
    {
        parent::__construct($relation, $is_parent);

        $this->post_type = $post_type;
    }

    private function formatter(): Select\Post\LabelFormatter
    {
        return new Select\Post\LabelFormatter\PostTitle();
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
        return (new Select\Post\PaginatedFactory())->create([
            's'             => $search,
            'paged'         => $page,
            'post_type'     => $this->post_type,
            'search_fields' => ['post_title', 'ID'],
        ]);
    }

}