<?php
/**
 * WC_CP_Products class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API functions to support product modifications when contained in Composites.
 *
 * @class    WC_CP_Products
 * @version  8.0.0
 */
class WC_CP_Products {

	/**
	 * Composited product being filtered - @see 'add_filters'.
	 * @var WC_CP_Product|false
	 */
	public static $filtered_component_option = false;

	/**
	 * Composited products being filtered -- all states.
	 * @var WC_Bundled_Item
	 */
	private static $filtered_component_option_pre;

	/**
	 * Price calc task data to add to the queue.
	 * @var array
	 */
	private static $price_calc_tasks_to_queue = array();

	/**
	 * Task runner.
	 * @var WC_CP_Price_Calc_Task_Runner
	 */
	private static $price_calc_task_runner;

	/**
	 * IDs of composites updated in this request.
	 * @var array
	 */
	private static $updated_composite_ids = array();

	/**
	 * Setup hooks.
	 */
	public static function init() {

		// Reset CP query cache when clearing product transients.
		add_action( 'woocommerce_delete_product_transients', array( __CLASS__, 'flush_query_cache' ) );

		// Reset CP price data when changing product prices.
		add_action( 'woocommerce_product_object_updated_props', array( __CLASS__, 'invalidate_price_data' ), 10, 2 );

		// Reset CP query cache + price sync cache during post status transitions.
		add_action( 'delete_post', array( __CLASS__, 'post_status_transition' ) );
		add_action( 'wp_trash_post', array( __CLASS__, 'post_status_transition' ) );
		add_action( 'untrashed_post', array( __CLASS__, 'post_status_transition' ) );

		// Delete meta reserved to the composite/bundle types.
		add_action( 'woocommerce_before_product_object_save', array( __CLASS__, 'before_product_object_save' ) );

		include_once( WC_CP_ABSPATH . 'includes/class-wc-cp-price-calc-task-runner.php' );

		// Spawn task runner.
		add_action( 'init', array( __CLASS__, 'initialize_price_calc_task_runner' ), 5 );

		// Schedule price calculation tasks on shutdown.
		add_action( 'shutdown', array( __CLASS__, 'schedule_price_calc_tasks' ), 100 );

		// Always-on price filters used in cart context.
		if ( 'filters' === self::get_composited_cart_item_discount_method() ) {

			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price_cart' ), 99, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price_cart' ), 99, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price_cart' ), 99, 2 );

			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price_cart' ), 99, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price_cart' ), 99, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price_cart' ), 99, 2 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Class Methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * A non-strict way to tell if a product's prices are being altered due to the presence of a parent composite.
	 *
	 * @since  6.0.0
	 *
	 * @param  WC_Product|WC_Bundled_Item  $product
	 * @param  string      $context
	 * @return boolean
	 */
	public static function is_component_option_pricing_context( $product, $context = 'any' ) {

		if ( in_array( $context, array( 'any', 'catalog' ) ) ) {
			return self::$filtered_component_option && in_array( self::$filtered_component_option->get_product_id(), array( $product->get_id(), $product->get_parent_id() ) );
		} elseif ( in_array( $context, array( 'any', 'cart' ) ) ) {
			return isset( $product->composited_cart_item );
		}
	}

	/**
	 * Returns the currently filtered component option.
	 *
	 * @since  6.0.4
	 *
	 * @param  WC_CP_Product  $product
	 * @return boolean
	 */
	public static function get_filtered_component_option() {
		return self::$filtered_component_option;
	}

	/**
	 * Method to use for calculating cart item discounts. Values: 'filters' | 'props'
	 *
	 * @since  6.0.0
	 *
	 * @return string  $method
	 */
	public static function get_composited_cart_item_discount_method() {
		/**
		 * 'woocommerce_composited_cart_item_discount_method' filter.
		 *
		 * @since  6.0.0
		 *
		 * @param  string  $method  Method to use for calculating cart item discounts. Values: 'filters' | 'props'.
		 */
		$discount_method = apply_filters( 'woocommerce_composited_cart_item_discount_method', 'filters' );
		return in_array( $discount_method, array( 'filters', 'props' ) ) ? $discount_method : 'filters';
	}

