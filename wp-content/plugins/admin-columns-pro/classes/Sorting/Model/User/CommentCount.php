<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\Order;

class CommentCount implements QueryBindings
{

    use SqlTrait;

    public const STATUS_APPROVED = '1';
    public const STATUS_SPAM = 'spam';
    public const STATUS_TRASH = 'trash';
    public const STATUS_PENDING = '0';

    private $status;

    private $post_types;

    public function __construct(array $status = [], array $post_types = [])
    {
        if (empty($status)) {
            $status = [self::STATUS_APPROVED, self::STATUS_PENDING];
        }

        $this->status = $status;
        $this->post_types = $post_types;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('comment_count');

        $join = "\nLEFT JOIN $wpdb->comments AS $alias ON $alias.user_id = $wpdb->users.ID";

        if ($this->status) {
            $join .= sprintf(
                "\nAND $alias.comment_approved IN ( %s )",
                $this->esc_sql_array($this->status)
            );
        }

        if ($this->post_types) {
            $join .= sprintf(
                "\nLEFT JOIN $wpdb->posts AS acsort_posts ON acsort_posts.ID = $alias.comment_post_ID AND acsort_posts.post_type IN ( %s )",
                $this->esc_sql_array($this->post_types)
            );
        }

        $bindings->join($join);
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                "$alias.comment_ID",
                (string)$order
            )
        );

        return $bindings;
    }

}