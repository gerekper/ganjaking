<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Store_Catalog_PDF_Download_Frontend {
	private static $_this;

	public $link_label;

	/**
	 * Init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		include_once( 'class-wc-store-catalog-pdf-download-shortcode.php' );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_styles' ) );

		add_action( 'woocommerce_after_shop_loop', array( $this, 'display_button_on_category' ), 9 );

		add_action( 'woocommerce_single_product_summary', array( $this, 'display_button_on_single' ), 55 );

		// get the download link label
		$this->link_label = get_option( 'wc_store_catalog_pdf_download_link_label', __( 'Download Catalog', 'woocommerce-store-catalog-pdf-download' ) );

    	return true;
	}

	/**
	 * Get instance
	 *
	 * @since 1.0.0
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}

	/**
	 * Load frontend scripts and styles
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_scripts_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wc-store-catalog-pdf-download-style', plugins_url( 'assets/css/frontend-styles.css', dirname( __FILE__ ) ) );

		wp_enqueue_script( 'wc-store-catalog-pdf-download-js', plugins_url( 'assets/js/frontend' . $suffix . '.js', dirname( __FILE__ ) ) );

		$localized_vars = array(
			'ajaxurl'              => admin_url( 'admin-ajax.php' ),
			'ajaxPDFDownloadNonce' => wp_create_nonce( '_wc_store_catalog_pdf_download_nonce' )
		);
		
		wp_localize_script( 'wc-store-catalog-pdf-download-js', 'wc_store_catalog_pdf_download_local', $localized_vars );

		return true;
	}

	/**
	 * Displays the button on category pages
	 *
	 * @since 1.0.0
	 * @return mixed html
	 */
	public function display_button_on_category() {
		$woocommerce_shop_page_display = get_option( 'woocommerce_shop_page_display' );
		$woocommerce_category_archive_display = get_option( 'woocommerce_category_archive_display' );

		// only show pdf button if there are products on the page
		if ( 'subcategories' === $woocommerce_shop_page_display && is_shop() ) {
			return;
		}

		global $wp_query;

		$post_ids = array();

		// gather all ids
		foreach( $wp_query->posts as $post ) {
			$post_ids[] = $post->ID;
		}

		if ( ! is_single() ) {
			
			// list, grid ( on single product page this option has no affect as single layout will be used )
			$pdf_layout = get_option( 'wc_store_catalog_pdf_download_layout', 'list' );
					
			$output = '';
			$output .= '<p class="wc-store-catalog-pdf-download">' . PHP_EOL;

			$output .= '<a href="#" class="wc-store-catalog-pdf-download-link button" target="_blank" download=""><i class="icon-file-pdf" aria-hidden="true"></i> ' . $this->link_label . '</a>' . PHP_EOL;

			$output .= '<input type="hidden" value="' . esc_attr( $pdf_layout ) . '" name="pdf_layout" />' . PHP_EOL;

			$output .= '<input type="hidden" value="false" name="is_single" />' . PHP_EOL;

			$output .= '<input type="hidden" value="' . esc_attr( json_encode( $post_ids ) ) . '" name="posts" />' . PHP_EOL;

			$output .= '</p>' . PHP_EOL;
		}

		echo $output;
	}

	/**
	 * Displays the button on single product page
	 *
	 * @since 1.0.0
	 * @return mixed html
	 */
	public function display_button_on_single() {
		global $wp_query;

		$post_ids = array();

		// gather all ids
		foreach( $wp_query->posts as $post ) {
			$post_ids[] = $post->ID;
		}

		$output = '';
		$output .= '<p class="wc-store-catalog-pdf-download">' . PHP_EOL;

		$output .= '<a href="#" class="wc-store-catalog-pdf-download-link button" target="_blank" download=""><i class="icon-file-pdf" aria-hidden="true"></i> ' . $this->link_label . '</a>' . PHP_EOL;

		$output .= '<input type="hidden" value="true" name="is_single" />' . PHP_EOL;

		$output .= '<input type="hidden" value="' . esc_attr( json_encode( $post_ids ) ) . '" name="posts" />' . PHP_EOL;

		$output .= '</p>' . PHP_EOL;

		echo $output;
	}
}

new WC_Store_Catalog_PDF_Download_Frontend();
