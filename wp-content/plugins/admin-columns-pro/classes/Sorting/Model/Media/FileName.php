<?php

namespace ACP\Sorting\Model\Media;

use AC;
use ACP\Sorting\Model;

class FileName extends Model\Meta {

	public function get_sorting_vars() {
		$ids = $this->strategy->get_results( parent::get_sorting_vars() );

		$query = new AC\Meta\QueryColumn( $this->column );
		$query->select( 'id, meta_value' )
		      ->where_in( $ids );

		if ( acp_sorting_show_all_results() ) {
			$query->left_join();
		} else {
			$query->where( 'meta_value', '!=', '' );
		}

		$values = [];

		foreach ( $query->get() as $value ) {
			$values[ $value->id ] = strtolower( basename( $value->meta_value ) );
		}

		return [
			'ids' => $this->sort( $values ),
		];
	}

}