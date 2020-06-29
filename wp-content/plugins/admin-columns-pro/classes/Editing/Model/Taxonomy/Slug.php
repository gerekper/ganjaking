<?php

namespace ACP\Editing\Model\Taxonomy;

use ACP\Editing\Model;

class Slug extends Model\Taxonomy {

	public function get_edit_value( $id ) {
		return ac_helper()->taxonomy->get_term_field( 'slug', $id, $this->get_column()->get_taxonomy() );
	}

	public function save( $id, $value ) {
		return $this->update_term( $id, [ 'slug' => $value ] );
	}

}