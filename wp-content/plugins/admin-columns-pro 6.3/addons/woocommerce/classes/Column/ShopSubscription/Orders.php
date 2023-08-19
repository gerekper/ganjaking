<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.4
 */
class Orders extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'orders' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	/**
	 * @param int $id
	 *
	 * @return int
	 */
	public function get_raw_value( $id ) {
		return count( wcs_get_subscription( $id )->get_related_orders() );
	}

	public function export() {
		return new ACP\Export\Model\RawValue( $this );
	}

}