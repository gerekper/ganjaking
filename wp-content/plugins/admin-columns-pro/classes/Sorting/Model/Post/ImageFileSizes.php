<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue\ContentTotalImageSize;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\Order;

class ImageFileSizes implements QueryBindings, WarningAware
{

    use PostRequestTrait;

    private $post_type;

    private $format_value;

    public function __construct()
    {
        $this->format_value = new ContentTotalImageSize();
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->posts.ID",
                $this->get_sorted_post_ids(),
                (string)$order
            )
        );
    }

    private function get_sorted_post_ids(): array
    {
        $items = [];

        foreach ($this->get_posts() as $post) {
            $items[(int)$post->id] = $this->format_value->format_value($post->content);
        }

        asort($items, SORT_NUMERIC);

        return array_keys($items);
    }

    private function get_posts(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
            SELECT $wpdb->posts.ID AS id, $wpdb->posts.post_content as content 
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_type = %s
                AND $wpdb->posts.post_content LIKE '%<img%'
            ",
            $this->get_var_post_type()
        );

        $status = $this->get_var_post_status();

        if ($status) {
            $sql .= $wpdb->prepare("\nAND $wpdb->posts.post_status = %s", $status);
        }

        return $wpdb->get_results($sql);
    }

}