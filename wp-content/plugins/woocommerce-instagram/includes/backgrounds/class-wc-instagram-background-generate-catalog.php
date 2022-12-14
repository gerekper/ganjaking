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
		$limit = apply_filters( 'wc_instagram_product_catalog_items_per_page', 25 );

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
				'total'            => 0,
			)
		);

		$initial_data = $data;
		$catalog_file = $catalog->get_file( $format, 'tmp' );

		// Cancel the process.
		if ( 'canceling' === $catalog_file->get_status() ) {
			$catalog_file->delete_context();
			$catalog_file->set_status( '' );

			delete_option( $data_option );

			$this->log(
				sprintf(
					"Canceled the process for generating the catalog %s.\n",
					$catalog->get_slug() . ".{$format}"
				)
			);

			return false;
		}

		$catalog_items = new WC_Instagram_Product_Catalog_Items(
			$catalog,
			array(
				'limit'  => $this->get_limit(),
				'offset' => $data['offset'],
			),
			'grouped'
		);

		if ( 0 === $data['offset'] ) {
			$data['total'] = count( $catalog->get_product_ids() );
			update_option( $data_option, $data );

			$this->log(
				sprintf(
					'Generating catalog file %1$s with %2$d products.',
					$catalog->get_slug() . ".{$format}",
					$data['total']
				)
			);
			$catalog_file->set_status( 'processing' );
			$catalog_file->init();
		}

		while ( $catalog_items->has_next() ) {
			$product_item = $catalog_items->get_next();

			// Iterate over the product variations.
			if ( is_array( $product_item ) ) {
				// Skip variation offset for the first product item.
				if ( $data['variation_offset'] > 0 ) {
					$product_item = array_slice( $product_item, 0, $data['variation_offset'] );
				}

				foreach ( $product_item as $variation ) {
					$catalog_file->add_item( $variation );
					$data['variation_offset']++;

					update_option( $data_option, $data );
				}
			} elseif ( $product_item ) {
				$catalog_file->add_item( $product_item );
			}

			$data['offset']++;
			$data['variation_offset'] = 0;

			update_option( $data_option, $data );
		}

		// This iteration didn't process any product or variation. Skip the process to avoid lock-in.
		$skip = ( $data['offset'] === $initial_data['offset'] && $data['variation_offset'] === $initial_data['variation_offset'] );

		if ( $skip ) {
			$this->log( 'No more products found. Finishing the process&hellip;' );
		} else {
			$this->log( "Processed {$data['offset']} of {$data['total']} products." );
		}

		if ( ! $skip && $data['offset'] < $data['total'] ) {
			update_option( $data_option, $data );
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
