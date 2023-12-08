<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class FeaturedImageSize implements WarningAware, QueryBindings
{

    use PostRequestTrait;

    private $meta_key;

    private $post_type;

    public function __construct(string $meta_key, string $post_type)
    {
        $this->meta_key = $meta_key;
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

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
			SELECT pp.ID AS id, pm2.meta_value AS file_path 
			FROM $wpdb->posts AS pp
			LEFT JOIN $wpdb->postmeta AS pm1 ON pm1.post_id = pp.ID 
			    AND pm1.meta_key = %s
			LEFT JOIN $wpdb->postmeta AS pm2 ON pm1.meta_value = pm2.post_id
				AND pm2.meta_key = '_wp_attached_file'
			WHERE pp.post_type = %s
				AND pm2.meta_value != ''
		",
            $this->meta_key,
            $this->post_type
        );

        $status = $this->get_var_post_status();

        if ($status) {
            $sql .= $wpdb->prepare("\nAND pp.post_status = %s", $status);
        }

        $results = $wpdb->get_results($sql);

        if ( ! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $row) {
            $values[$row->id] = (new FormatValue\FileSize())->format_value($row->file_path);
        }

        return (new Sorter())->sort($values, new DataType(DataType::NUMERIC));
    }

}