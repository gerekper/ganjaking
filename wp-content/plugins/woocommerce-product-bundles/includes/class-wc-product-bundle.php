<?php
/**
 * WC_Product_Bundle class
 *
 * @package  WooCommerce Product Bundles
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Class.
 *
 * @class    WC_Product_Bundle
 * @version  6.15.4
 */
class WC_Product_Bundle extends WC_Product {

	/**
	 * Group mode options data.
	 * @see 'WC_Product_Bundle::get_group_mode_options'.
	 * @var array
	 */
	private static $group_mode_options_data = null;

	/**
	 * Layout options data.
	 * @see 'WC_Product_Bundle::get_layout_options'.
	 * @var array
	 */
	private static $layout_options_data = null;

	/**
	 * Array of bundle-type extended product data fields used in CRUD and runtime operations.
	 * @var array
	 */
	private $extended_data = array(
		'virtual_bundle'                  => false,
		'min_bundle_size'                 => '',
		'max_bundle_size'                 => '',
		'layout'                          => 'default',
		'group_mode'                      => 'parent',
		'bundle_stock_quantity'           => '',
		'bundled_items_stock_status'      => '',
		'bundled_items_stock_sync_status' => '',
		'editable_in_cart'                => false,
		'aggregate_weight'                => false,
		'sold_individually_context'       => 'product',
		'add_to_cart_form_location'       => 'default',
		'min_raw_price'                   => '',
		'min_raw_regular_price'           => '',
		'max_raw_price'                   => '',
		'max_raw_regular_price'           => ''
	);

	/**
	 * Array of bundled item data objects.
	 * @var array
	 */
	private $bundled_data_items = null;

	/**
	 * Bundled item data objects that need deleting are stored here.
	 * @var array
	 */
	private $bundled_data_items_delete_queue = array();

	/**
	 * Indicates whether bundled data items have temporary IDs (saving needed).
	 * @var array
	 */
	private $bundled_data_items_save_pending = false;

	/**
	 * Array of form data for consumption by the front-end script.
	 * @var array
	 */
	private $bundle_form_data = array();

	/**
	 * Runtime cache for bundle prices.
	 * @var array
	 */
	private $bundle_price_cache = array();

	/**
	 * Bundle object instance context.
	 */
	private $object_context = '';

	/**
	 * Storage of 'contains' keys, most set during sync.
	 * @var array
	 */
	private $contains = array();

	/**
	 * True if the bundle is in sync with bundled items.
	 * @var boolean
	 */
	private $is_synced = false;

	/**
	 * True if the bundle is currently syncing.
	 * @var boolean
	 */
	private $is_syncing = false;

	/**
	 * The type of data store to use.
	 * @var string
	 */
	private $data_store_type = 'bundle';

	/**
	 * Constructor.
	 *
	 * @param  mixed  $product
	 */
	public function __construct( $product = 0 ) {

		// Initialize the data store type. Yes, WC 3.0 decouples the data store from the product class.
		if ( ( $product instanceof WC_Product ) && false === $product->is_type( 'bundle' ) ) {
			$this->data_store_type = $product->get_type();
		}

		// Initialize private properties.
		$this->load_defaults();

		// Define/load type-specific data.
		$this->load_extended_data();

		// Load product data.
		parent::__construct( $product );
	}

	/**
	 * Get internal type.
	 *
	 * @since  5.1.0
	 *
	 * @return string
	 */
	public function get_type() {
		return 'bundle';
	}

	/**
	 * Get data store type.
	 *
	 * @since  5.6.0
	 *
	 * @return string
	 */
	public function get_data_store_type() {
		return $this->data_store_type;
	}

	/**
	 * Load property and runtime cache defaults to trigger a re-sync.
	 *
	 * @since 5.2.0
	 */
	public function load_defaults( $reset_objects = false ) {

		$this->contains = array(
			'priced_individually'               => null,
			'shipped_individually'              => null,
			'assembled'                         => null,
			'optional'                          => false,
			'mandatory'                         => false,
			'on_backorder'                      => false,
			'subscriptions'                     => false,
			'subscriptions_priced_individually' => false,
			'subscriptions_priced_variably'     => false,
			'multiple_subscriptions'            => false,
			'nyp'                               => false,
			'non_purchasable'                   => false,
			'options'                           => false,
			'out_of_stock'                      => false, // Not including optional and zero min qty items (bundle can still be purchased).
			'out_of_stock_strict'               => false, // Including optional and zero min qty items (admin needs to be aware).
			'sold_in_multiples'                 => false,
			'sold_individually'                 => false,
			'discounted'                        => false,
			'discounted_mandatory'              => false,
			'configurable_quantities'           => false,
			'hidden'                            => false,
			'visible'                           => false
		);

		$this->is_synced          = false;
		$this->bundle_form_data   = array();
		$this->bundle_price_cache = array();

		if ( $reset_objects ) {
			$this->bundled_data_items = null;
		}
	}

	/**
	 * Define type-specific data.
	 *
	 * @since  5.2.0
	 */
	private function load_extended_data() {

		// Back-compat.
		$this->product_type = 'bundle';

		// Define type-specific fields and let WC use our data store to read the data.
		$this->data = array_merge( $this->data, $this->extended_data );
	}

	/**
	 * Sync bundle props with bundled item objects.
	 *
	 * @since  5.5.0
	 *
	 * @param  bool  $force
	 * @return bool
	 */
	public function sync( $force = false ) {

		if ( $this->is_synced && false === $force ) {
			return false;
		}

		$this->is_syncing = true;

		$bundled_items = $this->get_bundled_items();
		$group_mode    = $this->get_group_mode();
		$is_front_end  = WC_PB_Helpers::is_front_end();

		if ( ! empty( $bundled_items ) ) {

			// Scan bundled items and sync bundle properties.
			foreach ( $bundled_items as $bundled_item ) {

				$min_quantity = $bundled_item->get_quantity( 'min', array( 'context' => 'sync', 'check_optional' => true ) );
				$max_quantity = $bundled_item->get_quantity( 'max', array( 'context' => 'sync' ) );

				if ( $min_quantity !== $max_quantity ) {
					$this->contains[ 'configurable_quantities' ] = true;
				}

				if ( $bundled_item->is_sold_individually() ) {
					$this->contains[ 'sold_individually' ] = true;
				} else {
					$this->contains[ 'sold_in_multiples' ] = true;
				}

				if ( $bundled_item->is_optional() ) {
					$this->contains[ 'optional' ]                = true;
					$this->contains[ 'configurable_quantities' ] = true;
				} elseif ( $min_quantity > 0 ) {
					$this->contains[ 'mandatory' ] = true;
				}

				if ( ! $this->contains[ 'out_of_stock_strict' ] && false === $bundled_item->has_enough_stock( $min_quantity ) ) {
					$this->contains[ 'out_of_stock_strict' ] = true;
					if ( false === $bundled_item->is_optional() && $min_quantity !== 0 ) {
						$this->contains[ 'out_of_stock' ] = true;
					}
				}

				if ( ! $this->contains[ 'on_backorder' ] && $bundled_item->is_on_backorder() && $bundled_item->product->backorders_require_notification() && false === $bundled_item->is_optional() && $min_quantity !== 0 ) {
					$this->contains[ 'on_backorder' ] = true;
				}

				if ( false === $bundled_item->is_purchasable() && false === $bundled_item->is_optional() && $min_quantity !== 0 ) {
					$this->contains[ 'non_purchasable' ] = true;
				}

				if ( ( ! $this->contains[ 'discounted' ] || ! $this->contains[ 'discounted_mandatory' ] ) && $bundled_item->get_discount( 'sync' ) > 0 ) {
					$this->contains[ 'discounted' ] = true;
					if ( false === $bundled_item->is_optional() && $min_quantity !== 0 ) {
						$this->contains[ 'discounted_mandatory' ] = true;
					}
				}

				if ( ! $this->contains[ 'nyp' ] && $bundled_item->is_nyp() ) {
					$this->contains[ 'nyp' ] = true;
				}

				if ( $bundled_item->is_subscription() ) {

					if ( $this->contains[ 'subscriptions' ] ) {
						$this->contains[ 'multiple_subscriptions' ] = true;
					}

					$this->contains[ 'subscriptions' ] = true;

					if ( $bundled_item->is_priced_individually() ) {
						$this->contains[ 'subscriptions_priced_individually' ] = true;
					}

					// If it's a variable sub with a variable price, show 'From:' string before Bundle price.
					if ( $bundled_item->is_variable_subscription() ) {
						$bundled_item->add_price_filters();
						if ( $bundled_item->product->get_variation_price( 'min' ) !== $bundled_item->product->get_variation_price( 'max' ) || $bundled_item->product->get_meta( '_min_variation_period', true ) !== $bundled_item->product->get_meta( '_max_variation_period', true ) || $bundled_item->product->get_meta( '_min_variation_period_interval', true ) !== $bundled_item->product->get_meta( '_max_variation_period_interval', true ) ) {
							$this->contains[ 'subscriptions_priced_variably' ] = true;
						}
						$bundled_item->remove_price_filters();
					}
				}

				// Significant cost due to get_product_addons - skip this in the admin area since it is only used to modify add to cart button behaviour.
				if ( $is_front_end ) {
					if ( false === $bundled_item->is_optional() ) {
						if ( ! $this->contains[ 'options' ] && $bundled_item->requires_input() ) {
							$this->contains[ 'options' ] = true;
						}
					}
				}

				if ( $bundled_item->is_visible() ) {
					$this->contains[ 'visible' ] = true;
				} else {
					$this->contains[ 'hidden' ] = true;
				}
			}
		}

		/**
		 * Give third parties a chance to modify the content flags of this bundle.
		 *
		 * @since  6.5.2
		 *
		 * @param  array              $contains
		 * @param  WC_Product_Bundle  $this
		 */
		$this->contains = apply_filters( 'woocommerce_bundles_synced_contents_data', $this->contains, $this );

		// Allow adding to cart via ajax if no user input is required.
		if ( $is_front_end ) {
			// Is a child selection required by the chosen group mode?
			if ( false === $this->contains[ 'mandatory' ] && false === self::group_mode_has( $group_mode, 'parent_item' ) ) {
				$this->contains[ 'options' ] = true;
			}
			// Any addons at bundle level?
			if ( ! $this->contains[ 'options' ] && WC_PB()->compatibility->has_addons( $this, true ) ) {
				$this->contains[ 'options' ] = true;
			}
		}

		if ( ! $this->contains[ 'options' ] ) {
			$this->supports[] = 'ajax_add_to_cart';
		}

		// Set this now to avoid infinite loops.
		$this->is_synced  = true;
		$this->is_syncing = false;

		/*
		 * Sync bundled items stock status.
		 */
		$this->sync_stock();

		/*
		 * Sync min/max raw prices.
		 */
		$this->sync_raw_prices();

		/**
		 * 'woocommerce_bundles_synced_bundle' action.
		 *
		 * @param  WC_Product_Bundle  $this
		 */
		do_action( 'woocommerce_bundles_synced_bundle', $this );

		return true;
	}

