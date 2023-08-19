<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Search;
use ACA\WC\Sorting\ShopOrder\ItemCount;
use ACP;

/**
 * @since 2.0
 */
class Purchased extends AC\Column
	implements AC\Column\AjaxValue, ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use FilteredHtmlIntegerFormatterTrait;

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-purchased' )
		     ->set_label( __( 'Purchased', 'codepress-admin-columns' ) );
	}

	/**
	 * @param int $id
	 *
	 * @return bool|string
	 */
	public function get_value( $id ) {
		$count = $this->get_item_count( $id );

		if ( $count <= 0 ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%d item', '%d items', $count, 'codepress-admin-columns' ), $count );

		return ac_helper()->html->get_ajax_modal_link( $count, [
			'title'     => get_the_title( $id ),
			'edit_link' => get_edit_post_link( $id ),
			'class'     => "-nopadding",
		] );
	}

	/**
	 * @param int $order_id
	 *
	 * @return int
	 */
	private function get_item_count( $order_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "
                SELECT SUM( oim.meta_value )
                FROM {$wpdb->prefix}woocommerce_order_items AS oi
                  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
                WHERE oi.order_item_type = 'line_item'
                  AND oim.meta_key = '_qty'
                  AND oi.order_id = %d;
                  ", $order_id );

		return absint( $wpdb->get_var( $sql ) );
	}

	/**
	 * @param int $order_id
	 */
	public function get_ajax_value( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$order_items = $order->get_items();

		if ( count( $order_items ) <= 0 ) {
			return false;
		}

		$view = new AC\View( [
			'items' => $order_items,
		] );

		echo $view->set_template( 'modal-value/purchased' )->render();
		exit;
	}

	public function get_raw_value( $id ) {
		return $this->get_item_count( $id );
	}

	public function sorting() {
		return new ItemCount();
	}

	public function search() {
		return new Search\ShopOrder\ProductCount();
	}
}