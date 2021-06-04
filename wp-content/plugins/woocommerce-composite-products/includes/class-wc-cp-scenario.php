<?php
/**
 * WC_CP_Scenario class
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
 * Scenario object.
 *
 * @class    WC_CP_Scenario
 * @version  8.0.0
 */
class WC_CP_Scenario {

	/**
	 * Scenario ID.
	 * @var string
	 */
	private $id;

	/**
	 * Scenario name.
	 * @var string
	 */
	private $name;

	/**
	 * Scenario description.
	 * @var string
	 */
	private $description;

	/**
	 * Matching conditions.
	 * @var array
	 */
	private $configuration_data;

	/**
	 * Matching conditions.
	 * @var array
	 */
	private $actions_data;

	/**
	 * Constructor.
	 *
	 * @param  array  $scenario_data
	 */
	public function __construct( $scenario_data ) {

		$this->id = isset( $scenario_data[ 'id' ] ) ? strval( $scenario_data[ 'id' ] ) : '0';

		$configuration = array();
		$actions       = array();

		if ( ! empty( $scenario_data[ 'component_data' ] ) && is_array( $scenario_data[ 'component_data' ] ) ) {
			foreach ( $scenario_data[ 'component_data' ] as $component_id => $component_data ) {

				$modifier = 'in';

				if ( isset( $scenario_data[ 'modifier' ][ $component_id ] ) && 'not-in' === $scenario_data[ 'modifier' ][ $component_id ] ) {
					$modifier = 'not-in';
				} elseif ( isset( $scenario_data[ 'modifier' ][ $component_id ] ) && 'masked' === $scenario_data[ 'modifier' ][ $component_id ] ) {
					$modifier = 'masked';
				} elseif ( isset( $scenario_data[ 'exclude' ][ $component_id ] ) && 'yes' === $scenario_data[ 'exclude' ][ $component_id ] ) {
					$modifier = 'not-in';
				}

				$component_data = 'masked' === $modifier ? array( 0 ) : array_map( 'intval', $component_data );

				$configuration[ strval( $component_id ) ] = array(
					'component_options' => $component_data,
					'options_modifier'  => $modifier
				);
			}
		}

		if ( ! empty( $scenario_data[ 'scenario_actions' ] ) && is_array( $scenario_data[ 'scenario_actions' ] ) ) {
			foreach ( $scenario_data[ 'scenario_actions' ] as $action_id => $action_data ) {

				$parsed_data = array_merge( array_diff_key( $action_data, array( 'is_active' => 1 ) ), array( 'is_active' => isset( $action_data[ 'is_active' ] ) && 'yes' === $action_data[ 'is_active' ] ) );

				if ( 'conditional_components' === $action_id ) {
					if ( empty( $action_data[ 'hidden_components' ] ) ) {
						$parsed_data[ 'is_active' ] = false;
					}
				}

				if ( 'conditional_options' === $action_id ) {

					$action_configuration = array();

					if ( ! empty( $action_data[ 'component_data' ] ) && is_array( $action_data[ 'component_data' ] ) ) {
						foreach ( $action_data[ 'component_data' ] as $component_id => $component_data ) {

							$modifier = 'masked';

							if ( isset( $action_data[ 'modifier' ][ $component_id ] ) && 'not-in' === $action_data[ 'modifier' ][ $component_id ] ) {
								$modifier = 'not-in';
							} elseif ( isset( $action_data[ 'modifier' ][ $component_id ] ) && 'in' === $action_data[ 'modifier' ][ $component_id ] ) {
								$modifier = 'in';
							}

							if ( 'masked' === $modifier ) {
								continue;
							}

							$component_data = array_map( 'intval', $component_data );

							$action_configuration[ strval( $component_id ) ] = array(
								'component_options' => $component_data,
								'options_modifier'  => $modifier
							);
						}
					}

					$parsed_data[ 'configuration' ] = $action_configuration;
				}

				$actions[ strval( $action_id ) ] = $parsed_data;
			}

		} else {
			$actions[ 'compat_group' ] = array(
				'is_active' => true
			);
		}

		$this->configuration_data = $configuration;
		$this->actions_data       = $actions;
		$this->name               = isset( $scenario_data[ 'title' ] ) ? $scenario_data[ 'title' ] : __( 'Untitled Scenario', 'woocommerce-composite-products' );
		$this->description        = isset( $scenario_data[ 'description' ] ) ? $scenario_data[ 'description' ] : '';
	}

