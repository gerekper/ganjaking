<?php
/**
 * Abstract Class WooCommerce Order Barcodes Generator file.
 *
 * @package woocommerce-order-barcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WooCommerce_Order_Barcodes_Generator' ) ) {
	return;
}

/**
 * Class WooCommerce_Order_Barcodes_Generator
 */
abstract class WooCommerce_Order_Barcodes_Generator {
	/**
	 * Foreground color of the barcode.
	 *
	 * @var foreground_color.
	 */
	protected $foreground_color;

	/**
	 * Get barcode for data matrix.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	abstract public function get_datamatrix( $barcode_text, $type );

	/**
	 * Get barcode for QR code.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	abstract public function get_qrcode( $barcode_text, $type );

	/**
	 * Get barcode for code 39.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	abstract public function get_code_39( $barcode_text, $type );

	/**
	 * Get barcode for code 93.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	abstract public function get_code_93( $barcode_text, $type );

	/**
	 * Get barcode for code 128.
	 *
	 * @param String $barcode_text Barcode text.
	 * @param String $type Type of barcode content. Example : 'PNG', 'HTML', 'SVG'.
	 */
	abstract public function get_code_128( $barcode_text, $type );
}
