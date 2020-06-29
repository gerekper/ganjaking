<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Filtering;

class TaxonomyParent extends AC\Column
	implements Editing\Editable, Filtering\Filterable {

	public function __construct() {
		$this->set_type( 'column-term_parent' );
		$this->set_label( __( 'Parent', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$term_parent_id = $this->get_raw_value( $id );

		if ( ! $term_parent_id ) {
			return $this->get_empty_char();
		}

		$parent = get_term( $term_parent_id, $this->get_taxonomy() );

		if ( ! $parent || is_wp_error( $parent ) ) {
			return $this->get_empty_char();
		}

		return $this->get_formatted_value( $parent, $parent );
	}

	public function get_raw_value( $term_id ) {
		$term = get_term( $term_id, $this->get_taxonomy() );

		if ( ! $term || is_wp_error( $term ) || 0 === $term->parent ) {
			return false;
		}

		return (int) $term->parent;
	}

	public function editing() {
		return new Editing\Model\Taxonomy\TaxonomyParent( $this );
	}

	public function filtering() {
		return new Filtering\Model\Taxonomy\TaxonomyParent( $this );
	}

	public function is_valid() {
		return is_taxonomy_hierarchical( $this->get_taxonomy() );
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Term( $this ) );
		$this->add_setting( new AC\Settings\Column\TermLink( $this ) );
	}

}