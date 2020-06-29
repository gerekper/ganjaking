<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Store_Catalog_PDF_Download_Shortcode {
	private static $_this;

	/**
	 * Init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_shortcode( 'wc-store-catalog-pdf', array( $this, 'shortcode' ) );

    	return true;
	}

	/**
	 * Get instance
	 *
	 * @access public
	 * @since 1.0.0
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}
	
	/**
	 * Shortcode function
	 *
	 * @access public
	 * @since 1.0.0
	 * @return 
	 */
	public function shortcode( $atts ) {
		extract( shortcode_atts( array(  
			'link_label' => get_option( 'wc_store_catalog_pdf_download_link_label', __( 'Download Catalog', 'woocommerce-store-catalog-pdf-download' ) )
		), $atts ) );  

		$custom_pdf_url = wp_get_attachment_url( get_option( 'wc_store_catalog_pdf_download_custom_pdf', '' ) );

		$output = '';
		$output .= '<p class="wc-store-catalog-pdf-download">' . PHP_EOL;

		// user filter to determine if PDF should directly download or view
		if ( apply_filters( 'wc_store_catalog_pdf_download_view_only', true ) ) {
			$view_download = '';
		} else {
			$parsed_url = parse_url( $custom_pdf_url );

			$view_download = 'download="' . esc_attr( basename( $parsed_url['path'] ) ) . '"';
		}

		$output .= '<a href="' . esc_url( $custom_pdf_url ) . '" class="button wc-store-catalog-pdf-download-custom-link" target="_blank" ' . $view_download . '><i class="icon-file-pdf" aria-hidden="true"></i> ' . $link_label . '</a>' . PHP_EOL;

		$output .= '</p>' . PHP_EOL;

		return $output;	
	}	
}

new WC_Store_Catalog_PDF_Download_Shortcode();
