<?php
use Dompdf\Dompdf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Store_Catalog_PDF_Download_Ajax {
	private static $_this;

	/**
	 * Init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'wp_ajax_wc_store_catalog_pdf_download_frontend_generate_pdf_ajax', array( $this, 'generate_pdf_ajax' ) );
		
		add_action( 'wp_ajax_nopriv_wc_store_catalog_pdf_download_frontend_generate_pdf_ajax', array( $this, 'generate_pdf_ajax' ) );

		add_action( 'wc_store_catalog_pdf_download_product_attr', array( $this, 'display_attributes' ) );

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
	 * Get the download
	 *
	 * @since 1.0.0
	 * @return html
	 */
	public function generate_pdf_ajax() {
		$nonce = $_POST['ajaxPDFDownloadNonce'];

		// bail if nonce don't check out
		if ( ! wp_verify_nonce( $nonce, '_wc_store_catalog_pdf_download_nonce' ) ) {
		     die ( 'error' );		
		}

		global $wc_posts, $layout, $is_single;
	
		if ( isset( $_POST['posts'] ) && ! empty( $_POST['posts'] ) ) {
		
			$posts = json_decode( $_POST['posts'] );

			$posts = array_map( 'absint', $posts );

		} else { 
			$posts = false;
		}

		$is_single = isset( $_POST['is_single'] ) ? sanitize_text_field( $_POST['is_single'] ) : '';

		$layout = isset( $_POST['layout'] ) ? sanitize_text_field( $_POST['layout'] ) : '';

		// single template trumps others
		$layout = ! empty( $is_single ) && $is_single === 'true' ? 'single' : $layout;

		require_once( 'vendor/autoload.php' );

		// portrait, landscape
		$orientation = apply_filters( 'wc_store_catalog_pdf_download_orientation', 'portrait' );

		// 'letter', 'A4', 'legal'
		$size = apply_filters( 'wc_store_catalog_pdf_download_size', 'letter' );

		// create the object
		$dompdf = new DOMPDF();
		$dompdf->set_option( 'defaultFont', 'DejaVu Sans' );
		$dompdf->set_option( 'isRemoteEnabled', true );

		add_action( 'woocommerce_store_catalog_pdf_download_dompdf_options', $dompdf );

		if ( $wc_posts = $posts ) {

			@set_time_limit( 0 );

			ob_start();
			
			include( self::get_header_template( $layout ) );
			include( self::get_body_template( $layout ) );
			include( self::get_footer_template( $layout ) );

			$html = ob_get_clean();

			// render pdf
			$dompdf->load_html( $html );
			$dompdf->set_paper( $size, $orientation );
			$dompdf->render();

			$upload_dir = wp_upload_dir(); 
			$pdf_path   = $upload_dir['basedir'] . '/woocommerce-store-catalog-pdf-download/';
			$pdf_url    = $upload_dir['baseurl'] . '/woocommerce-store-catalog-pdf-download/';
			$filename   = apply_filters( 'wc_store_catalog_pdf_download_filename', str_replace( ' ', '-', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) . '-' . __( 'Store-Catalog', 'woocommerce-store-catalog-pdf-download' ) . '-' . time() . '.pdf' );

			if ( ! is_dir( $pdf_path ) ) {
				mkdir( $pdf_path, 0777, true );
			}

			file_put_contents( $pdf_path . $filename, $dompdf->output() );

			echo $pdf_url . $filename;
			exit;
		}

		echo 'error';
		exit;
	}

	/**
	 * Display the product attributes
	 *
	 * @since 1.0.0
	 * @return html template
	 */
	public function display_attributes( $product ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			wc_get_template( 'single-product/product-attributes.php', array( 'product' => $product ) );
		} else {
			wc_display_product_attributes( $product );
		}
	}

	/**
	 * Returns product image, by making sure it contains absolute URLs to be used in templates.
	 *
	 * @param WC_Product $product
	 * @param array      $size
	 *
	 * @return string
	 */
	public static function get_product_image( $product, $size ) {
		$product_image = $product->get_image( $size );

		if ( version_compare( WC_VERSION, '3.4.0', '<' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$product_image = str_replace( '//', ( is_ssl() ? 'https://' : 'http://' ), $product_image );
		}

		return $product_image;
	}

	/**
	 * Get the product meta template
	 *
	 * @since 1.0.0
	 * @return html template
	 */
	public static function get_product_meta_template( $product ) {
		global $product;

		// check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-product-meta-html.php' ) ) {
			
			return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-product-meta-html.php';
		
		} else {
			
			return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-product-meta-html.php';
		}
	}
	
	/**
	 * Get the body template
	 *
	 * @since 1.0.0
	 * @param string $layout
	 * @return html template
	 */
	public static function get_body_template( $layout ) {

		if ( 'list' === $layout ) {

			// check if template has been overriden
			if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-list-html.php' ) ) {
				
				return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-list-html.php';

			} else  {
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-list-html.php';
			}
		}

		if ( 'grid' === $layout ) {
			// check if template has been overriden
			if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-grid-html.php' ) ) {
				
				return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-grid-html.php';

			} else  {
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-grid-html.php';
			}
		}

		if ( 'single' === $layout ) {
			// check if template has been overriden
			if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-single-html.php' ) ) {
				
				return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-single-html.php';

			} else  {
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-single-html.php';
			}
		}

		return true;
	}

	/**
	 * Get the header template
	 *
	 * @since 1.0.0
	 * @return html template
	 */
	public static function get_header_template() {

		// check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-header-html.php' ) ) {

			return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-header-html.php';

		} else {
			
			return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-header-html.php';
		}
	}

	/**
	 * Get the footer template
	 *
	 * @since 1.0.0
	 * @return html template
	 */
	public static function get_footer_template() {

		// check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-footer-html.php' ) ) {
			
			return get_stylesheet_directory() . '/woocommerce-store-catalog-pdf-download/pdf-layout-footer-html.php';
		
		} else {
			
			return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/pdf-layout-footer-html.php';
		}
	}

	/**
	 * Get placeholder image
	 *
	 * @since 1.0.0
	 * @param array $image_size
	 * @return html template
	 */
	public static function get_placeholder_image( $image_size ) {
		if ( ! isset( $image_size ) || empty( $image_size ) ) {
			$image_size = array( 450, 450 );
		}

		return '<img src="' . wc_placeholder_img_src() . '" alt="' . __( 'Placeholder', 'woocommerce-store-catalog-pdf-download' ) . '" width="' . esc_attr( $image_size[0] ) . '" height="' . esc_attr( $image_size[1] ) . '" class="woocommerce-placeholder wp-post-image" />';
	}
}

new WC_Store_Catalog_PDF_Download_Ajax();
