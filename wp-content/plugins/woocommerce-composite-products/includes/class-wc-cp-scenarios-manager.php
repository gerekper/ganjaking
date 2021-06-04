<?php
/**
 * WC_CP_Scenarios_Manager class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates configurations against scenarios.
 *
 * @class    WC_CP_Scenarios_Manager
 * @version  8.0.2
 */
class WC_CP_Scenarios_Manager {

	/**
	 * Scenario objects.
	 * @var array
	 */
	private $scenarios;

	/**
	 * Component objects.
	 * @var array
	 */
	private $components;

	/**
	 * Count of scenarios with defined actions.
	 * @var integer
	 */
	private $complexity = 0;

	/**
	 * Optional component IDs.
	 * @var array
	 */
	private $optional_components;

	/**
	 * Constructor.
	 *
	 * @param  WC_Product_Composite|array  $data
	 */
	public function __construct( $data, $context = 'view' ) {

		$this->scenarios           = array();
		$this->components          = array();
		$this->optional_components = is_array( $data ) && ! empty( $data[ 'optional_components' ] ) ? $data[ 'optional_components' ] : array();

		$compat_group_action_exists = false;


		if ( $data instanceof WC_Product_Composite ) {
			$scenarios_data   = $data->get_scenario_data( $context );
			$this->components = $data->get_components();
		} elseif ( is_array( $data ) && ! empty( $data[ 'scenario_data' ] ) ) {
			$scenarios_data =  (array) $data[ 'scenario_data' ];
		} else {
			$scenarios_data = array();
		}

		// Define optional components.
		if ( ! empty( $this->components ) ) {
			foreach ( $this->components as $component_id => $component ) {
				if ( $component->is_optional() ) {
					$this->optional_components[] = $component_id;
				}
			}
		}

		// Create scenario objects.
		if ( ! empty( $scenarios_data ) && is_array( $scenarios_data ) ) {
			foreach ( $scenarios_data as $scenario_id => $scenario_data ) {

				$scenario = new WC_CP_Scenario( array_merge( $scenario_data, array( 'id' => $scenario_id ) ) );

				if ( $scenario->has_action( 'compat_group' ) ) {
					$compat_group_action_exists = true;
				}

				$this->complexity += sizeof( $scenario->get_actions() );

				$this->scenarios[ $scenario->get_id() ] = $scenario;
			}
		}

		// When no 'compat_group' scenarios are defined, create a placeholder scenario where all options are valid.
		if ( ! $compat_group_action_exists && 'view' === $context ) {
			$this->scenarios[ '0' ] = new WC_CP_Scenario( array( 'id' => '0' ) );
			$this->complexity++;
		}
	}

	/**
	 * Validates a composite configuration against scenarios. Example:
	 *
	 *    $config = array(
	 *        134567890 => array(              // ID of component.
	 *            'product_id'        => 15,   // ID of selected product option.
	 *            'variation_id'      => 43    // ID of chosen variation, if applicable.
	 *        )
	 *    );
	 *
	 * Note: Only validates the supplied IDs against Scenarios. Does not validate that they exist!
	 *
	 * @param  array    $configuration
	 * @param  array    $args
	 * @return boolean
	 */
	public function validate_configuration( $configuration, $args = array() ) {

		$matching_scenarios = $this->find_matching( $configuration, $args );

		if ( is_wp_error( $matching_scenarios ) ) {
			$result = $matching_scenarios;
		} else {
			$result = true;
		}

		return $result;
	}