	/**
	 * Sync product bundle raw price meta.
	 *
	 * @since  5.5.0
	 *
	 * @return boolean
	 */
	private function sync_raw_prices() {

		$min_raw_price         = $this->get_price( 'sync' );
		$min_raw_regular_price = $this->get_regular_price( 'sync' );
		$max_raw_price         = $this->get_price( 'sync' );
		$max_raw_regular_price = $this->get_regular_price( 'sync' );

		if ( $this->is_nyp() ) {
			$max_raw_price = $max_raw_regular_price = INF;
		}

		$bundled_items = $this->get_bundled_items( 'edit' );

		if ( ! empty( $bundled_items ) ) {
			foreach ( $bundled_items as $bundled_item ) {

				if ( $bundled_item->is_priced_individually() ) {

					$min_quantity = $bundled_item->get_quantity( 'min', array( 'context' => 'price', 'check_optional' => true ) );
					$max_quantity = $bundled_item->get_quantity( 'max', array( 'context' => 'price' ) );

					$min_raw_price         += $min_quantity * (double) $bundled_item->min_price;
					$min_raw_regular_price += $min_quantity * (double) $bundled_item->min_regular_price;

					if ( '' === $max_quantity ) {
						$max_raw_price = $max_raw_regular_price = INF;
					}

					$item_max_raw_price         = INF !== $bundled_item->max_price ? (double) $bundled_item->max_price : INF;
					$item_max_raw_regular_price = INF !== $bundled_item->max_regular_price ? (double) $bundled_item->max_regular_price : INF;

					if ( INF !== $max_raw_price ) {
						if ( INF !== $item_max_raw_price ) {
							$max_raw_price         += $max_quantity * $item_max_raw_price;
							$max_raw_regular_price += $max_quantity * $item_max_raw_regular_price;
						} else {
							$max_raw_price = $max_raw_regular_price = INF;
						}
					}
				}
			}

			// Calculate the min bundled item price and use it when the active group mode requires a child selection.
			if ( false === self::group_mode_has( $this->get_group_mode( 'edit' ), 'parent_item' ) && false === $this->contains[ 'mandatory' ] ) {

				$min_item_price = null;

				foreach ( $bundled_items as $bundled_item ) {
					$min_quantity = max( $bundled_item->get_quantity( 'min' ), 1 );
					if ( is_null( $min_item_price ) || $min_quantity * (double) $bundled_item->min_price < $min_item_price ) {
						$min_item_price = $min_quantity * (double) $bundled_item->min_price;
					}
				}

				if ( $min_item_price > 0 ) {
					$min_raw_price = $min_item_price;
				}
			}
		}

		/**
		 * 'woocommerce_bundle_min/max_raw_[regular_]price' filters.
		 *
		 * @since  5.8.1
		 *
		 * @param  mixed              $price
		 * @param  WC_Product_Bundle  $this
		 */
		$min_raw_price         = apply_filters( 'woocommerce_bundle_min_raw_price', $min_raw_price, $this );
		$min_raw_regular_price = apply_filters( 'woocommerce_bundle_min_raw_regular_price', $min_raw_regular_price, $this );
		$max_raw_price         = apply_filters( 'woocommerce_bundle_max_raw_price', $max_raw_price, $this );
		$max_raw_regular_price = apply_filters( 'woocommerce_bundle_max_raw_regular_price', $max_raw_regular_price, $this );

		$raw_price_meta_changed = false;

		if ( $this->get_min_raw_price( 'sync' ) !== $min_raw_price || $this->get_min_raw_regular_price( 'sync' ) !== $min_raw_regular_price || $this->get_max_raw_price( 'sync' ) !== $max_raw_price || $this->get_max_raw_regular_price( 'sync' ) !== $max_raw_regular_price ) {
			$raw_price_meta_changed = true;
		}

		$this->set_min_raw_price( $min_raw_price );
		$this->set_min_raw_regular_price( $min_raw_regular_price );
		$this->set_max_raw_price( $max_raw_price );
		$this->set_max_raw_regular_price( $max_raw_regular_price );

		if ( $raw_price_meta_changed ) {

			if ( 'bundle' === $this->get_data_store_type() ) {
				$this->data_store->save_raw_price_props( $this );
			}

			return true;
		}

		return false;
	}

	/**
	 * Syncs stock data. Reads data from bundled data items, avoiding overhead of 'WC_Bundled_Item'.
	 *
	 * @since  6.5.0
	 *
	 * @return bool
	 */
	public function sync_stock() {

		$props_to_save          = array();
		$bundled_items_in_stock = true;

		/*
		 * Sync 'bundled_items_stock_status' prop.
		 */
		foreach ( $this->get_bundled_data_items( 'edit' ) as $bundled_data_item ) {

			$bundled_item_stock_status = $bundled_data_item->get_meta( 'stock_status' );

			if ( is_null( $bundled_item_stock_status ) ) {
				$bundled_item              = $this->get_bundled_item( $bundled_data_item, 'edit' );
				$bundled_item_stock_status = $bundled_item && $bundled_item->exists() ? $bundled_item->get_stock_status() : null;
			}

			if ( 'out_of_stock' === $bundled_item_stock_status && 'no' === $bundled_data_item->get_meta( 'optional' ) && $bundled_data_item->get_meta( 'quantity_min' ) > 0 ) {
				$bundled_items_in_stock = false;
			}
		}

		/**
		 * 'woocommerce_synced_bundled_items_stock_status' filter.
		 *
		 * @since  6.5.0
		 *
		 * @param  string             $bundled_items_stock_status
		 * @param  WC_Product_Bundle  $this
		 */
		$bundled_items_stock_status = apply_filters( 'woocommerce_synced_bundled_items_stock_status', $bundled_items_in_stock ? 'instock' : 'outofstock', $this );

		if ( $bundled_items_stock_status !== $this->get_bundled_items_stock_status( 'edit' ) ) {
			$this->set_bundled_items_stock_status( $bundled_items_stock_status );
			$props_to_save[] = 'bundled_items_stock_status';
		}

		/*
		 * Sync 'bundle_stock_quantity' prop.
		 */

		$bundle_stock_quantity = '';

		if ( in_array( $this->get_bundle_stock_status( 'edit' ), array( 'outofstock', 'insufficientstock' ) ) ) {

			$bundle_stock_quantity = 0;

		} else {

			// Find parent quantity.
			$parent_stock_quantity = '';

			if ( ! $this->backorders_allowed() && $this->managing_stock() ) {
				$parent_stock_quantity = $this->get_stock_quantity( 'edit' );
				$parent_stock_quantity = null === $parent_stock_quantity ? '' : $parent_stock_quantity;
			}

			// Find bundled items stock quantity based on the least stocked item.
			$bundled_items_stock_quantity = '';

			foreach ( $this->get_bundled_data_items( 'edit' ) as $bundled_data_item ) {

				$bundled_item_min_qty = $bundled_data_item->get_meta( 'quantity_min' );

				if ( 'yes' === $bundled_data_item->get_meta( 'optional' ) || 0 === $bundled_item_min_qty || is_null( $bundled_item_min_qty ) ) {
					continue;
				}

				$bundled_item_stock_quantity = $bundled_data_item->get_meta( 'max_stock' );

				// Infinite qty? Move on.
				if ( '' === $bundled_item_stock_quantity || is_null( $bundled_item_stock_quantity ) ) {
					continue;
				}

				// No stock? Break.
				if ( 0 === $bundled_item_stock_quantity ) {
					$bundled_items_stock_quantity = 0;
					break;
				}

				// How many times could this bundle be purchased if it only contained this item?
				$bundled_item_parent_stock_quantity = intval( floor( $bundled_item_stock_quantity / $bundled_item_min_qty ) );

				if ( '' === $bundled_items_stock_quantity || $bundled_item_parent_stock_quantity < $bundled_items_stock_quantity ) {
					$bundled_items_stock_quantity = $bundled_item_parent_stock_quantity;
				}
			}

			if ( '' === $parent_stock_quantity && '' === $bundled_items_stock_quantity ) {
				$bundle_stock_quantity = '';
			} elseif ( 0 === $parent_stock_quantity || 0 === $bundled_items_stock_quantity ) {
				$bundle_stock_quantity = 0;
			} elseif ( '' === $parent_stock_quantity ) {
				$bundle_stock_quantity = $bundled_items_stock_quantity;
			} elseif ( '' === $bundled_items_stock_quantity ) {
				$bundle_stock_quantity = $parent_stock_quantity;
			} else {
				$bundle_stock_quantity = intval( min( $bundled_items_stock_quantity, $parent_stock_quantity ) );
			}
		}

		/**
		 * 'woocommerce_synced_bundle_stock_quantity' filter.
		 *
		 * @since  6.5.0
		 *
		 * @param  int                $bundle_stock_quantity
		 * @param  WC_Product_Bundle  $this
		 */
		$bundle_stock_quantity = apply_filters( 'woocommerce_synced_bundle_stock_quantity', $bundle_stock_quantity, $this );

		if ( $bundle_stock_quantity !== $this->get_bundle_stock_quantity( 'edit' ) ) {
			$this->set_bundle_stock_quantity( $bundle_stock_quantity );
			$props_to_save[] = 'bundle_stock_quantity';
		}

		/*
		 * Sync 'bundled_items_stock_sync_status' prop.
		 */

		if ( 'unsynced' === $this->get_bundled_items_stock_sync_status() ) {
			$this->set_bundled_items_stock_sync_status( 'synced' );
			$props_to_save[] = 'bundled_items_stock_sync_status';
		}

		if ( 'bundle' === $this->get_data_store_type() ) {
			$this->data_store->save_stock_sync_props( $this, $props_to_save );
		}

		return ! empty( $props_to_save );
	}

