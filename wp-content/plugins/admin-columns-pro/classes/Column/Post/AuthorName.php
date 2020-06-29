<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Settings;
use ACP\Sorting;

class AuthorName extends AC\Column\Post\AuthorName
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Export\Exportable, Search\Searchable {

	public function sorting() {
		return ( new Sorting\Model\Post\AuthorFactory() )->create( $this->get_user_setting()->get_value(), $this );
	}

	public function editing() {
		if ( 'custom_field' === $this->get_user_setting()->get_value() ) {
			return new Editing\Model\Disabled( $this );
		}

		return new Editing\Model\Post\Author( $this );
	}

	public function filtering() {
		if ( 'custom_field' === $this->get_user_setting()->get_value() ) {
			return new Filtering\Model\Disabled( $this );
		}

		if ( 'roles' === $this->get_user_setting()->get_value() ) {
			return new Filtering\Model\Post\Roles( $this );
		}

		return new Filtering\Model\Post\AuthorName( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\User( $this ) );
	}

	/**
	 * @return AC\Settings\Column\User
	 */
	private function get_user_setting() {
		return $this->get_setting( AC\Settings\Column\User::NAME );
	}

	public function search() {
		switch ( $this->get_user_setting()->get_value() ) {
			case AC\Settings\Column\User::PROPERTY_DISPLAY_NAME :
			case AC\Settings\Column\User::PROPERTY_FIRST_NAME :
			case AC\Settings\Column\User::PROPERTY_LAST_NAME :
			case AC\Settings\Column\User::PROPERTY_NICKNAME :
			case AC\Settings\Column\User::PROPERTY_LOGIN :
			case AC\Settings\Column\User::PROPERTY_EMAIL :
			case AC\Settings\Column\User::PROPERTY_FULL_NAME :
			case AC\Settings\Column\User::PROPERTY_NICENAME :
				return new Search\Comparison\Post\Author( $this->get_post_type() );
			default:
				return false;
		}

	}

}