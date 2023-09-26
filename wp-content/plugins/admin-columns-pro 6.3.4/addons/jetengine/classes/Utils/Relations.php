<?php

namespace ACA\JetEngine\Utils;

final class Relations {

	const RELATION_ONE_TO_ONE = 'one_to_one';
	const RELATION_ONE_TO_MANY = 'one_to_many';
	const RELATION_MANY_TO_MANY = 'many_to_many';

	/**
	 * @param array  $relation
	 * @param string $current_post_type
	 *
	 * @return string|null
	 */
	static function get_related_post_type( $relation, $current_post_type ) {
		if ( ! $relation || ! isset( $relation['post_type_1'] ) || ! isset( $relation['post_type_2'] ) ) {
			return null;
		}

		return $relation['post_type_1'] === $current_post_type
			? $relation['post_type_2']
			: $relation['post_type_1'];
	}

	/**
	 * @param array  $relation
	 * @param string $current_post_type
	 *
	 * @return boolean
	 */
	static function has_multiple_relations( $relation, $current_post_type ) {
		switch ( $relation['type'] ) {

			case self::RELATION_ONE_TO_MANY:
				return $relation['post_type_1'] === $current_post_type;

			case self::RELATION_MANY_TO_MANY:
				return true;

			case self::RELATION_ONE_TO_ONE:
			default:
				return false;
		}
	}

}