	/**
	 * Find scenarios matching a composite configuration - @see 'WC_CP_Scenarios_Manager::validate_composite_configuration'.
	 *
	 * @param  array    $configuration
	 * @param  array    $args
	 * @return boolean
	 */
	public function find_matching( $configuration, $args = array() ) {

		$matching_components     = array();
		$hidden_components       = array();
		$validating_defaults     = isset( $args[ 'validating_defaults' ] ) && $args[ 'validating_defaults' ];
		$matching_scenarios      = isset( $args[ 'scenarios' ] ) && is_array( $args[ 'scenarios' ] ) ? $args[ 'scenarios' ] : array();
		$matching_scenarios_cg   = $this->get_ids_by_action( 'compat_group', $matching_scenarios );
		$matching_scenarios_cc   = $this->get_ids_by_action( 'conditional_components', $matching_scenarios );
		$matching_scenarios_init = ! empty( $matching_scenarios );

		$configuration = $this->parse_configuration( $configuration );

		foreach ( $configuration as $component_id => $component_configuration ) {

			$matching_components[] = $component_id;

			$scenarios_matching_component    = array();
			$scenarios_matching_component_cg = array();
			$scenarios_matching_component_cc = array();
			$scenarios_matching_component_co = array();
			$scenarios_hiding_component      = array();
			$scenarios_hiding_option         = array();

			$component_is_hidden = false;
			$selection_is_valid  = true;

			$product_id    = $component_configuration[ 'product_id' ];
			$variation_ids = $component_configuration[ 'variation_ids' ];

			// Find matching scenarios.
			foreach ( $this->scenarios as $scenario ) {

				$scenario_contains_option = false;
				$scenario_hides_option    = false;
				$scenario_hides_component = $scenario->hides_component( $component_id );

				if ( is_array( $variation_ids ) && ! empty( $variation_ids ) ) {

					foreach ( $variation_ids as $variation_id ) {
						if ( $scenario->contains_component_option( $component_id, $product_id, absint( $variation_id ) ) ) {
							$scenario_contains_option = true;
							break;
						}
					}

					if ( $scenario_contains_option && $scenario->has_action( 'conditional_options' ) ) {
						foreach ( $variation_ids as $variation_id ) {
							if ( $scenario->hides_component_option( $component_id, $product_id, absint( $variation_id ) ) ) {
								$scenario_hides_option = true;
								break;
							}
						}
					}

				} else {

					$scenario_contains_option = $scenario->contains_component_option( $component_id, $product_id );

					if ( -1 === $product_id && ! in_array( $component_id, $this->optional_components ) ) {
						$scenario_contains_option = false;
					}

					if ( $scenario_contains_option && $scenario->has_action( 'conditional_options' ) ) {
						$scenario_hides_option = $scenario->hides_component_option( $component_id, $product_id );
					}
				}

				if ( $scenario_contains_option ) {

					$scenarios_matching_component[] = $scenario->get_id();

					if ( $scenario->has_action( 'compat_group' ) ) {
						$scenarios_matching_component_cg[] = $scenario->get_id();
					}
				}

				if ( $scenario_hides_component ) {

					$matching_shaping_components = array_diff( $matching_components, $hidden_components, $scenario->get_masked_components() );

					// Scenario hides component or option only if some of the scenario shaping components are non-masked and non-hidden.
					if ( ! empty( $matching_shaping_components ) ) {
						$scenarios_hiding_component[] = $scenario->get_id();
					}
				}

				if ( $scenario_hides_option ) {
					$scenarios_hiding_option[] = $scenario->get_id();
				}
			}

			// Is component hidden?
			$matching_scenarios_cc           = $this->get_ids_by_action( 'conditional_components', $matching_scenarios );
			$scenarios_matching_component_cc = array_intersect( $matching_scenarios_cc, $scenarios_hiding_component );
			$component_is_hidden             = sizeof( $scenarios_matching_component_cc );

			// Is option hidden?
			$matching_scenarios_co           = $this->get_ids_by_action( 'conditional_options', $matching_scenarios );
			$scenarios_matching_component_co = array_intersect( $matching_scenarios_co, $scenarios_hiding_option );
			$option_is_hidden                = sizeof( $scenarios_matching_component_co );

			// If so, ensure selection is -1.
			if ( $component_is_hidden ) {

				$scenarios_matching_component = array_unique( array_merge( $scenarios_matching_component, $matching_scenarios ) );

				// Load matching scenarios from previous iteration.
				if ( -1 === $product_id ) {
					if ( empty( $scenarios_matching_component_cg ) ) {
						$scenarios_matching_component_cg = $matching_scenarios_cg;
					}
				} elseif ( false === $validating_defaults ) {
					$selection_is_valid = false;
				}
			}

			if ( empty( $scenarios_matching_component_cg ) ) {
				$selection_is_valid = false;
			}

			if ( $option_is_hidden ) {
				if ( false === $component_is_hidden || false === $validating_defaults ) {
					$selection_is_valid = false;
				}
			}

			if ( $selection_is_valid ) {

				if ( $component_is_hidden ) {
					$hidden_components[] = $component_id;
				}

				if ( false === $matching_scenarios_init ) {
					$matching_scenarios      = $scenarios_matching_component;
					$matching_scenarios_cg   = $scenarios_matching_component_cg;
					$matching_scenarios_init = true;
				} else {
					$matching_scenarios      = array_intersect( $matching_scenarios, $scenarios_matching_component );
					$matching_scenarios_cg   = array_intersect( $matching_scenarios_cg, $scenarios_matching_component_cg );
				}

				if ( empty( $matching_scenarios_cg ) ) {
					return new WP_Error( 'woocommerce_composite_configuration_invalid', '', array( 'component_id' => $component_id ) );
				}

			} else {
				return new WP_Error( 'woocommerce_composite_configuration_selection_' . ( -1 === $product_id ? 'required' : 'invalid' ) , '', array( 'component_id' => $component_id ) );
			}
		}

		return $matching_scenarios;
	}

