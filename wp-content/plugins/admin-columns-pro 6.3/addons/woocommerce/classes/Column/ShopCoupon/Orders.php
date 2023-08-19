<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Export;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use ACP\ConditionalFormat\Formatter\SanitizedFormatter;

/**
 * @since 2.0
 */
class Orders extends AC\Column implements AC\Column\AjaxValue, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_orders' )
		     ->set_label( __( 'Orders', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order_ids = $this->get_raw_value( $id );

		if ( ! $order_ids ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', count( $order_ids ) ), count( $order_ids ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	private function get_order_ids_by_coupon_id( $id ): array {
		global $wpdb;

		$table = $wpdb->prefix . 'wc_order_coupon_lookup';

		$sql = "
			SELECT DISTINCT(order_id)
			FROM {$table}
			WHERE coupon_id = %d
		";

		return $wpdb->get_col( $wpdb->prepare( $sql, $id ) );
	}

	public function get_raw_value( $id ) {
		return $this->get_order_ids_by_coupon_id( $id );
	}

	public function get_ajax_value( $id ) {
		$values = [];
		foreach ( $this->get_order_ids_by_coupon_id( $id ) as $order_id ) {
			$order = wc_get_order( $order_id );
			$values[] = ac_helper()->html->link( $order->get_edit_order_url(), '#' . $order_id );
		}

		return implode( ', ', $values );
	}

	public function export() {
		return new Export\ShopCoupon\Orders( $this );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new FilterHtmlFormatter( SanitizedFormatter::from_ignore_strings( new IntegerFormatter() ) ) );
	}

}