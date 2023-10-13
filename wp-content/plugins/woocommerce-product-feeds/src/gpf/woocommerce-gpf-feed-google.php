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
	 * Whether to include additional images in the feed.
	 * @var bool
	 */
	private $include_additional_images = true;

	/**
	 * Array of allowed HTML tags to pass to wp_kses to trim out unsupported markup in the description element.
	 *
	 * @var array
	 */
	private $allowed_description_markup = array();

	/**
	 * @var float
	 */
	private $start_ts;

	private $start_rusage;

	/**
	 * @var int
	 */
	private $start_mem;

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

		if ( ! empty( $this->store_info->base_country ) ) {
			if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ||
				'CA' === substr( $this->store_info->base_country, 0, 2 ) ) {
				$this->tax_excluded = true;
				if ( 'US' === substr( $this->store_info->base_country, 0, 2 ) ) {
					$this->tax_attribute = true;
				}
			}
		}
		$this->tax_excluded              = apply_filters(
			'woocommerce_gpf_tax_excluded',
			$this->tax_excluded,
			$this->store_info
		);
		$this->tax_attribute             = apply_filters(
			'woocommerce_gpf_tax_attribute',
			$this->tax_attribute,
			$this->store_info
		);
		$this->hide_if_no_images         = apply_filters(
			'woocommerce_gpf_hide_if_no_images_google',
			$this->hide_if_no_images
		);
		$this->include_additional_images = apply_filters(
			'woocommerce_gpf_google_additional_images',
			$this->include_additional_images
		);

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

		static $max_lengths = [];
		if ( ! empty( $max_lengths ) ) {
			return $max_lengths;
		}
		// Max lengths for core fields
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
	 * Render the feed header information
	 *
	 * @access public
	 */
	public function render_header() {
		$this->start_ts     = microtime( true );
		$this->start_rusage = function_exists( 'getrusage' ) ? getrusage() : null;
		$this->start_mem    = memory_get_peak_usage();
		header( 'Content-Type: application/xml; charset=UTF-8' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['feeddownload'] ) ) {
			header( 'Content-Disposition: attachment; filename="E-Commerce_Product_List.xml"' );
		} else {
			header( 'Content-Disposition: inline; filename="E-Commerce_Product_List.xml"' );
		}
		// Core feed information
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
		echo '<channel>';
		$this->render_feed_title();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<link>' . $this->esc_xml( $this->store_info->site_url ) . '</link>';
		echo '<description>';
		echo 'This is the WooCommerce Product List RSS feed. ';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo 'Generated by WooCommerce Google Product Feed Plugin v' . WOOCOMMERCE_GPF_VERSION . '. ';
		echo '(https://plugins.leewillis.co.uk/downloads/woocommerce-google-product-feed/). ';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo 'Feed URL: ' . $this->esc_xml( $this->store_info->feed_url );
		echo '</description>';
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
			$this->debug->log( 'No images found for %d, hidden...', [ $feed_item->specific_id ] );
			return '';
		}

		// Strip out any disallowed tags, preserving their contents.
		// Enforce max length here so we can do it after unnecessary markup stripped.
		$product_description = mb_substr(
			wp_kses( $feed_item->description, $this->allowed_description_markup ),
			0,
			5000
		);

		$output  = '<item>';
		$output .= $this->generate_item_id( $feed_item );
		if ( isset( $this->settings['send_item_group_id'] ) && 'on' === $this->settings['send_item_group_id'] ) {
			$output .= '<g:item_group_id>' . $feed_item->item_group_id . '</g:item_group_id>';
		}
		$output .= '<title>' . $this->esc_xml( $feed_item->title ) . '</title>';
		$output .= $this->generate_link( $feed_item );
		$output .= '<description>' . $this->esc_xml( $product_description ) . '</description>';

		if ( ! empty( $feed_item->image_link ) ) {
			$output .= '<g:image_link>' . $this->esc_xml( $feed_item->image_link ) . '</g:image_link>';
		}

		if ( ! empty( $feed_item->lifestyle_image_link ) ) {
			$output .= '<g:lifestyle_image_link>' . $this->esc_xml( $feed_item->lifestyle_image_link ) . '</g:lifestyle_image_link>';
		}

		$output .= $this->render_prices( $feed_item );

		$cnt = 0;
		if ( $this->include_additional_images ) {
			foreach ( $feed_item->additional_images as $image_url ) {
				if ( $image_url === $feed_item->lifestyle_image_link ) {
					continue;
				}
				// Google limit the number of additional images to 10
				if ( 10 === $cnt ) {
					break;
				}
				$output .= '<g:additional_image_link>' . $this->esc_xml( $image_url ) . '</g:additional_image_link>';
				++$cnt;
			}
		}

		$done_weight = false;

		foreach ( $feed_item->additional_elements as $element_name => $element_values ) {
			foreach ( $element_values as $element_value ) {
				/**
				 * These conditions handle their output directly.
				 */
				if ( 'identifier_exists' === $element_name ) {
					if ( 'included' === $element_value ) {
						$identifier_count  = 0;
						$identifier_count += (int) ( ! empty( $feed_item->additional_elements['brand'] ) );
						$identifier_count += (int) ( ! empty( $feed_item->additional_elements['gtin'] ) );
						$identifier_count += (int) ( ! empty( $feed_item->additional_elements['mpn'] ) );
						if ( $identifier_count < 2 ) {
							$output .= '<g:identifier_exists>FALSE</g:identifier_exists>';
						}
					}
					continue;
				}
				if ( 'is_bundle' === $element_name ) {
					if ( 'on' === $element_value ) {
						$output .= '<g:is_bundle>TRUE</g:is_bundle>';
					}
					continue;
				}
				/**
				 * These condition checks manipulate the value and fall through to output it further down.
				 */
				if ( 'availability' === $element_name ) {
					// Google no longer supports "available for order". Mapped this to "in stock" as per
					// specification update September 2014.
					if ( 'available for order' === $element_value ) {
						$element_value = 'in stock';
					}
				}
				if ( 'availability_date' === $element_name ) {
					if ( strlen( $element_value ) === 10 ) {
						$tz_offset      = get_option( 'gmt_offset' );
						$element_value .= 'T00:00:00' . sprintf( '%+03d', $tz_offset ) . '00';
					}
				}
				if ( 'shipping_weight' === $element_name ) {
					$done_weight = true;
				}

				/**
				 * Output the element.
				 */
				$output .= '<g:' . $element_name . '>';
				if ( is_array( $element_value ) ) {
					foreach ( $element_value as $sub_element_name => $sub_element_value ) {
						$output .= '<g:' . $sub_element_name . '>';
						$output .= $this->esc_xml( $sub_element_value );
						$output .= '</g:' . $sub_element_name . '>';
					}
				} else {
					$output .= $this->esc_xml( $element_value );
				}
				$output .= '</g:' . $element_name . '>';
			}
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
				$output .= "<g:shipping_weight>$weight $weight_units</g:shipping_weight>";
			}
		}
		$output .= '</item>';

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
		$output = '<g:price>' . $price . ' ' . $this->store_info->currency . '</g:price>';

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
		$output .= '<g:sale_price>' . $sale_price . ' ' . $this->store_info->currency . '</g:sale_price>';

		// Include start / end dates if provided.
		if ( ! empty( $feed_item->sale_price_start_date ) &&
			! empty( $feed_item->sale_price_end_date ) ) {
			$effective_date  = (string) $feed_item->sale_price_start_date;
			$effective_date .= '/';
			$effective_date .= (string) $feed_item->sale_price_end_date;
			$output         .= '<g:sale_price_effective_date>' . $effective_date . '</g:sale_price_effective_date>';
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
		$endts    = microtime( true );
		$endmem1  = memory_get_peak_usage();
		$startmem = round( $this->start_mem / 1024 / 1024, 2 );
		$endmem   = round( $endmem1 / 1024 / 1024, 2 );
		$memusage = round( ( $endmem1 - $this->start_mem ) / 1024 / 1024, 2 );

		$utime = 0;
		$stime = 0;
		if ( function_exists( 'getrusage' ) ) {
			$end_rusage = getrusage();
			$utime      = ( $end_rusage['ru_utime.tv_sec'] - $this->start_rusage['ru_utime.tv_sec'] ) * 1000000;
			$utime     += ( $end_rusage['ru_utime.tv_usec'] - $this->start_rusage['ru_utime.tv_usec'] );
			$utime      = round( $utime / 1000000, 4 );
			$stime      = ( $end_rusage['ru_stime.tv_sec'] - $this->start_rusage['ru_stime.tv_sec'] ) * 1000000;
			$stime     += ( $end_rusage['ru_stime.tv_usec'] - $this->start_rusage['ru_stime.tv_usec'] );
			$stime      = round( $stime / 1000000, 4 );
		}

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$this->debug->log( 'Total queries:  %s', [ str_pad( count( $wpdb->queries ), 7, ' ', STR_PAD_LEFT ) ] );
		}
		$this->debug->log( 'Start mem:      %s', [ str_pad( $startmem, 7, ' ', STR_PAD_LEFT ) . ' MB' ] );
		$this->debug->log( 'End mem:        %s', [ str_pad( $endmem, 7, ' ', STR_PAD_LEFT ) . ' MB' ] );
		$this->debug->log( 'Memory usage:   %s', [ str_pad( $memusage, 7, ' ', STR_PAD_LEFT ) . ' MB' ] );
		$this->debug->log( 'Duration:       %s', [ str_pad( round( $endts - $this->start_ts, 2 ), 7, ' ', STR_PAD_LEFT ) . ' s' ] );
		$this->debug->log( 'User time:      %s', [ str_pad( $utime, 7, ' ', STR_PAD_LEFT ) . ' seconds' ] );
		$this->debug->log( 'System time:    %s', [ str_pad( $stime, 7, ' ', STR_PAD_LEFT ) . ' seconds' ] );

		echo '</channel>';
		echo '</rss>';
		exit();
	}

	/*
	 * Output the "title" element in the feed intro.
	 */
	protected function render_feed_title() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<title>' . $this->esc_xml( $this->store_info->blog_name . ' Products' ) . '</title>';
	}

	/**
	 * Generate the item ID in the feed for an item.
	 *
	 * @param $feed_item
	 *
	 * @return string
	 */
	protected function generate_item_id( $feed_item ) {
		return '<g:id>' . $feed_item->guid . '</g:id>';
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

		return '<link>' . $escaped_url . '</link>';
	}
}
