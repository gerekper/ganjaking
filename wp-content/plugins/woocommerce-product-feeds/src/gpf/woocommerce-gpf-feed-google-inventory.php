<?php

/**
 * Google feed class - renders the Google feed.
 */
class WoocommerceGpfFeedGoogleInventory extends WoocommerceGpfFeed {

	private $tax_excluded  = false;
	private $tax_attribute = false;

	/**
	 * Constructor. Grab the settings, and add filters if we have stuff to do
	 *
	 * @access public
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfDebugService $debug
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfDebugService $debug
	) {
		parent::__construct( $woocommerce_gpf_common, $debug );
		$this->store_info->feed_url = add_query_arg( 'woocommerce_gpf', 'googleinventory', $this->store_info->feed_url_base );
		if ( ! empty( $this->store_info->base_country ) ) {
			if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ||
				 'CA' === substr( $this->store_info->base_country, 0, 2 ) ||
				 'IN' === substr( $this->store_info->base_country, 0, 2 ) ) {
				$this->tax_excluded = true;
				if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ) {
					$this->tax_attribute = true;
				}
			}
		}
	}

	/**
	 * Render the feed header information
	 *
	 * @access public
	 */
	public function render_header() {

		header( 'Content-Type: application/xml; charset=UTF-8' );
		if ( isset( $_REQUEST['feeddownload'] ) ) {
			header( 'Content-Disposition: attachment; filename="E-Commerce_Product_Inventory.xml"' );
		} else {
			header( 'Content-Disposition: inline; filename="E-Commerce_Product_Inventory.xml"' );
		}

		// Core feed information
		echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
		echo "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:g='http://base.google.com/ns/1.0'>\n";
		echo "  <channel>\n";
		echo '    <title>' . $this->esc_xml( $this->store_info->blog_name . ' Products' ) . "</title>\n";
		echo '    <link>' . $this->store_info->site_url . "</link>\n";
		echo "    <description>This is the WooCommerce Product Inventory feed</description>\n";
		echo '    <generator>WooCommerce Google Product Feed Plugin v' . WOOCOMMERCE_GPF_VERSION . " (https://plugins.leewillis.co.uk/downloads/woocommerce-google-product-feed/)</generator>\n";
		echo "    <atom:link href='" . esc_url( $this->store_info->feed_url ) . "' rel='self' type='application/rss+xml' />\n";

	}


	/**
	 * Generate the output for an individual item
	 *
	 * @access public
	 *
	 * @param object $feed_item The information about the item
	 */
	public function render_item( $feed_item ) {
		// Google do not allow free items in the feed.
		if ( empty( $feed_item->price_inc_tax ) ) {
			return '';
		}
		$output  = '';
		$output .= "    <item>\n";
		$output .= '      <guid>' . $feed_item->guid . "</guid>\n";

		$output .= $this->render_prices( $feed_item );

		if ( count( $feed_item->additional_elements ) ) {
			foreach ( $feed_item->additional_elements as $element_name => $element_values ) {
				foreach ( $element_values as $element_value ) {
					if ( 'availability' === $element_name ) {
						// Google no longer supports "available for order". Mapped this to "in stock" as per
						// specification update September 2014.
						if ( 'available for order' === $element_value ) {
							$element_value = 'in stock';
						}
						// Only send a value if the product is in stock
						if ( ! $feed_item->is_in_stock ) {
							$element_value = 'out of stock';
						}
					}
					if ( 'identifier_exists' === $element_name ) {
						if ( 'included' === $element_value ) {
							if ( ! $this->has_identifier( $feed_item ) ) {
								$output .= ' <g:identifier_exists>FALSE</g:identifier_exists>';
							}
							continue;
						} else {
							continue;
						}
					}
					if ( 'availability_date' === $element_name ) {
						if ( strlen( $element_value ) === 10 ) {
							$tz_offset      = get_option( 'gmt_offset' );
							$element_value .= 'T00:00:00' . sprintf( '%+03d', $tz_offset ) . '00';
						}
					}
					$output .= '      <g:' . $element_name . '>';
					$output .= $this->esc_xml( $element_value );
					$output .= '</g:' . $element_name . ">\n";
				}
			}
		}

		$output .= "    </item>\n";

		return $output;
	}

	/**
	 * Render the applicable price elements.
	 *
	 * @param object $feed_item The feed item to be rendered.
	 */
	private function render_prices( $feed_item ) {

		// Regular price
		if ( $this->tax_excluded ) {
			// Some country prices have to be submitted excluding tax
			$price = number_format( $feed_item->regular_price_ex_tax, 2, '.', '' );
		} else {
			// Others have to be submitted including tax
			$price = number_format( $feed_item->regular_price_inc_tax, 2, '.', '' );
		}
		$output = '      <g:price>' . $price . ' ' . $this->store_info->currency . "</g:price>\n";

		// If there's no sale price, then we're done.
		if ( empty( $feed_item->sale_price_inc_tax ) ) {
			return $output;
		}

		// Otherwise, include the sale_price tag.
		if ( $this->tax_excluded ) {
			$sale_price = number_format( $feed_item->sale_price_ex_tax, 2, '.', '' );
		} else {
			$sale_price = number_format( $feed_item->sale_price_inc_tax, 2, '.', '' );
		}
		$output .= '      <g:sale_price>' . $sale_price . ' ' . $this->store_info->currency . "</g:sale_price>\n";

		// Include start / end dates if provided.
		if ( ! empty( $feed_item->sale_price_start_date ) &&
			 ! empty( $feed_item->sale_price_end_date ) ) {
			$effective_date  = (string) $feed_item->sale_price_start_date;
			$effective_date .= '/';
			$effective_date .= (string) $feed_item->sale_price_end_date;
			$output         .= '      <g:sale_price_effective_date>' . $effective_date . '</g:sale_price_effective_date>';
		}

		return $output;
	}

	/**
	 * Output the feed footer
	 *
	 * @access public
	 */
	public function render_footer() {
		echo "  </channel>\n";
		echo '</rss>';
		exit();
	}

}
