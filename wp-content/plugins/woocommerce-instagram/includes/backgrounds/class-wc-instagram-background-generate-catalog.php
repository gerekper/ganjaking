<?php
/**
 * Background: Generate catalog.
 *
 * @package WC_Instagram/Backgrounds
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Background_Generate_Catalog.
 */
class WC_Instagram_Background_Generate_Catalog extends WC_Instagram_Background_Process {

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->action = 'generate_catalog';

		parent::__construct();
	}

	/**
	 * Pushes the catalog file to the queue if not present.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed  $the_catalog Product catalog object, ID, or slug.
	 * @param string $format      The file format.
	 * @return bool
	 */
	public function maybe_push_catalog( $the_catalog, $format ) {
		$catalog = wc_instagram_get_product_catalog( $the_catalog );

		if ( ! $catalog ) {
			return false;
		}

		$catalog_file = $catalog->get_file( $format );

		// Don't queue the catalog file twice.
		if ( ! $catalog_file || $catalog_file->get_status() ) {
			return false;
		}

		$this->push_to_queue(
			array(
				'catalog_id' => $catalog->get_id(),
				'format'     => $format,
			)
		);

		$catalog_file->set_status( 'queued' );

		return true;
	}

	/**
	 * Gets the number of items to process per page.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	protected function get_limit() {
		/**
		 * Filters the number of items to process per page.
		 *
		 * @since 4.0.0
		 *
		 * @param int $limit The number of items to process per page.
		 */
		$limit = apply_filters( 'wc_instagram_product_catalog_items_per_page', 50 );

		return absint( $limit );
	}

	/**
	 * Task.
	 *
	 * @since 4.0.0
	 *
	 * @param array $args Optional. Task arguments. Default empty.
	 * @return array|false
	 */
	protected function task( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'format'     => 'xml',
				'catalog_id' => 0,
			)
		);

		$catalog_id = absint( $args['catalog_id'] );
		$catalog    = wc_instagram_get_product_catalog( $catalog_id );

		if ( ! $catalog ) {
			$this->log( "Product catalog with ID {$catalog_id} not found.", 'error' );

			return false;
		}

		$format      = sanitize_text_field( $args['format'] );
		$data_option = "wc_instagram_generate_catalog_{$catalog_id}_{$format}";
		$data        = get_option(
			$data_option,
			array(
				'offset'           => 0,
				'variation_offset' => 0,
			)
		);

		$limit         = $this->get_limit();
		$catalog_file  = $catalog->get_file( $format, 'tmp' );
		$catalog_items = new WC_Instagram_Product_Catalog_Items(
			$catalog,
			array(
				'limit'  => $limit,
				'offset' => $data['offset'],
			),
			'grouped'
		);

		if ( 0 === $data['offset'] ) {
			$this->log( 'Generating catalog: ' . $catalog->get_slug() . ".{$format}" );
			$catalog_file->set_status( 'processing' );
			$catalog_file->init();
		}

		$initial_offset = $data['offset'];
		$product_item   = $catalog_items->get_next();

		// Skip variation offset.
		if ( $data['variation_offset'] > 0 && is_array( $product_item ) ) {
			$product_item = array_slice( $product_item, 0, $data['variation_offset'] );
		}

		$data['variation_offset'] = 0;

		while ( $product_item ) {
			// Iterate over the product variations.
			if ( is_array( $product_item ) ) {
				foreach ( $product_item as $variation ) {
					$catalog_file->add_item( $variation );
					$data['variation_offset']++;

					update_option( $data_option, $data );
				}
			} else {
				$catalog_file->add_item( $product_item );
			}

			$data['offset']++;
			$data['variation_offset'] = 0;

			update_option( $data_option, $data );

			$product_item = $catalog_items->get_next();
		}

		$batch_items = absint( $data['offset'] - $initial_offset );

		$this->log( "Processed {$batch_items} items." );

		if ( $limit <= $batch_items ) {
			$catalog_file->close();

			return $args;
		}

		$catalog_file->finish();
		$catalog_file->close();
		$catalog_file->publish();
		$catalog_file->set_status( '' );

		delete_option( $data_option );

		$this->log( "Catalog generated successfully.\n" );

		return false;
	}

	/**
	 * Logs a message.
	 *
	 * @since 4.0.0
	 *
	 * @param string $message The message to log.
	 * @param string $level   Optional. The level. Default 'notice'.
	 */
	protected function log( $message, $level = 'notice' ) {
		wc_instagram_log( $message, $level, 'wc_instagram_product_catalogs' );
	}
}
