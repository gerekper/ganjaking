<?php

namespace ACP\Editing\Model\Taxonomy;

use ACP\Editing\Model;

class Description extends Model\Taxonomy {

	public function get_edit_value( $id ) {
		return ac_helper()->taxonomy->get_term_field( 'description', $id, $this->column->get_taxonomy() );
	}

	public function get_view_settings() {
		return [
			'type' => 'textarea',
		];
	}

	public function save( $id, $value ) {
		return $this->update_term( $id, [ 'description' => $value ] );
	}

}