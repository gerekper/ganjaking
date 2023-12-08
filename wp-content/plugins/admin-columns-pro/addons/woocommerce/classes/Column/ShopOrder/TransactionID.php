<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Search;
use ACP\Sorting;

class TransactionID extends AC\Column\Meta
	implements Sorting\Sortable, Search\Searchable, Formattable {

	use ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-transaction_id' )
		     ->set_label( __( 'Transaction ID', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_transaction_id';
	}

	public function get_value( $post_id ) {
		$transaction_id = $this->get_raw_value( $post_id );

		if ( ! $transaction_id ) {
			return $this->get_empty_char();
		}

		return $transaction_id;
	}

	public function sorting() {
		return new Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key() );
	}

}