	/**
	 * Scenario ID getter.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Scenario name getter.
	 *
	 * @return string
	 */
	public function get_name() {
		return esc_html( $this->name );
	}

	/**
	 * Scenario description getter.
	 *
	 * @return string
	 */
	public function get_description() {
		return wp_kses_post( $this->description );
	}

	/**
	 * Returns true if the scenario contains a product/variation ID selection from a specific component. Uses 'WC_CP_Scenario::contains_id' but also takes exclusion rules into account.
	 * When checking a variation, it also checks the parent.
	 *
	 * @param  string  $component_id
	 * @param  int     $product_id
	 * @param  int     $variation_id
	 * @return boolean
	 */
	public function contains_component_option( $component_id, $product_id, $variation_id = 0 ) {

		$component_id = strval( $component_id );
		$modifier     = isset( $this->configuration_data[ $component_id ][ 'options_modifier' ] ) ? $this->configuration_data[ $component_id ][ 'options_modifier' ] : 'in';

		// Any product or variation...
		if ( '0' === $this->id || $this->contains_id( $component_id, 0 ) ) {
			return true;
		}

		$exclude  = 'not-in' === $modifier;
		$contains = false;

		if ( $variation_id > 0 && $product_id > 0 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $variation_id ) || $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $variation_id ) && ! $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			}
		} elseif ( $product_id > 0 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			}
		} elseif ( $product_id === -1 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $product_id ) ) {
					$contains = true;
				}
			}
		}

		return $contains;
	}

	/**
	 * Returns true if the scenario hides a product/variation ID selection in a specific component. Uses 'WC_CP_Scenario::contains_id' but also takes exclusion rules into account.
	 * When checking a variation, it also checks the parent.
	 *
	 * @param  string  $component_id
	 * @param  int     $product_id
	 * @param  int     $variation_id
	 * @return boolean
	 */
	public function hides_component_option( $component_id, $product_id, $variation_id = 0 ) {

		if ( ! $this->has_action( 'conditional_options' ) ) {
			return false;
		}

		$configuration = $this->actions_data[ 'conditional_options' ][ 'configuration' ];

		if ( empty( $configuration[ $component_id ] ) ) {
			return false;
		}

		$component_id = strval( $component_id );
		$modifier     = isset( $configuration[ $component_id ][ 'options_modifier' ] ) ? $configuration[ $component_id ][ 'options_modifier' ] : 'masked';

		if ( 'masked' === $modifier ) {
			return false;
		}

		$exclude  = 'not-in' === $modifier;
		$contains = false;

		if ( $variation_id > 0 && $product_id > 0 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $variation_id, $configuration ) || $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $variation_id, $configuration ) && ! $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			}
		} elseif ( $product_id > 0 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			}
		} elseif ( $product_id === -1 ) {
			if ( false === $exclude ) {
				if ( $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			} else {
				if ( ! $this->contains_id( $component_id, $product_id, $configuration ) ) {
					$contains = true;
				}
			}
		}

		return $contains;
	}

	/**
	 * Get components masked in scenario.
	 *
	 * @return array
	 */
	public function get_masked_components() {

		$masked = array();

		if ( ! empty( $this->configuration_data ) ) {
			foreach ( $this->configuration_data as $component_id => $data ) {
				if ( isset( $data[ 'options_modifier' ] ) && 'masked' === $data[ 'options_modifier' ] ) {
					$masked[] = $component_id;
				}
			}
		}

		return array_map( 'strval', $masked );
	}

	/**
	 * Get components with all options in scenario.
	 *
	 * @return array
	 */
	public function get_any_components() {

		$any = array();

		if ( ! empty( $this->configuration_data ) ) {
			foreach ( $this->configuration_data as $component_id => $data ) {
				if ( isset( $data[ 'options_modifier' ] ) && 'in' === $data[ 'options_modifier' ] && $this->contains_id( $component_id, 0 ) ) {
					$any[] = $component_id;
				}
			}
		}

		return array_map( 'strval', $any );
	}

	/**
	 * Indicates whether a component is masked in this scenario.
	 *
	 * @param  string  $component_id
	 * @return boolean
	 */
	public function masks_component( $component_id ) {

		$is_masked    = false;
		$component_id = strval( $component_id );

		if ( ! empty( $this->configuration_data[ $component_id ] ) ) {
			if ( isset( $this->configuration_data[ $component_id ][ 'options_modifier' ] ) && 'masked' === $this->configuration_data[ $component_id ][ 'options_modifier' ] ) {
				$is_masked = true;
			}
		}

		return $is_masked;
	}

	/**
	 * Get components hidden by the scenario.
	 *
	 * @return array
	 */
	public function get_hidden_components() {

		$hidden = array();

		if ( $this->has_action( 'conditional_components' ) ) {
			if ( ! empty( $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] ) && is_array( $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] ) ) {
				$hidden = array_values( $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] );
			}
		}

		return array_map( 'strval', $hidden );
	}

	/**
	 * Indicates whether a components is hidden by the scenario.
	 *
	 * @param  string  $component_id
	 * @return boolean
	 */
	public function hides_component( $component_id ) {

		$is_hidden = array();

		if ( $this->has_action( 'conditional_components' ) ) {
			if ( ! empty( $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] ) && is_array( $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] ) ) {
				$is_hidden = in_array( strval( $component_id ), $this->actions_data[ 'conditional_components' ][ 'hidden_components' ] );
			}
		}

		return $is_hidden;
	}

	/**
	 * Returns active action IDs.
	 *
	 * @return array
	 */
	public function get_actions() {

		$action_ids = array();

		if ( ! empty( $this->actions_data ) ) {
			foreach ( $this->actions_data as $action_id => $data ) {
				if ( $this->has_action( $action_id ) ) {
					$action_ids[] = $action_id;
				}
			}
		}

		return $action_ids;
	}

	/**
	 * Checks if an action is defined and active in the scenario by ID.
	 *
	 * @param  string  $action_id
	 * @return boolean
	 */
	public function has_action( $action_id ) {

		$action_id = strval( $action_id );

		return ! empty( $this->actions_data[ $action_id ] ) && isset( $this->actions_data[ $action_id ][ 'is_active' ] ) && $this->actions_data[ $action_id ][ 'is_active' ];
	}

	/**
	 * Action data getter.
	 *
	 * @param  string  $action_id
	 * @return array
	 */
	public function get_action_data( $action_id ) {

		$action_id   = strval( $action_id );
		$action_data = null;

		if ( ! empty( $this->actions_data[ $action_id ] ) ) {
			$action_data = $this->actions_data[ $action_id ];
		}

		return $action_data;
	}

	/**
	 * Get raw component option IDs definition.
	 *
	 * @since  3.13.0
	 *
	 * @param  string  $component_id
	 * @return array
	 */
	public function get_ids( $component_id ) {
		return ! $this->masks_component( $component_id ) && ! empty( $this->configuration_data[ $component_id ][ 'component_options' ] ) && is_array( $this->configuration_data[ $component_id ][ 'component_options' ] ) ? $this->configuration_data[ $component_id ][ 'component_options' ] : array();
	}

	/**
	 * Get modifier.
	 *
	 * @since  8.1.0
	 *
	 * @param  string  $component_id
	 * @return array
	 */
	public function get_modifier( $component_id ) {
		return ! empty( $this->configuration_data[ $component_id ][ 'options_modifier' ] ) ? $this->configuration_data[ $component_id ][ 'options_modifier' ] : null;
	}

	/**
	 * Returns true if a component option ID is defined in the scenario. Also @see 'WC_CP_Scenario::contains_product'.
	 *
	 * @param  string       $component_id
	 * @param  int          $id
	 * @param  array|false  $data
	 * @return boolean
	 */
	public function contains_id( $component_id, $id, $configuration = false ) {

		$configuration = false === $configuration ? $this->configuration_data : $configuration;

		if ( ! empty( $configuration[ $component_id ][ 'component_options' ] ) && is_array( $configuration[ $component_id ][ 'component_options' ] ) && in_array( $id, $configuration[ $component_id ][ 'component_options' ] ) ) {
			return true;
		} else {
			return false;
		}
	}
}