	/**
	 * Returns form data passed to JS.
	 *
	 * @since  6.4.0
	 *
	 * @return array
	 */
	public function get_bundle_form_data() {

		if ( empty( $this->bundle_form_data ) ) {

			$data = array();

			$raw_bundle_price_min = $this->get_bundle_price( 'min', true );
			$raw_bundle_price_max = $this->get_bundle_price( 'max', true );

			$group_mode = $this->get_group_mode();

			$data[ 'layout' ] = $this->get_layout();

			$data[ 'hide_total_on_validation_fail' ] = 'no';

			$data[ 'zero_items_allowed' ] = self::group_mode_has( $group_mode, 'parent_item' ) ? 'yes' : 'no';

			$data[ 'raw_bundle_price_min' ] = (double) $raw_bundle_price_min;
			$data[ 'raw_bundle_price_max' ] = '' === $raw_bundle_price_max ? '' : (double) $raw_bundle_price_max;

			$data[ 'is_purchasable' ]    = $this->is_purchasable() ? 'yes' : 'no';
			$data[ 'show_free_string' ]  = 'no';
			$data[ 'show_total_string' ] = 'no';

			$data[ 'prices' ]         = array();
			$data[ 'regular_prices' ] = array();

			$data[ 'prices_tax' ] = array();

			$data[ 'addons_prices' ]         = array();
			$data[ 'regular_addons_prices' ] = array();

			$data[ 'quantities' ] = array();

			$data[ 'product_ids' ] = array();

			$data[ 'is_sold_individually' ] = array();

			$data[ 'recurring_prices' ]         = array();
			$data[ 'regular_recurring_prices' ] = array();

			$data[ 'recurring_html' ] = array();
			$data[ 'recurring_keys' ] = array();

			$data[ 'base_price' ]         = $this->get_price();
			$data[ 'base_regular_price' ] = $this->get_regular_price();
			$data[ 'base_price_tax' ]     = WC_PB_Product_Prices::get_tax_ratios( $this );

			$totals = new stdClass;

			$totals->price          = 0.0;
			$totals->regular_price  = 0.0;
			$totals->price_incl_tax = 0.0;
			$totals->price_excl_tax = 0.0;

			$data[ 'base_price_totals' ] = $totals;
			$data[ 'subtotals' ]         = $totals;
			$data[ 'totals' ]            = $totals;
			$data[ 'recurring_totals' ]  = $totals;

			$bundled_items = $this->get_bundled_items();

			if ( empty( $bundled_items ) ) {
				return;
			}

			foreach ( $bundled_items as $bundled_item ) {

				if ( ! $bundled_item->is_purchasable() ) {
					continue;
				}

				$min_quantity = $bundled_item->get_quantity( 'min', array( 'context' => 'sync', 'check_optional' => true ) );
				$max_quantity = $bundled_item->get_quantity( 'max', array( 'context' => 'sync' ) );

				$data[ 'has_variable_quantity' ][ $bundled_item->get_id() ] = $min_quantity !== $max_quantity ? 'yes' : 'no';

				$data[ 'quantities_available' ][ $bundled_item->get_id() ]            = '';
				$data[ 'is_in_stock' ][ $bundled_item->get_id() ]                     = '';
				$data[ 'backorders_allowed' ][ $bundled_item->get_id() ]              = '';
				$data[ 'backorders_require_notification' ][ $bundled_item->get_id() ] = '';

				if ( $bundled_item->get_product()->is_type( 'simple' ) ) {

					$data[ 'quantities_available' ][ $bundled_item->get_id() ]            = $bundled_item->get_stock_quantity();
					$data[ 'is_in_stock' ][ $bundled_item->get_id() ]                     = $bundled_item->is_in_stock() ? 'yes' : 'no';
					$data[ 'backorders_allowed' ][ $bundled_item->get_id() ]              = $bundled_item->is_on_backorder() || $bundled_item->get_product()->backorders_allowed() ? 'yes' : 'no';
					$data[ 'backorders_require_notification' ][ $bundled_item->get_id() ] = $bundled_item->get_product()->backorders_require_notification() ? 'yes' : 'no';
				}

				$data[ 'is_nyp' ][ $bundled_item->get_id() ] = $bundled_item->is_nyp() ? 'yes' : 'no';

				$data[ 'product_ids' ][ $bundled_item->get_id() ] = $bundled_item->get_product_id();

				$data[ 'is_sold_individually' ][ $bundled_item->get_id() ]   = $bundled_item->is_sold_individually() ? 'yes' : 'no';
				$data[ 'is_priced_individually' ][ $bundled_item->get_id() ] = $bundled_item->is_priced_individually() ? 'yes' : 'no';

				$data[ 'prices' ][ $bundled_item->get_id() ]         = $bundled_item->get_price( 'min' );
				$data[ 'regular_prices' ][ $bundled_item->get_id() ] = $bundled_item->get_regular_price( 'min' );

				$data[ 'prices_tax' ][ $bundled_item->get_id() ] = WC_PB_Product_Prices::get_tax_ratios( $bundled_item->product );

				$data[ 'addons_prices' ][ $bundled_item->get_id() ]         = '';
				$data[ 'regular_addons_prices' ][ $bundled_item->get_id() ] = '';

				$data[ 'bundled_item_' . $bundled_item->get_id() . '_totals' ]           = $totals;
				$data[ 'bundled_item_' . $bundled_item->get_id() . '_recurring_totals' ] = $totals;

				$data[ 'quantities' ][ $bundled_item->get_id() ] = '';

				$data[ 'recurring_prices' ][ $bundled_item->get_id() ]         = '';
				$data[ 'regular_recurring_prices' ][ $bundled_item->get_id() ] = '';

				// Store sub recurring key for summation (variable sub keys are stored in variations data).
				$data[ 'recurring_html' ][ $bundled_item->get_id() ] = '';
				$data[ 'recurring_keys' ][ $bundled_item->get_id() ] = '';

				if ( $bundled_item->is_priced_individually() && $bundled_item->is_subscription() && ! $bundled_item->is_variable_subscription() ) {

					$data[ 'recurring_prices' ][ $bundled_item->get_id() ]         = $bundled_item->get_recurring_price( 'min' );
					$data[ 'regular_recurring_prices' ][ $bundled_item->get_id() ] = $bundled_item->get_regular_recurring_price( 'min' );

					$data[ 'recurring_keys' ][ $bundled_item->get_id() ] = str_replace( '_synced', '', WC_Subscriptions_Cart::get_recurring_cart_key( array( 'data' => $bundled_item->product ), ' ' ) );
					$data[ 'recurring_html' ][ $bundled_item->get_id() ] = WC_PB_Product_Prices::get_recurring_price_html_component( $bundled_item->product );
				}
			}

			if ( $this->contains( 'subscriptions_priced_individually' ) ) {
				$data[ 'price_string_recurring' ]          = '<span class="bundled_subscriptions_price_html">%r</span>';
				$data[ 'price_string_recurring_up_front' ] = sprintf( _x( '%1$s<span class="bundled_subscriptions_price_html"> one time%2$s</span>', 'subscription price html', 'woocommerce-product-bundles' ), '%s', '%r' );;
			}

			$group_mode              = $this->get_group_mode();
			$group_mode_options_data = self::get_group_mode_options_data();

			$data[ 'group_mode_features' ] = ! empty( $group_mode_options_data[ $group_mode ][ 'features' ] ) && is_array( $group_mode_options_data[ $group_mode ][ 'features' ] ) ? $group_mode_options_data[ $group_mode ][ 'features' ] : array();

			/**
			 * 'woocommerce_bundle_price_data' filter.
			 *
			 * Filter price data - to be encoded and passed to JS.
			 *
			 * @param  array              $bundle_price_data
			 * @param  WC_Product_Bundle  $this
			 */
			$this->bundle_form_data = apply_filters( 'woocommerce_bundle_price_data', $data, $this );
		}

		return $this->bundle_form_data;
	}

