<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

class SKU extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'sku' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return '';
	}

	public function get_meta_key() {
		return '_sku';
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Text() )->set_clear_button( true ),
			new Editing\Storage\Product\Sku()
		);
	}

	public function export() {
		return new Export\Product\SKU();
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key() );
	}

}