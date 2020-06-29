<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Sorter;

class Menu extends AbstractModel {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @param string $taxonomy
	 */
	public function __construct( $taxonomy ) {
		parent::__construct();

		$this->taxonomy = $taxonomy;
	}

	public function get_sorting_vars() {
		return [
			'ids' => $this->get_sorted_ids(),
		];
	}

	/**
	 * @return array
	 */
	private function get_sorted_ids() {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT t.term_id AS id, menu.ID AS menu_id
				FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id
				    AND tt.taxonomy = %s
				INNER JOIN {$wpdb->postmeta} AS pm1 ON pm1.meta_value = t.term_id
					AND pm1.meta_key = '_menu_item_object_id'
				INNER JOIN {$wpdb->posts} AS menu ON menu.ID = pm1.post_id
					AND menu.post_type = 'nav_menu_item'
				INNER JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = menu.ID
					AND pm2.meta_key = '_menu_item_type' AND pm2.meta_value = %s

			", $this->taxonomy, 'taxonomy' );

		$values = [];

		foreach ( $wpdb->get_results( $sql ) as $object ) {
			$values[ $object->id ][] = $this->get_menu_label( $object->menu_id );
		}

		foreach ( $values as $id => $_values ) {
			natcasesort( $_values );

			$values[ $id ] = implode( ' ', $_values );
		}

		return ( new Sorter() )->sort( $values );
	}

	/**
	 * @param int $menu_item_id
	 *
	 * @return string|null
	 */
	private function get_menu_label( $menu_item_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT t.name
				FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id
				INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN {$wpdb->posts} AS menu ON menu.ID = tr.object_id
				    AND menu.post_type = 'nav_menu_item'
    			WHERE menu.ID = %d
			", $menu_item_id );

		return $wpdb->get_var( $sql );
	}

}