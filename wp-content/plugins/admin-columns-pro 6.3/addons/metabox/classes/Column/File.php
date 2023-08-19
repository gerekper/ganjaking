<?php

namespace ACA\MetaBox\Column;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing;
use ACA\MetaBox\Search;
use ACP;

class File extends Column implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		$results = [];
		foreach ( $value as $data ) {
			$results[] = ac_helper()->html->tooltip( sprintf( '<a href="%s" download>%s</a>', $data['url'], $data['name'] ), $data['url'] );
		}

		return implode( ', ', $results );
	}

	public function is_multiple() {
		return true;
	}

	public function search() {
		return ( new Search\Factory\Meta )->create( $this );
	}

	public function editing() {
		return ( new Editing\ServiceFactory\File )->create( $this );
	}

}