<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Export;

/**
 * @since 4.0
 */
class Slug extends AC\Column
	implements Editing\Editable, Export\Exportable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'slug' );
	}

	public function editing() {
		return new Editing\Service\Taxonomy\Slug( $this->get_taxonomy() );
	}

	public function export() {
		return new Export\Model\Term\Slug( $this );
	}

}