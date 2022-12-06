<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.4
 */
class Status extends AC\Column
	implements ACP\Search\Searchable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'status' )
		     ->set_original( true );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Status( 'shop_subscription' );
	}

	public function export() {
		return new Export\ShopSubscription\Status( $this );
	}

}