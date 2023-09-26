<?php

namespace ACA\WC\Column\Product;

use AC;
use ACP;

/**
 * @since 1.2
 */
class Name extends AC\Column
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'name' )
		     ->set_original( true );
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			new ACP\Editing\View\Text(),
			new ACP\Editing\Storage\Post\Field( 'post_title' )
		);
	}

	public function export() {
		return new ACP\Export\Model\Post\Title();
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Title();
	}

}