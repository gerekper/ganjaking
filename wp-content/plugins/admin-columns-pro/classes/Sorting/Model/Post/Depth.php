<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Depth implements QueryBindings
{

    use PostRequestTrait;

    private $post_type;

    public function __construct(string $post_type)
    {
        $this->post_type = $post_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_depth(int $id, array $ids, int $depth = 0): int
    {
        if ( ! array_key_exists($id, $ids)) {
            return $depth;
        }

        // prevents infinite loop
        if ($depth > 99) {
            return $depth;
        }

        $parent = (int)$ids[$id];

        return 0 === $parent
            ? $depth
            : $this->get_depth($parent, $ids, ++$depth);
    }

    private function get_sorted_ids(): array
    {
        $status = $this->get_var_post_status();

        $ids = get_posts([
            'fields'         => 'id=>parent',
            'post_type'      => $this->post_type,
            'post_status'    => $status ?: ['any'],
            'posts_per_page' => -1,
        ]);

        $values = [];

        foreach (array_keys($ids) as $id) {
            $values[$id] = $this->get_depth($id, $ids);
        }

        asort($values, SORT_NUMERIC);

        return array_keys($values);
    }

}