<?php

namespace ACP\Settings\Column;

use AC;
use AC\View;

class Term extends AC\Settings\Column {

	const NAME = 'term_id';

	/**
	 * @var string
	 */
	private $term_id;

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( AC\Column $column, $taxonomy ) {
		$this->taxonomy = $taxonomy;

		parent::__construct( $column );
	}

	/**
	 * @return string
	 */
	protected function get_taxonomy() {
		return $this->taxonomy;
	}

	protected function define_options() {
		return [ self::NAME ];
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$taxonomy = $this->create_element( 'select', 'term_id' );
		$taxonomy->set_no_result( __( 'No terms available.', 'codepress-admin-columns' ) )
		         ->set_options( $this->get_term_for_post_type() );

		return new View( [
			'setting' => $taxonomy,
			'label'   => __( 'Term', 'codepress-admin-columns' ),
		] );
	}

	/**
	 * @return string
	 */
	public function get_term_id() {
		return $this->term_id;
	}

	/**
	 * @param string $term
	 *
	 * @return bool
	 */
	public function set_term_id( $term_id ) {
		$this->term_id = $term_id;

		return true;
	}

	private function get_term_for_post_type() {
		$terms = get_terms( $this->get_taxonomy() );
		$options = [];

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}

}