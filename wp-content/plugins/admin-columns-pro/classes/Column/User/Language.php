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

	private $translations = null;

	public function __construct() {
		$this->set_type( 'column-user_default_language' );
		$this->set_label( __( 'Language' ) );
	}

	public function get_meta_key() {
		return 'locale';
	}

	public function get_value( $id ) {
		$translations = $this->get_translations();
		$locale = $this->get_raw_value( $id );

		if ( ! isset( $translations[ $locale ] ) ) {
			return ac_helper()->html->tooltip( $this->get_empty_char(), _x( 'Site Default', 'default site language' ) );
		}

		return $translations[ $locale ]['native_name'];
	}

	/**
	 * @return array
	 */
	private function get_translations() {
		if ( null === $this->translations ) {
			require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
			$this->translations = wp_get_available_translations();
		}

		return $this->translations;
	}

	/**
	 * @param bool $include_default
	 *
	 * @return array
	 */
	public function get_language_options( $include_default = true ) {
		$translations = $this->get_translations();
		$languages = get_available_languages();
		$options = [];

		if ( $include_default ) {
			$options[''] = _x( 'Site Default', 'default site language' );
		}

		foreach ( $languages as $language ) {
			$options[ $language ] = $translations[ $language ]['native_name'];
		}

		return $options;
	}

	public function editing() {
		return new Editing\Model\User\Language( $this );
	}

	public function filtering() {
		return new Filtering\Model\User\Language( $this );
	}

	public function sorting() {
		return new Sorting\Model\User\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\Comparison\User\Languages( $this->get_language_options( false ) );
	}

}