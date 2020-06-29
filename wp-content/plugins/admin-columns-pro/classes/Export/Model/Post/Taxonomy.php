<?php

namespace ACP\Export\Model\Post;

use AC;
use ACP\Export\Model;

/**
 * Taxonomy (default column) exportability model
 * @property AC\Column\Taxonomy $column
 * @since 4.1
 */
class Taxonomy extends Model {

	public function __construct( $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$terms = wp_get_post_terms( $id, $this->column->get_taxonomy(), [ 'fields' => 'names' ] );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}

		return implode( ', ', $terms );
	}

}