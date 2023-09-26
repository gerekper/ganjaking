<?php

namespace ACA\MetaBox\Column;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class Checkbox extends ACA\MetaBox\Column
	implements ACP\Search\Searchable, ACP\Editing\Editable {

	public function format_single_value( $value, $id = null ) {
		return $value
			? ac_helper()->icon->yes()
			: ac_helper()->icon->no();
	}

	public function get_multiple_values( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return $this->get_empty_char();
		}

		$values = [];
		$number_checks = count( (array) $raw_value );

		for ( $i = 0; $i < $number_checks; $i++ ) {
			$values[] = ac_helper()->icon->yes();
		}

		return implode( ', ', $values );
	}

	public function editing() {
		return $this->is_clonable()
			? false
			: new ACP\Editing\Service\Basic(
				( new ACP\Editing\View\Toggle( new ToggleOptions( new Option( '' ), new Option( '1' ) ) ) ),
				( new Editing\StorageFactory() )->create( $this )
			);
	}

	public function search() {
		return ( new Search\Factory\Meta() )->create( $this );
	}

}