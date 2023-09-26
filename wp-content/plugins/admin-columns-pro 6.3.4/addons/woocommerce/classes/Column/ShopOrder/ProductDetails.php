<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use WC_Order_Item_Product;

/**
 * @since 1.4
 */
class ProductDetails extends AC\Column implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-product_details' )
		     ->set_label( __( 'Product - Details', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $order_id ) {
		$result = [];

		$order = wc_get_order( $order_id );

		$order_items = $order->get_items();

		if ( 0 === count( $order_items ) ) {
			return $this->get_empty_char();
		}

		foreach ( $order_items as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			if ( ! $item->get_quantity() ) {
				continue;
			}

			$output = false;

			$quantity = '<span class="qty">' . absint( $item->get_quantity() ) . 'x</span>';

			$product = $item->get_product();

			if ( $product ) {
				$edit_link_id = $product->get_parent_id() ?: $product->get_id();
				$output .= '<strong>' . $quantity . ac_helper()->html->link( get_edit_post_link( $edit_link_id ), $product->get_name() ) . '</strong>';

				if ( wc_product_sku_enabled() && $product->get_sku() ) {
					$output .= '<div class="meta">' . __( 'SKU', 'woocommerce' ) . ': ' . $product->get_sku() . '</div>';
				}

			} else {
				$output .= '<strong>' . $quantity . esc_html( $item->get_name() ) . '</strong>';
			}

			$meta = $item->get_formatted_meta_data( '_', true );
			$meta_values = [];

			foreach ( $meta as $info ) {
				$meta_values[] = sprintf('<span class="ac-wc-meta-%s">', $info->key ) . $info->display_key . ': ' . strip_tags( $info->display_value ) .'</span>';
			}

			$output .= '<div class="meta">' . implode( '<span class="ac-wc-meta-sep">, </span>', $meta_values ) . '</div>';

			$result[] = '<div class="ac-wc-product">' . $output . '</div>';
		}

		return implode( $result );
	}

	public function get_raw_value( $order_id ) {
		$order = wc_get_order( $order_id );
		$product_ids = [];

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$product_ids[] = $item->get_product_id();
		}

		return $product_ids;
	}

	public function export() {
		return new Export\ShopOrder\ProductCount();
	}

	public function search() {
		return new Search\ShopOrder\Product( $this->get_post_type() );
	}

}