<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class PostType extends Comparison
    implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('pst');

        $where = ComparisonFactory::create(
            "$alias.post_type",
            $operator,
            $value
        )->prepare();

        return $bindings
            ->join("JOIN $wpdb->posts AS $alias ON $wpdb->comments.comment_post_ID = $alias.ID")
            ->where($where);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->get_post_types());
    }

    private function get_post_types(): array
    {
        $post_types = [];
        foreach (get_post_types([], 'object') as $post_type) {
            if ( ! post_type_supports($post_type->name, 'comments')) {
                continue;
            }

            $post_types[$post_type->name] = sprintf(
                '%s <em>(%s)</em>',
                $post_type->labels->singular_name,
                $post_type->name
            );
        }

        natcasesort($post_types);

        return $post_types;
    }

}