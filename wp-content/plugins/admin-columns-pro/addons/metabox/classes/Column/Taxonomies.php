<?php

namespace ACA\MetaBox\Column;

use AC\Settings;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Editing\StorageFactory;
use WP_Term;

class Taxonomies extends Taxonomy {

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $value as $term ) {
			if ( $term instanceof WP_Term ) {
				$values[] = $this->format_term( $term );
			}
		}

		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $values, $setting_limit ? $setting_limit->get_value() : false );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new Editing\Service\Taxonomies(
				( new StorageFactory() )->create( $this, false ),
				$this->get_taxonomy()
			);
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new Settings\Column\NumberOfItems( $this ) );
	}

}