<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\AbstractModel;

/**
 * Sorts a list table by pre sorted fields that are associated with the supplied meta key.
 * @since 5.2
 */
class MetaMappingFactory {

	/**
	 * @param string $meta_type e.g. post, user, comment or taxonomy
	 * @param string $meta_key  e.g. 'my_custom_field_key'
	 * @param array  $sorted_fields
	 *
	 * @return AbstractModel
	 */
	public function create( $meta_type, $meta_key, $sorted_fields ) {
		switch ( $meta_type ) {
			case MetaType::POST :
				return new Post\MetaMapping( $meta_key, $sorted_fields );
			case MetaType::USER :
				return new User\MetaMapping( $meta_key, $sorted_fields );
			case MetaType::COMMENT :
				return new Comment\MetaMapping( $meta_key, $sorted_fields );
			case MetaType::TERM :
				return new Taxonomy\MetaMapping( $meta_key, $sorted_fields );
			default :
				return new Disabled();
		}
	}

}