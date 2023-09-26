<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA;
use ACA\MetaBox\Editing\StorageFactory;
use ACA\MetaBox\Search;
use ACA\MetaBox\Sorting;
use ACP;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\Editing\Service;
use ACP\Editing\View;

class Date extends ACA\MetaBox\Column implements ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\Date( $this ) );
	}

	public function get_saved_format() {
		$save_format = $this->get_field_setting( 'save_format' );

		if ( ! $save_format ) {
			$save_format = $this->is_timestamp() ? 'U' : 'Y-m-d';
		}

		return $save_format;
	}

	protected function is_timestamp() {
		return $this->get_field_setting( 'timestamp' );
	}

	public function sorting() {
		return ( new Sorting\Factory\Date )->create( $this );
	}

	public function search() {
		return ( new Search\Factory\Date )->create( $this );
	}

	public function editing() {
		return new Service\Date(
			( new View\Date() )->set_clear_button( true ),
			( new StorageFactory() )->create( $this ),
			$this->get_saved_format()
		);
	}

	public function conditional_format(): ?ConditionalFormat\FormattableConfig {
		return new ConditionalFormat\FormattableConfig(
			new DateFormatter\FormatFormatter( $this->get_saved_format() )
		);
	}

}