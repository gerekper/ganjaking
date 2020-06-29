<?php

namespace ACP\Sorting\Model;

use AC\MetaType;
use ACP;
use ACP\Sorting;
use ACP\Sorting\AbstractModel;

/**
 * For sorting a list table (e.g. post or user) on a meta_key that holds a User ID (single).
 * @since 5.2
 */
class MetaRelatedUserFactory {

	/**
	 * @param string $meta_type     List table type. e.g. post, user, comment or term
	 * @param string $user_property The user property to sort on (e.g. fullname, last name)
	 * @param string $meta_key      The meta key that contains the user ID
	 *
	 * @return AbstractModel|null
	 */
	public function create( $meta_type, $user_property, $meta_key ) {

		switch ( $meta_type ) {
			case MetaType::POST :
				return ( new Sorting\Model\Post\MetaRelatedUserFactory() )->create( $user_property, $meta_key );
			case MetaType::USER :
				return ( new Sorting\Model\User\MetaRelatedUserFactory() )->create( $user_property, $meta_key );
		}

		return null;
	}

}