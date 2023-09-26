<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Search;
use ACP;

class LowOnStock extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-wc-low_on_stock' )
		     ->set_label( __( 'Low on Stock', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );
		$stock = $product->get_stock_quantity();

		if ( ! $stock ) {
			return $this->get_empty_char();
		}

		$stock_treshold = $this->get_low_on_stock_amount( $id );
		$threshold_display = ac_helper()->html->tooltip( $stock_treshold, __( 'Low stock threshold', 'woocommerce' ) );

		if ( $stock > $stock_treshold ) {
			return $threshold_display;
		}

		$low_on_stock = ac_helper()->icon->dashicon( [
			'icon'    => 'warning',
			'tooltip' => sprintf( __( 'Current stock of %s is lower than the set threshold of  %s', 'codepress-admin-columns' ), $stock, $stock_treshold ),
		] );

		return sprintf( '%s %s', $threshold_display, $low_on_stock );
	}

	public function get_meta_key() {
		return '_low_stock_amount';
	}

	private function get_low_on_stock_amount( $post_id ) {
		$amount = $this->get_raw_value( $post_id );

		if ( ! $amount ) {
			$amount = get_option( 'woocommerce_notify_low_stock_amount' );
		}

		return $amount;
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			new ACP\Editing\View\Number(),
			new ACP\Editing\Storage\Post\Meta( $this->get_meta_key() )
		);
	}

	public function search() {
		return new Search\Product\LowOnStock();
	}

}