	/**
	 * Parses a composite configuration for validation.
	 *
	 * @param  array  $configuration
	 * @return array
	 */
	private function parse_configuration( $configuration ) {

		$processed_configuration = array();

		foreach ( $configuration as $component_id => $component_configuration ) {

			$product_id    = isset( $component_configuration[ 'product_id' ] ) && absint( $component_configuration[ 'product_id' ] ) > 0 ? absint( $component_configuration[ 'product_id' ] ) : -1;
			$variation_ids = false;

			if ( isset( $component_configuration[ 'variation_id' ] ) ) {
				if ( 'any' === $component_configuration[ 'variation_id' ] ) {
					$variation_ids = WC_CP_Helpers::get_product_variations( $product_id );
				} else {
					$variation_ids = absint( $component_configuration[ 'variation_id' ] ) > 0 && $product_id > 0 ? array( absint( $component_configuration[ 'variation_id' ] ) ) : false;
				}
			}

			$processed_configuration[ $component_id ] = array(
				'product_id'    => $product_id,
				'variation_ids' => $variation_ids
			);
		}

		return $processed_configuration;
	}

	/**
	 * Filter scenarios by action type.
	 *
	 * @param  string  $action_id
	 * @param  array   $subset_ids
	 * @return array
	 */
	public function get_ids_by_action( $action_id, $subset_ids = false ) {

		$filtered_ids = array();
		$subset_ids   = false === $subset_ids ? $this->get_ids() : (array) $subset_ids;

		if ( ! empty( $subset_ids ) ) {
			foreach ( $subset_ids as $scenario_id ) {
				if ( ! empty( $this->scenarios[ $scenario_id ] ) && $this->scenarios[ $scenario_id ]->has_action( $action_id ) ) {
					$filtered_ids[] = $this->scenarios[ $scenario_id ]->get_id();
				}
			}
		}

		return $filtered_ids;
	}

	/**
	 * Get all scenario IDs.
	 *
	 * @return array
	 */
	public function get_ids() {
		return array_map( 'strval', array_keys( $this->scenarios ) );
	}

	/**
	 * Get all scenarios.
	 *
	 * @return array
	 */
	public function get_scenarios() {
		return $this->scenarios;
	}

