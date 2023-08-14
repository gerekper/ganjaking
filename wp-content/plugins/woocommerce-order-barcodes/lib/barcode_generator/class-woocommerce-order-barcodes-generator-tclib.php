<?php
/**
 * Class WooCommerce Order Barcodes Generator Tclib file.
 *
 * @package WooCommerce_Order_Barcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WooCommerce_Order_Barcodes_Generator_Tclib' ) ) {
	return;
}

require_once( WC_ORDER_BARCODES_DIR_PATH . '/lib/barcode_generator/class-woocommerce-order-barcodes-generator.php' );

/**
 * Class WooCommerce_Order_Barcodes_Generator_Tclib
 */
class WooCommerce_Order_Barcodes_Generator_Tclib extends WooCommerce_Order_Barcodes_Generator {
	/**
	 * Barcode TC Lib object.
	 *
	 * @var dns1d.
	 */
	protected $barcode;

	/**
	 * Type of barcode that will be used.
	 *
	 * @var string.
	 */
	protected $barcode_type;

	/**
	 * Class constructor
	 *
	 * @param String $foreground_color Foreground color of the barcode.
	 * @param String $barcode_type     Type of barcode that will be used.
	 */
	public function __construct( $foreground_color, $barcode_type ) {
		// Instantiate the barcode class.
		$this->foreground_color = $foreground_color;
		$this->barcode          = new \Com\Tecnick\Barcode\Barcode();
		$this->barcode_type     = $barcode_type;
	}

	/**
	 * Get output of barcode based on the type.
	 *
	 * @param \Com\Tecnick\Barcode\Barcode $bobj Barcode object.
	 * @param String                       $type Type of barcode.
	 *
	 * @return String.
	 */
	protected function get_output( $bobj, $type ) {
		if ( 'PNG' === $type ) {
			return $bobj->getPngData();
		} elseif ( 'SVG' === $type ) {
			return $bobj->getSvgCode();
		}

		return $bobj->getHtmlDiv();
	}

	/**
	 * Get generated barcode.
	 *
	 * @param String $barcode        Barcode text.
	 * @param String $barcode_type   Type of barcode that will be used.
	 * @param String $barcode_output Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 *
	 * @return String.
	 */
	public function get_generated_barcode( $barcode, $barcode_output = 'HTML' ) {
		// Generate barcode image based on string and selected type.
		switch ( $this->barcode_type ) {
			case 'datamatrix':
				$barcode_img = $this->get_datamatrix( $barcode, $barcode_output );
				break;
			case 'qr':
				$barcode_img = $this->get_qrcode( $barcode, $barcode_output );
				break;
			case 'code39':
				$barcode_img = $this->get_code_39( $barcode, $barcode_output );
				break;
			case 'code93':
				$barcode_img = $this->get_code_93( $barcode, $barcode_output );
				break;
			case 'code128':
			default:
				$barcode_img = $this->get_code_128( $barcode, $barcode_output );
				break;
		}

		return $barcode_img;
	}

	/**
	 * Get barcode for data matrix.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	public function get_datamatrix( $barcode_text, $type = 'HTML' ) {
		$bobj = $this->barcode->getBarcodeObj( 'DATAMATRIX', $barcode_text, -10, -10, $this->foreground_color, array( 0, 0, 0, 0 ) )->setBackgroundColor( 'white' );

		return $this->get_output( $bobj, $type );
	}

	/**
	 * Get barcode for QR code.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	public function get_qrcode( $barcode_text, $type = 'HTML' ) {
		$bobj = $this->barcode->getBarcodeObj( 'QRCODE', $barcode_text, -5, -5, $this->foreground_color, array( 0, 0, 0, 0 ) )->setBackgroundColor( 'white' );

		return $this->get_output( $bobj, $type );
	}

	/**
	 * Get barcode for code 39.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	public function get_code_39( $barcode_text, $type = 'HTML' ) {
		$bobj = $this->barcode->getBarcodeObj( 'C39', $barcode_text, -1, -48, $this->foreground_color, array( 0, 0, 0, 0 ) )->setBackgroundColor( 'white' );

		return $this->get_output( $bobj, $type );
	}

	/**
	 * Get barcode for code 93.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	public function get_code_93( $barcode_text, $type = 'HTML' ) {
		$bobj = $this->barcode->getBarcodeObj( 'C93', $barcode_text, -1, -48, $this->foreground_color, array( 0, 0, 0, 0 ) )->setBackgroundColor( 'white' );

		return $this->get_output( $bobj, $type );
	}

	/**
	 * Get barcode for code 128.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	public function get_code_128( $barcode_text, $type = 'HTML' ) {
		$bobj = $this->barcode->getBarcodeObj( 'C128', $barcode_text, -1, -48, $this->foreground_color, array( 0, 0, 0, 0 ) )->setBackgroundColor( 'white' );

		return $this->get_output( $bobj, $type );
	}
}
