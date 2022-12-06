<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Taxonomy extends Field implements Field\Multiple, Field\TermRelation {

	/**
	 * @return bool
	 */
	public function uses_native_term_relation() {
		return ( 1 === $this->settings['load_terms'] && 1 === $this->settings['save_terms'] );
	}

	/**
	 * @return bool
	 */
	public function is_multiple() {
		return in_array( $this->settings['field_type'], [ 'checkbox', 'multi_select' ], true );
	}

	/**
	 * @return string
	 */
	public function get_taxonomy() {
		return (string) $this->settings['taxonomy'];
	}

}