	/**
	 * Get all components.
	 *
	 * @return array
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Maps component options to scenario IDs.
	 *
	 * @param  array   $component_options_subset  Subset of the available component options (product IDs only) to use, indexed by component ID. Optional.
	 * @param  array   $scenarios_subset          Subset of the available scenarios to use. Optional.
	 * @param  string  $map_type                  Map type. Optional.
	 * @return array                              Map of product IDs and variation IDs (indexed by component ID) to scenario IDs.
	 */
	public function get_map( $component_options_subset = false, $scenarios_subset = false, $map_type = 'conditions' ) {

		$contains_fn = 'conditional_options' === $map_type ? 'hides_component_option' : 'contains_component_option';
		$scenarios   = $this->get_scenarios();
		$components  = $this->get_components();
		$options_map = array();

		if ( is_array( $scenarios_subset ) ) {
			$scenarios = array_diff_key( $scenarios, $scenarios_subset );
		}

		foreach ( $components as $component_id => $component ) {

			// When indicated, build scenarios map only for the specified subset of component options.
			if ( is_array( $component_options_subset ) ) {
				if ( isset( $component_options_subset[ $component_id ] ) ) {
					if ( is_array( $component_options_subset[ $component_id ] ) ) {
						$component_options = $component_options_subset[ $component_id ];
					} elseif ( true === $component_options_subset[ $component_id ] ) {
						$component_options = $component->get_options();
					}
				} else {
					continue;
				}
			// Otherwise build map for all component options (avoid).
			} else {
				$component_options = $component->get_options();
			}

			// Get variations/products map with one query.
			$variation_parents = $component->get_composite()->get_data_store()->get_expanded_component_options( $component_options, 'mapped' );

			$options_map[ $component_id ] = array();

			// No selection.
			$options_map[ $component_id ][ 0 ] = array();

			foreach ( $scenarios as $scenario_id => $scenario ) {
				if ( $scenario->$contains_fn( $component_id, -1 ) ) {
					$options_map[ $component_id ][ 0 ][] = strval( $scenario_id );
				}
			}

			if ( 'conditions' === $map_type ) {
				if ( $component->is_optional() && ! in_array( '0', $options_map[ $component_id ][ 0 ] ) ) {
					$options_map[ $component_id ][ 0 ][] = '0';
				}
			}

			foreach ( $component_options as $component_option_id ) {

				$product_id = absint( $component_option_id );

				if ( in_array( $product_id, $variation_parents ) ) {

					$child_ids = array_keys( $variation_parents, $product_id );

					if ( ! empty( $child_ids ) ) {

						$variations_in_scenarios = array();

						foreach ( $child_ids as $child_id ) {

							$variation_id           = absint( $child_id );
							$variation_in_scenarios = 'conditions' === $map_type ? array( '0' ) : array();

							foreach ( $scenarios as $scenario_id => $scenario ) {

								if ( $scenario->$contains_fn( $component_id, $product_id, $variation_id ) ) {
									$variation_in_scenarios[] = strval( $scenario_id );
								}
							}

							$options_map[ $component_id ][ $variation_id ] = array_values( array_unique( $variation_in_scenarios ) );

							$variations_in_scenarios = array_merge( $variations_in_scenarios, $variation_in_scenarios );
						}

						// When working with 'condition' type maps, if a variation belongs to a scenario, then its parent automatically belongs to it as well.
						if ( 'conditions' === $map_type ) {
							$options_map[ $component_id ][ $product_id ] = array_values( array_unique( $variations_in_scenarios ) );
						}

						$parent_in_scenarios = 'conditions' === $map_type ? array( '0' ) : array();

						foreach ( $scenarios as $scenario_id => $scenario ) {

							$scenario_contains_all_variations     = true;
							$scenario_contains_variation          = false;
							$scenario_contains_all_variations_raw = true;

							foreach ( $child_ids as $child_id ) {

								$variation_id = absint( $child_id );

								if ( in_array( $scenario_id, $options_map[ $component_id ][ $variation_id ] ) ) {
									$scenario_contains_variation = true;
								} else {
									$scenario_contains_all_variations = false;
								}

								if ( ! $scenario->contains_id( $component_id, $variation_id ) ) {
									$scenario_contains_all_variations_raw = false;
								}
							}

							if ( 'conditions' === $map_type ) {

								if ( ( $scenario_contains_variation || $scenario_contains_all_variations_raw ) && 'not-in' === $scenario->get_modifier( $component_id ) ) {
									$parent_in_scenarios[] = strval( $scenario_id );
								}

								if ( $scenario_contains_all_variations && ! $scenario_contains_all_variations_raw ) {
									$parent_in_scenarios[] = strval( $scenario_id );
								}

							} else {

								if ( $scenario_contains_all_variations ) {
									$parent_in_scenarios[] = strval( $scenario_id );
								}
							}
						}

						// When working with 'condition' type maps, we want to know what to do with empty variation selections.
						if ( 'conditions' === $map_type ) {
							$options_map[ $component_id ][ $product_id . '_empty' ] = array_values( array_unique( $parent_in_scenarios ) );
						// When working with 'conditional_options' maps, if all variations are hidden by a scenario, then their parent needs to be hidden as well.
						} else {
							$options_map[ $component_id ][ $product_id ] = array_values( array_unique( $parent_in_scenarios ) );
						}

					}

				} else {

					$product_in_scenarios = 'conditions' === $map_type ? array( '0' ) : array();

					foreach ( $scenarios as $scenario_id => $scenario ) {

						if ( $scenario->$contains_fn( $component_id, $product_id ) ) {
							$product_in_scenarios[] = strval( $scenario_id );
						}
					}

					$options_map[ $component_id ][ $product_id ] = array_values( array_unique( $product_in_scenarios ) );
				}
			}
		}

		return $options_map;
	}

