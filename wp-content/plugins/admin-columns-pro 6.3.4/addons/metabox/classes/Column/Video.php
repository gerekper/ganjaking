<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class Video extends Column implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		$results = [];

		foreach ( $value as $file ) {
			$results[] = sprintf( '<a href="%s">%s</a>', $file['src'], $file['title'] );
		}

		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $results, $setting_limit ? $setting_limit->get_value() : false );
	}

	protected function register_settings() {
		parent::register_settings();
		$this->add_setting( new AC\Settings\Column\NumberOfItems( $this ) );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new ACP\Editing\Service\Basic(
				( new ACP\Editing\View\Video() )->set_clear_button( true )->set_multiple( true ),
				( new Editing\StorageFactory() )->create( $this, false )
			);
	}

	public function search() {
		return ( new Search\Factory\Video() )->create( $this );
	}

}