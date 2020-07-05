<?php namespace Premmerce\WooCommercePinterest\Admin\Product\PinAll;

use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use WP_Query;

class PinAllCategory {

	/**
	 * PinAll data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * PinAllManager
	 *
	 * @var BulkPin
	 */
	private $pinAllManager;

	const PRODUCTS_PER_PROCESS = 10;

	const CURRENT_PROCESS_META_KEY = 'woocommerce_pinterest_pin_all_process_key';

	/**
	 * BulkPinCategory constructor.
	 *
	 * @param array $data
	 * @param PinAllManager $pinAllManager
	 */
	public function __construct( $data, PinAllManager $pinAllManager ) {
		$this->data          = $data;
		$this->pinAllManager = $pinAllManager;
	}

	public function process() {
		$query = new WP_Query( array(
			'product_cat'    => $this->data['slug'],
			'posts_per_page' => self::PRODUCTS_PER_PROCESS,
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => self::CURRENT_PROCESS_META_KEY,
					'value'   => $this->pinAllManager->getCurrentProcessUniqueKey(),
					'compare' => '!='
				),
				array(
					'key'     => self::CURRENT_PROCESS_META_KEY,
					'compare' => 'NOT EXISTS'
				)
			)
		) );

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $productId ) {
				$this->pinProduct( $productId );
			}

			$newProceededCount                = $this->data['products_processed'] + $query->post_count;
			$this->data['products_processed'] = min( $this->data['products_total'], $newProceededCount );
			$this->pinAllManager->setCategoryToProcess( $this->data );

		} else {
			// Finish current category
			$this->pinAllManager->setCategoryToProcess( array() );
		}

	}

	protected function pinProduct( $productId ) {

		$imagesToPin[] = get_post_thumbnail_id( $productId );

		if ( $this->pinAllManager->isPinAllGalleryImages() ) {

			$gallery = get_post_meta( $productId, '_product_image_gallery', true );

			$galleryIds = explode( ',', $gallery );

			$imagesToPin = array_merge( $imagesToPin, $galleryIds );
		}

		$imagesToPin = array_filter( $imagesToPin );

		try {
			$this->pinAllManager->getPinService()->synchronize( $productId, $imagesToPin );
		} catch ( PinterestModelException $e ) {
			wc_get_logger()->warning( $e->getMessage(), array( 'source' => 'Pinterest for Woocommerce' ) );
		}

		update_post_meta( $productId, self::CURRENT_PROCESS_META_KEY, $this->pinAllManager->getCurrentProcessUniqueKey() );
	}
}