	/**
	 * Spawn task runner.
	 *
	 * @since  4.0.0
	 * @return void
	 */
	public static function initialize_price_calc_task_runner() {
		self::$price_calc_task_runner = new WC_CP_Price_Calc_Task_Runner();
	}

	/**
	 * Schedule task.
	 *
	 * @since  4.0.0
	 * @param  $data
	 */
	public static function schedule_price_calc_task( $data ) {

		$scheduled_price_calc_ids = wp_list_pluck( self::$price_calc_tasks_to_queue, 'composite_id' );

		if ( ! empty( $data[ 'composite_id' ] ) && ! in_array( $data[ 'composite_id' ], $scheduled_price_calc_ids ) ) {
			self::$price_calc_tasks_to_queue[] = $data;
		}
	}

	/**
	 * Get tasks to queue.
	 *
	 * @since  4.0.0
	 * @param  $data
	 */
	public static function get_price_calc_tasks_to_queue() {
		return self::$price_calc_tasks_to_queue;
	}

	/**
	 * Get composites updated in this request.
	 *
	 * @since  7.0.0
	 * @param  $data
	 */
	public static function get_updated_composite_ids() {
		return self::$updated_composite_ids;
	}

	/**
	 * Add filters to modify products when contained in Composites.
	 *
	 * @param  WC_CP_Product  $product
	 * @return void
	 */
	public static function add_filters( $component_option ) {

		$add_filters = false;

		if ( empty( self::$filtered_component_option_pre ) ) {
			self::$filtered_component_option_pre = array();
			$add_filters                         = true;
		}

		self::$filtered_component_option_pre[] = $component_option;
		self::$filtered_component_option       = $component_option;

		if ( $add_filters ) {

			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price' ), 16, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 16, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 16, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price' ), 16, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 16, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 16, 2 );

			add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_get_price_html' ), 5, 2 );

