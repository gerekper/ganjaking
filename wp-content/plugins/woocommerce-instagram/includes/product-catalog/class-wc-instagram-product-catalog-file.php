<?php
/**
 * A class for handling the file of a product catalog.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_File class.
 */
class WC_Instagram_Product_Catalog_File {

	/**
	 * The Product Catalog.
	 *
	 * @var WC_Instagram_Product_Catalog
	 */
	protected $product_catalog;

	/**
	 * The file format.
	 *
	 * @var string
	 */
	protected $format;

	/**
	 * The file context.
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * The catalog file.
	 *
	 * @var resource
	 */
	protected $file;

	/**
	 * The catalog formatter.
	 *
	 * @var WC_Instagram_Product_Catalog_Format
	 */
	protected $formatter;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param string                       $format          The file format.
	 * @param string                       $context         Optional. The file context. Default empty.
	 */
	public function __construct( $product_catalog, $format, $context = '' ) {
		$this->product_catalog = $product_catalog;
		$this->format          = $format;
		$this->context         = $context;
	}

	/**
	 * Gets the catalog filename.
	 *
	 * @since 4.0.0
	 *
	 * @param string $context Optional. The file context. Default empty.
	 * @return string
	 */
	public function get_filename( $context = '' ) {
		$filename = ( $this->product_catalog->get_slug() . '.' . $this->format );

		if ( ! empty( $context ) ) {
			$filename = "{$context}-{$filename}";
		}

		$filename = WC_INSTAGRAM_CATALOGS_PATH . '/' . $filename;

		/**
		 * Filters the catalog filename.
		 *
		 * @since 4.0.0
		 *
		 * @param string                       $filename        The catalog filename.
		 * @param WC_Instagram_Product_Catalog $product_catalog Product Catalog object.
		 * @param string                       $format          The file format.
		 * @param string                       $context         The file context.
		 */
		return apply_filters( 'wc_instagram_product_catalog_filename', $filename, $this->product_catalog, $this->format, $context );
	}

	/**
	 * Gets the date in which the catalog file was updated.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_DateTime|false
	 */
	public function get_last_modified() {
		$filename = $this->get_filename();

		if ( ! is_readable( $filename ) ) {
			return false;
		}

		$timestamp = filemtime( $filename );

		return wc_instagram_timestamp_to_datetime( $timestamp );
	}

	/**
	 * Gets the status of the catalog file.
	 *
	 * @since 4.0.1
	 *
	 * @return string
	 */
	public function get_status() {
		return (string) get_transient( $this->get_transient_name( 'status' ) );
	}

	/**
	 * Sets the status of the catalog file.
	 *
	 * @since 4.0.1
	 *
	 * @param string $status The file status.
	 */
	public function set_status( $status ) {
		$transient_name = $this->get_transient_name( 'status' );

		if ( $status ) {
			set_transient( $transient_name, $status, DAY_IN_SECONDS );
		} else {
			delete_transient( $transient_name );
		}
	}

	/**
	 * Opens the file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $mode The type of access.
	 */
	public function open( $mode ) {
		if ( ! $this->file ) {
			$this->file = fopen( $this->get_filename( $this->context ), $mode ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		}

		return $this->file;
	}

	/**
	 * Writes the content to the file.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $content The content to include in the file.
	 * @return int|false The number of bytes written. False on failure.
	 */
	public function write( $content ) {
		if ( ! $content ) {
			return false;
		}

		// Open the file to append content to it.
		if ( ! $this->file ) {
			$this->open( 'a' );
		}

		return fwrite( $this->file, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
	}

	/**
	 * Closes the file.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function close() {
		if ( ! $this->file ) {
			return false;
		}

		return fclose( $this->file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
	}

	/**
	 * Initializes the file.
	 *
	 * @since 4.0.0
	 *
	 * @return int|false The number of bytes written. False on failure.
	 */
	public function init() {
		// Close the file in case it's already open.
		$this->close();

		// Open the file and truncate the content.
		$this->open( 'w' );

		// Add the catalog starting content.
		return $this->write( $this->get_formatter()->get_output_start() );
	}

	/**
	 * Adds the final content to the file.
	 *
	 * @since 4.0.0
	 *
	 * @return int|false The number of bytes written. False on failure.
	 */
	public function finish() {
		return $this->write( $this->get_formatter()->get_output_end() );
	}

	/**
	 * Adds a product item to the file.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog_Item $product_item Product item.
	 * @return int|false The number of bytes written. False on failure.
	 */
	public function add_item( $product_item ) {
		return $this->write( $this->get_formatter()->get_output_item( $product_item ) );
	}

	/**
	 * Publishes the catalog file.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function publish() {
		if ( ! $this->context ) {
			return true;
		}

		return rename( $this->get_filename( $this->context ), $this->get_filename() );
	}

	/**
	 * Deletes the catalog file.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $with_context Optional. Should delete the file with context too?. Default false.
	 * @return bool
	 */
	public function delete( $with_context = false ) {
		$deleted = $this->delete_file( $this->get_filename() );

		if ( $with_context ) {
			$deleted = ( $this->delete_context() || $deleted );
		}

		return $deleted;
	}

	/**
	 * Deletes the contextual file of the catalog.
	 *
	 * @since 4.2.0
	 *
	 * @return bool
	 */
	public function delete_context() {
		return ( ! empty( $this->context ) && $this->delete_file( $this->get_filename( $this->context ) ) );
	}

	/**
	 * Gets the file content.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_content() {
		$filename = $this->get_filename();
		$content  = '';

		if ( is_readable( $filename ) ) {
			$content = (string) file_get_contents( $filename ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents
		}

		return $content;
	}

	/**
	 * Gets the catalog formatter.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_Instagram_Product_Catalog_Format|false
	 */
	protected function get_formatter() {
		if ( is_null( $this->formatter ) ) {
			$this->formatter = wc_instagram_get_product_catalog_formatter( $this->product_catalog, array(), $this->format );
		}

		return $this->formatter;
	}

	/**
	 * Deletes a file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $filename The file to delete.
	 * @return bool
	 */
	protected function delete_file( $filename ) {
		return ( file_exists( $filename ) && unlink( $filename ) );
	}

	/**
	 * Gets the transient name for the specified key.
	 *
	 * @since 4.0.1
	 *
	 * @param string $key Action key.
	 * @return string
	 */
	protected function get_transient_name( $key ) {
		return "wc_instagram_catalog_file_{$key}_{$this->product_catalog->get_id()}_{$this->format}";
	}
}
