<?php
/**
 * A class for handling the product catalogs.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalogs class.
 */
class WC_Instagram_Product_Catalogs {

	/**
	 * Init.
	 *
	 * @since 4.0.0
	 */
	public static function init() {
		add_action( 'wc_instagram_product_catalog_created', array( __CLASS__, 'init_catalog_files' ) );
		add_action( 'wc_instagram_product_catalog_updated', array( __CLASS__, 'generate_catalog_file' ) );
		add_action( 'wc_before_delete_instagram_product_catalog', array( __CLASS__, 'delete_catalog_files' ) );
	}

	/**
	 * Initializes the catalog files.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 */
	public static function init_catalog_files( $product_catalog ) {
		self::create_empty_file( $product_catalog, 'xml' );
		self::generate_catalog_file( $product_catalog, 'xml' );
	}

	/**
	 * Deletes the catalog files.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 */
	public static function delete_catalog_files( $product_catalog ) {
		foreach ( array( 'xml', 'csv' ) as $format ) {
			$file = $product_catalog->get_file( $format, 'tmp' );

			if ( $file ) {
				$file->delete( true );
			}
		}
	}

	/**
	 * Generates a catalog file.
	 *
	 * @since 4.0.0
	 * @since 4.0.1 The parameter `$format` is optional and its default value is 'xml'.
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param string                       $format          Optional. File format. Default 'xml'.
	 */
	public static function generate_catalog_file( $product_catalog, $format = 'xml' ) {
		$background = WC_Instagram_Backgrounds::get( 'generate_catalog' );

		if ( ! $background ) {
			return;
		}

		if ( $background->maybe_push_catalog( $product_catalog, $format ) ) {
			$background->save()->dispatch();
		}
	}

	/**
	 * Creates an empty catalog file.
	 *
	 * The file doesn't contain any product, but it avoids getting an error 404 when visiting the catalog URL.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param string                       $format          The file format.
	 * @return WC_Instagram_Product_Catalog_File|false
	 */
	public static function create_empty_file( $product_catalog, $format ) {
		$file = $product_catalog->get_file( $format, 'empty' );

		if ( $file ) {
			$file->init();
			$file->finish();
			$file->publish();
		}

		return $file;
	}
}

WC_Instagram_Product_Catalogs::init();
