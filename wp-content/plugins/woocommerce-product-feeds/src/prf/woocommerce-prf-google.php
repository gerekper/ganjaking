<?php

class WoocommercePrfGoogle {

	/**
	 * @var \WoocommerceGpfTemplateLoader
	 */
	private $tpl;

	/**
	 * Timestamp when header output.
	 *
	 * @var int
	 */
	private $start_ts;

	/**
	 * Memory usage at start of feed generation.
	 *
	 * @var int
	 */
	private $start_mem;

	/**
	 * Timestamp when footer output requested.
	 *
	 * @var int
	 */
	private $end_ts;

	/**
	 * Memory usage when footer output requested.
	 *
	 * @var int
	 */
	private $end_mem;

	/**
	 * Constructor.
	 *
	 * Store the template loader for future use.
	 * Set the escaping callback in the template loader appropriately.
	 *
	 * @param WoocommerceGpfTemplateLoader $template_loader
	 */
	public function __construct( WoocommerceGpfTemplateLoader $template_loader ) {
		$this->tpl = $template_loader;
	}

	/**
	 * Render the header of the XML feed.
	 */
	public function render_header() {
		$this->start_ts  = microtime( true );
		$this->start_mem = memory_get_peak_usage();
		header( 'Content-Type: application/xml; charset=UTF-8' );
		if ( isset( $_REQUEST['feeddownload'] ) ) {
			header( 'Content-Disposition: attachment; filename="woocommerce-review.xml"' );
		} else {
			header( 'Content-Disposition: inline; filename="woocommerce-review.xml"' );
		}
		$variables               = array();
		$variables['store_name'] = $this->esc_xml( get_bloginfo( 'name' ) );
		$variables['version']    = WOOCOMMERCE_GPF_VERSION;
		$this->tpl->output_template_with_variables( 'woo-prf', 'google-xml-header', $variables );
	}

	/**
	 * Render an individual item in the XML feed.
	 *
	 * @param array $item Array of item values
	 *
	 * @return bool
	 */
	public function render_item( $item ) {

		$variables = array(
			'user_id'           => $this->esc_xml( $item['user_id'] ),
			'review_id'         => $this->esc_xml( $item['review_id'] ),
			'reviewer_name'     => $this->esc_xml( $this->treat_name( $item['reviewer_name'] ) ),
			'review_content'    => $this->esc_xml( $item['review_content'] ),
			'product_url'       => $this->esc_xml( $item['product_url'] ),
			'product_name'      => $this->esc_xml( $item['product_name'] ),
			'review_rating'     => $this->esc_xml( $item['review_rating'] ),
			'collection_method' => $this->esc_xml( $item['collection_method'] ),
			'reviewer'          => $this->render_reviewer( $item['reviewer_id'] ),
			'product_ids'       => $this->render_product_ids( $item ),
			'review_timestamp'  => $item['review_timestamp'],
			'name_is_anonymous' => $item['name_is_anonymous'] ? 'true' : 'false',
		);
		$this->tpl->output_template_with_variables( 'woo-prf', 'google-xml-item', $variables );

		return true;
	}

