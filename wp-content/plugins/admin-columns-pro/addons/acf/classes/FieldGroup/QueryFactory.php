<?php

namespace ACA\ACF\FieldGroup;

use AC\ListScreen;
use ACA\ACF\FieldGroup;
use ACP;

final class QueryFactory {

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return Query|null
	 */
	public function create( ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ACP\ListScreen\Media:
				return new FieldGroup\Location\Media();
			case $list_screen instanceof ACP\ListScreen\Post:
				return new FieldGroup\Location\Post( $list_screen->get_post_type() );
			case $list_screen instanceof ACP\ListScreen\User:
				return new FieldGroup\Location\User();
			case $list_screen instanceof ACP\ListScreen\Taxonomy:
				return new FieldGroup\Location\Taxonomy();
			case $list_screen instanceof ACP\ListScreen\Comment:
				return new FieldGroup\Location\Comment();
		}

		return null;
	}

}