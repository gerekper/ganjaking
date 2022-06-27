<?php
/**
 * A class for handling the product items of a catalog.
 *
 * @package WC_Instagram/Product_Catalog
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Instagram_Product_Catalog_Items class.
 */
class WC_Instagram_Product_Catalog_Items {

	/**
	 * The Product Catalog object.
	 *
	 * @var WC_Instagram_Product_Catalog
	 */
	protected $product_catalog;

	/**
	 * How to store the product variations.
	 *
	 * Options:
	 *   - flattened: Store the variations at the same level as the product items.
	 *   - grouped:   Group the variations of a product in an array.
	 *
	 * @var string
	 */
	protected $variations_mode = 'flattened';

	/**
	 * The product IDs.
	 *
	 * @var ArrayIterator
	 */
	protected $product_ids;

	/**
	 * The products items.
	 *
	 * @var ArrayIterator
	 */
	protected $product_items;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Instagram_Product_Catalog $product_catalog Product catalog object.
	 * @param array                        $args            Optional. Query arguments.
	 * @param string                       $variations_mode Optional. How to store the product variations.
	 *                                                      Accepts 'flattened', 'grouped'. Default 'flattened'.
	 */
	public function __construct( $product_catalog, $args = array(), $variations_mode = 'flattened' ) {
		$args['return'] = 'ids';

		$this->variations_mode = $variations_mode;
		$this->product_catalog = $product_catalog;
		$this->product_ids     = new ArrayIterator( $this->product_catalog->query( $args ) );
		$this->product_items   = new ArrayIterator();
	}

	/**
	 * Gets the Product Catalog object.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_Instagram_Product_Catalog
	 */
	public function get_product_catalog() {
		return $this->product_catalog;
	}

	/**
	 * Gets the mode in which the product variations are stored.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_variations_mode() {
		return $this->variations_mode;
	}

	/**
	 * Gets the product IDs.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public function get_product_ids() {
		return $this->product_ids->getArrayCopy();
	}

	/**
	 * Gets all the product items.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_Instagram_Product_Catalog_Item[]
	 */
	public function get_all() {
		$this->load_product_items();

		return $this->product_items->getArrayCopy();
	}

	/**
	 * Gets if there are still product items to process.
	 *
	 * @since 4.1.3
	 *
	 * @return bool
	 */
	public function has_next() {
		return ( $this->product_items->valid() || $this->product_ids->valid() );
	}

	/**
	 * Gets the next product item.
	 *
	 * @since 4.0.0
	 *
	 * @return WC_Instagram_Product_Catalog_Item|WC_Instagram_Product_Catalog_Item_Variation[]|false
	 */
	public function get_next() {
		$product_item = false;

		if ( ! $this->product_items->valid() && $this->product_ids->valid() ) {
			$this->load_product_item( $this->product_ids->current() );
			$this->product_ids->next();
		}

		if ( $this->product_items->valid() ) {
			$product_item = $this->product_items->current();
			$this->product_items->next();
		}

		return $product_item;
	}

	/**
	 * Loads the product items.
	 *
	 * @since 4.0.0
	 */
	protected function load_product_items() {
		array_map( array( $this, 'load_product_item' ), $this->get_product_ids() );
	}

	/**
	 * Loads the product item for the specified product.
	 *
	 * @since 4.0.0
	 *
	 * @param int $product_id Product ID.
	 */
	protected function load_product_item( $product_id ) {
		// Product already loaded.
		if ( $this->product_items->offsetExists( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		try {
			if ( $product instanceof WC_Product_Variable && $this->product_catalog->get_include_variations() ) {
				$stock_status     = $this->product_catalog->get_stock_status();
				$group_variations = ( 'grouped' === $this->variations_mode );
				$variations       = $product->get_available_variations();
				$item_variations  = array();

				foreach ( $variations as $variation ) {
					if ( 'instock' === $stock_status && ! $variation['is_in_stock'] ) {
						continue;
					}

					$variation_id      = $variation['variation_id'];
					$product_variation = wc_get_product( $variation_id );

					if ( ! $product_variation ) {
						continue;
					}

					$item_variation = new WC_Instagram_Product_Catalog_Item_Variation( $product_variation );

					if ( $group_variations ) {
						$item_variations[ $variation_id ] = $item_variation;
					} else {
						$this->product_items->offsetSet( $variation_id, $item_variation );
					}
				}

				if ( ! empty( $item_variations ) && $group_variations ) {
					$this->product_items->offsetSet( $product_id, $item_variations );
				}
			} else {
				$this->product_items->offsetSet( $product_id, new WC_Instagram_Product_Catalog_Item( $product ) );
			}
		} catch ( Exception $e ) {
			return;
		}
	}
}
