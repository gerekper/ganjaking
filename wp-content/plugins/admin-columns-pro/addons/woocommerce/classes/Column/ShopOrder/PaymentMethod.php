<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;
use WC_Payment_Gateway;

class PaymentMethod extends AC\Column\Meta
	implements ACP\Sorting\Sortable, ACP\Filtering\Filterable, ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-payment_method' )
		     ->set_label( __( 'Payment Method', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_payment_method_title';
	}

	public function get_value( $post_id ) {
		$title = $this->get_payment_method( get_post_meta( $post_id, '_payment_method', true ) );

		if ( ! $title ) {
			$title = get_post_meta( $post_id, '_payment_method_title', true );
		}

		if ( ! $title ) {
			return $this->get_empty_char();
		}

		return $title;
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function filtering() {
		return new Filtering\ShopOrder\PaymentMethod( $this );
	}

	public function search() {
		return new Search\ShopOrder\PaymentMethod();
	}

	public function editing() {
		return new Editing\ShopOrder\PaymentMethod();
	}

	private function get_payment_method( $method ) {

		/* @var WC_Payment_Gateway[] $payment_gateways */
		$payment_gateways = WC()->payment_gateways()->payment_gateways();

		if ( ! isset( $payment_gateways[ $method ] ) ) {
			return false;
		}

		return $payment_gateways[ $method ]->get_title();
	}

}