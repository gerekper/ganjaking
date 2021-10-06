<?php

namespace ACP\Settings\Column\Post;

use AC;
use AC\View;
use ACP\Settings\Column\Term;

class TaxonomyTerm extends AC\Settings\Column {

	const NAME = 'taxonomy';

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( AC\Column $column, $post_type ) {
		$this->post_type = $post_type;

		parent::__construct( $column );
	}

	protected function define_options() {
		return [ self::NAME ];
	}

	public function get_dependent_settings() {
		$dependent_settings = parent::get_dependent_settings();

		if ( $this->get_taxonomy() ) {
			$dependent_settings[] = new Term( $this->column, $this->get_taxonomy() );
		}

		return $dependent_settings;
	}

	/**
	 * @return string
	 */
	protected function get_post_type() {
		return $this->post_type;
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$taxonomy = $this->create_element( 'select', 'taxonomy' );
		$taxonomy->set_no_result( __( 'No taxonomies available.', 'codepress-admin-columns' ) )
		         ->set_options( ac_helper()->taxonomy->get_taxonomy_selection_options( $this->get_post_type() ) )
		         ->set_attribute( 'data-refresh', 'column' );

		return new View( [
			'setting' => $taxonomy,
			'label'   => __( 'Taxonomy', 'codepress-admin-columns' ),
		] );
	}

	public function get_first_taxonomy() {
		$taxonomies = ac_helper()->taxonomy->get_taxonomy_selection_options( $this->get_post_type() );

		if ( empty( $taxonomies ) ) {
			return null;
		}

		reset( $taxonomies );

		return key( $taxonomies );
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->taxonomy ?: $this->get_first_taxonomy();
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return bool
	 */
	public function set_taxonomy( $taxonomy ) {
		$this->taxonomy = $taxonomy;

		return true;
	}

	public function get_term_id() {
		$setting = $this->column->get_setting( Term::NAME );

		return $setting ? $setting->get_value() : null;
	}

}