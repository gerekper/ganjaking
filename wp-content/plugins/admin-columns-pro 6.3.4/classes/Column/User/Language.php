<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.2
 */
class Language extends AC\Column\Meta
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-user_default_language' );
		$this->set_label( __( 'Language' ) );
	}

	public function get_meta_key() {
		return 'locale';
	}

	public function get_value( $id ) {
		$translations = ( new AC\Helper\User() )->get_translations_remote();

		$locale = $this->get_raw_value( $id );

		if ( ! isset( $translations[ $locale ] ) ) {
			return ac_helper()->html->tooltip( $this->get_empty_char(), _x( 'Site Default', 'default site language' ) );
		}

		return $translations[ $locale ]['native_name'];
	}

	/**
	 * @return array
	 */
	public function get_language_options() {
		$translations = ( new AC\Helper\User() )->get_translations_remote();
		$options = [];

		foreach ( get_available_languages() as $language ) {
			$options[ $language ] = isset( $translations[ $language ] )
				? $translations[ $language ]['native_name']
				: $language;
		}

		natcasesort( $options );

		return $options;
	}

	public function editing() {
		return new Editing\Service\User\Language( $this->get_language_options() );
	}

	public function filtering() {
		return new Filtering\Model\User\Language( $this );
	}

	public function sorting() {
		return new Sorting\Model\User\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\Comparison\User\Languages( $this->get_language_options() );
	}

}