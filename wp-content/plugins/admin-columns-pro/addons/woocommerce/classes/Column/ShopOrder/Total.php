<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;

/**
 * @since 2.0
 */
class Total extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'order_total' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	protected function register_settings() {
		$width = $this->get_setting( 'width' );

		$width->set_default( 90 );
		$width->set_default( 'px', 'width_unit' );
	}

	public function get_meta_key() {
		return '_order_total';
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Decimal( $this->get_meta_key(), AC\MetaType::POST );
	}

}