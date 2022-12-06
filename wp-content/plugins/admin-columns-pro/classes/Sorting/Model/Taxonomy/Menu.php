<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;

class Menu extends AbstractModel {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var array
	 */
	private $menu_labels;

	/**
	 * @param string $taxonomy
	 */
	public function __construct( $taxonomy ) {
		parent::__construct();

		$this->taxonomy = $taxonomy;
		$this->menu_labels = [];
	}

	public function get_sorting_vars() {
		add_filter( 'terms_clauses', [ $this, 'pre_term_query_callback' ] );

		return [];
	}

	public function pre_term_query_callback( $clauses ) {
		remove_filter( 'terms_clauses', [ $this, __FUNCTION__ ] );

		$clauses['orderby'] = sprintf( 'ORDER BY %s, t.term_id',
			SqlOrderByFactory::create_with_ids( "t.term_id", $this->get_sorted_ids(), $this->get_order() ) ?: $clauses['orderby']
		);
		$clauses['order'] = '';

		return $clauses;
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
			$values[ $object->id ][] = $this->get_menu_label( (int) $object->menu_id );
		}

		foreach ( $values as $id => $_values ) {
			natcasesort( $_values );

			$values[ $id ] = implode( ' ', $_values );
		}

		return ( new Sorter() )->sort( $values );
	}

	private function get_menu_label( int $menu_item_id ): ?string {
		global $wpdb;

		if ( $menu_item_id === 0 ) {
			return '';
		}
		if ( ! isset( $this->menu_labels[ $menu_item_id ] ) ) {
			$sql = $wpdb->prepare( "
			SELECT t.name
				FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id
				INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN {$wpdb->posts} AS menu ON menu.ID = tr.object_id
				    AND menu.post_type = 'nav_menu_item'
    			WHERE menu.ID = %d
			", $menu_item_id );

			$this->menu_labels[ $menu_item_id ] = $wpdb->get_var( $sql );
		}

		return $this->menu_labels[ $menu_item_id ];
	}

}