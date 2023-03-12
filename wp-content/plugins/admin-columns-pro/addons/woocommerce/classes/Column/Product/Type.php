<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Export;
use ACP;

/**
 * @since 2.0
 */
class Type extends AC\Column
	implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'product_type' )
		     ->set_original( true );
	}

	public function export() {
		return new Export\Product\Type();
	}

}