	/**
	 * Render the footer of the XML feed.
	 */
	public function render_footer() {
		global $wpdb;
		$this->end_ts  = microtime( true );
		$this->end_mem = memory_get_peak_usage();
		// Dump out queries before we exit
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
				echo '<!-- Total queries:  ' . str_pad( count( $wpdb->queries ), 7, ' ', STR_PAD_LEFT ) . ' -->';
				echo '<!--                         -->';
			}
			$start_mem = round( $this->start_mem / 1024 / 1024, 2 );
			$end_mem   = round( $this->end_mem / 1024 / 1024, 2 );
			$mem_usage = round( ( $this->end_mem - $this->start_mem ) / 1024 / 1024, 2 );
			echo '<!-- Duration:      ' . str_pad( round( $this->end_ts - $this->start_ts, 2 ), 7, ' ', STR_PAD_LEFT ) . 's -->';
			echo '<!--                         -->';
			echo '<!-- Start mem:    ' . str_pad( $start_mem, 7, ' ', STR_PAD_LEFT ) . 'MB -->';
			echo '<!-- End mem:      ' . str_pad( $end_mem, 7, ' ', STR_PAD_LEFT ) . 'MB -->';
			echo '<!-- Memory usage: ' . str_pad( $mem_usage, 7, ' ', STR_PAD_LEFT ) . 'MB -->';
		}
		$this->tpl->output_template_with_variables( 'woo-prf', 'google-xml-footer', array() );
	}

	/**
	 * Return a reviewer tagset, or empty string if tag should not be output.
	 *
	 * @param int $user_id The user ID to reference in the tag. Non positive int values will
	 *                           cause no tag to be output and an empty string to be returned.
	 *
	 * @return string            The tag to be output.
	 */
	public function render_reviewer( $user_id ) {
		if ( $user_id <= 0 ) {
			return '';
		}

		return $this->tpl->get_template_with_variables(
			'woo-prf',
			'google-xml-reviewer-id',
			array( 'reviewer_id' => $this->esc_xml( (int) $user_id ) )
		);
	}

	/**
	 * Return the <product_ids> tag, or empty string of the tag should not be output.
	 *
	 * @param array $item The feed item.
	 *
	 * @return string               The tag to be output.
	 */
	public function render_product_ids( $item ) {
		$variables           = array();
		$variables['gtins']  = $this->render_gtins( $item );
		$variables['mpns']   = $this->render_mpns( $item );
		$variables['brands'] = $this->render_brands( $item );
		$variables['skus']   = $this->render_skus( $item );

		return $this->tpl->get_template_with_variables( 'woo-prf', 'google-xml-product-ids', $variables );
	}

	/**
	 * Render the GTINS tag for a product, or an empty string if none available.
	 *
	 * @param array $item The feed item array.
	 *
	 * @return string                 The GTINS tag, or empty string.
	 */
	protected function render_gtins( $item ) {
		if ( empty( $item['gtins'] ) ) {
			return '';
		}
		$gtins = '';
		foreach ( $item['gtins'] as $gtin ) {
			$gtins .= $this->tpl->get_template_with_variables(
				'woo-prf',
				'google-xml-gtin',
				[ 'gtin' => $this->esc_xml( $gtin ) ]
			);
		}

		return $this->tpl->get_template_with_variables( 'woo-prf', 'google-xml-gtins', [ 'gtins' => $gtins ] );
	}

	/**
	 * Render the MPNS tag for a product, or an empty string if none available.
	 *
	 * @param array $item The feed item array.
	 *
	 * @return string                 The MPNS tag, or empty string.
	 */
	protected function render_mpns( $item ) {
		if ( empty( $item['mpns'] ) ) {
			return '';
		}
		$mpns = '';
		foreach ( $item['mpns'] as $mpn ) {
			$mpns .= $this->tpl->get_template_with_variables(
				'woo-prf',
				'google-xml-mpn',
				[ 'mpn' => $this->esc_xml( $mpn ) ]
			);
		}

		return $this->tpl->get_template_with_variables( 'woo-prf', 'google-xml-mpns', [ 'mpns' => $mpns ] );
	}

	/**
	 * Render the brands tag for a product, or an empty string if none available.
	 *
	 * @param array $item The feed item array.
	 *
	 * @return string                 The brands tag, or empty string.
	 */
	protected function render_brands( $item ) {
		if ( empty( $item['brands'] ) ) {
			return '';
		}
		$brands = '';
		foreach ( $item['brands'] as $brand ) {
			$brands .= $this->tpl->get_template_with_variables(
				'woo-prf',
				'google-xml-brand',
				[ 'brand' => $this->esc_xml( $brand ) ]
			);
		}

		return $this->tpl->get_template_with_variables( 'woo-prf', 'google-xml-brands', [ 'brands' => $brands ] );
	}


	/**
	 * Render the SKUs tag for a product, or an empty string if none available.
	 *
	 * @param array $item The feed item array.
	 *
	 * @return string                 The SKUs tag, or empty string.
	 */
	protected function render_skus( $item ) {
		if ( empty( $item['skus'] ) ) {
			return '';
		}
		$skus = '';
		foreach ( $item['skus'] as $sku ) {
			$skus .= $this->tpl->get_template_with_variables(
				'woo-prf',
				'google-xml-sku',
				[ 'sku' => $this->esc_xml( $sku ) ]
			);
		}

		return $this->tpl->get_template_with_variables( 'woo-prf', 'google-xml-skus', [ 'skus' => $skus ] );
	}

	/**
	 * Escape a value for use in XML.
	 *
	 * Uses WordPress' esc_xml if available.
	 *
	 * @param $value
	 *
	 * @return string
	 */
	private function esc_xml( $value ) {
		if ( function_exists( 'esc_xml' ) ) {
			return esc_xml( $value );
		}
		$value = preg_replace(
			'/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u',
			'',
			$value
		);
		$value = str_replace( ']]>', ']]]]><![CDATA[>', $value );

		return '<![CDATA[' . $value . ']]>';
	}

	/**
	 * Try to attempt to make a name string not personally-identifiable.
	 *
	 * @param string $reviewer_name
	 *
	 * @return string
	 */
	private function treat_name( $reviewer_name ) {
		$parts = explode( ' ', $reviewer_name );
		if ( count( $parts ) === 1 ) {
			$name_to_use = $reviewer_name;
		} else {
			$name_to_use = array_shift( $parts );
			foreach ( $parts as $part ) {
				$name_to_use .= ' ' . strtoupper( mb_substr( $part, 0, 1 ) ) . '.';
			}
		}

		return $name_to_use;
	}
}
