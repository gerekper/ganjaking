<?php
/**
 * WC_Product_Composite_Data_Store_CPT class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Composite Product Data Store class
 *
 * Composite data stored as Custom Post Type. For use with the WC 3.0+ CRUD API.
 *
 * @class    WC_Product_Composite_Data_Store_CPT
 * @version  7.0.3
 */
class WC_Product_Composite_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Data stored in meta keys, but not considered "meta" for the Composite type.
	 * @var array
	 */
	protected $extended_internal_meta_keys = array(
		'_bto_data',
		'_bto_scenario_data',
		'_bto_base_price',
		'_bto_base_regular_price',
		'_bto_base_sale_price',
		'_bto_shop_price_calc',
		'_bto_style',
		'_bto_add_to_cart_form_location',
		'_bto_edit_in_cart',
		'_bto_aggregate_weight',
		'_bto_sold_individually',
		'_wc_sw_max_price'
	);

	/**
	 * Maps extended properties to meta keys.
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'price'                     => '_bto_base_price',
		'regular_price'             => '_bto_base_regular_price',
		'sale_price'                => '_bto_base_sale_price',
		'layout'                    => '_bto_style',
		'add_to_cart_form_location' => '_bto_add_to_cart_form_location',
		'shop_price_calc'           => '_bto_shop_price_calc',
		'editable_in_cart'          => '_bto_edit_in_cart',
		'aggregate_weight'          => '_bto_aggregate_weight',
		'sold_individually_context' => '_bto_sold_individually',
		'min_raw_price'             => '_price',
		'max_raw_price'             => '_wc_sw_max_price'
	);

	/**
	 * Callback to exclude composite-specific meta data.
	 *
	 * @param  object  $meta
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->extended_internal_meta_keys );
	}

	/**
	 * Reads all composite-specific post meta.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	protected function read_product_data( &$product ) {

		parent::read_product_data( $product );

		$id           = $product->get_id();
		$props_to_set = array();

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$meta_value = get_post_meta( $id, $meta_key, true );

			// Back compat.
			if ( 'shop_price_calc' === $property && '' === $meta_value ) {
				if ( 'yes' === get_post_meta( $id, '_bto_hide_shop_price', true ) ) {
					$meta_value = 'hidden';
				} elseif ( '' !== $props_to_set[ 'layout' ] ) {
					$meta_value = 'min_max';
				}
			}

			// Add to props array.
			$props_to_set[ $property ] = $meta_value;
		}

		// Base prices are overridden by NYP min price.
		if ( $product->is_nyp() ) {
			$props_to_set[ 'price' ]      = $props_to_set[ 'regular_price' ] = get_post_meta( $id, '_min_price', true );
			$props_to_set[ 'sale_price' ] = '';
		}

		$product->set_props( $props_to_set );

		// Load component/scenario meta.
		$composite_meta = get_post_meta( $id, '_bto_data', true );
		$scenario_meta  = get_post_meta( $id, '_bto_scenario_data', true );

		$product->set_composite_data( $composite_meta );
		$product->set_scenario_data( $scenario_meta );
	}

	/**
	 * Writes all composite-specific post meta.
	 *
	 * @param  WC_Product_Composite  $product
	 * @param  boolean               $force
	 */
	protected function update_post_meta( &$product, $force = false ) {

		parent::update_post_meta( $product, $force );

		$id                 = $product->get_id();
		$meta_keys_to_props = array_flip( array_diff_key( $this->props_to_meta_keys, array( 'price' => 1, 'min_raw_price' => 1, 'max_raw_price' => 1 ) ) );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize it for storage.
			if ( in_array( $property, array( 'editable_in_cart', 'aggregate_weight' ) ) ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			$updated = update_post_meta( $id, $meta_key, $meta_value );

			if ( $updated && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $property;
			}
		}

		// Save components/scenarios.
		update_post_meta( $id, '_bto_data', $product->get_composite_data( 'edit' ) );
		update_post_meta( $id, '_bto_scenario_data', $product->get_scenario_data( 'edit' ) );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	protected function handle_updated_props( &$product ) {

		$id = $product->get_id();

		if ( in_array( 'date_on_sale_from', $this->updated_props ) || in_array( 'date_on_sale_to', $this->updated_props ) || in_array( 'regular_price', $this->updated_props ) || in_array( 'sale_price', $this->updated_props ) ) {
			if ( $product->is_on_sale( 'update-price' ) ) {
				update_post_meta( $id, '_bto_base_price', $product->get_sale_price( 'edit' ) );
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				update_post_meta( $id, '_bto_base_price', $product->get_regular_price( 'edit' ) );
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		if ( in_array( 'stock_quantity', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock', $product );
		}

		if ( in_array( 'stock_status', $this->updated_props ) ) {
			do_action( 'woocommerce_product_set_stock_status', $product->get_id(), $product->get_stock_status(), $product );
		}

		// Update WC 3.6+ lookup table.
		if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
			if ( array_intersect( $this->updated_props, array( 'sku', 'total_sales', 'average_rating', 'stock_quantity', 'stock_status', 'manage_stock', 'downloadable', 'virtual', 'tax_status', 'tax_class' ) ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}
		}

		// Trigger action so 3rd parties can deal with updated props.
		do_action( 'woocommerce_product_object_updated_props', $product, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Get data to save to a lookup table.
	 *
	 * @since  4.0.0
	 *
	 * @param  int     $id
	 * @param  string  $table
	 * @return array
	 */
	protected function get_data_for_lookup_table( $id, $table ) {

		if ( 'wc_product_meta_lookup' === $table ) {

			$min_price_meta   = (array) get_post_meta( $id, '_price', false );
			$max_price_meta   = (array) get_post_meta( $id, '_wc_sw_max_price', false );
			$manage_stock = get_post_meta( $id, '_manage_stock', true );
			$stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
			$price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
			$sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );

			$data = array(
				'product_id'     => absint( $id ),
				'sku'            => get_post_meta( $id, '_sku', true ),
				'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
				'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
				'min_price'      => reset( $min_price_meta ),
				'max_price'      => end( $max_price_meta ),
				'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
				'stock_quantity' => $stock,
				'stock_status'   => get_post_meta( $id, '_stock_status', true ),
				'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
				'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
				'total_sales'    => get_post_meta( $id, 'total_sales', true )
			);

			if ( WC_CP_Core_Compatibility::is_wc_version_gte( '4.0' ) ) {
				$data = array_merge( $data, array(
					'tax_status' => get_post_meta( $id, '_tax_status', true ),
					'tax_class'  => get_post_meta( $id, '_tax_class', true )
				) );
			}

			return $data;

		}

		return array();
	}

	/**
	 * Writes composite raw price meta to the DB.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	public function save_raw_prices( &$product ) {

		if ( defined( 'WC_CP_UPDATING' ) ) {
			return;
		}

		/**
		 * 'woocommerce_composite_update_price_meta' filter.
		 *
		 * Use this to prevent composite min/max raw price meta from being updated.
		 *
		 * @param  boolean               $update
		 * @param  WC_Product_Composite  $this
		 */
		$update_raw_price_meta = apply_filters( 'woocommerce_composite_update_price_meta', true, $product );

		if ( ! $update_raw_price_meta ) {
			return;
		}

		$id = $product->get_id();

		$updated_props   = array();
		$props_to_update = array_intersect( array_flip( $this->props_to_meta_keys ), array( 'min_raw_price', 'max_raw_price' ) );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;
			$meta_value      = $product->$property_get_fn( 'edit' );

			if ( update_post_meta( $id, $meta_key, $meta_value ) ) {
				$updated_props[] = $property;
			}
		}

		if ( ! empty( $updated_props ) ) {

			$sale_price_changed = false;

			/**
			 * 'woocommerce_compsite_product_sale_price_db_context' filter.
			 *
			 * Use the 'update-price' context if you don't want Component discounts to make this product appear as on sale in the database.
			 *
			 * @since  6.2.3
			 */
			$sale_context = apply_filters( 'woocommerce_compsite_product_sale_price_db_context', 'edit', $product );

			// Update sale price.
			if ( $product->is_on_sale( $sale_context ) ) {
				$sale_price_changed = update_post_meta( $id, '_sale_price', $product->get_min_raw_price( 'edit' ) );
			} else {
				$sale_price_changed = update_post_meta( $id, '_sale_price', '' );
			}

			// Delete on-sale transient.
			if ( $sale_price_changed ) {
				delete_transient( 'wc_products_onsale' );
			}

			// Update WC 3.6+ lookup table.
			if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			do_action( 'woocommerce_product_object_updated_props', $product, $updated_props );
		}
	}

	/**
	 * Calculate component option vectors for min/max price calculations.
	 *
	 * @since  4.0.0
	 *
	 * @param  WC_Product_Composite  $product
	 * @return array
	 */
	private function read_permutation_vectors( $product ) {

		/**
		 * 'woocommerce_composite_price_data_permutation_vectors' filter.
		 *
		 * When searching for the permutations with the min/max price, use this filter to narrow down the initial search vectors and speed up the search.
		 * Typically you would use this filter to populate each Component vector only with product IDs that belong in the min/max price permutations.
		 * Of course this assumes that you know the min/max price permutations already.
		 *
		 * @param  array                 $vectors
		 * @param  WC_Product_Composite  $product
		 */
		$permutation_vectors = apply_filters( 'woocommerce_composite_price_data_permutation_vectors', array(), $product );

		foreach ( $product->get_components() as $component_id => $component ) {

			// Skip component?
			if ( sizeof( $product->scenarios()->get_ids_by_action( 'conditional_components' ) ) || $component->is_priced_individually() ) {

				$component_options = isset( $permutation_vectors[ $component_id ] ) && is_array( $permutation_vectors[ $component_id ] ) ? $permutation_vectors[ $component_id ] : array();

				if ( empty( $component_options ) ) {

					$default_option = $component->get_default_option();

					if ( 'defaults' === $product->get_shop_price_calc() ) {

						if ( $default_option ) {
							$component_options = array( $default_option );
						} elseif ( $component->is_optional() ) {
							$component_options = array( 0 );
						}

					} else {
						$component_options = $component->get_options();
					}
				}

				if ( ! empty( $component_options ) ) {
					// Add 0 to validate whether the component can be skipped.
					$component_options = in_array( 0, $component_options ) ? $component_options : array_merge( array( 0 ), $component_options );
					// Set vectors.
					$permutation_vectors[ $component_id ] = $component_options;
				}
			}
		}

		return $permutation_vectors;
	}

	/**
	 * Calculates and returns the configuration that correspond to the minimum & maximum price.
	 *
	 * @param  WC_Product_Composite  $product
	 * @param  mixed                 $task_runner_args
	 * @return array
	 */
	public function read_price_data( &$product, $task_runner_args = false ) {

		$permutation_vectors = $this->read_permutation_vectors( $product );

		/**
		 * 'woocommerce_composite_permutations_search_max_iterations' filter.
		 *
		 * Controls the maximum number of background task iterations for calculating the min/max catalog price of a Composite product.
		 *
		 * @since  4.0.0
		 *
		 * @param  int  $max_iterations
		 */
		$max_iterations = apply_filters( 'woocommerce_composite_permutations_search_max_iterations', 30 );

		/*
		 * Read stored price data unless:
		 *
		 * - calculating data in the background;
		 * - component options have changed;
		 * - the price of a product in the store has changed;
		 * - scenarios have changed.
		 */

		$price_data = false === $task_runner_args ? $product->get_meta( '_bto_price_data', true ) : false;

		if ( ! $price_data && false === $task_runner_args ) {
			$price_data = WC_CP_Helpers::cache_get( 'price_data_' . $product->get_id() );
		}

		$price_data_hash = apply_filters( 'woocommerce_composite_price_data_hash', md5( json_encode( array(
			$max_iterations,
			$permutation_vectors,
			$product->get_shop_price_calc(),
			$product->get_scenario_data( 'edit' ),
			WC_Cache_Helper::get_transient_version( 'wc_cp_product_prices' )
		) ) ), $product );

		if ( $price_data && is_array( $price_data ) && isset( $price_data[ 'hash' ] ) && $price_data[ 'hash' ] === $price_data_hash ) {
			if ( isset( $price_data[ 'min' ] ) && is_array( $price_data[ 'min' ] ) && isset( $price_data[ 'max' ] ) && is_array( $price_data[ 'max' ] ) ) {
				return $price_data;
			}
		}

		$price_data = array(
			'min'    => array(),
			'max'    => array(),
			'hash'   => $price_data_hash,
			'status' => 'completed'
		);

		$components = $product->get_components();

		if ( $task_runner_args && isset( $task_runner_args[ 'min' ] ) && is_array( $task_runner_args[ 'min' ] ) ) {
			$price_data[ 'min' ] = $task_runner_args[ 'min' ];
		}

		if ( $task_runner_args && isset( $task_runner_args[ 'max' ] ) && is_array( $task_runner_args[ 'max' ] ) ) {
			$price_data[ 'max' ] = $task_runner_args[ 'max' ];
		}

		$iteration               = $task_runner_args && isset( $task_runner_args[ 'iteration' ] ) ? absint( $task_runner_args[ 'iteration' ] ): 1;
		$resume_from_permutation = $task_runner_args && isset( $task_runner_args[ 'resume' ] ) ? absint( $task_runner_args[ 'resume' ] ): false;

		$permutation_vector_data = array();
		$component_option_prices = array();

		/**
		 * 'woocommerce_composite_price_data_permutation_calc_scenarios' filter.
		 *
		 * When using scenarios, it is impossible to accurately know what's the min/max price of a Composite without evaluating all possible configurations.
		 * This flag controls whether CP will try to search through all possible configurations to find the ones with the min/max price.
		 *
		 * @param  boolean
		 */
		$permutations_calc_scenarios = apply_filters( 'woocommerce_composite_price_data_permutation_calc_scenarios', $product->scenarios()->exist() && function_exists( 'wc_cp_cartesian' ), $product );

		/*
		 * Set up permutation vector data and amend permutation vectors as needed.
		 */

		foreach ( $permutation_vectors as $component_id => $component_options ) {

			if ( ! empty( $component_options ) ) {

				// Store data.
				$permutation_vector_data[ $component_id ][ 'option_ids' ]          = $component_options;
				$permutation_vector_data[ $component_id ][ 'parent_ids' ]          = $product->get_data_store()->get_expanded_component_options( $component_options, 'mapped' );
				$permutation_vector_data[ $component_id ][ 'expanded_option_ids' ] = $product->get_data_store()->get_expanded_component_options( $component_options, 'expanded' );

				// Build expanded set.
				if ( $permutations_calc_scenarios ) {

					/**
					 * 'woocommerce_composite_price_data_permutation_search_accuracy_expand' filter.
					 *
					 * Expand the permutation search accuracy for this component to include variations?
					 *
					 * @since  3.14.0
					 *
					 * @param  bool  $expand
					 */
					$expand_permutation_search_accuracy = apply_filters( 'woocommerce_expand_composite_price_data_permutation_search_accuracy', true, $product, $component_id );

					if ( $expand_permutation_search_accuracy ) {

						$component_options_in_scenarios = array();

						foreach ( $product->scenarios()->get_scenarios() as $scenario ) {
							$component_options_in_scenarios = array_merge( $component_options_in_scenarios, $scenario->get_ids( $component_id ) );
						}

						if ( sizeof( array_diff( $component_options_in_scenarios, $component_options, array( 0, -1 ) ) ) ) {
							$permutation_vectors[ $component_id ] = $permutation_vector_data[ $component_id ][ 'expanded_option_ids' ];
						}
					}
				}
			}
		}

		/*
		 * Set up prices.
		 */
		if ( ! empty( $permutation_vectors ) ) {

			foreach ( $permutation_vectors as $component_id => $permutation_vector ) {

				$component_option_prices[ $component_id ] = $product->get_data_store()->get_raw_component_option_prices( $permutation_vector );

				if ( $permutations_calc_scenarios ) {
					$component_option_prices[ $component_id ][ 'min' ][ 0 ] = 0.0;
					$component_option_prices[ $component_id ][ 'max' ][ 0 ] = 0.0;
				}
			}

			/*
			 * Find cheapest/most expensive permutations taking scenarios into account.
			 */
			if ( $permutations_calc_scenarios ) {

				/**
				 * 'woocommerce_composite_permutations_search_time_limit' filter.
				 *
				 * Enter a min/max permutation search time limit.
				 *
				 * @since  3.14.0
				 *
				 * @param  bool  $limit
				 */
				$search_time_limit = apply_filters( 'woocommerce_composite_permutations_search_time_limit', false === $task_runner_args ? 2 : 10 );

				/**
				 * 'woocommerce_composite_permutations_search_time_limit' filter.
				 *
				 * Limits the number of permutations to test per run. No limit by default.
				 *
				 * @since  3.14.0
				 *
				 * @param  int  $limit
				 */
				$permutations_search_count_limit = apply_filters( 'woocommerce_composite_permutations_search_count_limit', 0 );

				$min_price                 = empty( $price_data[ 'min' ] ) ? '' : $this->get_permutation_price( $price_data[ 'min' ], $components, $component_option_prices, 'min' );
				$max_price                 = empty( $price_data[ 'max' ] ) ? '' : $this->get_permutation_price( $price_data[ 'max' ], $components, $component_option_prices, 'max' );
				$start_time                = time();
				$permutations_count        = 0;
				$permutations_search_count = 0;
				$invalid_permutation_part  = false;

				foreach ( wc_cp_cartesian( $permutation_vectors ) as $permutation ) {

					$permutations_count++;

					// Resuming?
					if ( $resume_from_permutation ) {

						if ( $permutations_count < $resume_from_permutation ) {
							continue;
						} else {
							$resume_from_permutation = false;
						}
					}

					// Check the number of tested perms.
					if ( $permutations_search_count_limit && $permutations_search_count > $permutations_search_count_limit ) {
							$resume_from_permutation = $permutations_count;
							break;
					}

					$permutations_search_count++;

					// Check the elapsed time every 10000 tests.
					if ( $permutations_count % 10000 === 0 ) {
						if ( time() - $start_time > $search_time_limit ) {
							$resume_from_permutation = $permutations_count;
							break;
						}
					}

					// Skip permutation if already found invalid.
					if ( is_array( $invalid_permutation_part ) ) {

						$validate_permutation = false;

						foreach ( $invalid_permutation_part as $invalid_permutation_part_key => $invalid_permutation_part_value ) {
							if ( $invalid_permutation_part_value !== $permutation[ $invalid_permutation_part_key ] ) {
								$validate_permutation = true;
								break;
							}
						}

						if ( ! $validate_permutation ) {
							continue;
						} else {
							$invalid_permutation_part = false;
						}
					}

					$configuration = array();

					foreach ( $permutation as $component_id => $component_option_id ) {

						// Is it a variation?
						if ( isset( $permutation_vector_data[ $component_id ][ 'parent_ids' ][ $component_option_id ] ) ) {
							$configuration[ $component_id ] = array(
								'product_id'   => $permutation_vector_data[ $component_id ][ 'parent_ids' ][ $component_option_id ],
								'variation_id' => $component_option_id
							);
						} else {
							$configuration[ $component_id ] = array(
								'product_id' => $component_option_id
							);
						}
					}

					$validation_result = $product->scenarios()->validate_configuration( $configuration );

					if ( is_wp_error( $validation_result ) ) {

						$error_data               = $validation_result->get_error_data( $validation_result->get_error_code() );
						$invalid_permutation_part = array();

						// Keep a copy of the invalid permutation up to the offending component.
						foreach ( $permutation as $component_id => $component_option_id ) {
							$invalid_permutation_part[ $component_id ] = $component_option_id;
							if ( $component_id === $error_data[ 'component_id' ] ) {
								break;
							}
						}

					} else {

						/*
						 * Find the permutation with the min/max price.
						 */

						$min_permutation_price = $this->get_permutation_price( $permutation, $components, $component_option_prices, 'min' );
						$max_permutation_price = $this->get_permutation_price( $permutation, $components, $component_option_prices, 'max' );

						if ( false === $min_permutation_price || false === $max_permutation_price ) {
							continue;
						}

						if ( $min_permutation_price < $min_price || '' === $min_price ) {
							$price_data[ 'min' ] = $permutation;
							$min_price           = $min_permutation_price;
						}

						if ( INF !== $max_permutation_price ) {
							if ( $max_permutation_price > $max_price || '' === $max_price ) {
								$price_data[ 'max' ] = $permutation;
								$max_price           = $max_permutation_price;
							}
						} else {
							$price_data[ 'max' ] = array();
						}
					}
				}

				if ( $resume_from_permutation ) {

					if ( $iteration < $max_iterations ) {
						$price_data[ 'resume' ] = $resume_from_permutation;
						$price_data[ 'status' ] = 'pending';
					} else {
						$price_data[ 'status' ] = 'failed';
					}
				}

			/*
			 * Find cheapest/most expensive permutation without considering scenarios.
			 */
			} else {

				$has_inf_max_price       = false;
				$resume_from_permutation = false;

				/*
				 * Use filtered prices to find the permutation with the min/max price.
				 */
				foreach ( $component_option_prices as $component_id => $component_option_price_data ) {

					$component = $components[ $component_id ];

					$component_option_prices_min = $component_option_price_data[ 'min' ];
					asort( $component_option_prices_min );

					$component_option_prices_max = $component_option_price_data[ 'max' ];
					asort( $component_option_prices_max );

					$min_component_price = current( $component_option_prices_min );
					$max_component_price = end( $component_option_prices_max );

					$min_component_price_ids = array_keys( $component_option_prices_min );
					$max_component_price_ids = array_keys( $component_option_prices_max );

					$min_component_price_id  = current( $min_component_price_ids );
					$max_component_price_id  = end( $max_component_price_ids );

					$quantity_min = $component->get_quantity( 'min' );
					$quantity_max = $component->get_quantity( 'max' );

					$price_data[ 'min' ][ $component_id ] = $component->is_optional() || 0 === $quantity_min ? 0 : $min_component_price_id;

					if ( ! $has_inf_max_price ) {
						if ( INF !== $max_component_price && '' !== $quantity_max ) {
							$price_data[ 'max' ][ $component_id ] = $max_component_price_id;
						} else {
							$price_data[ 'max' ] = array();
							$has_inf_max_price   = true;
						}
					}
				}
			}
		}

		// Not finished yet?
		if ( $resume_from_permutation ) {

			// 1st time running? Continue in the background!
			if ( false === $task_runner_args ) {

				WC_CP_Products::schedule_price_calc_task( array(
					'min'          => $price_data[ 'min' ],
					'max'          => $price_data[ 'max' ],
					'hash'         => $price_data[ 'hash' ],
					'resume'       => $resume_from_permutation,
					'composite_id' => $product->get_id(),
				) );
			}
		}

		$product->add_meta_data( '_bto_price_data', $price_data, true );
		update_post_meta( $product->get_id(), '_bto_price_data', $price_data );
		WC_CP_Helpers::cache_set( 'price_data_' . $product->get_id(), $price_data );

		return $price_data;
	}

	/**
	 * Calculates the price of a permutation.
	 *
	 * @since  4.0.0
	 *
	 * @param  array  $permutation
	 * @param  array  $components
	 * @param  array  $prices
	 * @param  string $min_or_max
	 * @return mixed
	 */
	private function get_permutation_price( $permutation, $components, $prices, $min_or_max = 'min' ) {

		$price = 0.0;

		foreach ( $components as $component_id => $component ) {

			// Skip component if not relevant for price calculations.
			if ( ! isset( $permutation[ $component_id ] ) ) {
				continue;
			}

			$component_option_id    = $permutation[ $component_id ];
			$component_option_price = 0.0;

			if ( $component_option_id > 0 ) {

				// Empty price.
				if ( ! isset( $prices[ $component_id ][ $min_or_max ][ $component_option_id ] ) ) {
					if ( $component->is_priced_individually() ) {
						return false;
					} else {
						continue;
					}
				}

				$component_option_price = $prices[ $component_id ][ $min_or_max ][ $component_option_id ];
			}

			$quantity = $component->get_quantity( $min_or_max );

			if ( 'max' !== $min_or_max ) {

				$price += $quantity * (double) $component_option_price;

			} else {

				if ( INF !== $price ) {
					if ( INF !== $component_option_price && '' !== $quantity ) {
						$price += $quantity * (double) $component_option_price;
					} else {
						$price = INF;
					}
				}
			}
		}

		/**
		 * 'woocommerce_composite_permutation_price' filter.
		 *
		 * @since  4.0.0
		 *
		 * @param  mixed  $price
		 * @param  array  $permutation
		 * @param  array  $components
		 * @param  array  $prices
		 * @param  string $min_or_max
		 */
		return apply_filters( 'woocommerce_composite_permutation_price', $price, $permutation, $components, $prices, $min_or_max );
	}

	/**
	 * Get raw product prices straight from the DB.
	 *
	 * @param  array  $product_ids
	 * @return array
	 */
	public function get_raw_component_option_prices( $product_ids ) {

		global $wpdb;

		$expanded_ids = $this->get_expanded_component_options( $product_ids, 'expanded' );
		$parent_ids   = $this->get_expanded_component_options( $product_ids, 'mapped' );

		$results_cache_key = 'raw_component_option_prices_' . md5( json_encode( $product_ids ) );
		$results           = WC_CP_Helpers::cache_get( $results_cache_key );

		if ( null === $results ) {

			$results = $wpdb->get_results( "
				SELECT postmeta.post_id AS id, postmeta.meta_value as price FROM {$wpdb->postmeta} AS postmeta
				WHERE postmeta.meta_key = '_price'
				AND postmeta.post_id IN ( " . implode( ',', $expanded_ids ) . " )
			", ARRAY_A );

			WC_CP_Helpers::cache_set( $results_cache_key, $results );
		}

		$prices = array(
			'min' => array(),
			'max' => array()
		);

		/**
		 * 'woocommerce_composite_read_nyp_component_option_prices' filter.
		 *
		 * Ideally, we should read the NYP min price for NYP products (NYP doesn't update the '_price' meta).
		 * However these queries introduce an overhead that's not necessarily needed.
		 * Having NYP + CP active on the same site doesn't always imply that NYP products are used in Composites.
		 * Use this filter to attempt to read NYP's min price meta when it exists.
		 *
		 * @since  4.0.0
		 * @param  bool  $read_nyp_component_option_prices
		 */
		if ( class_exists( 'WC_Name_Your_Price_Helpers' ) && apply_filters( 'woocommerce_composite_read_nyp_component_option_prices', false ) ) {

			$nyp_results_cache_key = $results_cache_key . '_nyp';
			$nyp_results           = WC_CP_Helpers::cache_get( $nyp_results_cache_key );

			if ( null === $nyp_results ) {

				$nyp_id_results = $wpdb->get_results( "
					SELECT postmeta.post_id AS id FROM {$wpdb->postmeta} AS postmeta
					WHERE postmeta.meta_key = '_nyp'
					AND postmeta.meta_value = 'yes'
					AND postmeta.post_id IN ( " . implode( ',', $expanded_ids ) . " )
				", ARRAY_A );

				$nyp_ids     = wp_list_pluck( $nyp_id_results, 'id' );
				$nyp_results = array();

				if ( ! empty( $nyp_ids ) ) {
					$nyp_results = $wpdb->get_results( "
						SELECT postmeta.post_id AS id, postmeta.meta_value AS min_price FROM {$wpdb->postmeta} AS postmeta
						WHERE postmeta.meta_key = '_min_price'
						AND postmeta.post_id IN ( " . implode( ',', $nyp_ids ) . " )
					", ARRAY_A );
				}

				WC_CP_Helpers::cache_set( $nyp_results_cache_key, $nyp_results );
			}

			foreach ( $nyp_results as $nyp_result ) {

				// Is it a variation?
				if ( isset( $parent_ids[ $nyp_result[ 'id' ] ] ) ) {

					// If it's included in the search vector, then add an entry.
					if ( in_array( $nyp_result[ 'id' ], $product_ids ) ) {
						$product_id = $nyp_result[ 'id' ];
					// Otherwise, add an entry for the parent.
					} else {
						$product_id = $parent_ids[ $nyp_result[ 'id' ] ];
					}

				} else {
					$product_id = $nyp_result[ 'id' ];
				}

				$price_min = '' === $nyp_result[ 'min_price' ] ? 0.0 : (double) $nyp_result[ 'min_price' ];
				$price_max = INF;

				$prices[ 'min' ][ $product_id ] = $price_min;
				$prices[ 'max' ][ $product_id ] = $price_max;
			}
		}

		// Multiple '_price' meta may exist.
		foreach ( $results as $result ) {

			if ( '' === $result[ 'price' ] ) {
				continue;
			}

			// Is it a variation?
			if ( isset( $parent_ids[ $result[ 'id' ] ] ) ) {

				// If it's included in the search vector, then add an entry.
				if ( in_array( $result[ 'id' ], $product_ids ) ) {
					$product_id = $result[ 'id' ];
				// Otherwise, add an entry for the parent.
				} else {
					$product_id = $parent_ids[ $result[ 'id' ] ];
				}

			} else {
				$product_id = $result[ 'id' ];
			}

			$price_min = isset( $prices[ 'min' ][ $product_id ] ) ? min( (double) $result[ 'price' ], $prices[ 'min' ][ $product_id ] ) : (double) $result[ 'price' ];
			$price_max = isset( $prices[ 'max' ][ $product_id ] ) ? max( (double) $result[ 'price' ], $prices[ 'max' ][ $product_id ] ) : (double) $result[ 'price' ];

			$prices[ 'min' ][ $product_id ] = $price_min;
			$prices[ 'max' ][ $product_id ] = $price_max;
		}

		return $prices;
	}

	/**
	 * Get expanded component options to include variations straight from the DB.
	 *
	 * @since  3.14.0
	 *
	 * @param  array  $product_ids
	 * @return array
	 */
	public static function get_expanded_component_options( $product_ids, $return = 'expanded' ) {

		global $wpdb;

		$results = array(
			'merged'   => array(),
			'expanded' => array(),
			'mapped'   => array()
		);

		if ( empty( $product_ids ) ) {
			return 'all' === $return ? $results : $results[ $return ];
		}

		$results_cache_key = 'expanded_component_options_' . md5( json_encode( $product_ids ) );
		$cached_results    = WC_CP_Helpers::cache_get( $results_cache_key );

		if ( null === $cached_results ) {

			$query_results = $wpdb->get_results( "
				SELECT posts.ID AS id, posts.post_parent as parent_id FROM {$wpdb->posts} AS posts
				WHERE posts.post_type = 'product_variation'
				AND post_parent IN ( " . implode( ',', $product_ids ) . " )
				AND post_parent > 0
				AND posts.post_status = 'publish'
			", ARRAY_A );

			$results[ 'merged' ]   = array_merge( $product_ids, wp_list_pluck( $query_results, 'id' ) );
			$results[ 'expanded' ] = array_merge( array_diff( $product_ids, wp_list_pluck( $query_results, 'parent_id' ) ), wp_list_pluck( $query_results, 'id' ) );
			$results[ 'mapped' ]   = empty( $query_results ) ? array() : array_combine( wp_list_pluck( $query_results, 'id' ), wp_list_pluck( $query_results, 'parent_id' ) );

			$cached_results = $results;

			WC_CP_Helpers::cache_set( $results_cache_key, $cached_results );
		}

		return 'all' === $return ? $cached_results : $cached_results[ $return ];
	}

	/**
	 * Use 'WP_Query' to preload product data from the 'posts' table.
	 * Useful when we know we are going to call 'wc_get_product' against a list of IDs.
	 *
	 * @since  3.13.2
	 *
	 * @param  array  $product_ids
	 * @return void
	 */
	public function preload_component_options_data( $product_ids ) {

		if ( empty( $product_ids ) ) {
			return;
		}

		$cache_key = 'wc_component_options_db_data_' . md5( json_encode( $product_ids ) );
		$data      = WC_CP_Helpers::cache_get( $cache_key );

		if ( null === $data ) {

			$data = new WP_Query( array(
				'post_type' => 'product',
				'nopaging'  => true,
				'post__in'  => $product_ids
			) );

			WC_CP_Helpers::cache_set( $cache_key, $data );
		}
	}

	/**
	 * Component option query handler.
	 *
	 * @since  3.14.0
	 *
	 * @param  array  $component_data
	 * @param  array  $query_args
	 * @return array
	 */
	public function query_component_options( $component_data, $query_args ) {

		$defaults = array(
			// Set to false when running raw queries.
			'orderby'              => false,
			// Use false to get all results -- set to false when running raw queries or dropdown-template queries.
			'per_page'             => false,
			// Page number to load, in effect only when 'per_page' is set.
			// When set to 'selected', 'load_page' will point to the page that contains the current option, passed in 'selected_option'.
			'load_page'            => 1,
			'post_ids'             => ! empty( $component_data[ 'assigned_ids' ] ) ? $component_data[ 'assigned_ids' ] : false,
			'query_type'           => ! empty( $component_data[ 'query_type' ] ) ? $component_data[ 'query_type' ] : 'product_ids',
			// ID of selected option, used when 'load_page' is set to 'selected'.
			'selected_option'      => '',
			'disable_cache'        => false,
			// See 'WC_CP_Component::exclude_out_of_stock_options'.
			'exclude_out_of_stock' => false
		);

		$query_args = wp_parse_args( $query_args, $defaults );
		$args       = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'nopaging'            => true,
			'order'               => 'desc',
			'fields'              => 'ids',
			'meta_query'          => array()
		);

		/*-----------------------------------------------------------------------------------*/
		/*  Prepare query for product IDs.                                                   */
		/*-----------------------------------------------------------------------------------*/

		if ( 'product_ids' === $query_args[ 'query_type' ] ) {

			if ( $query_args[ 'post_ids' ] ) {
				$args[ 'post__in' ] = array_values( $query_args[ 'post_ids' ] );
			} else {
				$args[ 'post__in' ] = array( '0' );
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Sort results.                                                                    */
		/*-----------------------------------------------------------------------------------*/

		$orderby = $query_args[ 'orderby' ];

		if ( $orderby ) {

			$orderby_value = explode( '-', $orderby );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : '';

			switch ( $orderby ) {

				case 'default' :
					if ( 'product_ids' === $query_args[ 'query_type' ] ) {
						$args[ 'orderby' ] = 'post__in';
					}
				break;

				case 'menu_order' :
					if ( 'product_ids' === $query_args[ 'query_type' ] ) {
						$args[ 'orderby' ] = 'menu_order title';
						$args[ 'order' ]   = $order == 'desc' ? 'desc' : 'asc';
					}
				break;

				case 'title' :
					$args[ 'orderby' ] = 'title';
					$args[ 'order' ]   = 'desc' === $order ? 'desc' : 'asc';
				break;

				case 'rand' :
					$args[ 'orderby' ] = 'rand';
				break;

				case 'date' :
					$args[ 'orderby' ] = 'date';
				break;

				case 'price' :

					$callback = 'desc' === $order ? 'order_by_price_desc_post_clauses' : 'order_by_price_asc_post_clauses';

					add_filter( 'posts_clauses', array( WC()->query, $callback ) );

				break;

				case 'popularity' :

					if ( ! WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {
						$args[ 'meta_key' ] = 'total_sales';
					}

					add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );

				break;

				case 'rating' :

					if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

						add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );

					} else {

						$args[ 'meta_key' ] = '_wc_average_rating';
						$args[ 'orderby' ]  = array(
							'meta_value_num' => 'DESC',
							'ID'             => 'ASC',
						);
					}

				break;

			}

		// In effect for back-end queries and queries carried out during sync().
		} else {

			// Make ids appear in the sequence they are saved.
			if ( 'product_ids' === $query_args[ 'query_type' ] ) {
				$args[ 'orderby' ] = 'post__in';
			} else {
				$args[ 'orderby' ] = 'date title';
			}
		}

		$is_raw_component_options_query = false === $query_args[ 'orderby' ] && false === $query_args[ 'per_page' ];
		$use_transients_cache           = false;

		/*-----------------------------------------------------------------------------------*/
		/*	Remove out-of-stock results in front-end queries.
		/*-----------------------------------------------------------------------------------*/

		if ( ! $is_raw_component_options_query ) {

			if ( isset( $query_args[ 'exclude_out_of_stock' ] ) && $query_args[ 'exclude_out_of_stock' ] ) {

				$product_visibility_terms = wc_get_product_visibility_term_ids();

				$args[ 'tax_query' ][] = array(
					'taxonomy'      => 'product_visibility',
					'field'         => 'term_taxonomy_id',
					'terms'         => $product_visibility_terms[ 'outofstock' ],
					'operator'      => 'NOT IN'
				);
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Pagination.                                                                      */
		/*-----------------------------------------------------------------------------------*/

		$load_selected_page = false;

		// Check if we need to find the page that contains the current selection -- 'load_page' must be set to 'selected' and all relevant parameters must be provided.

		if ( 'selected' === $query_args[ 'load_page' ] ) {

			if ( $query_args[ 'per_page' ] && $query_args[ 'selected_option' ] !== '' ) {
				$load_selected_page = true;
			} else {
				$query_args[ 'load_page' ] = 1;
			}
		}

		// Otherwise, just check if we need to do a paginated query -- note that when looking for the page that contains the current selection, we are running an unpaginated query first.

		if ( $query_args[ 'per_page' ] && false === $load_selected_page ) {

			$args[ 'nopaging' ]       = false;
			$args[ 'posts_per_page' ] = $query_args[ 'per_page' ];
			$args[ 'paged' ]          = $query_args[ 'load_page' ];
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Optimize 'raw' queries.                                                          */
		/*-----------------------------------------------------------------------------------*/

		if ( $is_raw_component_options_query ) {

			$args[ 'update_post_term_cache' ] = false;
			$args[ 'update_post_meta_cache' ] = false;
			$args[ 'cache_results' ]          = false;

			if ( false === $query_args[ 'disable_cache' ] && ! empty( $component_data[ 'component_id' ] ) && ! empty( $component_data[ 'composite_id' ] ) && ! defined( 'WC_CP_DEBUG_QUERY_TRANSIENTS' ) ) {
				$use_transients_cache = true;
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Filtering attributes?                                                            */
		/*-----------------------------------------------------------------------------------*/

		if ( ! empty( $query_args[ 'filters' ] ) && ! empty( $query_args[ 'filters' ][ 'attribute_filter' ] ) ) {

			$attribute_filters = $query_args[ 'filters' ][ 'attribute_filter' ];

			$args[ 'tax_query' ][ 'relation' ] = 'AND';

			foreach ( $attribute_filters as $taxonomy_attribute_name => $selected_attribute_values ) {

				$args[ 'tax_query' ][] = array(
					'taxonomy' => $taxonomy_attribute_name,
					'terms'    => $selected_attribute_values,
					'operator' => 'IN'
				);
			}
		}

		/*-----------------------------------------------------------------------------------*/
		/*  Querying by category?                                                            */
		/*-----------------------------------------------------------------------------------*/

		if ( 'category_ids' === $query_args[ 'query_type' ] ) {

			$args[ 'tax_query' ][] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'terms'    => ! empty( $component_data[ 'assigned_category_ids' ] ) ? array_values( $component_data[ 'assigned_category_ids' ] ) : array( '0' ),
					'operator' => 'IN'
				),
				array(
					'taxonomy' => 'product_type',
					'field'    => 'name',
					'terms'    => apply_filters( 'woocommerce_composite_products_supported_types', array( 'simple', 'variable', 'bundle' ) ),
					'operator' => 'IN'
				)
			);

		}

		/*-----------------------------------------------------------------------------------*/
		/*  Modify query and apply filters by hooking at this point.                         */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Filter args passed to WP_Query.
		 *
		 * @param  array  $wp_query_args
		 * @param  array  $cp_query_args
		 * @param  array  $component_data
		 */
		$args = apply_filters( 'woocommerce_composite_component_options_query_args', $args, $query_args, $component_data );

		/*-----------------------------------------------------------------------------------*/
		/*  Go for it.                                                                       */
		/*-----------------------------------------------------------------------------------*/

		$query                = false;
		$cached_results       = false;
		$cached_results_array = false;
		$component_id         = $use_transients_cache ? $component_data[ 'component_id' ] : '';
		$transient_name       = $use_transients_cache ? 'wc_cp_query_results_' . $component_data[ 'composite_id' ] : '';
		$cached_results_array = $use_transients_cache ? get_transient( $transient_name ) : false;

		// Is it an array indexed by component ID?
		if ( is_array( $cached_results_array ) && ! isset( $cached_results_array[ 'version' ] ) ) {
			// Does it contain cached query results for this component?
			if ( isset( $cached_results_array[ $component_id ] ) && is_array( $cached_results_array[ $component_id ] ) ) {
				// Are the results up-to-date?
				if ( isset( $cached_results_array[ $component_id ][ 'version' ] ) && $cached_results_array[ $component_id ][ 'version' ] === WC_Cache_Helper::get_transient_version( 'product' ) ) {
					$cached_results = $cached_results_array[ $component_id ];
				}
			}
		}

		if ( false === $cached_results ) {

			$query   = new WP_Query( $args );
			$results = array(
				'query_args'        => $query_args,
				'pages'             => $query->max_num_pages,
				'current_page'      => $query->get( 'paged' ),
				'component_options' => $query->posts
			);

			if ( empty( $query->posts ) ) {
				$use_transients_cache = false;
			}

			if ( $use_transients_cache ) {

				if ( is_array( $cached_results_array ) && ! isset( $cached_results_array[ 'version' ] ) ) {
					$cached_results_array[ $component_id ] = array_merge( $results, array( 'version' => WC_Cache_Helper::get_transient_version( 'product' ) ) );
				} else {
					$cached_results_array = array(
						$component_id => array_merge( $results, array( 'version' => WC_Cache_Helper::get_transient_version( 'product' ) ) )
					);
				}

				// Cache results for 7-8 days. RAND prevents them all from expiring at the same time to help with performance.
				set_transient( $transient_name, $cached_results_array, DAY_IN_SECONDS * 7 + rand( 0 , DAY_IN_SECONDS ) );
			}

		} else {
			$results = $cached_results;
		}

		/*-----------------------------------------------------------------------------------------------------------------------------------------------*/
		/*  When told to do so, use the results of the 1st query to find the page that contains the current selection.                                   */
		/*-----------------------------------------------------------------------------------------------------------------------------------------------*/

		if ( $load_selected_page && $query_args[ 'per_page' ] && $query_args[ 'per_page' ] < $query->found_posts ) {

			$results               = ! empty( $results[ 'component_options' ] ) ? $results[ 'component_options' ] : array();
			$selected_option_index = array_search( $query_args[ 'selected_option' ], $results ) + 1;
			$selected_option_page  = ceil( $selected_option_index / $query_args[ 'per_page' ] );

			// Sorting and filtering has been done, so now just run a simple query to paginate the results.
			if ( ! empty( $results ) ) {

				$selected_args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'nopaging'            => false,
					'posts_per_page'      => $query_args[ 'per_page' ],
					'paged'               => $selected_option_page,
					'order'               => 'desc',
					'orderby'             => 'post__in',
					'post__in'            => $results,
					'fields'              => 'ids',
				);

				$query = new WP_Query( $selected_args );

				$results = array(
					'query_args'        => $query_args,
					'pages'             => $query->max_num_pages,
					'current_page'      => $query->get( 'paged' ),
					'component_options' => $query->posts
				);
			}
		}

		return $results;
	}
}
