<?php

class WoocommerceGpfFeedGoogleLocalProducts extends WoocommerceGpfFeedGoogle {

	/*
	 * Output the "title" element in the feed intro.
	 */
	protected function render_feed_title() {
		echo '    <title>' . $this->esc_xml( $this->store_info->blog_name . ' Local Products' ) . "</title>\n";
	}

	/**
	 * Generate the item ID in the feed for an item.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_item_id( $feed_item ) {
		return '      <g:itemid>' . $feed_item->guid . "</g:itemid>\n" .
			   '      <g:webitemid>' . $feed_item->guid . "</g:webitemid>\n";
	}

	/**
	 * Generate the link for a product.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_link( $feed_item ) {
		return '';
	}
}
