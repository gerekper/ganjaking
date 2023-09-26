<?php

namespace ACA\MetaBox\Editing\Storage;

use RWMB_Taxonomy_Field;
use WP_Term;

class TermField extends Field {

	public function get( int $id ) {
		return $this->single
			? $this->get_single_term( $id )
			: $this->get_multiple_terms( $id );
	}

	public function get_single_term( $id ): ?int {
		$term = rwmb_get_value( $this->meta_key, [ 'object_type' => $this->meta_type->get() ], $id );

		return $term instanceof WP_Term
			? $term->term_id
			: null;
	}

	public function get_multiple_terms( $id ): array {
		$terms = rwmb_get_value( $this->meta_key, [ 'object_type' => $this->meta_type->get() ], $id );

		$result = [];
		foreach ( $terms as $term ) {
			if ( $term instanceof WP_Term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function update( int $id, $data ): bool {
		return $this->single
			? $this->update_single_term( $id, $data )
			: $this->update_multiple_terms( $id, $data );
	}

	private function update_single_term( $id, $value ): bool {
		if ( $value ) {
			$term = get_term( $value );
			$value = $term->slug ?? '';
		}

		RWMB_Taxonomy_Field::save( $value, null, $id, $this->field_settings );

		return true;
	}

	private function update_multiple_terms( $id, $value ): bool {
		$value = array_map( 'intval', (array) $value );

		RWMB_Taxonomy_Field::save( $value, null, $id, $this->field_settings );

		return true;
	}

}