<?php

namespace ACA\GravityForms\Column\Entry\Original;

use AC;
use ACA\GravityForms\Search;
use ACP;

class TransactionId extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'field_id-transaction_id' );
	}

	public function search() {
		return new Search\Comparison\Entry\TextColumn( 'transaction_id' );
	}

}