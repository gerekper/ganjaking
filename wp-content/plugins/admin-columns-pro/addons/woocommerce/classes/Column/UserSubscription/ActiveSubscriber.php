<?php

namespace ACA\WC\Column\UserSubscription;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP\Export\Exportable;
use ACP\Search\Searchable;

class ActiveSubscriber extends AC\Column
	implements Searchable, Exportable {

	public function __construct() {
		$this->set_type( 'woocommerce_active_subscriber' )
		     ->set_original( true );
	}

	public function get_value( $user_id ) {
		return null;
	}

	public function search() {
		return new Search\UserSubscription\ActiveSubscriber();
	}

	public function export() {
		return new Export\UserSubscription\ActiveSubscriber( $this );
	}

}