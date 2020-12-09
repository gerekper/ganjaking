<?php

/**
 * Google feed class - renders the Google feed.
 */
class WoocommerceGpfFeedGoogle extends WoocommerceGpfFeed {

	/**
	 * Whether tax should be excluded from prices.
	 *
	 * @var bool
	 */
	private $tax_excluded = false;

	/**
	 * Whether the tax attribute should be sent.
	 *
	 * @var bool
	 */
	private $tax_attribute = false;

	/**
	 * Whether to hide products from the feed if they do not have images.
	 *
	 * @var bool
	 */
	private $hide_if_no_images = false;

	/**
	 * Array of allowed HTML tags to pass to wp_kses to trim out unsupported markup in the description element.
	 *
	 * @var array
	 */
	private $allowed_description_markup = array();

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

		$this->store_info->feed_url = add_query_arg( 'woocommerce_gpf', 'google', $this->store_info->feed_url_base );

		if ( ! empty( $this->store_info->base_country ) ) {
			if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ||
				 'CA' === substr( $this->store_info->base_country, 0, 2 ) ) {
				$this->tax_excluded = true;
				if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ) {
					$this->tax_attribute = true;
				}
			}
		}
		$this->tax_excluded  = apply_filters( 'woocommerce_gpf_tax_excluded', $this->tax_excluded, $this->store_info );
		$this->tax_attribute = apply_filters( 'woocommerce_gpf_tax_attribute', $this->tax_attribute, $this->store_info );

		$this->hide_if_no_images          = apply_filters( 'woocommerce_gpf_hide_if_no_images_google', false );
		$this->allowed_description_markup = array(
			'strong'   => array(),
			'b'        => array(),
			'i'        => array(),
			'h1'       => array(),
			'h2'       => array(),
			'h3'       => array(),
			'h4'       => array(),
			'h5'       => array(),
			'h6'       => array(),
			'table'    => array(),
			'tr'       => array(),
			'td'       => array(),
			'th'       => array(),
			'p'        => array(),
			'fieldset' => array(),
			'header'   => array(),
			'em'       => array(),
			'ul'       => array(),
			'ol'       => array(),
			'li'       => array(),
			'br'       => array(),
			'sub'      => array(),
			'sup'      => array(),
			'div'      => array(),
			'span'     => array(),
			'dl'       => array(),
			'dt'       => array(),
			'dd'       => array(),
		);
		add_filter( 'woocommerce_gpf_feed_item_google', array( $this, 'enforce_max_lengths' ) );
	}

	/**
	 * Generate a simple list of field and max length from the field config array.
	 *
	 * @return array  Array of max lengths, keyed on field name.
	 *
	 * * @SuppressWarnings(PHPMD.UndefinedVariable)
	 */
	private function get_field_max_lengths() {

		static $max_lengths = array();
		if ( ! empty( $max_lengths ) ) {
			return $max_lengths;
		}
		// Max lengths for core fields
		$max_lengths['title']       = 150;
		$max_lengths['description'] = 5000;
		// Max lengths for non-core fields
		foreach ( $this->woocommerce_gpf_common->product_fields as $field_name => $field_config ) {
			if ( isset( $field_config['google_len'] ) ) {
				$max_lengths[ $field_name ] = $field_config['google_len'];
			}
		}

		return $max_lengths;
	}

	/**
	 * Enforce maximum lengths of fields in the Google field.
	 */
	public function enforce_max_lengths( $feed_item ) {
		$max_lengths = $this->get_field_max_lengths();
		foreach ( $max_lengths as $field_name => $length ) {
			if ( ! empty( $feed_item->$field_name ) ) {
				$feed_item->$field_name = mb_substr( $feed_item->$field_name, 0, $length );
			}
			if ( ! empty( $feed_item->additional_elements[ $field_name ] ) ) {
				foreach ( $feed_item->additional_elements[ $field_name ] as $key => $value ) {
					$feed_item->additional_elements[ $field_name ][ $key ] = mb_substr( $value, 0, $length );
				}
			}
		}

		return $feed_item;
	}

	/**
	 * Figure out if the item has the identifiers it requires
	 *
	 * @param object $item The item being rendered
	 *
	 * @return boolean       True if the item doesn't need identifiers, or has the required
	 *                       identifiers. False if not.
	 */
	private function has_identifier( $item ) {
		if ( empty( $item->additional_elements['brand'] ) ) {
			return false;
		}
		if ( empty( $item->additional_elements['gtin'] ) &&
			 empty( $item->additional_elements['mpn'] ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Render the feed header information
	 *
	 * @access public
	 */
	public function render_header() {
		$this->startts  = microtime( true );
		$this->startmem = memory_get_peak_usage();
		header( 'Content-Type: application/xml; charset=UTF-8' );
		if ( isset( $_REQUEST['feeddownload'] ) ) {
			header( 'Content-Disposition: attachment; filename="E-Commerce_Product_List.xml"' );
		} else {
			header( 'Content-Disposition: inline; filename="E-Commerce_Product_List.xml"' );
		}
		// Core feed information
		echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
		echo "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:g='http://base.google.com/ns/1.0'>\n";
		echo "  <channel>\n";
		$this->render_feed_title();
		echo '    <link>' . $this->store_info->site_url . "</link>\n";
		echo "    <description>This is the WooCommerce Product List RSS feed</description>\n";
		echo '    <generator>WooCommerce Google Product Feed Plugin v' . WOOCOMMERCE_GPF_VERSION . " (https://plugins.leewillis.co.uk/downloads/woocommerce-google-product-feed/)</generator>\n";
		echo "    <atom:link href='" . esc_url( $this->store_info->feed_url ) . "' rel='self' type='application/rss+xml' />\n";
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
			$this->debug->log( 'Empty price for %d, skipping...', [ $feed_item->specific_id ] );

			return '';
		}
		// Google do not allow items without images.
		if ( empty( $feed_item->image_link ) && $this->hide_if_no_images ) {
			return '';
		}

		// Strip out any disallowed tags, preserving their contents.
		$product_description = wp_kses( $feed_item->description, $this->allowed_description_markup );

		$output  = '';
		$output .= "    <item>\n";
		$output .= $this->generate_item_id( $feed_item );
		if ( isset( $this->settings['send_item_group_id'] ) && 'on' === $this->settings['send_item_group_id'] ) {
			$output .= '      <g:item_group_id>' . $feed_item->item_group_id . "</g:item_group_id>\n";
		}
		$output .= '      <title>' . $this->esc_xml( $feed_item->title ) . "</title>\n";
		$output .= $this->generate_link( $feed_item );
		$output .= '      <description>' . $this->esc_xml( $product_description ) . "</description>\n";

		if ( ! empty( $feed_item->image_link ) ) {
			$output .= '      <g:image_link>' . $this->esc_xml( $feed_item->image_link ) . "</g:image_link>\n";
		}

		$output .= $this->render_prices( $feed_item );

		$cnt = 0;
		if ( apply_filters( 'woocommerce_gpf_google_additional_images', true ) ) {
			foreach ( $feed_item->additional_images as $image_url ) {
				// Google limit the number of additional images to 10
				if ( 10 === $cnt ) {
					break;
				}
				$output .= '      <g:additional_image_link>' . $this->esc_xml( $image_url ) . "</g:additional_image_link>\n";
				$cnt ++;
			}
		}

		$done_condition = false;
		$done_weight    = false;

		if ( count( $feed_item->additional_elements ) ) {
			foreach ( $feed_item->additional_elements as $element_name => $element_values ) {
				foreach ( $element_values as $element_value ) {
					if ( 'availability' === $element_name ) {
						// Google no longer supports "available for order". Mapped this to "in stock" as per
						// specification update September 2014.
						if ( 'available for order' === $element_value ) {
							$element_value = 'in stock';
						}
						// Only send the value if the product is in stock, otherwise force to
						// "out of stock".
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
					if ( 'is_bundle' === $element_name ) {
						if ( 'on' === $element_value ) {
							$output .= " <g:is_bundle>TRUE</g:is_bundle>\n";
						}
						continue;
					}
					$output .= '      <g:' . $element_name . '>';
					if ( is_array( $element_value ) ) {
						foreach ( $element_value as $sub_element_name => $sub_element_value ) {
							$output .= '      <g:' . $sub_element_name . '>';
							$output .= $this->esc_xml( $sub_element_value );
							$output .= '</g:' . $sub_element_name . ">\n";
						}
					} else {
						$output .= $this->esc_xml( $element_value );
					}
					$output .= '</g:' . $element_name . ">\n";

				}

				if ( 'shipping_weight' === $element_name ) {
					$done_weight = true;
				}

				if ( 'condition' === $element_name ) {
					$done_condition = true;
				}
			}
		}

		if ( ! $done_condition ) {
			$output .= "      <g:condition>new</g:condition>\n";
		}

		if ( ! $done_weight ) {
			$weight       = apply_filters(
				'woocommerce_gpf_shipping_weight',
				$feed_item->shipping_weight,
				$feed_item->ID
			);
			$weight_units = $feed_item->shipping_weight_unit;
			if ( 'lbs' === $feed_item->shipping_weight_unit ) {
				$weight_units = 'lb';
			}
			if ( $weight && is_numeric( $weight ) && $weight > 0 ) {
				$output .= "      <g:shipping_weight>$weight $weight_units</g:shipping_weight>\n";
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
	protected function render_prices( $feed_item ) {

		// Regular price
		if ( $this->tax_excluded ) {
			// Some country's prices have to be submitted excluding tax.
			$price = number_format( $feed_item->regular_price_ex_tax, 2, '.', '' );
		} else {
			// Non-US prices have to be submitted including tax
			$price = number_format( $feed_item->regular_price_inc_tax, 2, '.', '' );
		}
		$output = '      <g:price>' . $price . ' ' . $this->store_info->currency . "</g:price>\n";

		// If there's no sale price, then we're done.
		if ( empty( $feed_item->sale_price_inc_tax ) ) {
			return $output;
		}

		// Otherwise, include the sale_price tag.
		if ( $this->tax_excluded ) {
			// US prices have to be submitted excluding tax.
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
		global $wpdb;

		// Debug feed performance.
		$this->endts  = microtime( true );
		$this->endmem = memory_get_peak_usage();
		$startmem     = round( $this->startmem / 1024 / 1024, 2 );
		$endmem       = round( $this->endmem / 1024 / 1024, 2 );
		$memusage     = round( ( $this->endmem - $this->startmem ) / 1024 / 1024, 2 );
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->debug->log( 'Total queries:  %d', [ count( $wpdb->queries ) ] );
		}
		$this->debug->log( 'Duration:     %s', [ str_pad( round( $this->endts - $this->startts, 2 ), 7, ' ', STR_PAD_LEFT ) . 's' ] );
		$this->debug->log( 'Start mem:    %s', [ str_pad( $startmem, 7, ' ', STR_PAD_LEFT ) . 'MB' ] );
		$this->debug->log( 'End mem:      %s', [ str_pad( $endmem, 7, ' ', STR_PAD_LEFT ) . 'MB' ] );
		$this->debug->log( 'Memory usage: %s', [ str_pad( $memusage, 7, ' ', STR_PAD_LEFT ) . 'MB' ] );

		echo "  </channel>\n";
		echo '</rss>';
		exit();
	}

	/*
	 * Output the "title" element in the feed intro.
	 */
	protected function render_feed_title() {
		echo '    <title>' . $this->esc_xml( $this->store_info->blog_name . ' Products' ) . "</title>\n";
	}

	/**
	 * Generate the item ID in the feed for an item.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_item_id( $feed_item ) {
		return '      <g:ID>' . $feed_item->guid . "</g:ID>\n";
	}

	/**
	 * Generate the link for a product.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_link( $feed_item ) {
		$escaped_url = apply_filters(
			'woocommerce_gpf_feed_item_escaped_url',
			esc_url( $feed_item->purchase_link ),
			$feed_item
		);

		return '      <link>' . $escaped_url . "</link>\n";
	}
}
