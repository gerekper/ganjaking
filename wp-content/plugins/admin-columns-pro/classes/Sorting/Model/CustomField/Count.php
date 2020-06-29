<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model;

class Count extends Model\Meta {

	public function get_sorting_vars() {
		$ids = $this->strategy->get_results( parent::get_sorting_vars() );

		$query = new AC\Meta\QueryColumn( $this->column );
		$query->select( 'id' )->count( 'meta_key' )
		      ->where_in( $ids )
		      ->group_by( 'id' )
		      ->order_by( 'count, id', $this->get_order() );

		if ( acp_sorting_show_all_results() ) {
			$query->left_join();
		}

		$values = [];

		foreach ( $query->get() as $result ) {
			$values[] = $result->id;
		}

		return [
			'ids' => $values,
		];
	}

}