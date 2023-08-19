<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Editing\StorageFactory;
use ACA\MetaBox\Search;
use ACP;
use WP_Term;

class Taxonomy extends Column implements
	ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function format_single_value( $value, $id = null ) {
		if ( $value instanceof WP_Term ) {
			return $this->format_term( $value );
		}

		return $this->get_empty_char();
	}

	public function format_term( WP_Term $term ) {
		return $this->get_formatted_value( $term, $term );
	}

	public function get_taxonomy() {
		return $this->get_field_setting( 'taxonomy' );
	}

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\Term( $this ) );
		$this->add_setting( new AC\Settings\Column\TermLink( $this ) );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new Editing\Service\Taxonomy( ( new StorageFactory() )->create( $this ), $this->get_taxonomy() );
	}

	public function search() {
		return ( new Search\Factory\Taxonomy() )->create( $this );
	}

}