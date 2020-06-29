<?php

namespace ACP\Sorting\Strategy;

use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Strategy;
use WP_Term_Query;

class Taxonomy extends Strategy {

	/**
	 * @var WP_Term_Query
	 */
	private $term_query;

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( AbstractModel $model, $taxonomy ) {
		parent::__construct( $model );

		$this->taxonomy = $taxonomy;
	}

	public function manage_sorting() {
		add_action( 'pre_get_terms', [ $this, 'handle_sorting_request' ] );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_results( array $args = [] ) {
		return $this->get_terms( $args );
	}

	/**
	 * @param array $args
	 *
	 * @return int[]
	 */
	protected function get_terms( array $args = [] ) {
		$defaults = [
			'fields'     => 'ids',
			'taxonomy'   => $this->taxonomy,
			'hide_empty' => false,
		];

		$args = array_merge( $defaults, $args );

		$query = new WP_Term_Query( $args );

		return (array) $query->get_terms();
	}

	/**
	 * @return string
	 */
	public function get_order() {
		return $this->get_query_var( 'order' );
	}

	/**
	 * @param WP_Term_Query $term_query
	 */
	private function set_term_query( WP_Term_Query $term_query ) {
		$this->term_query = $term_query;
	}

	/**
	 * return boolean
	 */
	private function is_main_query() {
		$term_query = new ACP\TermQueryInformation();

		if ( ! $this->get_query_var( 'orderby' ) || ! $term_query->is_main_query( $this->term_query ) ) {
			return false;
		}

		$taxonomies = $this->get_query_var( 'taxonomy' );

		return ! ( empty( $taxonomies ) || ! in_array( $this->taxonomy, $taxonomies ) );
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return void
	 */
	public function handle_sorting_request( WP_Term_Query $query ) {
		$this->set_term_query( $query );

		if ( ! $this->is_main_query() ) {
			return;
		}

		foreach ( $this->model->get_sorting_vars() as $key => $value ) {
			if ( $this->is_universal_id( $key ) ) {
				$key = 'include';
			}

			$query->query_vars[ $key ] = $value;
		}

		// pre-sorting done with an array
		$include = $query->query_vars['include'];

		if ( ! empty( $include ) ) {
			$query->query_vars['orderby'] = 'include';
		}
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		$taxonomy = $this->get_query_var( 'taxonomy' );

		return (string) $taxonomy[0];
	}

	/**
	 * @param string $var
	 *
	 * @return string|array|null
	 */
	protected function get_query_var( $var ) {
		return $this->term_query instanceof WP_Term_Query && isset( $this->term_query->query_vars[ $var ] )
			? $this->term_query->query_vars[ $var ]
			: null;
	}

}