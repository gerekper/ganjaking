<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;
use WC_Coupon;

/**
 * @since 1.3
 */
class CouponsUsed extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-order_coupons_used' )
		     ->set_label( __( 'Coupons Used', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_recorded_coupon_usage_counts';
	}

	public function get_value( $post_id ) {
		$used_coupons = $this->get_raw_value( $post_id );

		if ( ! $used_coupons ) {
			return $this->get_empty_char();
		}

		$coupons = [];

		foreach ( $used_coupons as $code ) {
			$coupon = new WC_Coupon( $code );

			$coupons[] = ac_helper()->html->link( get_edit_post_link( $coupon->get_id() ), $code );
		}

		return implode( ' | ', $coupons );
	}

	public function get_raw_value( $post_id ) {
		$order = wc_get_order( $post_id );

		if ( ! $order ) {
			return [];
		}

		if ( method_exists( $order, 'get_coupon_codes' ) ) {
			return $order->get_coupon_codes();
		}

		return $order->get_used_coupons();
	}

	public function filtering() {
		return new Filtering\ShopOrder\CouponUsed( $this );
	}

	public function search() {
		return new Search\ShopOrder\CouponsUsed();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}