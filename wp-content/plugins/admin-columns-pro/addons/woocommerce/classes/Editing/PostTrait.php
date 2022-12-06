<?php declare( strict_types=1 );

namespace ACA\WC\Editing;

trait PostTrait {

	/**
	 * @param int[]|int $post_ids
	 * @param string    $field
	 *
	 * @return array [ int $post_id => string $post_field ]
	 */
	protected function get_editable_posts_values( $post_ids, string $field = 'post_title' ) : array {
		$value = [];

		if ( ! $post_ids ) {
			return [];
		}

		if ( is_scalar( $post_ids ) ) {
			$post_ids = [ $post_ids ];
		}

		if ( ! is_array( $post_ids ) ) {
			return [];
		}

		foreach ( $post_ids as $id ) {
			if ( ! get_post( $id ) ) {
				continue;
			}

			$value[ $id ] = get_post_field( $field, (int) $id ) ?: $id;
		}

		return $value;
	}

}