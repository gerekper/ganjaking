<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACA\WC\Filtering;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.0
 * @Deprecated
 */
class CustomerMessage extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'customer_message' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		return wc_get_order( $post_id )->get_customer_note();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Excerpt();
	}

	public function filtering() {
		return new Filtering\ShopOrder\CustomerMessage( $this );
	}

	public function export() {
		return new Export\ShopOrder\CustomerMessage( $this );
	}

}