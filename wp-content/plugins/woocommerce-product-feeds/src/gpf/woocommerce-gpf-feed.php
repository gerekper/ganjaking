<?php

/**
 * Feed base case.
 *
 * Provides common methods used by all feeds, and sets up main feed properties.
 */
abstract class WoocommerceGpfFeed {

	/**
	 * The plugin settings.
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * The core store inforation.
	 *
	 * @var stdClass
	 */
	protected $store_info;

	/**
	 * @var WoocommerceGpfCommon
	 */
	protected $woocommerce_gpf_common;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * Constructor.
	 * Grab the settings, and set up the store info object
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
		$this->woocommerce_gpf_common = $woocommerce_gpf_common;
		$this->debug                  = $debug;

		$this->settings = get_option( 'woocommerce_gpf_config' );

		$this->store_info                = new stdClass();
		$this->store_info->site_url      = home_url( '/' );
		$this->store_info->feed_url_base = home_url( '/' );
		$this->store_info->blog_name     = get_option( 'blogname' );
		$this->store_info->currency      = get_woocommerce_currency();
		$this->store_info->weight_units  = get_option( 'woocommerce_weight_unit' );
		$this->store_info->base_country  = wc()->countries->get_base_country();

		$this->store_info = apply_filters( 'woocommerce_gpf_store_info', $this->store_info );
	}

	/**
	 * Helper function used to output an escaped value for use in a CSV
	 *
	 * @access protected
	 *
	 * @param string $string The string to be escaped
	 *
	 * @return string         The escaped string
	 */
	protected function csvescape( $string ) {

		$doneescape = false;
		if ( stristr( $string, '"' ) ) {
			$string     = str_replace( '"', '""', $string );
			$string     = "\"$string\"";
			$doneescape = true;
		}

		$string = str_replace( "\n", ' ', $string );
		$string = str_replace( "\r", ' ', $string );

		if ( stristr( $string, apply_filters( 'ses_wpscd_csv_separator', ',' ) ) && ! $doneescape ) {
			$string = "\"$string\"";
		}

		return apply_filters( 'ses_wpscd_csv_escape_string', $string );

	}

	/**
	 * Helper function used to output an escaped value for use in a tab separated file
	 *
	 * @access protected
	 *
	 * @param string $string The string to be escaped
	 * @param bool $charset_convert
	 *
	 * @return string         The escaped string
	 */
	protected function tsvescape( $string, $charset_convert = true ) {

		$string = html_entity_decode( $string, ENT_HTML401 | ENT_QUOTES ); // Convert any HTML entities
		if ( $charset_convert ) {
			$string = iconv(
				'UTF-8',
				'ASCII//TRANSLIT//IGNORE',
				$string
			);
		}

		$doneescape = false;
		if ( stristr( $string, '"' ) ) {
			$string     = str_replace( '"', '""', $string );
			$string     = "\"$string\"";
			$doneescape = true;
		}
		$string = str_replace( "\n", ' ', $string );
		$string = str_replace( "\r", ' ', $string );
		$string = str_replace( "\t", ' ', $string );

		if ( stristr( $string, apply_filters( 'woocommerce_gpf_tsv_separator', "\t" ) ) && ! $doneescape ) {
			$string = "\"$string\"";
		}

		return apply_filters( 'woocommerce_gpf_tsv_escape_string', $string );
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
	protected function esc_xml( $value ) {
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
	 * Override this to generate output at the start of the file
	 * Opening XML declarations, CSV header rows etc.
	 *
	 * @access public
	 */
	abstract public function render_header();

	/**
	 * Override this to generate the output for an individual item
	 *
	 * @access public
	 *
	 * @param $item object Item object
	 */
	abstract public function render_item( $item );

	/**
	 * Override this to generate output at the start of the file
	 * Opening XML declarations, CSV header rows etc.
	 *
	 * @access public
	 *
	 * @param  $store_info object Object containing information about the store
	 */
	abstract public function render_footer();
}
