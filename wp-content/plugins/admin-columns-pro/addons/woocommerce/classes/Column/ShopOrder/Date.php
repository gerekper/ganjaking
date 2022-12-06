<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP;

/**
 * @since 2.0
 */
class Date extends AC\Column
	implements ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'order_date' )
		     ->set_original( true );
	}

	protected function register_settings() {
		$width = $this->get_setting( 'width' );

		$width->set_default( 120 );
		$width->set_default( 'px', 'width_unit' );
	}

	public function filtering() {
		return new ACP\Filtering\Model\Post\Date( $this );
	}

	public function export() {
		return new ACP\Export\Model\Post\Date( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Date\PostDate();
	}

}