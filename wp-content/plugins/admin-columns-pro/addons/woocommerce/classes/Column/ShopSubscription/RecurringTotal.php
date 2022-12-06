<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.4
 */
class RecurringTotal extends AC\Column
	implements ACP\Search\Searchable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'recurring_total' )
		     ->set_original( true );
	}

	public function search() {
		return new Search\ShopOrder\Total();
	}

	public function get_value( $id ) {
		$this->get_raw_value( $id );
	}

	public function get_raw_value( $id ) {
		return wcs_get_subscription( $id )->get_formatted_order_total();
	}

	public function export() {
		return new ACP\Export\Model\StrippedRawValue( $this );
	}

}