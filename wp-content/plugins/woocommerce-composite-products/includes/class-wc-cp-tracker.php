<?php
/**
 * WC_CP_Tracker class
 *
 * @package  WooCommerce Composite Products
 * @since    8.5.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Products Tracker.
 *
 * @class    WC_CP_Tracker
 * @version  8.5.1
 */
class WC_CP_Tracker {

	/**
	 * Property to store reusable query data.
	 *
	 * @var array
	 */
	private static $reusable_data = array();

	/**
	 * Property to store the plugin's data.
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * Property to store the starting time of the process.
	 *
	 * @var int
	 */
	private static $start_time = 0;

	/**
	 * Property to store the number of batches of the process.
	 *
	 * @var int
	 */
	private static $batch_size = 30;

	/**
	 * Property to store pending products.
	 *
	 * @var array
	 */
	private static $pending_products = array();

	/**
	 * Initialize the Tracker.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'setup_tracker' ) );
	}

	/**
	 * Setup tracker - Cron and Action Scheduler.
	 *
	 * @return void
	 */
	public static function setup_tracker() {
		if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			add_filter( 'woocommerce_tracker_data', array( __CLASS__, 'add_tracking_data' ), 10 );

			// Async tasks.
			add_action( 'wc_cp_daily', array( __CLASS__, 'cron_calculate_tracking_data' ) );

			if ( method_exists( WC(), 'queue' ) ) {
				// Action Scheduler action to calculate tracking data.
				add_action( 'wc_cp_process_tracking_data_batch', array( __CLASS__, 'maybe_calculate_tracking_data' ) );
			}
		}
	}

	/**
	 * Adds CP data to the tracked data.
	 *
	 * @param  array  $data
	 * @return array  all the tracking data.
	 */
	public static function add_tracking_data( $data ) {
		$data[ 'extensions' ][ 'wc_cp' ] = self::get_tracking_data();
		return $data;
	}

	/**
	 * Get all tracking data from options.
	 *
	 * @return array CP's tracking data.
	 */
	private static function get_tracking_data() {
		self::read_data();
		self::maybe_initialize_data();

		if ( self::has_pending_calculations() ) {
			return array();
		}

		if ( isset( self::$data[ 'info' ][ 'month' ] ) ) {
			unset( self::$data[ 'info' ][ 'month' ] );
		}
		if ( isset( self::$data[ 'info' ][ 'year' ] ) ) {
			unset( self::$data[ 'info' ][ 'year' ] );
		}
		if ( isset( self::$data[ 'info' ][ 'last_processed_product_id' ] ) ) {
			unset( self::$data[ 'info' ][ 'last_processed_product_id' ] );
		}

		// Move integration data from composites into integrations.
		if ( isset( self::$data[ 'composites' ][ 'with_subscription_plans_count' ], self::$data[ 'integrations' ][ 'all_products_for_subscriptions' ] ) ) {
			self::$data[ 'integrations' ][ 'all_products_for_subscriptions' ][ 'composites_with_subscription_plans_count' ] = self::$data[ 'composites' ][ 'with_subscription_plans_count' ];
			unset( self::$data[ 'composites' ][ 'with_subscription_plans_count' ] );
		}

		// Move integration data from components into integrations.
		if ( isset( self::$data[ 'components' ][ 'contain_bundles_count' ], self::$data[ 'integrations' ][ 'product_bundles' ] ) ) {
			self::$data[ 'integrations' ][ 'product_bundles' ][ 'components_contain_bundles_count' ] = self::$data[ 'components' ][ 'contain_bundles_count' ];
			unset( self::$data[ 'components' ][ 'contain_bundles_count' ] );
		}

		// Move integration data from components into integrations.
		if ( isset( self::$data[ 'components' ][ 'static_contain_bundles_count' ], self::$data[ 'integrations' ][ 'product_bundles' ] ) ) {
			self::$data[ 'integrations' ][ 'product_bundles' ][ 'components_static_contain_bundles_count' ] = self::$data[ 'components' ][ 'static_contain_bundles_count' ];
			unset( self::$data[ 'components' ][ 'static_contain_bundles_count' ] );
		}

		return self::$data;
	}

	/**
	 * Cron job to start the calculations.
	 *
	 * @return void
	 */
	public static function cron_calculate_tracking_data() {
		WC()->queue()->cancel_all( 'wc_cp_process_tracking_data_batch' );
		self::maybe_calculate_tracking_data();
	}

	/**
	 * Maybe calculate tracking data.
	 * Used by Action Scheduler to execute the next batch.
	 *
	 * @return bool Returns true if the data are re-calculated, false otherwise.
	 */
	public static function maybe_calculate_tracking_data( $args = array() ) {

		self::read_data();
		self::maybe_initialize_data();

		// Let's check if the array has pending data to calculate.
		if ( self::has_pending_calculations() ) {

			self::calculate_tracking_data();
			self::increase_iterations();
			self::set_option_data();

			// If we still have pending calculation, set up an Action Scheduler action to continue the batch process.
			if ( self::has_pending_calculations() ) {
				$args = array(
					'next_iteration'            => self::get_iterations(),
					'last_processed_product_id' => self::get_last_processed_product_id(),
				);
				// Schedule the action to run once at some time in the future with a small padding.
				WC()->queue()->schedule_single( time() + 10, 'wc_cp_process_tracking_data_batch', $args, 'wc_cp_tracking' );
			}

			return true;
		}

		return false;
	}

	/**
	 * Calculates all tracking-related data for the current month.
	 * Runs independently in a background task.
	 *
	 * @return array All the tracking data.
	 * @see ::maybe_calculate_tracking_data().
	 */
	private static function calculate_tracking_data() {
		self::set_start_time();
		self::calculate_aggregation_data();
		self::calculate_integration_data();
	}

	/**
	 * Calculate and set pending products.
	 *
	 * @return void
	 */
	private static function calculate_pending_products() {

		global $wpdb;

		self::$pending_products = $wpdb->get_col( $wpdb->prepare( "
			SELECT
				posts.ID
			FROM
				`{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->term_relationships}` AS term_relationships ON posts.ID = term_relationships.object_id
				INNER JOIN `{$wpdb->term_taxonomy}` AS term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
				INNER JOIN `{$wpdb->terms}` AS terms ON term_taxonomy.term_id = terms.term_id
			WHERE
				term_taxonomy.taxonomy = 'product_type'
				AND terms.slug = 'composite'
				AND posts.post_type = 'product'
				AND posts.post_status = 'publish'
				AND posts.ID > %d
				ORDER BY posts.ID ASC
				LIMIT %d
			",
			self::get_last_processed_product_id(),
			self::$batch_size
		) );

	}

	/**
	 * Calculate data for pending products.
	 *
	 * @return void
	 */
	private static function calculate_aggregation_data() {

		// Number of products in catalog.
		$args  = array(
			'status' => 'publish',
			'limit'  => -1,
			'return' => 'ids',
		);
		$query = new WC_Product_Query( $args );

		self::$data[ 'products' ][ 'count' ] = count( $query->get_products() );

		// Number of composites in use.
		$args  = array(
			'status' => 'publish',
			'type'   => 'composite',
			'limit'  => -1,
			'return' => 'ids',
		);
		$query = new WC_Product_Query( $args );

		self::$data[ 'composites' ][ 'count' ] = count( $query->get_products() );

		foreach ( self::$pending_products as $product_id ) {
			$product = wc_get_product( $product_id );

			// Bail out early
			if ( ! is_object( $product ) || ! is_a( $product, 'WC_Product_Composite' ) ) {
				self::set_last_processed_product_id( (int) $product_id );
			}

			self::calculate_product_data( $product );
			self::calculate_component_data( $product );
			self::calculate_scenario_data( $product );
			self::set_last_processed_product_id( (int) $product_id );

			if ( self::time_or_memory_exceeded() ) {
				break;
			}
		}

	}

	/**
	 * Aggregate product data and increase counters.
	 *
	 * @param  WC_Product_Composite  $product
	 *
	 * @return void
	 */
	private static function calculate_product_data( $product ) {

		$keys_to_increase = array();

		// Let's loop over the available keys.
		foreach ( self::$data[ 'composites' ] as $key => $value ) {
			if (
				// Number of virtual composites.
				( 'virtual_count' === $key && $product->is_virtual_composite() )
				// Number of assembled composites.
				|| ( 'assembled_count' === $key && ! $product->is_virtual() )
				// Number of unassembled composites.
				|| ( 'unassembled_count' === $key
				     && $product->is_virtual()
				     && ! $product->is_virtual_composite() )
				// Number of assembled composites with preserved assembled weight.
				|| ( 'assembled_preserved_weight_count' === $key
				     && ! $product->is_virtual()
				     && $product->get_aggregate_weight() )
				// Number of assembled composites with ignored assembled weight.
				|| ( 'assembled_ignored_weight_count' === $key
				     && ! $product->is_virtual()
				     && ! $product->get_aggregate_weight() )
				// Number of composites with the Stacked layout.
				|| ( 'layout_stacked_count' === $key && 'single' === $product->get_composite_layout_style() )
				// Number of composites with the Progressive layout.
				|| ( 'layout_progressive_count' === $key && 'progressive' === $product->get_composite_layout_style() )
				// Number of composites with the Stepped layout.
				|| ( 'layout_stepped_count' === $key
				     && 'paged' === $product->get_composite_layout_style()
				     && 'standard' === $product->get_composite_layout_style_variation() )
				// Number of composites with the Componentized layout.
				|| ( 'layout_componentized_count' === $key
				     && 'paged' === $product->get_composite_layout_style()
				     && 'componentized' === $product->get_composite_layout_style_variation() )
				// Number of composites having a Default form location.
				|| ( 'form_location_default_count' === $key && 'default' === $product->get_add_to_cart_form_location() )
				// Number of composites having a Before Tabs form location.
				|| ( 'form_location_before_tabs_count' === $key && 'after_summary' === $product->get_add_to_cart_form_location() )
				// Number of composites having a default catalog price display format.
				|| ( 'catalog_price_default_count' === $key && 'defaults' === $product->get_shop_price_calc() )
				// Number of composites having a "Calculate from/to" catalog price display format.
				|| ( 'catalog_price_calculate_from_to_count' === $key && 'min_max' === $product->get_shop_price_calc() )
				// Number of composites having a hidden catalog price display format.
				|| ( 'catalog_price_hidden_count' === $key && 'hidden' === $product->get_shop_price_calc() )
				// Number of composites that are Editable in Cart.
				|| ( 'editable_in_cart_count' === $key && $product->get_editable_in_cart() )
				// Number of composites with subscription plans.
				|| ( 'with_subscription_plans_count' === $key
				     && class_exists( 'WCS_ATT_Product_Schemes' )
				     && WCS_ATT_Product_Schemes::has_subscription_schemes( $product ) )
			) {
				$keys_to_increase[ $key ] = 1;
			}
		}
		self::increase_counters( 'composites', $keys_to_increase );

	}

	/**
	 * Aggregate component data and increase counters.
	 *
	 * @param  WC_Product_Composite  $product
	 *
	 * @return void
	 */
	private static function calculate_component_data( $product ) {

		$keys_to_increase = array();
		$components       = $product->get_components();
		$components_count = count( $components );

		// Number of components in the largest composite.
		if ( $components_count > self::$data[ 'components' ][ 'max' ] ) {
			self::$data[ 'components' ][ 'max' ] = $components_count;
		}

		/* @var WC_CP_Component $component */
		foreach ( $components as $component ) {

			// Let's loop over the available keys.
			foreach ( self::$data[ 'components' ] as $key => $value ) {

				$is_static = $component->is_static();

				if (
					// Number of components with a non-empty description.
					( 'description_not_empty_count' === $key && ! empty( $component->get_description() ) )
					// Number of components with a component image.
					|| ( 'image_not_empty_count' === $key
					     && isset( $component[ 'thumbnail_id' ] )
					     && ! empty( $component[ 'thumbnail_id' ] ) )
					// Number of components by product query type.
					|| ( 'query_type_product_count' === $key
					     && isset( $component[ 'query_type' ] )
					     && 'product_ids' === $component[ 'query_type' ] )
					// Number of components by category query type.
					|| ( 'query_type_category_count' === $key
					     && isset( $component[ 'query_type' ] )
					     && 'category_ids' === $component[ 'query_type' ] )
					// Number of non-static components by Dropdown Options Style.
					|| ( 'non_static_style_dropdowns_count' === $key
					     && 'dropdowns' === $component->get_options_style()
					     && ! $is_static )
					// Number of non-static components by Radio Options Style.
					|| ( 'non_static_style_radios_count' === $key
					     && 'radios' === $component->get_options_style()
					     && ! $is_static )
					// Number of non-static components by Thumbnail Options Style.
					|| ( 'non_static_style_thumbnails_count' === $key
					     && 'thumbnails' === $component->get_options_style()
					     && ! $is_static )
					// Number of static components (non-optional with 1 product option).
					|| ( 'static_count' === $key && $is_static )
					// Number of components that are optional.
					|| ( 'optional_count' === $key && $component->is_optional() )
					// Number of components with a populated default option.
					|| ( 'default_product_not_empty_count' === $key && ! empty( $component->get_default_option() ) )
					// Number of components that have a Max Qty !== Min Qty
					|| ( 'quantity_max_neq_min_count' === $key
					     && $component->get_quantity( 'max' ) !== $component->get_quantity( 'min' ) )
					// Number of components that are Shipped Individually.
					|| ( 'shipped_individually_count' === $key
					     && $component->is_shipped_individually()
					     && ! $product->is_virtual() )
					// Number of components that are Priced Individually.
					|| ( 'priced_individually_count' === $key && $component->is_priced_individually() )
					// Number of priced individually components with a discount.
					|| ( 'priced_individually_with_discount_count' === $key
					     && $component->is_priced_individually()
					     && ! empty( $component->get_discount() ) )
					// Number of priced individually components by absolute option price format.
					|| ( 'priced_individually_price_format_absolute_count' === $key
					     && $component->is_priced_individually()
					     && 'absolute' === $component->get_price_display_format() )
					// Number of priced individually components by relative option price format.
					|| ( 'priced_individually_price_format_relative_count' === $key
					     && $component->is_priced_individually()
					     && 'relative' === $component->get_price_display_format() )
					// Number of priced individually components by hidden option price format.
					|| ( 'priced_individually_price_format_hidden_count' === $key
					     && $component->is_priced_individually()
					     && 'hidden' === $component->get_price_display_format() )
					// Number of priced individually components whose prices are hidden in product templates.
					|| ( 'priced_individually_hidden_subtotal_product_count' === $key
					     && $component->is_priced_individually()
					     && ! $component->is_subtotal_visible( 'product' ) )
					// Number of priced individually components whose prices are hidden in cart templates.
					|| ( 'priced_individually_hidden_subtotal_cart_count' === $key
					     && $component->is_priced_individually()
					     && ! $component->is_subtotal_visible( 'cart' ) )
					// Number of priced individually components whose prices are hidden in order templates.
					|| ( 'priced_individually_hidden_subtotal_orders_count' === $key
					     && $component->is_priced_individually()
					     && ! $component->is_subtotal_visible( 'orders' ) )
					// Number of components whose title is hidden in product templates.
					|| ( 'hidden_product_title_count' === $key && $component->hide_selected_option_title() )
					// Number of components whose description is hidden in product templates.
					|| ( 'hidden_product_description_count' === $key && $component->hide_selected_option_description() )
					// Number of components whose thumbnail is hidden in product templates.
					|| ( 'hidden_product_thumbnail_count' === $key && $component->hide_selected_option_thumbnail() )
					// Number of components whose price is hidden in product templates.
					|| ( 'hidden_product_price_count' === $key
					     && $component->hide_selected_option_price()
					     && $component->is_priced_individually() )
					// Number of components with sorting enabled.
					|| ( 'sorting_enabled_count' === $key
					     && $component->show_sorting_options()
					     && $component->get_options() > 1 )
					// Number of components with filtering enabled.
					|| ( 'filtering_enabled_count' === $key && $component->show_filtering_options() )
				) {

					if ( ! isset( $keys_to_increase[ $key ] ) ) {
						$keys_to_increase[ $key ] = 0;
					}
					$keys_to_increase[ $key ] = $keys_to_increase[ $key ] + 1;
				}

				// Doing this outside the above if, just for contain_bundles_count
				// to avoid calling get_options and intersecting product bundles twice for both keys.
				if ( 'contain_bundles_count' === $key ) {

					if ( ! isset( $keys_to_increase[ 'contain_bundles_count' ] ) ) {
						$keys_to_increase[ 'contain_bundles_count' ] = 0;
					}

					if ( ! isset( $keys_to_increase[ 'static_contain_bundles_count' ] ) ) {
						$keys_to_increase[ 'static_contain_bundles_count' ] = 0;
					}

					$options         = $component->get_options();
					$product_bundles = self::get_reusable_data( 'product_bundles_array' );
					$has_bundles     = ! empty( array_intersect( $options, $product_bundles ) );

					if ( $has_bundles ) {
						// Number of components that contain bundles.
						$keys_to_increase[ 'contain_bundles_count' ] = $keys_to_increase[ 'contain_bundles_count' ] + 1;

						// Number of static components with a Product Bundle.
						if ( $is_static ) {
							$keys_to_increase[ 'static_contain_bundles_count' ] = $keys_to_increase[ 'static_contain_bundles_count' ] + 1;
						}
					}
				}
			}
		}

		// Number of components in all composites.
		$keys_to_increase[ 'count' ] = $components_count;

		self::increase_counters( 'components', $keys_to_increase );

		// Average number of components per composite.
		self::$data[ 'components' ][ 'per_composite_average' ] = ! empty( self::$data[ 'composites' ][ 'count' ] )
			? round( self::$data[ 'components' ][ 'count' ] / self::$data[ 'composites' ][ 'count' ], 2 )
			: 0;

	}

	/**
	 * Aggregate scenario data and increase counters.
	 *
	 * @param  WC_Product_Composite  $product
	 *
	 * @return void
	 */
	private static function calculate_scenario_data( $product ) {

		$keys_to_increase = array();
		$scenarios_data   = $product->get_scenario_data( 'edit' );

		foreach ( $scenarios_data as $scenario_id => $scenario_data ) {
			$scenario = new WC_CP_Scenario( array_merge( $scenario_data, array( 'id' => $scenario_id ) ) );

			// Run aggregations only for active scenarios.
			if ( ! isset( $scenario_data[ 'enabled' ] ) || 'yes' !== $scenario_data[ 'enabled' ] ) {
				continue;
			}

			// Let's loop over the available keys.
			foreach ( self::$data[ 'scenarios' ] as $key => $value ) {
				if (
					// Number of scenarios in all composites.
					'count' === $key
					// Number of scenarios in all composites with the Hide Components action enabled.
					|| ( 'action_conditional_components_count' === $key && $scenario->has_action( 'conditional_components' ) )
					// Number of scenarios in all composites with the Conditional Options action enabled.
					|| ( 'action_conditional_options_count' === $key && $scenario->has_action( 'conditional_options' ) )
					// Number of scenarios in all composites with the Overlay Image action enabled.
					|| ( 'action_overlay_image_count' === $key && $scenario->has_action( 'overlay_image' ) )
					// Number of composites with (deprecated!) states.
					|| ( 'composites_with_states_count' === $key && $scenario->has_action( 'compat_group' ) )
				) {
					if ( ! isset( $keys_to_increase[ $key ] ) ) {
						$keys_to_increase[ $key ] = 0;
					}
					$keys_to_increase[ $key ] = $keys_to_increase[ $key ] + 1;
				}
			}

		}

		// Number of composites with scenarios.
		if ( isset( $keys_to_increase[ 'count' ] ) && $keys_to_increase[ 'count' ] ) {
			$keys_to_increase[ 'composites_with_scenarios_count' ] = 1;
		}

		self::increase_counters( 'scenarios', $keys_to_increase );

		// Average number of scenarios per composite.
		self::$data[ 'scenarios' ][ 'per_composite_average' ] = ! empty( self::$data[ 'composites' ][ 'count' ] )
			? round( self::$data[ 'scenarios' ][ 'count' ] / self::$data[ 'composites' ][ 'count' ], 2 )
			: 0;

		// Number of scenarios in the composite with most scenarios.
		if ( isset( $keys_to_increase[ 'count' ] ) ) {
			if ( $keys_to_increase[ 'count' ] > self::$data[ 'scenarios' ][ 'max' ] ) {
				self::$data[ 'scenarios' ][ 'max' ] = $keys_to_increase[ 'count' ];
			}
		}

	}

	/**
	 * Increase counters.
	 *
	 * @param  string  $category  composites/components.
	 * @param  array   $keys      keys to increase.
	 *
	 * @return void
	 */
	public static function increase_counters( $category, $keys ) {

		foreach ( $keys as $key => $value ) {
			if ( isset( self::$data[ $category ][ $key ] ) ) {
				self::$data[ $category ][ $key ] += (int) $value;
			}
		}

	}

	/**
	 * Calculate integration data.
	 *
	 * @return void
	 */
	private static function calculate_integration_data() {

		// Avoid recalculations.
		if ( ! isset( self::$data[ 'info' ][ 'pending_integrations' ] ) ) {
			return;
		}

		foreach ( self::$data[ 'integrations' ] as $integration_key => $is_integration_enabled ) {
			self::$data[ 'integrations' ][ $integration_key ][ 'enabled' ] = WC_CP()->compatibility->is_module_loaded( $integration_key ) ? 'yes' : 'no';
		}

		if ( class_exists( 'WCS_ATT' ) ) {
			self::$data[ 'integrations' ][ 'all_products_for_subscriptions' ][ 'enabled' ] = 'yes';
		}

		if ( class_exists( 'WC_Bundles' ) ) {
			self::$data[ 'integrations' ][ 'product_bundles' ][ 'enabled' ] = 'yes';
		}

		// Number of composites with subscription plans.
		// Has moved into calculate_product_data to avoid additional loops.

		unset( self::$data[ 'info' ][ 'pending_integrations' ] );
	}

	/**
	 * Check if we have pending calculations.
	 *
	 * @return bool Pending status.
	 */
	private static function has_pending_calculations() {
		self::calculate_pending_products();
		return ! empty( self::$pending_products );
	}

	/**
	 * Initialize data if they are empty month/year has changed.
	 *
	 * @return void
	 */
	private static function maybe_initialize_data() {

		$current_month = (int) gmdate( 'm' );
		$current_year  = (int) gmdate( 'Y' );

		if (
			empty( self::$data )
			|| ! isset( self::$data[ 'info' ][ 'month' ] )
			|| ! isset( self::$data[ 'info' ][ 'year' ] )
			|| $current_month !== self::$data[ 'info' ][ 'month' ]
			|| $current_year !== self::$data[ 'info' ][ 'year' ]
		) {
			self::$data = array(
				'products'     => array(
					'count' => 0,
				),
				'composites'   => array(
					'count'                                 => 0,
					'virtual_count'                         => 0,
					'assembled_count'                       => 0,
					'unassembled_count'                     => 0,
					'assembled_preserved_weight_count'      => 0,
					'assembled_ignored_weight_count'        => 0,
					'layout_stacked_count'                  => 0,
					'layout_progressive_count'              => 0,
					'layout_stepped_count'                  => 0,
					'layout_componentized_count'            => 0,
					'form_location_default_count'           => 0,
					'form_location_before_tabs_count'       => 0,
					'catalog_price_default_count'           => 0,
					'catalog_price_calculate_from_to_count' => 0,
					'catalog_price_hidden_count'            => 0,
					'editable_in_cart_count'                => 0,
					'with_subscription_plans_count'         => 0,
				),
				'components'   => array(
					'count'                                             => 0,
					'description_not_empty_count'                       => 0,
					'image_not_empty_count'                             => 0,
					'query_type_product_count'                          => 0,
					'query_type_category_count'                         => 0,
					'non_static_style_dropdowns_count'                  => 0,
					'non_static_style_radios_count'                     => 0,
					'non_static_style_thumbnails_count'                 => 0,
					'contain_bundles_count'                             => 0,
					'static_count'                                      => 0,
					'static_contain_bundles_count'                      => 0,
					'optional_count'                                    => 0,
					'default_product_not_empty_count'                   => 0,
					'quantity_max_neq_min_count'                        => 0,
					'shipped_individually_count'                        => 0,
					'priced_individually_count'                         => 0,
					'priced_individually_with_discount_count'           => 0,
					'priced_individually_price_format_absolute_count'   => 0,
					'priced_individually_price_format_relative_count'   => 0,
					'priced_individually_price_format_hidden_count'     => 0,
					'hidden_product_title_count'                        => 0,
					'hidden_product_description_count'                  => 0,
					'hidden_product_thumbnail_count'                    => 0,
					'hidden_product_price_count'                        => 0,
					'priced_individually_hidden_subtotal_product_count' => 0,
					'priced_individually_hidden_subtotal_cart_count'    => 0,
					'priced_individually_hidden_subtotal_orders_count'  => 0,
					'sorting_enabled_count'                             => 0,
					'filtering_enabled_count'                           => 0,
					'per_composite_average'                             => 0,
					'max'                                               => 0,
				),
				'scenarios'    => array(
					'composites_with_scenarios_count'     => 0,
					'composites_with_states_count'        => 0,
					'count'                               => 0,
					'max'                                 => 0,
					'per_composite_average'               => 0,
					'action_conditional_components_count' => 0,
					'action_conditional_options_count'    => 0,
					'action_overlay_image_count'          => 0,
				),
				'integrations' => array(
					'all_products_for_subscriptions' => array( 'enabled' => 'no' ),
					'product_addons'                 => array( 'enabled' => 'no' ),
					'blocks'                         => array( 'enabled' => 'no' ),
					'cost_of_goods'                  => array( 'enabled' => 'no' ),
					'elementor'                      => array( 'enabled' => 'no' ),
					'divi'                           => array( 'enabled' => 'no' ),
					'flatsome'                       => array( 'enabled' => 'no' ),
					'jetpack'                        => array( 'enabled' => 'no' ),
					'memberships'                    => array( 'enabled' => 'no' ),
					'min_max_quantities'             => array( 'enabled' => 'no' ),
					'name_your_price'                => array( 'enabled' => 'no' ),
					'one_page_checkout'              => array( 'enabled' => 'no' ),
					'pip'                            => array( 'enabled' => 'no' ),
					'points_rewards_products'        => array( 'enabled' => 'no' ),
					'pre_orders'                     => array( 'enabled' => 'no' ),
					'ppec'                           => array( 'enabled' => 'no' ),
					'product_bundles'                => array( 'enabled' => 'no' ),
					'quickview'                      => array( 'enabled' => 'no' ),
					'storefront'                     => array( 'enabled' => 'no' ),
					'shipstation'                    => array( 'enabled' => 'no' ),
					'shipwire'                       => array( 'enabled' => 'no' ),
					'stripe'                         => array( 'enabled' => 'no' ),
					'subscriptions'                  => array( 'enabled' => 'no' ),
					'wcpay'                          => array( 'enabled' => 'no' ),
					'wc_services'                    => array( 'enabled' => 'no' ),
					'wishlists'                      => array( 'enabled' => 'no' ),
					'zapier'                         => array( 'enabled' => 'no' ),
				),
				'info'         => array(
					'pending_integrations'      => true,
					'last_processed_product_id' => 0,
					'iterations'                => 0,
					'month'                     => $current_month,
					'year'                      => $current_year,
				),
			);
		}
	}

	/**
	 * Get any reusable data, without re-querying the DB.
	 *
	 * @param  array  $key  Reusable data key.
	 * @return mixed
	 */
	private static function get_reusable_data( $key = '' ) {

		$valid_keys = array(
			'product_bundles_array',
		);

		if ( ! in_array( $key, $valid_keys ) ) {
			$notice = sprintf( __( 'Invalid key &quot;%1$s&quot; passed to get_reusable_data.', 'woocommerce-composite-products' ), $key );
			throw new Exception( $notice );
		}

		// Check if the specific data key is already calculated and bail out early.
		if ( isset( self::$reusable_data[ $key ] ) ) {
			return self::$reusable_data[ $key ];
		}

		if ( 'product_bundles_array' === $key ) {
			$bundled_args = array(
				'status' => 'publish',
				'type'   => 'bundle',
				'limit'  => -1,
				'return' => 'ids',
			);

			$bundled_products_query = new WC_Product_Query( $bundled_args );
			$bundled_products       = $bundled_products_query->get_products();

			self::$reusable_data[ 'product_bundles_array' ] = $bundled_products;
		}

		return self::$reusable_data[ $key ];

	}

	/**
	 * Get last processed product id.
	 *
	 * @return int
	 */
	private static function get_last_processed_product_id() {

		$product_id = 0;
		if ( isset( self::$data[ 'info' ] ) && isset( self::$data[ 'info' ][ 'last_processed_product_id' ] ) ) {
			$product_id = self::$data[ 'info' ][ 'last_processed_product_id' ];
		}

		return $product_id;
	}

	/**
	 * Set last processed product id.
	 *
	 * @param  int  $product_id
	 *
	 * @return void
	 */
	private static function set_last_processed_product_id( $product_id ) {

		if ( isset( self::$data[ 'info' ] ) && isset( self::$data[ 'info' ][ 'last_processed_product_id' ] ) ) {
			self::$data[ 'info' ][ 'last_processed_product_id' ] = $product_id;
		}
	}

	/**
	 * Check if execution time is high or if available memory is almost consumed.
	 *
	 * @return bool Returns true if we're about to consume our available resources.
	 */
	private static function time_or_memory_exceeded() {
		return self::time_exceeded() || self::memory_exceeded();
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	private static function time_exceeded() {
		$finish = self::$start_time + 20; // 20 seconds
		return time() >= $finish;
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	private static function memory_exceeded() {
		$memory_limit   = self::get_memory_limit() * 0.8; // 80% of max memory
		$current_memory = memory_get_usage( true );
		return $current_memory >= $memory_limit;
	}

	/**
	 * Get memory limit.
	 *
	 * @return int
	 */
	private static function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Increase iterations.
	 *
	 * @return void
	 */
	private static function increase_iterations() {
		if ( isset( self::$data[ 'info' ] ) && isset( self::$data[ 'info' ][ 'iterations' ] ) ) {
			self::$data[ 'info' ][ 'iterations' ] += 1;
		}
	}

	/**
	 * Get number of iterations.
	 *
	 * @return void
	 */
	private static function get_iterations() {

		$iterations = 0;
		if ( isset( self::$data[ 'info' ] ) && isset( self::$data[ 'info' ][ 'iterations' ] ) ) {
			$iterations = self::$data[ 'info' ][ 'iterations' ];
		}

		return $iterations;
	}

	/**
	 * Set starting time.
	 *
	 * @return void
	 */
	private static function set_start_time() {
		self::$start_time = time();
	}

	/**
	 * Set data from option.
	 *
	 * @return void
	 */
	private static function read_data() {
		self::$data = get_option( 'woocommerce_cp_tracking_data' );
	}

	/**
	 * Set option with data.
	 *
	 * @return void
	 */
	private static function set_option_data() {
		update_option( 'woocommerce_cp_tracking_data', self::$data );
	}
}

WC_CP_Tracker::init();
