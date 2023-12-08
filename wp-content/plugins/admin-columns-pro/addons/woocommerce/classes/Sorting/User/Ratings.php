<?php

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class Ratings implements QueryBindings
{

    /**
     * @var string 'AVG' or 'COUNT
     */
    private $sort_type;

    public function __construct(string $sort_type = null)
    {
        if (null === $sort_type) {
            $sort_type = 'COUNT';
        }

        $this->sort_type = $sort_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $computation = 'AVG' === $this->sort_type
            ? new ComputationType(ComputationType::AVG)
            : new ComputationType(ComputationType::COUNT);

        $alias_meta = $bindings->get_unique_alias('ratings');

        $bindings->join(
            "
			LEFT JOIN $wpdb->comments AS acsort_comments ON acsort_comments.user_id = $wpdb->users.ID
				AND acsort_comments.comment_approved = '1'
			LEFT JOIN $wpdb->commentmeta AS $alias_meta ON $alias_meta.comment_id = acsort_comments.comment_ID
				AND $alias_meta.meta_key = 'rating'
			LEFT JOIN $wpdb->posts AS acsort_posts ON acsort_comments.comment_post_ID = acsort_posts.ID
				AND acsort_posts.post_type = 'product'
			"
        );

        $bindings->group_by("$wpdb->users.ID");
        $bindings->join(
            SqlOrderByFactory::create_with_computation(
                $computation,
                "$alias_meta.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}