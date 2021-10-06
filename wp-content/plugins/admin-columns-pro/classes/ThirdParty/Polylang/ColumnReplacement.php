<?php

namespace ACP\ThirdParty\Polylang;

use AC;
use AC\Registrable;
use ACP\ThirdParty\Polylang\Column\Language;

class ColumnReplacement implements Registrable {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	/**
	 * @var []
	 */
	private $polylang_columns;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
		$this->polylang_columns = [];
	}

	public function register() {
		add_filter( $this->list_screen->get_heading_hookname(), [ $this, 'set_dynamic_columns' ], 199 );
		add_filter( $this->list_screen->get_heading_hookname(), [ $this, 're_add_dynamic_columns' ], 201 );
		add_filter( $this->list_screen->get_heading_hookname(), [ $this, 'remove_placeholder_columns' ], 202 );
	}

	public function set_dynamic_columns( $headings ) {
		foreach ( $headings as $key => $label ) {
			if ( strpos( $key, 'language_' ) !== false ) {
				$this->polylang_columns[ $key ] = $label;
			}
		}

		return $headings;
	}

	private function get_placeholder_column_key() {
		$columns = $this->get_placeholder_columns();

		return empty( $columns ) ? null : reset( $columns );
	}

	private function get_placeholder_columns() {
		$columns = array_filter( $this->list_screen->get_columns(), function ( $column ) {
			return $column instanceof Language;
		} );

		return array_keys( $columns );
	}

	public function remove_placeholder_columns( $headings ) {
		foreach ( $this->get_placeholder_columns() as $key ) {
			if ( array_key_exists( $key, $headings ) ) {
				unset( $headings[ $key ] );
			}
		}

		return $headings;
	}

	public function re_add_dynamic_columns( $headings ) {
		$replacement_key = $this->get_placeholder_column_key();

		return $replacement_key ? $this->replace_placeholder_column( $headings, $replacement_key ) : $headings;
	}

	private function replace_placeholder_column( $headings, $replacement_key ) {
		foreach ( $headings as $key => $label ) {
			if ( $replacement_key === $key ) {
				$index = array_search( $replacement_key, array_keys( $headings ) );

				$headings = array_slice( $headings, 0, $index, true ) + $this->polylang_columns + array_slice( $headings, $index, count( $headings ) - $index, true );
			}
		}

		return $headings;
	}

}