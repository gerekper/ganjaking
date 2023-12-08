<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\Order;

class Menu implements QueryBindings
{

    private $taxonomy;

    public function __construct(string $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "t.term_id",
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
			SELECT t.term_id AS id, menu.ID AS menu_id
			FROM $wpdb->terms AS t
			INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id
				AND tt.taxonomy = %s
			INNER JOIN $wpdb->postmeta AS pm1 ON pm1.meta_value = t.term_id
				AND pm1.meta_key = '_menu_item_object_id'
			INNER JOIN $wpdb->posts AS menu ON menu.ID = pm1.post_id
				AND menu.post_type = 'nav_menu_item'
			INNER JOIN $wpdb->postmeta AS pm2 ON pm2.post_id = menu.ID
				AND pm2.meta_key = '_menu_item_type' AND pm2.meta_value = %s
			",
            $this->taxonomy,
            'taxonomy'
        );

        $values = [];

        foreach ($wpdb->get_results($sql) as $object) {
            $menu_id = (int)$object->menu_id;

            if ($menu_id > 0) {
                $values[$object->id][] = $this->get_menu_label((int)$object->menu_id);
            }
        }

        foreach ($values as $id => $_values) {
            natcasesort($_values);

            $values[$id] = implode(' ', $_values);
        }

        return (new Sorter())->sort($values);
    }

    private function get_menu_label(int $menu_item_id): string
    {
        static $menu_labels = [];

        if ( ! isset($menu_labels[$menu_item_id])) {
            $menu_labels[$menu_item_id] = ac_helper()->menu->get_menu_label($menu_item_id);
        }

        return $menu_labels[$menu_item_id];
    }

}