	/**
	 * Return scenario settings indexed by scenario ID:
	 *
	 * - Active scenario action IDs.
	 * - Masked component IDs.
	 * - Hidden component IDs.
	 *
	 * @return array
	 */
	public function get_settings() {

		$scenarios = $this->get_scenarios();
		$settings  = array();

		foreach ( $scenarios as $scenario_id => $scenario ) {

			// Store active action IDs.
			$settings[ 'scenario_actions' ][ $scenario_id ] = $scenario->get_actions();
			// Store unselected components.
			$settings[ 'masked_components' ][ $scenario_id ] = $scenario->get_masked_components();
			// Store 'any' components.
			$settings[ 'any_components' ][ $scenario_id ] = $scenario->get_any_components();
			// Store hidden components.
			$settings[ 'conditional_components' ][ $scenario_id ] = $scenario->get_hidden_components();
		}

		return $settings;
	}

	/**
	 * Get scenarios data array.
	 *
	 * @param  array  $component_options_subset  Subset of the available component options (product IDs only) to use, indexed by component ID. Optional.
	 * @return array
	 */
	public function get_data( $component_options_subset = false ) {
		return array(
			'scenarios'                => $this->get_ids(),
			'scenario_settings'        => $this->get_settings(),
			'action_settings'          => $this->get_action_settings(),
			'scenario_data'            => $this->get_map( $component_options_subset ),
			'conditional_options_data' => $this->get_map( $component_options_subset, false, 'conditional_options' )
		);
	}

	/**
	 * Get scenario action calculation settings.
	 *
	 * @return array
	 */
	public function get_action_settings() {

		$action_settings = array(
			'conditional_components' => array(
				'is_managed'  => 'yes',
				'calculation' => array( 'strict' )
			),
			'conditional_options'    => array(
				'is_managed'  => 'no',
				'calculation' => array( 'strict' )
			),
			'compat_group'           => array(
				'is_managed'  => 'no',
				'calculation' => array( 'preemptive', 'masked', 'skip_invalid' )
			)
		);

		$extended_actions = array();
		$scenarios        = $this->get_scenarios();

		foreach ( $scenarios as $scenario_id => $scenario ) {
			$extended_actions = array_unique( array_merge( $extended_actions, $scenario->get_actions() ) );
		}

		$extended_actions = array_diff( $extended_actions, array_keys( $action_settings ) );

		foreach ( $extended_actions as $extended_action ) {

			/**
			 * Extended scenario action settings. Active scenarios for extended scenario actions are:
			 *
			 * - Automatically re-calculated by the Scenarios Manager JS class for all Components.
			 * - Strictly evaluated (all Scenario matching conditions for non-masked Components must be true).
			 *
			 * @since  5.0.0
			 *
			 * @param  array   $settings
			 * @param  string  $action
			 */
			$action_settings[ $extended_action ] = apply_filters( 'woocommerce_composite_scenario_action_settings', array(
				'is_managed'  => 'yes',
				'calculation' => array( 'strict' )
			), $extended_action );
		}

		return $action_settings;
	}

	/**
	 * Indicates whether any active scenarios exist. Active = scenarios with active actions, which introduce conditional logic.
	 *
	 * @return boolean
	 */
	public function exist() {

		$cg_ids     = $this->get_ids_by_action( 'compat_group' );
		$cc_ids     = $this->get_ids_by_action( 'conditional_components' );
		$active_ids = array_unique( array_merge( $cg_ids, $cc_ids ) );

		// At least 1 scenario will always exist. If it's the '0' ID scenario, then no conditional logic exists whatsoever.
		if ( 1 === sizeof( $active_ids ) && 1 === sizeof( $cg_ids ) && '0' === current( $cg_ids ) ) {
			$active_found = false;
		} else {
			$active_found = true;
		}

		return $active_found;
	}

	/**
	 * Validation complexity index (worst case).
	 * Doubling the components increases the complexity by a factor of ~1.5.
	 * A complexity index of 10 should be below the PHP timeout.
	 *
	 * @param  int  $permutations      Number of permutations to test.
	 * @param  int  $components_count  Number of components in passed configuration.
	 * @return float
	 */
	public function get_validation_complexity_index( $permutations = 1, $components_count = '' ) {
		$components_count = '' !== $components_count ? absint( $components_count ) : ( isset( $this->components ) ? sizeof( $this->components ) : 1 );
		return $this->complexity * $permutations * pow( 1.50, log( $components_count ) / log( 2 ) ) / 150000;
	}
}
