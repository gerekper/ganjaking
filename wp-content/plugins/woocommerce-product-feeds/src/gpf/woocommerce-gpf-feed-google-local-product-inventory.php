<?php

class WoocommerceGpfFeedGoogleLocalProductInventory extends WoocommerceGpfFeedGoogle {

	/*
	 * Output the "title" element in the feed intro.
	 */
	protected function render_feed_title() {
		echo '    <title>' . $this->esc_xml( $this->store_info->blog_name . ' Local Product Inventory' ) . "</title>\n";
	}

	/**
	 * Generate the item ID in the feed for an item.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_item_id( $feed_item ) {
		return '      <g:itemid>' . $feed_item->guid . "</g:itemid>\n";
	}

	/**
	 * Generate the output for an individual item, and return it
	 *
	 * @access public
	 *
	 * @param object $feed_item The information about the item.
	 *
	 * @return  string             The rendered output for this item.
	 */
	public function render_item( $feed_item ) {
		// Google do not allow free items in the feed.
		if ( empty( $feed_item->price_inc_tax ) ) {
			return '';
		}
		$output  = '';
		$output .= "    <item>\n";
		$output .= $this->generate_item_id( $feed_item );
		$output .= $this->render_prices( $feed_item );

		// Shop code
		$shop_code = ! empty( $this->settings['shop_code'] ) ?
			$this->settings['shop_code'] :
			'shop_001';
		$shop_code = apply_filters( 'woocommerce_gpf_googlelocalproductinventory_store_code', $shop_code, $feed_item );
		$output   .= '<g:store_code>' . $shop_code . '</g:store_code>';

		// Stock quantity
		$stock_quantity = ! empty( $feed_item->stock_quantity ) ?
			$feed_item->stock_quantity :
			apply_filters( 'woocommerce_gpf_local_product_inventory_default_stock_qty', 10, $feed_item );
		if ( ! $feed_item->is_in_stock ) {
			$stock_quantity = 0;
		}
		$output .= '<g:quantity>' . $stock_quantity . '</g:quantity>';

		// Calculate availability.
		$availability = ! empty( $feed_item->additional_elements['availability'][0] ) ?
			$feed_item->additional_elements['availability'][0] :
			'';
		// Only send the value if the product is in stock, otherwise force to "out of stock".
		if ( ! $feed_item->is_in_stock ) {
			$availability = 'out of stock';
		}
		$output .= '<g:availability>' . $availability . '</g:availability>';

		$output .= "    </item>\n";

		return $output;
	}

}
