<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.0
 */
class ExpiryDate extends AC\Column\Meta
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'expiry_date' )
		     ->set_original( true );
	}

	public function get_meta_key() {
		return 'date_expires';
	}

	public function get_value( $id ) {
		return null;
	}

	public function sorting() {
		return new Sorting\ShopCoupon\ExpiryDate( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\ShopCoupon\ExpiryDate();
	}

	public function search() {
		return new Search\Meta\Date\Timestamp( $this->get_meta_key(), AC\MetaType::POST );
	}

}