			add_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 16, 2 );
			add_filter( 'woocommerce_available_variation', array( __CLASS__, 'filter_available_variation' ), 10, 3 );
			add_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );

			/**
			 * Action 'woocommerce_composite_products_apply_product_filters'.
			 *
			 * @param  WC_Product            $product
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $composite
			 */
			do_action( 'woocommerce_composite_products_apply_product_filters', $component_option->get_product(), $component_option->get_component_id(), $component_option->get_composite() );
		}
	}

	/**
	 * Remove filters - @see 'add_filters'.
	 *
	 * @return void
	 */
	public static function remove_filters() {

		$filtered_component_option = self::$filtered_component_option;

		array_pop( self::$filtered_component_option_pre );

		self::$filtered_component_option = ! empty( self::$filtered_component_option_pre ) && is_array( self::$filtered_component_option_pre ) ? end( self::$filtered_component_option_pre ) : null;

		if ( $filtered_component_option && empty( self::$filtered_component_option ) ) {

			/**
			 * Action 'woocommerce_composite_products_remove_product_filters'.
			 */
			do_action( 'woocommerce_composite_products_remove_product_filters', $filtered_component_option );

			remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price' ), 16, 2 );
			remove_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 16, 2 );
			remove_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 16, 2 );
			remove_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price' ), 16, 2 );
			remove_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 16, 2 );
			remove_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 16, 2 );

			remove_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_get_price_html' ), 5, 2 );

			remove_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 16, 2 );
			remove_filter( 'woocommerce_available_variation', array( __CLASS__, 'filter_available_variation' ), 10, 3 );
			remove_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );

		}
	}

	/**
	 * Returns the incl/excl tax coefficients for calculating prices incl/excl tax on the client side.
	 *
	 * @since  3.13.6
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_tax_ratios( $product ) {

		WC_CP_Helpers::extend_price_display_precision();

		$ref_price      = 1000.0;
		$ref_price_incl = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );
		$ref_price_excl = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );

		WC_CP_Helpers::reset_price_display_precision();

		return array(
			'incl' => $ref_price_incl / $ref_price,
			'excl' => $ref_price_excl / $ref_price
		);
	}

	/**
	 * Calculates product prices.
	 *
	 * @since  3.12.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function get_product_price( $product, $args ) {

		$defaults = array(
			'price' => '',
			'qty'   => 1,
			'calc'  => ''
		);

		$args  = wp_parse_args( $args, $defaults );
		$price = $args[ 'price' ];
		$qty   = $args[ 'qty' ];
		$calc  = $args[ 'calc' ];

		if ( $price ) {

			if ( 'display' === $calc ) {
				$calc = 'excl' === wc_cp_tax_display_shop() ? 'excl_tax' : 'incl_tax';
			}

			if ( 'incl_tax' === $calc ) {
				$price = wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} elseif ( 'excl_tax' === $calc ) {
				$price = wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} else {
				$price = $price * $qty;
			}
		}

		return $price;
	}

	/**
	 * Discounted price getter.
	 *
	 * @param  mixed  $price
	 * @param  mixed  $discount
	 * @return mixed
	 */
	public static function get_discounted_price( $price, $discount ) {

		$discounted_price = $price;

		if ( ! empty( $price ) && ! empty( $discount ) ) {
			$discounted_price = round( ( double ) $price * ( 100 - $discount ) / 100, wc_cp_price_num_decimals( 'extended' ) );
		}

		return $discounted_price;
	}

	/*
	|--------------------------------------------------------------------------
	| Hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Calculate composite min/max price permutations.
	 *
	 * @see  'WC_CP_Price_Calc_Task_Runner::task'
	 *
	 * @return void
	 */
	public static function schedule_price_calc_tasks() {

		if ( ! is_object( self::$price_calc_task_runner ) ) {
			self::initialize_price_calc_task_runner();
		}

		// Need to queue extra items?
		if ( ! empty( self::$price_calc_tasks_to_queue ) ) {

			WC_CP_Core_Compatibility::log( 'Scheduling price calc tasks...', 'info', 'wc_cp_price_calc_tasks' );

			foreach ( self::$price_calc_tasks_to_queue as $key => $task_data ) {

				self::$price_calc_task_runner->push_to_queue( $task_data );
				self::$price_calc_task_runner->save();

				// Save each task as separate queue item.
				if ( $key !== sizeof( self::$price_calc_tasks_to_queue ) - 1 ) {
					self::$price_calc_task_runner->data( array() );
				}
			}

			WC_CP_Core_Compatibility::log( sprintf( 'Queued %s tasks.', sizeof( self::$price_calc_tasks_to_queue ) ), 'info', 'wc_cp_price_calc_tasks' );

			if ( ! self::$price_calc_task_runner->is_running() ) {

				// Give background processing a chance to work - 5 second grace period.
				if ( false === get_site_transient( 'wc_cp_price_calc_task_runner_manual_lock' ) ) {
					set_site_transient( 'wc_cp_price_calc_task_runner_manual_lock', microtime(), 5 );
				}

				// Remote post to self.
				self::$price_calc_task_runner->dispatch();
			}

		// Give background processing a chance to work before considering a manual run...
		} elseif ( false === get_site_transient( 'wc_cp_price_calc_task_runner_manual_lock' ) ) {

			$updating_composites = self::get_updated_composite_ids();

			if ( self::$price_calc_task_runner->is_queued() && ! self::$price_calc_task_runner->is_running() && empty( $updating_composites ) ) {

				WC_CP_Core_Compatibility::log( 'Task runner idling. Attempting to run queued tasks manually...', 'info', 'wc_cp_price_calc_tasks' );
				do_action( self::$price_calc_task_runner->get_cron_hook_identifier() );
			}
		}
	}

	/**
	 * Filter get_variation_prices() calls to include discounts when displaying composited variable product prices.
	 *
	 * @param  array                $prices_array
	 * @param  WC_Product_Variable  $product
	 * @return array
	 */
	public static function filter_get_variation_prices( $prices_array, $product ) {

		$filtered_component_option = self::$filtered_component_option;

		if ( ! empty( $filtered_component_option  ) ) {

			$prices         = array();
			$regular_prices = array();
			$sale_prices    = array();

			$discount           = $filtered_component_option->get_discount();
			$priced_per_product = $filtered_component_option->is_priced_individually();

			// Filter regular prices.
			foreach ( $prices_array[ 'regular_price' ] as $variation_id => $regular_price ) {

				if ( $priced_per_product ) {
					$regular_prices[ $variation_id ] = '' === $regular_price ? $prices_array[ 'price' ][ $variation_id ] : $regular_price;
				} else {
					$regular_prices[ $variation_id ] = 0;
				}
			}

			// Filter prices.
			foreach ( $prices_array[ 'price' ] as $variation_id => $price ) {

				if ( $priced_per_product ) {
					if ( false === $filtered_component_option->is_discount_allowed_on_sale_price() ) {
						$regular_price = $regular_prices[ $variation_id ];
					} else {
						$regular_price = $price;
					}
					$price                   = empty( $discount ) ? $price : round( ( double ) $regular_price * ( 100 - $discount ) / 100, wc_cp_price_num_decimals( 'extended' ) );
					$prices[ $variation_id ] = apply_filters( 'woocommerce_composited_variation_price', $price, $variation_id, $discount, $filtered_component_option );
				} else {
					$prices[ $variation_id ] = 0;
				}
			}

			// Filter sale prices.
			foreach ( $prices_array[ 'sale_price' ] as $variation_id => $sale_price ) {

				if ( $priced_per_product ) {
					$sale_prices[ $variation_id ] = empty( $discount ) ? $sale_price : $prices[ $variation_id ];
				} else {
					$sale_prices[ $variation_id ] = 0;
				}
			}

			if ( false === $filtered_component_option->is_discount_allowed_on_sale_price() ) {
				asort( $prices );
			}

			$prices_array = array(
				'price'         => $prices,
				'regular_price' => $regular_prices,
				'sale_price'    => $sale_prices
			);
		}

		return $prices_array;
	}


	/**
	 * Filters variation data in the show_product function.
	 *
	 * @param  mixed                 $variation_data
	 * @param  WC_Product            $bundled_product
	 * @param  WC_Product_Variation  $bundled_variation
	 * @return mixed
	 */
	public static function filter_available_variation( $variation_data, $product, $variation ) {

		$filtered_component_option = self::$filtered_component_option;

		if ( ! empty( $filtered_component_option  ) ) {

			// Add/modify price data.

			$variation_data[ 'price' ]         = $variation->get_price();
			$variation_data[ 'regular_price' ] = $variation->get_regular_price();

			$variation_data[ 'tax_ratios' ] = self::get_tax_ratios( $variation );

			$variation_data[ 'min_qty' ] = self::$filtered_component_option->get_quantity_min();
			$variation_data[ 'max_qty' ] = self::$filtered_component_option->get_quantity_max( true, $variation );

			// Add/modify availability data.
			$variation_data[ 'availability_html' ] = $filtered_component_option->get_availability_html( $variation );

			if ( ! $variation->is_in_stock() || ! $variation->has_enough_stock( $variation_data[ 'min_qty' ] ) ) {
				$variation_data[ 'is_in_stock' ] = false;
			}

			// Add flag for 3-p code.
			$variation_data[ 'is_composited' ] = true;

			// Modify variation images as we don't want the single-product sizes here.

			$variation_thumbnail_size = self::$filtered_component_option->get_selection_thumbnail_size();

			if ( ! in_array( $variation_thumbnail_size, array( 'single', 'shop_single', 'woocommerce_single' ) ) ) {

				if ( $variation_data[ 'image' ][ 'src' ] ) {

					$src = wp_get_attachment_image_src( $variation_data[ 'image_id' ], $variation_thumbnail_size );

					$variation_data[ 'image' ][ 'src' ]    = $src[0];
					$variation_data[ 'image' ][ 'src_w' ]  = $src[1];
					$variation_data[ 'image' ][ 'src_h' ]  = $src[2];
					$variation_data[ 'image' ][ 'srcset' ] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $variation_data[ 'image_id' ], $variation_thumbnail_size ) : false;
					$variation_data[ 'image' ][ 'sizes' ]  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $variation_data[ 'image_id' ], $variation_thumbnail_size ) : false;
				}
			}
		}

		return $variation_data;
	}

	/**
	 * Filter condition that allows WC to calculate variation price_html.
	 *
	 * @param  boolean               $show
	 * @param  WC_Product_Variable   $product
	 * @param  WC_Product_Variation  $variation
	 * @return boolean
	 */
	public static function filter_show_variation_price( $show, $product, $variation ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			$show = false;

			if ( self::$filtered_component_option->is_priced_individually() && false === self::$filtered_component_option->get_component()->hide_selected_option_price() ) {
				$show = true;
			}
		}

		return $show;
	}

	/**
	 * Filters get_price_html to include component discounts.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_price_html( $price_html, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {

			// Tells NYP to back off.
			$product->is_filtered_price_html = 'yes';

			if ( ! self::$filtered_component_option->is_priced_individually() ) {

				$price_html = '';

			} else {

				$add_suffix = true;

				// Don't add /pc suffix to products in composited bundles (possibly duplicate).
				$filtered_product = self::$filtered_component_option->get_product();
				$product_id       = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

				if ( $filtered_product->get_id() !== $product_id ) {
					$add_suffix = false;
				}

				if ( $add_suffix ) {
					$suffix     = '' === self::$filtered_component_option->get_quantity_max() || self::$filtered_component_option->get_quantity_max() > 1 ? ( ' <span class="component_option_each">' . __( 'each', 'woocommerce-composite-products' ) . '</span>' ) : '';
					$price_html = $price_html . $suffix;
				}
			}

			$price_html = apply_filters( 'woocommerce_composited_item_price_html', $price_html, $product, self::$filtered_component_option->get_component_id(), self::$filtered_component_option->get_composite_id() );
		}

		return $price_html;
	}

	/**
	 * Filters get_price_html to hide nyp prices in static pricing mode.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_nyp_price_html( $price_html, $product ) {

		if ( ! empty( self::$filtered_component_option ) ) {
			if ( ! self::$filtered_component_option->is_priced_individually() ) {
				$price_html = '';
			}
		}

		return $price_html;
	}

	/**
	 * Filters get_price to include component discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_price( $price, $product ) {

		$component_option = false;

		if ( self::$filtered_component_option ) {
			$component_option = self::$filtered_component_option;
		} elseif ( isset( $product->composited_cart_item ) ) {
			$component_option = $product->composited_cart_item;
		}

		if ( $component_option && ( $component_option instanceof WC_CP_Product ) ) {

			if ( '' === $price ) {
				return $price;
			}

			if ( ! $component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( $discount = $component_option->get_discount() ) {

				$offset_price     = ! empty( $product->composited_price_offset ) ? $product->composited_price_offset : false;
				$offset_price_pct = ! empty( $product->composited_price_offset_pct ) ? $product->composited_price_offset_pct : false;

				if ( false === $component_option->is_discount_allowed_on_sale_price() ) {
					$regular_price = $product->get_regular_price();
				} else {
					$regular_price = $price;
				}

				$price = empty( $regular_price ) ? $regular_price : self::get_discounted_price( $regular_price, $discount );

				// Add-on % prices.
				if ( $offset_price_pct ) {

					if ( ! $offset_price ) {
						$offset_price = 0.0;
					}

					foreach ( $offset_price_pct as $price_pct ) {
						$offset_price += $price * $price_pct / 100;
					}
				}

				// Add-on prices.
				if ( $offset_price ) {
					$price += $offset_price;
				}
			}
		}

		return $price;
	}

	/**
	 * Filters get_regular_price to include component discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_regular_price( $price, $product ) {

		$component_option = false;

		if ( self::$filtered_component_option ) {
			$component_option = self::$filtered_component_option;
		} elseif ( isset( $product->composited_cart_item ) ) {
			$component_option = $product->composited_cart_item;
		}

		if ( $component_option && ( $component_option instanceof WC_CP_Product ) ) {

			if ( ! $component_option->is_priced_individually() ) {
				return 0.0;
			}
		}

		return $price;
	}

	/**
	 * Filters get_sale_price to include component discounts.
	 *
	 * @param  double      $sale_price
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_sale_price( $sale_price, $product ) {

		$component_option = false;

		if ( self::$filtered_component_option ) {
			$component_option = self::$filtered_component_option;
		} elseif ( isset( $product->composited_cart_item ) ) {
			$component_option = $product->composited_cart_item;
		}

		if ( $component_option && ( $component_option instanceof WC_CP_Product ) ) {

			if ( ! $component_option->is_priced_individually() ) {
				return 0.0;
			}

			if ( $discount = $component_option->get_discount() ) {

				$offset_price     = ! empty( $product->composited_price_offset ) ? $product->composited_price_offset : false;
				$offset_price_pct = ! empty( $product->composited_price_offset_pct ) ? $product->composited_price_offset_pct : false;

				if ( '' === $sale_price || false === $component_option->is_discount_allowed_on_sale_price() ) {
					$regular_price = $product->get_regular_price();
				} else {
					$regular_price = $sale_price;
				}

				$sale_price = empty( $regular_price ) ? $regular_price : self::get_discounted_price( $regular_price, $discount );

				// Add-on % prices.
				if ( $offset_price_pct ) {

					if ( ! $offset_price ) {
						$offset_price = 0.0;
					}

					foreach ( $offset_price_pct as $price_pct ) {
						$offset_price += $sale_price * $price_pct / 100;
					}
				}

				// Add-on prices.
				if ( $offset_price ) {
					$sale_price += $offset_price;
				}
			}
		}

		return $sale_price;
	}

	/**
	 * Filter get_price() calls for composited cart items to include discounts.
	 *
	 * @since  6.0.0
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_price_cart( $price, $product ) {
		return self::is_component_option_pricing_context( $product, 'cart' ) ? self::filter_get_price( $price, $product ) : $price;
	}

	/**
	 * Filter get_sale_price() calls for composited cart items to include discounts.
	 *
	 * @since  6.0.0
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_sale_price_cart( $price, $product ) {
		return self::is_component_option_pricing_context( $product, 'cart' ) ? self::filter_get_sale_price( $price, $product ) : $price;
	}

	/**
	 * Filter get_regular_price() calls for composited cart items to include discounts.
	 *
	 * @since  6.1.3
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_regular_price_cart( $price, $product ) {
		return self::is_component_option_pricing_context( $product, 'cart' ) ? self::filter_get_regular_price( $price, $product ) : $price;
	}

	/**
	 * Delete component options query cache + composite product price sync cache.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function post_status_transition( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'product' === $post_type ) {
			self::flush_query_cache();
		}
	}

	/**
	 * Invalidate composite product query cache.
	 *
	 * @since  4.0.0
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function flush_query_cache( $post_id = 0 ) {

		if ( $post_id > 0 ) {
			delete_transient( 'wc_cp_query_results_' . $post_id );
		} else {
			// Invalidate all CP query cache entries.
			WC_Cache_Helper::get_transient_version( 'product', true );
		}
	}

	/**
	 * Invalidate composite product price data.
	 *
	 * @since  4.0.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $changed_props
	 * @return void
	 */
	public static function invalidate_price_data( $product, $updated_props ) {

		if ( ! $product->is_type( 'composite' ) && ( in_array( 'regular_price', $updated_props ) || in_array( 'sale_price', $updated_props ) ) ) {
			WC_Cache_Helper::get_transient_version( 'wc_cp_product_prices', true );
		}
	}

	/**
	 * Delete price meta reserved to bundles/composites (legacy).
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function delete_reserved_price_post_meta( $post_id ) {

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $post_id );

		if ( false === in_array( $product_type, array( 'bundle', 'composite' ) ) ) {
			delete_post_meta( $post_id, '_wc_sw_max_price' );
			delete_post_meta( $post_id, '_wc_sw_max_regular_price' );
		}
	}

	/**
	 * Delete price meta reserved to bundles/composites.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function delete_reserved_price_meta( $product ) {
		if ( false === in_array( $product->get_type(), array( 'bundle', 'composite' ) ) ) {
			$product->delete_meta_data( '_wc_sw_max_price' );
			$product->delete_meta_data( '_wc_sw_max_regular_price' );
		}
	}

	/**
	 * Log saved composites and delete reserved price meta.
	 *
	 * @since  7.0.0
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function before_product_object_save( $product ) {

		if ( $product->is_type( 'composite' ) ) {
			self::$updated_composite_ids[] = $product->get_id();
		}

		self::delete_reserved_price_meta( $product );
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Invalidate composite product query cache + price data.
	 *
	 * @deprecated  4.0.0
	 *
	 * @param  int   $post_id
	 * @return void
	 */
	public static function flush_cp_cache( $post_id = 0 ) {
		_deprecated_function( __METHOD__ . '()', '4.0.0', 'WC_Product_Composite_Data_Store_CPT::flush_query_cache() and WC_Cache_Helper::get_transient_version( "wc_cp_product_prices" )' );
		self::flush_query_cache( $post_id );

	}

	/**
	 * Calculates and returns:
	 *
	 * @deprecated  3.14.0
	 *
	 * - The permutations that correspond to the minimum & maximum configuration price.
	 * - The minimum & maximum raw price.
	 *
	 * @param  WC_Product_Composite  $product
	 * @return array
	 */
	public static function read_price_data( $product ) {
		_deprecated_function( __METHOD__ . '()', '3.14.0', 'WC_Product_Composite_Data_Store_CPT::read_price_data()' );
		return $product->get_data_store()->read_price_data( $product );
	}

	/**
	 * Get expanded component options to include variations straight from the DB.
	 *
	 * @deprecated  3.14.0
	 *
	 * @param  array $ids
	 * @return array
	 */
	public static function get_expanded_component_options( $ids ) {
		_deprecated_function( __METHOD__ . '()', '3.14.0', 'WC_Product_Composite_Data_Store_CPT::get_expanded_component_options()' );
		$data_store = WC_Data_Store::load( 'product-composite' );
		return $data_store->get_expanded_component_options( $ids );
	}

	/**
	 * Get raw product prices straight from the DB.
	 *
	 * @deprecated  3.14.0
	 *
	 * @param  array $ids
	 * @return array
	 */
	public static function get_raw_component_option_prices( $ids ) {
		_deprecated_function( __METHOD__ . '()', '3.14.0', 'WC_Product_Composite_Data_Store_CPT::get_raw_component_option_prices()' );
		$data_store = WC_Data_Store::load( 'product-composite' );
		return $data_store->get_raw_component_option_prices( $ids );
	}

	/**
	 * Calculates bundled product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @deprecated  3.12.0
	 */
	public static function get_product_display_price( $product, $price, $qty = 1 ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Products::get_product_price()' );
		return self::get_product_price( $product, array(
			'price' => $price,
			'qty'   => $qty,
			'calc'  => 'display'
		) );
	}
}

WC_CP_Products::init();
