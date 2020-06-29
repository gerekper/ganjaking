<?php

namespace ACP\Sorting\Model\User;

use AC;
use ACP\Sorting\AbstractModel;

/**
 * For sorting a user list table on a meta_key that holds a User ID (single).
 * @since 5.2
 */
class MetaRelatedUserFactory {

	/**
	 * @param string $user_property The user property to sort on (e.g. fullname, last name)
	 * @param string $meta_key      The meta key that contains the user ID
	 *
	 * @return AbstractModel|null
	 */
	public function create( $user_property, $meta_key ) {

		switch ( $user_property ) {
			case AC\Settings\Column\User::PROPERTY_ID :
			case AC\Settings\Column\User::PROPERTY_LOGIN :
			case AC\Settings\Column\User::PROPERTY_NICENAME :
			case AC\Settings\Column\User::PROPERTY_EMAIL :
			case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
				return new RelatedMeta\UserField( $user_property, $meta_key );
			case AC\Settings\Column\User::PROPERTY_FULL_NAME :
				return new RelatedMeta\UserMeta( 'last_name', $meta_key );
			case AC\Settings\Column\User::PROPERTY_LAST_NAME :
			case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
			case AC\Settings\Column\User::PROPERTY_NICKNAME :
				return new RelatedMeta\UserMeta( $user_property, $meta_key );
		}

		return null;
	}

}