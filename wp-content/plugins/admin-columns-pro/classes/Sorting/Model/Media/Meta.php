<?php

namespace ACP\Sorting\Model\Media;

use AC;
use ACP\Sorting\Model;

abstract class Meta extends Model\Meta {

	protected function get_meta_values() {
		$ids = $this->strategy->get_results( parent::get_sorting_vars() );

		$query = new AC\Meta\QueryColumn( $this->column );
		$query->select( 'id, meta_value' )
		      ->where_in( $ids )
		      ->order_by( 'meta_value', $this->get_order() );

		if ( acp_sorting_show_all_results() ) {
			$query->left_join();
		}

		$values = [];

		foreach ( $query->get() as $result ) {
			if ( $this->column->is_serialized() ) {
				$result->meta_value = unserialize( $result->meta_value );
			}

			$values[ $result->id ] = $result->meta_value;
		}

		return $values;
	}

	/**
	 * @param array  $meta
	 * @param string $key
	 *
	 * @return false|string
	 */
	protected function get_meta_value( $meta, $key ) {
		if ( empty( $meta ) || ! is_array( $meta ) || ! isset( $meta[ $key ] ) ) {
			return false;
		}

		return $meta[ $key ];
	}

}