	/**
	 * Min/max bundle price.
	 *
	 * @param  string   $min_or_max
	 * @param  boolean  $display
	 * @return mixed
	 */
	public function get_bundle_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'price'
		) );
	}

	/**
	 * Min/max bundle regular price.
	 *
	 * @param  string   $min_or_max
	 * @param  boolean  $display
	 * @return mixed
	 */
	public function get_bundle_regular_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Min/max bundle price including tax.
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_bundle_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'incl_tax',
			'prop'       => 'price'
		) );
	}

	/**
	 * Min/max bundle price excluding tax.
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_bundle_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'excl_tax',
			'prop'       => 'price'
		) );
	}

	/**
	 * Min/max regular bundle price including tax.
	 *
	 * @since  5.5.0
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_bundle_regular_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'incl_tax',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Min/max regular bundle price excluding tax.
	 *
	 * @since  5.5.0
	 *
	 * @param  string   $min_or_max
	 * @param  integer  $qty
	 * @return mixed
	 */
	public function get_bundle_regular_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price( array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'excl_tax',
			'prop'       => 'regular_price',
			'strict'     => true
		) );
	}

	/**
	 * Calculates bundle prices.
	 *
	 * @since  5.5.0
	 *
	 * @param  array  $args
	 * @return mixed
	 */
	public function calculate_price( $args ) {

		$min_or_max = isset( $args[ 'min_or_max' ] ) && in_array( $args[ 'min_or_max' ] , array( 'min', 'max' ) ) ? $args[ 'min_or_max' ] : 'min';
		$qty        = isset( $args[ 'qty' ] ) ? absint( $args[ 'qty' ] ) : 1;
		$price_prop = isset( $args[ 'prop' ] ) && in_array( $args[ 'prop' ] , array( 'price', 'regular_price' ) ) ? $args[ 'prop' ] : 'price';
		$price_calc = isset( $args[ 'calc' ] ) && in_array( $args[ 'calc' ] , array( 'incl_tax', 'excl_tax', 'display', '' ) ) ? $args[ 'calc' ] : '';
		$strict     = isset( $args[ 'strict' ] ) && $args[ 'strict' ] && 'regular_price' === $price_prop;

		if ( $this->contains( 'priced_individually' ) ) {

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_bundle_prices_hash', array(
				'prop'       => $price_prop,
				'min_or_max' => $min_or_max,
				'calc'       => $price_calc,
				'qty'        => $qty,
				'strict'     => $strict,
			), $this ) ) );


			if ( isset( $this->bundle_price_cache[ $cache_key ] ) ) {
				$price = $this->bundle_price_cache[ $cache_key ];
			} else {

				$raw_price_fn = 'get_' . $min_or_max . '_raw_' . $price_prop;

				if ( '' === $this->$raw_price_fn() || INF === $this->$raw_price_fn() ) {
					$price = '';
				} else {

					$price_fn = 'get_' . $price_prop;
					$price    = wc_format_decimal( WC_PB_Product_Prices::get_product_price( $this, array(
						'price' => $this->$price_fn(),
						'qty'   => $qty,
						'calc'  => $price_calc,
					) ), wc_pb_price_num_decimals() );

					$bundled_items = $this->get_bundled_items();

					if ( ! empty( $bundled_items ) ) {
						foreach ( $bundled_items as $bundled_item ) {

							if ( false === $bundled_item->is_purchasable() ) {
								continue;
							}

							if ( false === $bundled_item->is_priced_individually() ) {
								continue;
							}

							$bundled_item_qty = $qty * $bundled_item->get_quantity( $min_or_max, array( 'context' => 'price', 'check_optional' => $min_or_max === 'min' ) );

							if ( $bundled_item_qty ) {

								$price += wc_format_decimal( $bundled_item->calculate_price( array(
									'min_or_max' => $min_or_max,
									'qty'        => $bundled_item_qty,
									'strict'     => $strict,
									'calc'       => $price_calc,
									'prop'       => $price_prop
								) ), wc_pb_price_num_decimals() );
							}
						}

						$group_mode = $this->get_group_mode( 'edit' );

						// Calculate the min bundled item price and use it when the parent item is meant to be hidden and all items are optional.
						if ( 'min' === $min_or_max && false === self::group_mode_has( $group_mode, 'parent_item' ) && false === $this->contains( 'mandatory' ) ) {

							$min_price = null;

							foreach ( $bundled_items as $bundled_item ) {

								if ( false === $bundled_item->is_purchasable() ) {
									continue;
								}

								if ( false === $bundled_item->is_priced_individually() ) {
									continue;
								}

								$quantity = max( $bundled_item->get_quantity( 'min' ), 1 );

								$bundled_item_price = $bundled_item->calculate_price( array(
									'min_or_max' => $min_or_max,
									'qty'        => $quantity,
									'strict'     => $strict,
									'calc'       => $price_calc,
									'prop'       => $price_prop
								) );

								if ( is_null( $min_price ) || $bundled_item_price < $min_price ) {
									$min_price = $bundled_item_price;
								}
							}

							if ( $min_price > 0 ) {
								$price = $min_price;
							}
						}
					}
				}

				$this->bundle_price_cache[ $cache_key ] = $price;
			}

		} else {

			$price_fn = 'get_' . $price_prop;
			$price    = WC_PB_Product_Prices::get_product_price( $this, array(
				'price' => $this->$price_fn(),
				'qty'   => $qty,
				'calc'  => $price_calc,
			) );
		}

		return $price;
	}

	/**
	 * Prices incl. or excl. tax are calculated based on the bundled products prices, so get_price_suffix() must be overridden when individually-priced items exist.
	 *
	 * @return string
	 */
	public function get_price_suffix( $price = '', $qty = 1 ) {

		if ( ! $this->contains( 'priced_individually' ) ) {
			return parent::get_price_suffix();
		}

		$suffix      = get_option( 'woocommerce_price_display_suffix' );
		$suffix_html = '';

		if ( $suffix && wc_tax_enabled() ) {

			if ( 'range' === $price && strstr( $suffix, '{' ) ) {
				$suffix = false;
				$price  = '';
			}

			if ( $suffix ) {

				$replacements = array(
					'{price_including_tax}' => wc_price( $this->get_bundle_price_including_tax( 'min', $qty ) ),
					'{price_excluding_tax}' => wc_price( $this->get_bundle_price_excluding_tax( 'min', $qty ) )
				);

				$suffix_html = str_replace( array_keys( $replacements ), array_values( $replacements ), ' <small class="woocommerce-price-suffix">' . wp_kses_post( $suffix ) . '</small>' );
			}
		}

		/**
		 * 'woocommerce_get_price_suffix' filter.
		 *
		 * @param  string             $suffix_html
		 * @param  WC_Product_Bundle  $this
		 * @param  mixed              $price
		 * @param  int                $qty
		 */
		return apply_filters( 'woocommerce_get_price_suffix', $suffix_html, $this, $price, $qty );
	}

	/**
	 * Calculate subscriptions price html component by breaking up bundled subs into recurring scheme groups and adding up all prices in each group.
	 *
	 * @return string
	 */
	public function apply_subs_price_html( $price ) {

		$subs_details = $this->calculate_subs_price_data( array( 'bundled_items', 'price', 'regular_price', 'is_range', 'price_html' ) );

		if ( ! empty( $subs_details ) ) {

			$subs_details_html    = array();
			$from_string          = wc_get_price_html_from_text();
			$has_payment_up_front = $this->get_bundle_regular_price( 'min' ) > 0;
			$is_range             = false !== strpos( $price, $from_string );

			foreach ( $subs_details as $sub_details ) {

				if ( $sub_details[ 'is_range' ] ) {
					$is_range = true;
				}

				if ( $sub_details[ 'regular_price' ] > 0 ) {

					$sub_price_html = wc_price( $sub_details[ 'price' ] );

					if ( $sub_details[ 'price' ] !== $sub_details[ 'regular_price' ] ) {

						$sub_regular_price_html = wc_price( $sub_details[ 'regular_price' ] );
						$sub_price_html         = wc_format_sale_price( $sub_regular_price_html, $sub_price_html );
					}

					$sub_price_details_html = sprintf( $sub_details[ 'price_html' ], $sub_price_html );
					$subs_details_html[]    = '<span class="bundled_sub_price_html">' . $sub_price_details_html . '</span>';
				}
			}

			$subs_price_html       = '';
			$subs_details_html_len = count( $subs_details_html );

			foreach ( $subs_details_html as $i => $sub_details_html ) {
				if ( $i === $subs_details_html_len - 1 || ( $i === 0 && ! $has_payment_up_front ) ) {
					if ( $i > 0 || $has_payment_up_front ) {
						$subs_price_html = sprintf( _x( '%1$s, and</br>%2$s', 'subscription price html', 'woocommerce-product-bundles' ), $subs_price_html, $sub_details_html );
					} else {
						$subs_price_html = $sub_details_html;
					}
				} else {
					$subs_price_html = sprintf( _x( '%1$s,</br>%2$s', 'subscription price html', 'woocommerce-product-bundles' ), $subs_price_html, $sub_details_html );
				}
			}

			if ( $subs_price_html ) {

				if ( $has_payment_up_front ) {
					/* translators: %1$s: Product one-time price, %2$s: Product recurring price */
					$price = sprintf( _x( '%1$s<span class="bundled_subscriptions_price_html"> one time%2$s</span>', 'subscription price html', 'woocommerce-product-bundles' ), $price, $subs_price_html );
				} else {
					$price = '<span class="bundled_subscriptions_price_html">' . $subs_price_html . '</span>';
				}

				if ( $is_range && false === strpos( $price, $from_string ) ) {
					/* translators: %1$s: "From" string, %2$s: Product price */
					$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-product-bundles' ), $from_string, $price );
				}
			}
		}

		return $price;
	}

	/**
	 * Calculate subscriptions price data for each bundled item.
	 *
	 * Refer to `WC_Product_Bundle::apply_subs_price_html` for the structure of the $args array.
	 *
	 * @param  array $args
	 * @return array $subs_details
	 */
	public function calculate_subs_price_data( $args ) {

		$subs_details = array();

		if ( empty( $args ) ) {
			return $subs_details;
		}

		$bundled_items = $this->get_bundled_items();

		if ( empty( $bundled_items ) ) {
			return $subs_details;
		}

		foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

			if ( $bundled_item->is_subscription() && $bundled_item->is_priced_individually() && $bundled_item->is_purchasable() ) {

				$bundled_product = $bundled_item->product;

				if ( $bundled_item->is_variable_subscription() ) {
					$product = $bundled_item->min_price_product;
				} else {
					$product = $bundled_product;
				}

				$sub_string = str_replace( '_synced', '', WC_Subscriptions_Cart::get_recurring_cart_key( array( 'data' => $product ), ' ' ) );

				if ( in_array( 'bundled_items', $args, true ) ) {

					if ( ! isset( $subs_details[ $sub_string ][ 'bundled_items' ] ) ) {
						$subs_details[ $sub_string ][ 'bundled_items' ] = array();
					}

					$subs_details[ $sub_string ][ 'bundled_items' ][] = $bundled_item_id;
				}

				if ( in_array( 'price', $args, true ) ) {
					if ( ! isset( $subs_details[ $sub_string ][ 'price' ] ) ) {
						$subs_details[ $sub_string ][ 'price' ] = 0;
					}
					$subs_details[ $sub_string ][ 'price' ] += $bundled_item->get_quantity( 'min', array( 'context' => 'price', 'check_optional' => true ) ) * WC_PB_Product_Prices::get_product_price( $product, array( 'price' => $bundled_item->min_recurring_price, 'calc' => 'display' ) );
				}

				if ( in_array( 'regular_price', $args, true ) ) {
					if ( ! isset( $subs_details[ $sub_string ][ 'regular_price' ] ) ) {
						$subs_details[ $sub_string ][ 'regular_price' ] = 0;
					}
					$subs_details[ $sub_string ][ 'regular_price' ] += $bundled_item->get_quantity( 'min', array( 'context' => 'price', 'check_optional' => true ) ) * WC_PB_Product_Prices::get_product_price( $product, array( 'price' => $bundled_item->min_regular_recurring_price, 'calc' => 'display' ) );
				}

				if ( in_array( 'is_range', $args, true ) ) {
					if ( ! isset( $subs_details[ $sub_string ][ 'is_range' ] ) ) {
						$subs_details[ $sub_string ][ 'is_range' ] = false;
					}

					if ( $bundled_item->is_variable_subscription() ) {
						$bundled_item->add_price_filters();

						if ( $bundled_item->has_variable_subscription_price() ) {
							$subs_details[ $sub_string ][ 'is_range' ] = true;
						}

						$bundled_item->remove_price_filters();
					}
				}

				if ( in_array( 'price_html', $args, true ) && ! isset( $subs_details[ $sub_string ][ 'price_html' ] ) ) {
					$subs_details[ $sub_string ][ 'price_html' ] = WC_PB_Product_Prices::get_recurring_price_html_component( $product );
				}
			}
		}

		return $subs_details;
	}

	/**
	 * Returns range style html price string without min and max.
	 *
	 * @param  mixed  $price
	 * @return string
	 */
	public function get_price_html( $price = '' ) {

		if ( ! $this->is_purchasable() ) {
			/**
			 * 'woocommerce_bundle_empty_price_html' filter.
			 *
			 * @param  string             $price_html
			 * @param  WC_Product_Bundle  $this
			 */
			return apply_filters( 'woocommerce_bundle_empty_price_html', '', $this );
		}

		if ( $this->contains( 'priced_individually' ) ) {

			// Get the price.
			if ( '' === $this->get_bundle_price( 'min' ) ) {
				$price = apply_filters( 'woocommerce_bundle_empty_price_html', '', $this );
			} else {

				$suppress_range_price_html = INF === $this->get_max_raw_price() || $this->contains( 'configurable_quantities' ) || $this->contains( 'subscriptions_priced_variably' ) || $this->contains( 'nyp' );

				$price_min = $this->get_bundle_price( 'min', true );
				$price_max = $this->get_bundle_price( 'max', true );

				/**
				 * 'woocommerce_bundle_force_old_style_price_html' filter.
				 *
				 * Used to suppress the range-style display of bundle price html strings.
				 *
				 * @param  boolean            $force_suppress_range_format
				 * @param  WC_Product_Bundle  $this
				 */
				if ( $suppress_range_price_html || apply_filters( 'woocommerce_bundle_force_old_style_price_html', false, $this ) ) {

					$price = wc_price( $price_min );

					$regular_price_min = $this->get_bundle_regular_price( 'min', true );

					if ( $regular_price_min !== $price_min ) {

						$regular_price = wc_price( $regular_price_min );

						if ( $price_min !== $price_max ) {
							$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-product-bundles' ), wc_get_price_html_from_text(), wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix() );
						} else {
							$price = wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix();
						}

						/**
						 * 'woocommerce_bundle_sale_price_html' filter.
						 *
						 * @param  string             $sale_price_html
						 * @param  WC_Product_Bundle  $this
						 */
						$price = apply_filters( 'woocommerce_bundle_sale_price_html', $price, $this );

					} elseif ( 0.0 === $price_min && 0.0 === $price_max ) {

						$free_string = apply_filters( 'woocommerce_bundle_show_free_string', false, $this ) ? __( 'Free!', 'woocommerce' ) : $price;
						$price       = apply_filters( 'woocommerce_bundle_free_price_html', $free_string, $this );

					} else {

						if ( $price_min !== $price_max || $suppress_range_price_html ) {
							$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-product-bundles' ), wc_get_price_html_from_text(), $price . $this->get_price_suffix() );
						} else {
							$price = $price . $this->get_price_suffix();
						}

						/**
						 * 'woocommerce_bundle_price_html' filter.
						 *
						 * @param  string             $price_html
						 * @param  WC_Product_Bundle  $this
						 */
						$price = apply_filters( 'woocommerce_bundle_price_html', $price, $this );
					}

				} else {

					$is_range = false;

					if ( $price_min !== $price_max ) {
						$price    = wc_format_price_range( $price_min, $price_max );
						$is_range = true;
					} else {
						$price = wc_price( $price_min );
					}

					$regular_price_min = $this->get_bundle_regular_price( 'min', true );
					$regular_price_max = $this->get_bundle_regular_price( 'max', true );

					if ( $regular_price_max !== $price_max || $regular_price_min !== $price_min ) {

						if ( $regular_price_min !== $regular_price_max ) {
							$regular_price = wc_format_price_range( min( $regular_price_min, $regular_price_max ), max( $regular_price_min, $regular_price_max ) );
							$is_range = true;
						} else {
							$regular_price = wc_price( $regular_price_min );
						}

						/** Documented above. */
						$price = apply_filters( 'woocommerce_bundle_sale_price_html', wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix( $is_range ? 'range' : '' ), $this );

					} elseif ( 0.0 === $price_min && 0.0 === $price_max ) {

						$free_string = apply_filters( 'woocommerce_bundle_show_free_string', false, $this ) ? __( 'Free!', 'woocommerce' ) : $price;
						$price       = apply_filters( 'woocommerce_bundle_free_price_html', $free_string, $this );

					} else {
						/** Documented above. */
						$price = apply_filters( 'woocommerce_bundle_price_html', $price . $this->get_price_suffix( $is_range ? 'range' : '' ), $this );
					}
				}
			}

			/**
			 * 'woocommerce_get_bundle_price_html' filter.
			 *
			 * @param  string             $price_html
			 * @param  WC_Product_Bundle  $this
			 */
			$price = apply_filters( 'woocommerce_get_bundle_price_html', $price, $this );

			if ( $this->contains( 'subscriptions_priced_individually' ) ) {
				$price = $this->apply_subs_price_html( $price );
			}

			/** WC core filter. */
			return apply_filters( 'woocommerce_get_price_html', $price, $this );

		} else {

			return parent::get_price_html();
		}
	}

	/**
	 * Availability of bundle based on bundle-level stock and bundled-items-level stock.
	 *
	 * @return array
	 */
	public function get_availability() {

		$availability = parent::get_availability();

		// If a child does not have enough stock, let people know.
		if ( 'insufficientstock' === $this->get_bundle_stock_status() ) {

			$availability[ 'availability' ] = __( 'Insufficient stock', 'woocommerce-product-bundles' );
			$availability[ 'class' ]        = 'out-of-stock insufficient-stock';

		// If a child is on backorder, the parent should appear to be on backorder, too.
		} elseif ( parent::is_in_stock() && $this->contains( 'on_backorder' ) ) {

			$availability[ 'availability' ] = __( 'Available on backorder', 'woocommerce' );
			$availability[ 'class' ]        = 'available-on-backorder';

		// Override remaining quantity data if parent is in stock and at least one child exists that manages stock and displays quantity in the availability string.
		} elseif ( parent::is_in_stock() ) {

			$display_bundle_stock_quantity = true;

			$bundle_stock_quantity = $this->get_bundle_stock_quantity();
			$stock_format          = get_option( 'woocommerce_stock_format' );

			if (
				'' !== $bundle_stock_quantity
				&& 'no_amount' !== $stock_format
				&& ( 'low_amount' !== $stock_format || $bundle_stock_quantity <= get_option( 'woocommerce_notify_low_stock_amount' ) )
				&& ( ! $this->managing_stock() || $this->get_stock_quantity() > $bundle_stock_quantity )
			) {

				/*
				 * Do not show remaining stock at bundle level if:
				 * - a bundled item manages stock;
				 * - min !== max qty; and
				 * - purchasing the max possible qty affects the remaining bundle stock
				 */
				$has_undefined_bundle_stock_quantity = false;

				foreach ( $this->get_bundled_items() as $bundled_item ) {
					if (
						( $min_qty = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) ) ) !== ( $max_qty = $bundled_item->get_quantity( 'max' ) )
						&& '' !== ( $max_stock = $bundled_item->get_max_stock() )
						&& ( $max_qty === '' || ( $max_stock - $max_qty < $max_qty * $bundle_stock_quantity ) )
					) {
						$has_undefined_bundle_stock_quantity = true;
					}
				}

				$display_bundle_stock_quantity = ! $has_undefined_bundle_stock_quantity;

				if ( apply_filters( 'woocommerce_bundle_display_bundled_items_stock_quantity', $display_bundle_stock_quantity, $this ) ) {

					add_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'filter_stock_quantity' ), 1000 );
					$availability[ 'availability' ] = wc_format_stock_for_display( $this );
					remove_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'filter_stock_quantity' ), 1000 );

				} elseif ( ! $this->managing_stock() || $this->get_stock_quantity() > $bundle_stock_quantity ) {

					add_filter( 'pre_option_woocommerce_stock_format', array( $this, 'filter_stock_format' ), 1000 );
					$availability[ 'availability' ] = wc_format_stock_for_display( $this );
					remove_filter( 'pre_option_woocommerce_stock_format', array( $this, 'filter_stock_format' ), 1000 );
				}
			}
		}

		return apply_filters( 'woocommerce_get_bundle_availability', $availability, $this );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return 	string
	 */
	public function add_to_cart_url() {

		$url = $this->is_purchasable() && $this->is_in_stock() && ! $this->has_options() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() );

		/** WC core filter. */
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return 	string
	 */
	public function add_to_cart_text() {

		$text = __( 'Read more', 'woocommerce' );

		if ( $this->is_purchasable() && $this->is_in_stock() ) {

			if ( $this->has_options() ) {
				$text =  __( 'Select options', 'woocommerce' );
			} else {
				$text =  __( 'Add to cart', 'woocommerce' );
			}
		}

		/** WC core filter. */
		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {

		$text = __( 'Add to cart', 'woocommerce' );

		if ( isset( $_GET[ 'update-bundle' ] ) ) {

			$updating_cart_key = wc_clean( $_GET[ 'update-bundle' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$text = __( 'Update Cart', 'woocommerce-product-bundles' );
			}
		}

		/** WC core filter. */
		return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
	}

	/**
	 * Wrapper for get_permalink that adds bundle configuration data to the URL.
	 *
	 * @return string
	 */
	public function get_permalink() {

		$permalink     = get_permalink( $this->get_id() );
		$fn_args_count = func_num_args();

		if ( 1 === $fn_args_count ) {

			$cart_item = func_get_arg( 0 );

			if ( is_array( $cart_item ) && isset( $cart_item[ 'stamp' ] ) && is_array( $cart_item[ 'stamp' ] ) ) {

				$config_data = isset( $cart_item[ 'stamp' ] ) ? $cart_item[ 'stamp' ] : array();
				$args        = apply_filters( 'woocommerce_bundle_cart_permalink_args', WC_PB()->cart->rebuild_posted_bundle_form_data( $config_data ), $cart_item, $this );

				// Filter and encode keys and values so this is not broken by add_query_arg.
				$args_data = array_map( 'urlencode', $args );
				$args_keys = array_map( 'urlencode', array_keys( $args ) );

				if ( ! empty( $args ) ) {
					$permalink = add_query_arg( array_combine( $args_keys, $args_data ), $permalink );
				}
			}
		}

		return $permalink;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the product object.
	*/

	/**
	 * Forces all bundled products to be treated as virtual, along with the bundle itself.
	 *
	 * @since 6.11.0
	 *
	 * @param  string  $context
	 * @return boolean
	 */
	public function get_virtual_bundle( $context = 'view' ) {
		return $this->get_prop( 'virtual_bundle', $context );
	}

	/**
	 * Min bundle size.
	 *
	 * @since  6.6.0
	 *
	 * @param  string  $context
	 * @return int|''
	 */
	public function get_min_bundle_size( $context = 'view' ) {

		$value = $this->get_prop( 'min_bundle_size', $context );
		$value = '' !== $value ? absint( $value ) : '';

		return $value;
	}

	/**
	 * Max bundle size.
	 *
	 * @since  6.6.0
	 *
	 * @param  string  $context
	 * @return int|''
	 */
	public function get_max_bundle_size( $context = 'view' ) {

		$value = $this->get_prop( 'max_bundle_size', $context );
		$value = '' !== $value ? absint( $value ) : '';

		return $value;
	}

	/**
	 * Cart/order items grouping mode.
	 *
	 * @since  5.5.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_group_mode( $context = 'view' ) {

		$value = $this->get_prop( 'group_mode', $context );

		if ( 'view' === $context ) {
			if ( false === $this->validate_group_mode( $value ) ) {
				$value = 'parent';
			}
		}

		return $value;
	}

	/**
	 * Return the stock sync status.
	 *
	 * @since  6.5.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_bundled_items_stock_sync_status( $context = 'edit' ) {
		return $this->get_prop( 'bundled_items_stock_sync_status', 'edit' );
	}

	/**
	 * Bundle stock status, taking bundled item stock limitations into account.
	 *
	 * @since  6.9.0
	 *
	 * @param  string  $context
	 * @return int|''
	 */
	public function get_bundle_stock_status( $context = 'view' ) {

		$parent_stock_status        = $this->get_stock_status( $context );
		$bundled_items_stock_status = $this->get_bundled_items_stock_status( $context );

		$value = 'instock';

		if ( 'outofstock' === $parent_stock_status ) {
			$value = 'outofstock';
		} elseif ( 'outofstock' === $bundled_items_stock_status ) {
			$value = 'insufficientstock';
		}

		return $value;
	}

	/**
	 * Bundle quantity available for purchase, taking bundled item stock limitations into account.
	 *
	 * @since  6.5.0
	 *
	 * @param  string  $context
	 * @return int|''
	 */
	public function get_bundle_stock_quantity( $context = 'view' ) {

		if ( 'view' === $context && 'unsynced' === $this->get_prop( 'bundled_items_stock_sync_status', 'edit' ) ) {
			$this->sync_stock();
		}

		$value = $this->get_prop( 'bundle_stock_quantity', $context );
		$value = '' !== $value ? absint( $value ) : '';

		return $value;
	}

	/**
	 * Return the stock status.
	 *
	 * @since  5.5.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_bundled_items_stock_status( $context = 'view' ) {

		if ( 'view' === $context && 'unsynced' === $this->get_prop( 'bundled_items_stock_sync_status', 'edit' ) ) {
			$this->sync_stock();
		}

		return $this->get_prop( 'bundled_items_stock_status', $context );
	}

	/**
	 * Returns the base active price of the bundle.
	 *
	 * @since  5.2.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_price( $context = 'view' ) {
		$value = $this->get_prop( 'price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) ? (double) $value : $value;
	}

	/**
	 * Returns the base regular price of the bundle.
	 *
	 * @since  5.2.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_regular_price( $context = 'view' ) {
		$value = $this->get_prop( 'regular_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) ? (double) $value : $value;
	}

	/**
	 * Returns the base sale price of the bundle.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return mixed
	 */
	public function get_sale_price( $context = 'view' ) {
		$value = $this->get_prop( 'sale_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) && '' !== $value ? (double) $value : $value;
	}

	/**
	 * "Form Location" getter.
	 *
	 * @since  5.7.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_add_to_cart_form_location( $context = 'view' ) {
		return $this->get_prop( 'add_to_cart_form_location', $context );
	}

	/**
	 * "Layout" getter.
	 *
	 * @since  5.0.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_layout( $context = 'any' ) {
		return $this->get_prop( 'layout', $context );
	}

	/**
	 * "Edit in cart" getter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return boolean
	 */
	public function get_editable_in_cart( $context = 'any' ) {
		return $this->get_prop( 'editable_in_cart', $context );
	}

	/**
	 * "Aggregate weight" getter.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $context
	 * @return boolean
	 */
	public function get_aggregate_weight( $context = 'any' ) {
		return $this->get_prop( 'aggregate_weight', $context );
	}

	/**
	 * "Sold Individually" option context.
	 * Returns 'product' or 'configuration'.
	 *
	 * @since  5.0.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_sold_individually_context( $context = 'any' ) {
		return $this->get_prop( 'sold_individually_context', $context );
	}

	/**
	 * Minimum raw bundle price getter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_min_raw_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'min_raw_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) && '' !== $value ? (double) $value : $value;
	}

	/**
	 * Minimum raw regular bundle price getter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_min_raw_regular_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'min_raw_regular_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->contains( 'priced_individually' ) && '' !== $value ? (double) $value : $value;
	}

	/**
	 * Maximum raw bundle price getter.
	 *
	 * INF is 9999999999.0 in 'edit' (DB) context.
	 * INF is internally stored as 'INF'.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_max_raw_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'max_raw_price', $context );
		$value = 'INF' === $value ? INF : $value;
		$value = 'edit' !== $context && $this->contains( 'priced_individually' ) && '' !== $value && INF !== $value ? (double) $value : $value;
		$value = 'edit' === $context && INF === $value ? 9999999999.0 : $value;
		return $value;
	}

	/**
	 * Maximum raw regular bundle price getter.
	 *
	 * INF is 9999999999.0 in 'edit' (DB) context.
	 * INF is internally stored as 'INF'.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_max_raw_regular_price( $context = 'view' ) {
		if ( 'sync' !== $context ) {
			$this->sync();
		}
		$value = $this->get_prop( 'max_raw_regular_price', $context );
		$value = 'INF' === $value ? INF : $value;
		$value = 'edit' !== $context && $this->contains( 'priced_individually' ) && '' !== $value && INF !== $value ? (double) $value : $value;
		$value = 'edit' === $context && INF === $value ? 9999999999.0 : $value;
		return $value;
	}

	/**
	 * Returns bundled item data objects.
	 *
	 * @since  5.1.0
	 *
	 * @param  string  $context
	 * @return array
	 */
	public function get_bundled_data_items( $context = 'view' ) {

		if ( ! is_array( $this->bundled_data_items ) ) {

			$use_cache   = ! defined( 'WC_PB_DEBUG_OBJECT_CACHE' ) && 'bundle' === $this->get_data_store_type() && $this->get_id() && ! $this->has_bundled_data_item_changes();
			$cache_key   = WC_Cache_Helper::get_cache_prefix( 'bundled_data_items' ) . $this->get_id();
			$cached_data = $use_cache ? wp_cache_get( $cache_key, 'bundled_data_items' ) : false;

			if ( false !== $cached_data ) {
				$this->bundled_data_items = $cached_data;
			}

			if ( ! is_array( $this->bundled_data_items ) ) {

				$this->bundled_data_items = array();

				if ( $id = $this->get_id() ) {

					$args = array(
						'bundle_id' => $id,
						'return'    => 'objects',
						'order_by'  => array( 'menu_order' => 'ASC' )
					);

					$this->bundled_data_items = WC_PB_DB::query_bundled_items( $args );

					if ( $use_cache ) {
						wp_cache_set( $cache_key, $this->bundled_data_items, 'bundled_data_items' );
					}
				}
			}
		}

		if ( has_filter( 'woocommerce_bundled_data_items' ) ) {
			_deprecated_function( 'The "woocommerce_bundled_data_items" filter', '5.5.0', 'the "woocommerce_bundled_items" filter' );
		}

		return 'view' === $context ? apply_filters( 'woocommerce_bundled_data_items', $this->bundled_data_items, $this ) : $this->bundled_data_items;
	}

	/**
	 * Returns bundled item ids.
	 *
	 * @since  5.0.0
	 *
	 * @param  string  $context
	 * @return array
	 */
	public function get_bundled_item_ids( $context = 'view' ) {

		$bundled_item_ids = array();

		foreach ( $this->get_bundled_data_items( $context ) as $bundled_data_item ) {
			$bundled_item_ids[] = $bundled_data_item->get_id();
		}

		/**
		 * 'woocommerce_bundled_item_ids' filter.
		 *
		 * @param  array              $ids
		 * @param  WC_Product_Bundle  $this
		 */
		return 'view' === $context ? apply_filters( 'woocommerce_bundled_item_ids', $bundled_item_ids, $this ) : $bundled_item_ids;
	}

	/**
	 * Gets all bundled items.
	 *
	 * @param  string  $context
	 * @return array
	 */
	public function get_bundled_items( $context = 'view' ) {

		$bundled_items       = array();
		$bundled_data_items  = $this->get_bundled_data_items( $context );
		$bundled_product_ids = array();

		foreach ( $bundled_data_items as $bundled_data_item ) {
			$bundled_product_ids[] = $bundled_data_item->get_product_id();
		}

		if ( 'bundle' === $this->get_data_store_type() ) {
			$this->data_store->preload_bundled_product_data( $bundled_product_ids );
		}

		foreach ( $bundled_data_items as $bundled_data_item ) {

			$bundled_item = $this->get_bundled_item( $bundled_data_item, $context );

			if ( $bundled_item && $bundled_item->exists() ) {

				if ( 'view' === $context && ( 'draft' === $bundled_item->get_product()->get_status() ) ) {
					continue;
				}

				$bundled_items[ $bundled_data_item->get_id() ] = $bundled_item;
			}
		}

		/**
		 * 'woocommerce_bundled_items' filter.
		 *
		 * @param  array              $bundled_items
		 * @param  WC_Product_Bundle  $this
		 */
		return 'view' === $context ? apply_filters( 'woocommerce_bundled_items', $bundled_items, $this ) : $bundled_items;
	}

	/**
	 * Checks if a specific bundled item exists.
	 *
	 * @param  int     $bundled_item_id
	 * @param  string  $context
	 * @return boolean
	 */
	public function has_bundled_item( $bundled_item_id, $context = 'view' ) {

		if ( 'view' === $context ) {
			$has_bundled_item = WC_PB_Helpers::cache_get( 'has_bundled_item_' . $this->get_id() . '_' . $bundled_item_id );
			if ( ! is_null( $has_bundled_item ) ) {
				return $has_bundled_item;
			}
		}

		$has_bundled_item = false;
		$bundled_item_ids = $this->get_bundled_item_ids( $context );

		if ( in_array( $bundled_item_id, $bundled_item_ids ) ) {
			$has_bundled_item = true;
		}

		WC_PB_Helpers::cache_set( 'has_bundled_item_' . $this->get_id() . '_' . $bundled_item_id, $has_bundled_item );

		return $has_bundled_item;
	}

	/**
	 * Gets a specific bundled item.
	 *
	 * @param  WC_Bundled_Item_Data|int  $bundled_data_item
	 * @param  string                    $context
	 * @return WC_Bundled_Item
	 */
	public function get_bundled_item( $bundled_data_item, $context = 'view', $hash = array() ) {

		if ( $bundled_data_item instanceof WC_Bundled_Item_Data ) {
			$bundled_item_id = $bundled_data_item->get_id();
		} else {
			$bundled_item_id = $bundled_data_item = absint( $bundled_data_item );
		}

		$bundled_item = false;

		if ( $this->has_bundled_item( $bundled_item_id, $context ) ) {

			$cache_group  = 'wc_bundled_item_' . $bundled_item_id . '_' . $this->get_id();
			$cache_key    = md5( json_encode( apply_filters( 'woocommerce_bundled_item_hash', $hash, $this ) ) );

			$bundled_item = WC_PB_Helpers::cache_get( $cache_key, $cache_group );

			if ( $this->has_bundled_data_item_changes() || null === $bundled_item ) {

				$bundled_item = new WC_Bundled_Item( $bundled_data_item, $this );

				WC_PB_Helpers::cache_set( $cache_key, $bundled_item, $cache_group );
			}
		}

		return $bundled_item;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting product data. These do not update anything in the
	| database itself and only change what is stored in the class object.
	*/

	/**
	 * Set 'virtual_bundle' prop. Forced all bundled products to be treated as virtual.
	 *
	 * @since 6.11.0
	 *
	 * @param  string|boolean  $virtual
	 */
	public function set_virtual_bundle( $virtual ) {
		$virtual = wc_string_to_bool( $virtual );
		$this->set_prop( 'virtual_bundle', $virtual );
		if ( $virtual ) {
			$this->set_prop( 'virtual', true );
		}
	}

	/**
	 * Set min bundle size.
	 *
	 * @since  6.6.0
	 *
	 * @param  int|''  $quantity
	 */
	public function set_min_bundle_size( $min_bundle_size ) {
		$this->set_prop( 'min_bundle_size', $min_bundle_size );
	}

	/**
	 * Set max bundle size.
	 *
	 * @since  6.6.0
	 *
	 * @param int|''  $quantity
	 */
	public function set_max_bundle_size( $max_bundle_size ) {
		$this->set_prop( 'max_bundle_size', $max_bundle_size );
	}

	/**
	 * Set cart/order items group mode.
	 *
	 * @param string  $mode
	 */
	public function set_group_mode( $mode = '' ) {
		$this->set_prop( 'group_mode', in_array( $mode, array_keys( self::get_group_mode_options() ) ) ? $mode : 'parent' );
	}

	/**
	 * Set stock sync status.
	 *
	 * @param string  $status
	 */
	public function set_bundled_items_stock_sync_status( $status = '' ) {
		$this->set_prop( 'bundled_items_stock_sync_status', in_array( $status, array( 'synced', 'unsynced' ) ) ? $status : 'unsynced' );
	}

	/**
	 * Set bundle stock quantity.
	 * Quantity available for purchase, taking bundled item stock limitations into account.
	 *
	 * @param int|''  $quantity
	 */
	public function set_bundle_stock_quantity( $quantity ) {
		$this->set_prop( 'bundle_stock_quantity', $quantity );
	}

	/**
	 * Set stock status.
	 *
	 * @param string  $status
	 */
	public function set_bundled_items_stock_status( $status = '' ) {
		$this->set_prop( 'bundled_items_stock_status', in_array( $status, array( 'instock', 'outofstock' ) ) ? $status : 'instock' );
	}

	/**
	 * "Form Location" setter.
	 *
	 * @since  5.7.0
	 *
	 * @param  string  $value
	 */
	public function	set_add_to_cart_form_location( $value ) {
		$value = in_array( $value, array_keys( self::get_add_to_cart_form_location_options() ) ) ? $value : 'default';
		return $this->set_prop( 'add_to_cart_form_location', $value );
	}

	/**
	 * "Layout" setter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $layout
	 */
	public function set_layout( $layout ) {
		$layout = array_key_exists( $layout, self::get_layout_options() ) ? $layout : 'default';
		$this->set_prop( 'layout', $layout );
	}

	/**
	 * "Edit in cart" setter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $editable_in_cart
	 */
	public function set_editable_in_cart( $editable_in_cart ) {

		$editable_in_cart = wc_string_to_bool( $editable_in_cart );
		$this->set_prop( 'editable_in_cart', $editable_in_cart );

		if ( $editable_in_cart ) {
			if ( ! in_array( 'edit_in_cart', $this->supports ) ) {
				$this->supports[] = 'edit_in_cart';
			}
		} else {
			foreach ( $this->supports as $key => $value ) {
				if ( 'edit_in_cart' === $value ) {
					unset( $this->supports[ $key ] );
				}
			}
		}
	}

	/**
	 * "Aggregate weight" setter.
	 *
	 * @since  6.0.0
	 *
	 * @param  string  $aggregate_weight
	 */
	public function set_aggregate_weight( $aggregate_weight ) {
		$aggregate_weight = wc_string_to_bool( $aggregate_weight );
		$this->set_prop( 'aggregate_weight', $aggregate_weight );
	}

	/**
	 * "Sold individually" context setter.
	 *
	 * @since  5.2.0
	 *
	 * @param  string  $context
	 */
	public function set_sold_individually_context( $context ) {
		$context = in_array( $context, array( 'product', 'configuration' ) ) ? $context : 'product';
		$this->set_prop( 'sold_individually_context', $context );
	}

	/**
	 * Minimum raw bundle price setter.
	 *
	 * @since  5.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_min_raw_price( $value ) {
		$value = wc_format_decimal( $value );
		$this->set_prop( 'min_raw_price', $value );
	}

	/**
	 * Minimum raw regular bundle price setter.
	 *
	 * @since  5.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_min_raw_regular_price( $value ) {
		$value = wc_format_decimal( $value );
		$this->set_prop( 'min_raw_regular_price', $value );
	}

	/**
	 * Maximum raw bundle price setter.
	 *
	 * Converts 9999999999.0 to INF.
	 * Internally stores infinite values as 'INF' to prevent issues with 'json_encode'.
	 *
	 * @since  5.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_max_raw_price( $value ) {
		$value = INF === $value ? 'INF' : wc_format_decimal( $value );
		$value = 9999999999.0 === (double) $value ? 'INF' : $value;
		$this->set_prop( 'max_raw_price', $value );
	}

	/**
	 * Maximum raw regular bundle price setter.
	 *
	 * Converts 9999999999.0 to INF.
	 * Internally stores infinite values as 'INF' to prevent issues with 'json_encode'.
	 *
	 * @since  5.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_max_raw_regular_price( $value ) {
		$value = INF === $value ? 'INF' : wc_format_decimal( $value );
		$value = 9999999999.0 === (double) $value ? 'INF' : $value;
		$this->set_prop( 'max_raw_regular_price', $value );
	}

	/**
	 * Sets bundled item data objects.
	 * Expects each data element in array format - @see 'WC_Bundled_Item_Data::get_data()'.
	 * Until 'save_items' is called, all items get a temporary index-based ID (unit-testing only!).
	 *
	 * @since  5.2.0
	 *
	 * @param  array  $data
	 */
	public function set_bundled_data_items( $data ) {

		if ( is_array( $data ) ) {

			$existing_item_ids = array();
			$update_item_ids   = array();

			$bundled_data_items = $this->get_bundled_data_items( 'edit' );

			// Get real IDs.
			if ( ! empty( $bundled_data_items ) ) {
				if ( $this->has_bundled_data_item_changes() ) {
					foreach ( $this->bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {
						$existing_item_ids[] = $bundled_data_item->get_meta( 'real_id' );
					}
				} else {
					foreach ( $this->bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {
						$existing_item_ids[] = $bundled_data_item->get_id();
						$bundled_data_item->update_meta( 'real_id', $bundled_data_item->get_id() );
					}
				}
			}

			// Find existing IDs to update.
			if ( ! empty( $data ) ) {
				foreach ( $data as $item_key => $item_data ) {
					// Ignore items without a valid bundled product ID.
					if ( empty( $item_data[ 'product_id' ] ) ) {
						unset( $data[ $item_key ] );
					// If an item with the same ID exists, modify it.
					} elseif ( isset( $item_data[ 'bundled_item_id' ] ) && $item_data[ 'bundled_item_id' ] > 0 && in_array( $item_data[ 'bundled_item_id' ], $existing_item_ids ) ) {
						$update_item_ids[] = $item_data[ 'bundled_item_id' ];
					// Otherwise, add a new one that will be created after saving.
					} else {
						$data[ $item_key ][ 'bundled_item_id' ] = 0;
					}
				}
			}

			// Find existing IDs to remove.
			$remove_item_ids = array_diff( $existing_item_ids, $update_item_ids );

			// Remove items and delete them later.
			if ( ! empty( $this->bundled_data_items ) ) {
				foreach ( $this->bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {

					$real_item_id = $this->has_bundled_data_item_changes() ? $bundled_data_item->get_meta( 'real_id' ) : $bundled_data_item->get_id();

					if ( in_array( $real_item_id, $remove_item_ids ) ) {

						unset( $this->bundled_data_items[ $bundled_data_item_key ] );
						// Put item in the delete queue if saved in the DB.
						if ( $real_item_id > 0 ) {
							// Put back real ID.
							$bundled_data_item->set_id( $real_item_id );
							$this->bundled_data_items_delete_queue[] = $bundled_data_item;
						}
					}
				}
			}

			// Modify/add items.
			if ( ! empty( $data ) ) {
				foreach ( $data as $item_data ) {

					$item_data[ 'bundle_id' ] = $this->get_id();

					// Modify existing item.
					if ( in_array( $item_data[ 'bundled_item_id' ], $update_item_ids ) ) {

						foreach ( $this->bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {

							$real_item_id = $this->has_bundled_data_item_changes() ? $bundled_data_item->get_meta( 'real_id' ) : $bundled_data_item->get_id();

							if ( $item_data[ 'bundled_item_id' ] === $real_item_id ) {
								$bundled_data_item->set_all( $item_data );
							}
						}

					// Add new item.
					} else {
						$new_item = new WC_Bundled_Item_Data( $item_data );
						$new_item->update_meta( 'real_id', 0 );
						$this->bundled_data_items[] = $new_item;
					}
				}
			}

			// Modify all item IDs to temp values until saved.
			$temp_id = 0;
			if ( ! empty( $this->bundled_data_items ) ) {
				foreach ( $this->bundled_data_items as $bundled_data_item_key => $bundled_data_item ) {
					$temp_id++;
					$bundled_data_item->set_id( $temp_id );
				}
			}

			$this->bundled_data_items_save_pending = true;
			$this->load_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Just a different way to check the 'virtual_bundle' prop value.
	 *
	 * @since  6.11.0
	 *
	 * @return boolean
	 */
	public function is_virtual_bundle() {
		return $this->get_virtual_bundle();
	}

	/**
	 * Equivalent of 'get_changes', but boolean and for bundled data items only.
	 *
	 * @since  6.3.2
	 *
	 * @return boolean
	 */
	public function has_bundled_data_item_changes() {
		return $this->bundled_data_items_save_pending;
	}

	/**
	 * Getter of bundle 'contains' properties.
	 *
	 * @since  5.0.0
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function contains( $key ) {

		if ( 'subscription' === $key ) {
			$key = 'subscriptions';
		}

		// Prevent infinite loops in some edge cases.
		if ( ! $this->is_synced() && 'subscriptions' === $key ) {

			$contains = false;

			if ( $bundled_items = $this->get_bundled_items() ) {

				// Scan bundled items and sync bundle properties.
				foreach ( $bundled_items as $bundled_item ) {
					if ( $bundled_item->is_subscription() ) {
						$contains = true;
						break;
					}
				}
			}

			return $contains;
		}

		if ( 'priced_individually' === $key ) {

			if ( is_null( $this->contains[ $key ] ) ) {

				$priced_items_exist = false;

				// Any items priced individually?
				$bundled_data_items = $this->get_bundled_data_items();

				if ( ! empty( $bundled_data_items ) ) {
					foreach ( $bundled_data_items as $bundled_data_item ) {
						if ( 'yes' === $bundled_data_item->get_meta( 'priced_individually' ) ) {
							$priced_items_exist = true;
							break;
						}
					}
				}

				/**
				 * 'woocommerce_bundle_contains_priced_items' filter.
				 *
				 * @param  boolean            $priced_items_exist
				 * @param  WC_Product_Bundle  $this
				 */
				$this->contains[ 'priced_individually' ] = apply_filters( 'woocommerce_bundle_contains_priced_items', $priced_items_exist, $this );
			}

		} elseif ( 'shipped_individually' === $key ) {

			if ( is_null( $this->contains[ $key ] ) ) {

				$shipped_items_exist = false;

				// Any items shipped individually?
				if ( false === $this->get_virtual_bundle() ) {

					$bundled_data_items = $this->get_bundled_data_items();

					if ( ! empty( $bundled_data_items ) ) {
						foreach ( $bundled_data_items as $bundled_data_item ) {
							if ( 'yes' === $bundled_data_item->get_meta( 'shipped_individually' ) ) {
								$shipped_items_exist = true;
								break;
							}
						}
					}
				}

				/**
				 * 'woocommerce_bundle_contains_shipped_items' filter.
				 *
				 * @param  boolean            $shipped_items_exist
				 * @param  WC_Product_Bundle  $this
				 */
				$this->contains[ 'shipped_individually' ] = apply_filters( 'woocommerce_bundle_contains_shipped_items', $shipped_items_exist, $this );
			}

		} elseif ( 'assembled' === $key ) {

			if ( is_null( $this->contains[ $key ] ) ) {

				$assembled_items_exist = false;

				if ( false === $this->get_virtual() ) {

					// Any items assembled?
					$bundled_data_items = $this->get_bundled_data_items();

					if ( ! empty( $bundled_data_items ) ) {
						foreach ( $bundled_data_items as $bundled_data_item ) {
							if ( 'no' === $bundled_data_item->get_meta( 'shipped_individually' ) ) {
								$assembled_items_exist = true;
								break;
							}
						}
					}
				}

				/**
				 * 'woocommerce_bundle_contains_shipped_items' filter.
				 *
				 * @param  boolean            $assembled_items_exist
				 * @param  WC_Product_Bundle  $this
				 */
				$this->contains[ 'assembled' ] = apply_filters( 'woocommerce_bundle_contains_assembled_items', $assembled_items_exist, $this );
			}

		} else {
			$this->sync();
		}

		// Back-compat.
		if ( 'priced_indefinitely' === $key ) {
			return $this->contains[ 'configurable_quantities' ] || $this->contains[ 'subscriptions_priced_variably' ];
		}

		return isset( $this->contains[ $key ] ) ? $this->contains[ $key ] : null;
	}

	/**
	 * Indicates if the bundle props are in sync with bundled items.
	 *
	 * @return boolean
	 */
	public function is_synced() {
		return $this->is_synced;
	}

	/**
	 * Whether this instance is currently syncing.
	 *
	 * @since  6.2.5
	 *
	 * @return boolean
	 */
	public function is_syncing() {
		return $this->is_syncing;
	}

	/**
	 * A bundle is purchasable if it contains (purchasable) bundled items.
	 *
	 * @return boolean
	 */
	public function is_purchasable() {

		$purchasable = true;

		// Not purchasable while updating DB.
		if ( defined( 'WC_PB_UPDATING' ) ) {
			$purchasable = false;
		// Products must exist of course.
		} if ( ! $this->exists() ) {
			$purchasable = false;
		// When priced statically a price needs to be set.
		} elseif ( false === $this->contains( 'priced_individually' ) && '' === $this->get_price() ) {
			$purchasable = false;
		// Check the product is published.
		} elseif ( 'publish' !== $this->get_status() && ! current_user_can( 'edit_post', $this->get_id() ) ) {
			$purchasable = false;
		// Check if the product contains anything.
		} elseif ( 0 === count( $this->get_bundled_data_items() ) ) {
			$purchasable = false;
		// Check if all non-optional contents are purchasable.
		} elseif ( $this->contains( 'non_purchasable' ) ) {
			$purchasable = false;
		// Only purchasable if "Mixed Checkout" is enabled for WCS.
		} elseif ( $this->contains( 'subscriptions' ) && class_exists( 'WC_Subscriptions_Admin' ) && 'yes' !== get_option( WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase', 'no' ) ) {
			$purchasable = false;
		}

		/** WC core filter. */
		return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
	}

	/**
	 * Override on_sale status of product bundles. If a bundled item is on sale or has a discount applied, then the bundle appears as on sale.
	 *
	 * @param  string  $context
	 * @return boolean
	 */
	public function is_on_sale( $context = 'view' ) {

		$is_on_sale = false;

		if ( 'update-price' !== $context && $this->contains( 'priced_individually' ) && 'cart' !== $this->get_object_context() ) {
			$is_on_sale = parent::is_on_sale( $context ) || ( $this->contains( 'discounted_mandatory' ) && $this->get_min_raw_regular_price( $context ) > 0 );
		} else {
			$is_on_sale = parent::is_on_sale( $context );
		}

		/**
		 * 'woocommerce_product_is_on_sale' filter.
		 *
		 * @param  boolean            $is_on_sale
		 * @param  WC_Product_Bundle  $this
		 */
		return 'view' === $context ? apply_filters( 'woocommerce_product_is_on_sale', $is_on_sale, $this ) : $is_on_sale;
	}

	/**
	 * Sets Bundle object instance context.
	 *
	 * @since 5.13.0
	 *
	 * @param string $context
	 */
	public function set_object_context( $context ) {
		$this->object_context = $context;
	}

	/**
	 * Retrieves Bundle object instance context.
	 *
	 * @since 5.13.0
	 *
	 * @return string
	 */
	public function get_object_context() {
		return $this->object_context;
	}

	/**
	 * True if the product container is in stock.
	 *
	 * @return boolean
	 */
	public function is_parent_in_stock() {
		return parent::is_in_stock();
	}

	/**
	 * True if the product is in stock and all bundled items are in stock.
	 *
	 * @return boolean
	 */
	public function is_in_stock() {

		$is_in_stock = parent::is_in_stock() && 'instock' === $this->get_bundled_items_stock_status();

		return apply_filters( 'woocommerce_bundle_is_in_stock', $is_in_stock, $this );
	}

	/**
	 * Returns whether or not the product is visible in the catalog.
	 *
	 * @return boolean
	 */
	public function is_visible() {

		$visible = 'visible' === $this->get_catalog_visibility() || ( is_search() && 'search' === $this->get_catalog_visibility() ) || ( ! is_search() && 'catalog' === $this->get_catalog_visibility() );

		if ( 'trash' === $this->get_status() ) {
			$visible = false;
		} elseif ( 'publish' !== $this->get_status() && ! current_user_can( 'edit_post', $this->get_id() ) ) {
			$visible = false;
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $this->is_parent_in_stock() ) {
			$visible = false;
		}

		return apply_filters( 'woocommerce_product_is_visible', $visible, $this->get_id() );
	}

	/**
	 * A bundle appears "on backorder" if the container is on backorder, or if a bundled item is on backorder (and requires notification).
	 *
	 * @return boolean
	 */
	public function is_on_backorder( $qty_in_cart = 0 ) {
		return parent::is_on_backorder() || $this->contains( 'on_backorder' );
	}

	/**
	 * Bundle is a NYP product.
	 *
	 * @return boolean
	 */
	public function is_nyp() {

		if ( ! isset( $this->is_nyp ) ) {
			$this->is_nyp = WC_PB()->compatibility->is_nyp( $this );
		}

		return $this->is_nyp;
	}

	/**
	 * Indicates whether the product configuration can be edited in the cart.
	 * Optionally pass a cart item array to check.
	 *
	 * @param  array   $cart_item
	 * @return boolean
	 */
	public function is_editable_in_cart( $cart_item = false ) {
		/**
		 * 'woocommerce_bundle_is_editable_in_cart' filter.
		 *
		 * @param  boolean            $is
		 * @param  WC_Product_Bundle  $this
		 * @param  array              $cart_item
		 */
		return apply_filters( 'woocommerce_bundle_is_editable_in_cart', method_exists( $this, 'supports' ) && $this->supports( 'edit_in_cart' ) && $this->is_in_stock(), $this, $cart_item );
	}

	/**
	 * A bundle on backorder requires notification if the container is defined like this, or a bundled item is on backorder and requires notification.
	 *
	 * @return boolean
	 */
	public function backorders_require_notification() {
		return parent::backorders_require_notification() || $this->contains( 'on_backorder' );
	}

	/**
	 * Returns whether or not the bundle has any attributes set.
	 *
	 * @return boolean
	 */
	public function has_attributes() {

		$has_attributes = false;

		// Check bundle for attributes.
		if ( parent::has_attributes() ) {

			$has_attributes = true;

		// Check all bundled products for attributes.
		} else {

			$bundled_items = $this->get_bundled_items();

			if ( ! empty( $bundled_items ) ) {

				foreach ( $bundled_items as $bundled_item ) {

					if ( $bundled_item->has_attributes() ) {
						$has_attributes = true;
						break;
					}
				}
			}
		}

		return $has_attributes;
	}

	/**
	 * A bundle requires user input if: ( is nyp ) or ( has required addons ) or ( has items with variables ).
	 *
	 * @return boolean
	 */
	public function requires_input() {

		$requires_input = false;

		if ( $this->is_nyp || $this->contains( 'options' ) ) {
			$requires_input = true;
		}

		/**
		 * 'woocommerce_bundle_requires_input' filter.
		 *
		 * @param  boolean            $requires_input
		 * @param  WC_Product_Bundle  $this
		 */
		return apply_filters( 'woocommerce_bundle_requires_input', $requires_input, $this );
	}

	/**
	 * Returns whether or not the product has additional options that must be selected before adding to cart.
	 *
	 * @since  5.12.0
	 *
	 * @return boolean
	 */
	public function has_options() {
		return $this->requires_input();
	}

	/*
	|--------------------------------------------------------------------------
	| Other CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Validate props before saving.
	 *
	 * @since 5.5.0
	 */
	public function validate_props() {

		parent::validate_props();

		if ( false === $this->validate_group_mode() ) {
			$this->set_group_mode( 'parent' );
		}

		if ( $this->get_virtual_bundle( 'edit' ) ) {
			$this->set_virtual( true );
		}

		if ( $this->get_min_bundle_size( 'edit' ) > 0 && $this->get_max_bundle_size( 'edit' ) > 0 && $this->get_min_bundle_size( 'edit' ) > $this->get_max_bundle_size( 'edit' ) ) {
			$this->set_max_bundle_size( $this->get_min_bundle_size( 'edit' ) );
		}
	}

	/**
	 * Validate Group Mode before saving.
	 *
	 * @since 5.5.0
	 */
	public function validate_group_mode( $group_mode = null ) {

		$is_valid   = true;
		$group_mode = is_null( $group_mode ) ? $this->get_group_mode( 'edit' ) : $group_mode;

		if ( false === self::group_mode_has( $group_mode, 'parent_item' ) ) {
			if ( false === $this->get_virtual( 'edit' ) || $this->get_regular_price( 'edit' ) > 0 || $this->contains( 'assembled' ) ) {
				$is_valid = false;
			}
		}

		return $is_valid;
	}

	/**
	 * Alias for 'set_props'.
	 *
	 * @since 5.2.0
	 */
	public function set( $properties ) {
		return $this->set_props( $properties );
	}

	/**
	 * Override 'save' to handle bundled items saving.
	 *
	 * @since 5.2.0
	 */
	public function save() {

		// Save bundle props.
		if ( $this->get_type() === $this->get_data_store_type() && parent::save() ) {
			// Save bundled items.
			$this->save_items();
			// Save bundle props that depend on items.
			$this->sync( true );
		}

		return $this->get_id();
	}

	/**
	 * Saves bundled data items.
	 *
	 * @since 5.2.0
	 */
	public function save_items() {

		if ( $this->has_bundled_data_item_changes() ) {

			foreach ( $this->bundled_data_items_delete_queue as $item ) {
				$item->delete();
			}

			$bundled_data_items = $this->get_bundled_data_items( 'edit' );

			if ( ! empty( $bundled_data_items ) ) {

				foreach ( $bundled_data_items as $item ) {

					// Update.
					if ( $real_id = $item->get_meta( 'real_id' ) ) {
						$item->set_id( $real_id );
					// Create.
					} else {
						$item->set_id( 0 );
					}

					// Update bundle ID.
					$item->set_bundle_id( $this->get_id() );

					$item->delete_meta( 'real_id' );
					$item->save();
					$item->update_meta( 'real_id', $item->get_id() );

					// Delete runtime cache.
					WC_PB_Helpers::cache_invalidate( 'wc_bundled_item_' . $item->get_id() . '_' . $this->get_id() );
				}

			} else {
				$this->set_status( 'draft' );
				parent::save();
			}

			$this->bundled_data_items_save_pending = false;
			$this->load_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	public function filter_stock_quantity( $qty ) {
		return $this->get_bundle_stock_quantity();
	}

	public function filter_stock_format( $qty ) {
		return 'no_amount';
	}

	/*
	|--------------------------------------------------------------------------
	| Static methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Supported "Form Location" options.
	 *
	 * @since  5.7.0
	 *
	 * @return array
	 */
	public static function get_add_to_cart_form_location_options() {

		$options = array(
			'default'      => array(
				'title'       => __( 'Default', 'woocommerce-product-bundles' ),
				'description' => __( 'The add-to-cart form is displayed inside the single-product summary.', 'woocommerce-product-bundles' )
			),
			'after_summary' => array(
				'title'       => __( 'Before Tabs', 'woocommerce-product-bundles' ),
				'description' => __( 'The add-to-cart form is displayed before the single-product tabs. Usually allocates the entire page width for displaying form content. Note that some themes may not support this option.', 'woocommerce-product-bundles' )
			)
		);

		return apply_filters( 'woocommerce_bundle_add_to_cart_form_location_options', $options );
	}

	/**
	 * Supported layouts.
	 *
	 * @return array
	 */
	public static function get_layout_options() {
		if ( is_null( self::$layout_options_data ) ) {
			self::$layout_options_data = apply_filters( 'woocommerce_bundles_supported_layouts', array(
				'default' => __( 'Standard', 'woocommerce-product-bundles' ),
				'tabular' => __( 'Tabular', 'woocommerce-product-bundles' ),
				'grid'    => __( 'Grid', 'woocommerce-product-bundles' )
			) );
		}
		return self::$layout_options_data;
	}

	/**
	 * Supported group modes.
	 *
	 * @param  boolean  $visible
	 * @return array
	 */
	public static function get_group_mode_options( $visible = false ) {
		$group_mode_options_data = self::get_group_mode_options_data();
		$group_mode_options_data = $visible ? array_filter( $group_mode_options_data, array( __CLASS__, 'filter_invisible_group_modes' ) ) : $group_mode_options_data;
		return array_combine( array_keys( $group_mode_options_data ), wp_list_pluck( $group_mode_options_data, 'title' ) );
	}

	/**
	 * Filters-out invisible group modes.
	 *
	 * @param  array  $group_mode_data
	 * @return boolean
	 */
	private static function filter_invisible_group_modes( $group_mode_data ) {
		return ! isset( $group_mode_data[ 'is_visible' ] ) || $group_mode_data[ 'is_visible' ];
	}

	/**
	 * Indicates whether a specific feature is supported by a group mode.
	 *
	 * @param  string     $group_mode
	 * @param  string     $feature
	 * @param  int|false  $bundled_item_id
	 * @return bool
	 */
	public static function group_mode_has( $group_mode, $feature ) {

		$group_mode_options_data = self::get_group_mode_options_data();
		$group_mode_features     = isset( $group_mode_options_data[ $group_mode ][ 'features' ] ) ? $group_mode_options_data[ $group_mode ][ 'features' ] : false;

		return is_array( $group_mode_features ) && in_array( $feature, $group_mode_features );
	}

	/**
	 * Group mode data. Details:
	 *
	 * - 'parent_item':                  Container/parent line item visible in cart/order templates.
	 * - 'child_item_indent':            Bundled/child line items indented in cart/order templates.
	 * - 'aggregated_prices':            Bundled/child cart item prices are aggregated into their container/parent.
	 * - 'aggregated_subtotals':         Bundled/child cart/order item subtotals are aggregated into their container/parent.
	 * - 'child_item_meta':              "Part of" meta appended to bundled/child cart/order line items.
	 * - 'parent_cart_widget_item_meta': "Includes" meta appended to container/parent cart widget line items.
	 * - 'parent_cart_item_meta':        "Includes" meta appended to container/parent cart line items.
	 * - 'component_multiselect':        Replaces the parent title with configuration details in all applicable templates.
	 * - 'faked_parent_item':            First bundled/child line item acting as container/parent.
	 *
	 * Using the first child as a "fake" container:
	 *
	 * 'child'    => array(
	 *		'title'    => __( 'First child', 'woocommerce-product-bundles' ),
	 *		'features' => array( 'faked_parent_item', 'child_item_indent' )
	 *	)
	 *
	 * @return array
	 */
	private static function get_group_mode_options_data() {

		if ( is_null( self::$group_mode_options_data ) ) {

			self::$group_mode_options_data = apply_filters( 'woocommerce_bundles_group_mode_options_data', array(
				'parent'   => array(
					'title'      => __( 'Grouped', 'woocommerce-product-bundles' ),
					'features'   => array( 'parent_item', 'child_item_indent', 'aggregated_prices', 'aggregated_subtotals', 'parent_cart_widget_item_meta' ),
					'is_visible' => true
				),
				'noindent' => array(
					'title'      => __( 'Flat', 'woocommerce-product-bundles' ),
					'features'   => array( 'parent_item', 'child_item_meta' ),
					'is_visible' => true
				),
				'none'     => array(
					'title'      => __( 'None', 'woocommerce-product-bundles' ),
					'features'   => array( 'child_item_meta' ),
					'is_visible' => true
				)
			) );
		}

		return self::$group_mode_options_data;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public function sync_bundled_items_stock_status() {
		_deprecated_function( __METHOD__ . '()', '6.5.0', __CLASS__ . '::sync_stock()' );
		return $this->sync_stock();
	}
	public function get_bundle_price_data() {
		_deprecated_function( __METHOD__ . '()', '6.4.0', __CLASS__ . '::get_bundle_form_data()' );
		return $this->get_bundle_form_data();
	}
	public static function get_supported_layout_options() {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::get_layout_options()' );
		return self::get_layout_options();
	}
	public function maybe_sync_bundle() {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::sync()' );
		$this->sync();
	}
	public function sync_bundle() {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::sync( true )' );
		$this->sync( true );
	}
	public function get_bundled_item_quantities( $context = 'reference', $min_or_max = '' ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0', 'WC_Bundled_Item::get_quantity()' );

		$bundled_item_quantities = array(
			'reference' => array(
				'min' => array(),
				'max' => array()
			),
			'optimal'   => array(
				'min' => array(),
				'max' => array()
			),
			'worst'     => array(
				'min' => array(),
				'max' => array()
			),
			'required'  => array(
				'min' => array(),
				'max' => array()
			)
		);

		foreach ( $this->get_bundled_items() as $bundled_item ) {

			$min_qty = $bundled_item->is_optional() ? 0 : $bundled_item->get_quantity( 'min' );
			$max_qty = $bundled_item->get_quantity( 'max' );

			$bundled_item_quantities[ 'reference' ][ 'min' ][ $bundled_item->get_id() ] = $min_qty;
			$bundled_item_quantities[ 'reference' ][ 'max' ][ $bundled_item->get_id() ] = $max_qty;
		}

		$bundled_item_quantities[ 'optimal' ]  = apply_filters( 'woocommerce_bundled_item_optimal_price_quantities', $bundled_item_quantities[ 'reference' ], $this );
		$bundled_item_quantities[ 'worst' ]    = apply_filters( 'woocommerce_bundled_item_worst_price_quantities', $bundled_item_quantities[ 'reference' ], $this );
		$bundled_item_quantities[ 'required' ] = apply_filters( 'woocommerce_bundled_item_required_quantities', $bundled_item_quantities[ 'reference' ], $this );

		return '' === $min_or_max ? $bundled_item_quantities[ $context ] : $bundled_item_quantities[ $context ][ $min_or_max ];
	}
	public function get_bundle_variation_attributes() {
		_deprecated_function( __METHOD__ . '()', '5.2.0', 'WC_Bundled_Item::get_product_variation_attributes()' );

		$this->sync();

		$bundled_items = $this->get_bundled_items();

		if ( empty( $bundled_items ) ) {
			return array();
		}

		$bundle_attributes = array();

		foreach ( $bundled_items as $bundled_item ) {
			$bundle_attributes[ $bundled_item->get_id() ] = $bundled_item->get_product_variation_attributes();
		}

		return $bundle_attributes;
	}
	public function get_selected_bundle_variation_attributes() {
		_deprecated_function( __METHOD__ . '()', '5.2.0', 'WC_Bundled_Item::get_selected_product_variation_attributes()' );

		$this->sync();

		$bundled_items = $this->get_bundled_items();

		if ( empty( $bundled_items ) ) {
			return array();
		}

		$seleted_bundle_attributes = array();

		foreach ( $bundled_items as $bundled_item ) {
			$seleted_bundle_attributes[ $bundled_item->get_id() ] = $bundled_item->get_selected_product_variation_attributes();
		}

		return $seleted_bundle_attributes;
	}
	public function get_available_bundle_variations() {
		_deprecated_function( __METHOD__ . '()', '5.2.0', 'WC_Bundled_Item::get_product_variations()' );

		$this->sync();

		$bundled_items = $this->get_bundled_items();

		if ( empty( $bundled_items ) ) {
			return array();
		}

		$bundle_variations = array();

		foreach ( $bundled_items as $bundled_item ) {
			$bundle_variations[ $bundled_item->get_id() ] = $bundled_item->get_product_variations();
		}

		return $bundle_variations;
	}
	public function get_base_price() {
		_deprecated_function( __METHOD__ . '()', '5.1.0', __CLASS__ . '::get_price()' );
		return $this->get_price( 'edit' );
	}
	public function get_base_regular_price() {
		_deprecated_function( __METHOD__ . '()', '5.1.0', __CLASS__ . '::get_regular_price()' );
		return $this->get_regular_price( 'edit' );
	}
	public function get_base_sale_price() {
		_deprecated_function( __METHOD__ . '()', '5.1.0', __CLASS__ . '::get_sale_price()' );
		return $this->get_sale_price( 'edit' );
	}
	public function is_priced_per_product() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		return $this->contains( 'priced_individually' );
	}
	public function is_shipped_per_product() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		return $this->contains( 'shipped_individually' );
	}
	public function all_items_in_stock() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		return 'instock' === $this->get_bundled_items_stock_status();
	}
	public function contains_sub() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		return $this->contains( 'subscriptions' );
	}
	public function contains_nyp() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		return $this->contains( 'nyp' );
	}
	public function contains_optional( $exclusively = false ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::contains()' );
		if ( $exclusively ) {
			return false === $this->contains( 'mandatory' ) && $this->contains( 'optional' );
		}
		return $this->contains( 'optional' );
	}
}
