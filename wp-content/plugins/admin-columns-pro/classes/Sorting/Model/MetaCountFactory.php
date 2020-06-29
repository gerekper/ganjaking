<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP\Sorting\AbstractModel;

/**
 * Sort a user list table on the number of times the meta_key is used by an object.
 * @since 5.2
 */
class MetaCountFactory {

	/**
	 * @param string $meta_type
	 * @param string $meta_key
	 *
	 * @return AbstractModel
	 */
	public function create( $meta_type, $meta_key ) {

		switch ( $meta_type ) {
			case MetaType::POST :
				return new Post\MetaCount( $meta_key );
			case MetaType::USER :
				return new User\MetaCount( $meta_key );
			case MetaType::COMMENT :
				return new Comment\MetaCount( $meta_key );
			case MetaType::TERM :
				return new Taxonomy\MetaCount( $meta_key );
			default :
				return new Disabled();
		}
	}

}