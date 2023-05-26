<?php
/**
 * WC_CSP_Condition_Shipping_Country_State class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Shipping Country/State Condition.
 *
 * @class    WC_CSP_Condition_Shipping_Country_State
 * @version  1.15.0
 */
class WC_CSP_Condition_Shipping_Country_State extends WC_CSP_Package_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'shipping_country';
		$this->title                          = __( 'Shipping Country/State', 'woocommerce-conditional-shipping-and-payments' );
		$this->priority                       = 20;
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return false;
		}

		$message       = false;
		$package_index = false;
		$package_count = $this->get_package_count( $args );
		$country       = WC()->customer->get_shipping_country();

		if ( isset( $args[ 'package' ] ) ) {

			$package_index = $this->get_package_index( $args );
			$country       = $args[ 'package' ][ 'destination' ][ 'country' ];

		} else {

			$shipping_packages = $this->get_packages();

			if ( ! empty( $shipping_packages ) ) {

				$shipping_package_index = 0;

				foreach ( $shipping_packages as $shipping_package ) {

					if ( $this->check_condition( $data, array_merge( array( 'package' => $shipping_package ), $args ) ) ) {

						$shipping_package_index++;

						// Violation found?
						if ( false !== $package_index ) {

							if ( ! is_array( $package_index ) ) {
								$package_index = array( $package_index );
							}

							$package_index[] = $shipping_package_index;

							continue;
						}

						$package_index = $shipping_package_index;
						$country       = $shipping_package[ 'destination' ][ 'country' ];
					}
				}
			}
		}

		if ( empty( $data[ 'states' ][ $country ] ) ) {
			$mismatch_string = __( 'Country', 'woocommerce-conditional-shipping-and-payments' );
		} else {
			$locale          = WC()->countries->get_country_locale();
			$mismatch_string = isset( $locale[ $country ][ 'state' ][ 'label' ] ) ? $locale[ $country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
		}

		if ( 1 === $package_count || false === $package_index ) {

			$message = sprintf( __( 'choose a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), $mismatch_string );

		} else {

			if ( is_array( $package_index ) ) {
				$packages = $this->merge_titles( $package_index, array( 'prefix' => '#', 'quotes' => false ) );
				$message  = sprintf( __( 'choose a different shipping destination for shipping packages %2$s', 'woocommerce-conditional-shipping-and-payments' ), $mismatch_string, $packages );
			} else {
				$message = sprintf( __( 'choose a different shipping %s', 'woocommerce-conditional-shipping-and-payments' ), $mismatch_string );
			}
		}

		return $message;
	}

	/**
	 * Evaluate if a condition field is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		if ( ! empty( $args[ 'order' ] ) ) {

			$order = $args[ 'order' ];

			$country = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_shipping_country() : $order->shipping_country;
			$state   = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? $order->get_shipping_state() : $order->shipping_state;

		} else {

			$country = isset( $args[ 'package' ] ) ? $args[ 'package' ][ 'destination' ][ 'country' ] : WC()->customer->get_shipping_country();
			$state   = isset( $args[ 'package' ] ) ? $args[ 'package' ][ 'destination' ][ 'state' ] : WC()->customer->get_shipping_state();
		}

		$is_matching      = false;
		$showing_excluded = ! empty( $args[ 'restriction_data' ][ 'show_excluded' ] ) && 'yes' === $args[ 'restriction_data' ][ 'show_excluded' ];

		if ( ! $showing_excluded && empty( $state ) ) {
			$state = apply_filters( 'woocommerce_csp_shipping_country_condition_default_state', $state, $data, $args );
		}

		$shipping_continents       = WC()->countries->get_shipping_continents();
		$formatted_condition_value = array();

		// Map continents to the countries they contain.
		foreach ( $data[ 'value' ] as $value ) {

			if ( strpos( $value, 'country:' ) !== false ) {
				$value                       = str_replace( 'country:', '', $value );
				$formatted_condition_value[] = $value;

			} elseif ( strpos( $value, 'continent:' ) !== false ) {
				$value                   = str_replace( 'continent:', '', $value );
				$countries_per_continent = $shipping_continents[ $value ][ 'countries' ];

				foreach ( $countries_per_continent as $country_in_continent ) {
					$formatted_condition_value[] = $country_in_continent;
				}
			} else {
				// Backwards compatible.
				$formatted_condition_value[] = $value;
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( in_array( $country, $formatted_condition_value ) ) {

				// No states defined in condition?
				if ( empty( $data[ 'states' ][ $country ] ) ) {

					$is_matching = true;

				} else {

					// No state selected?
					if ( empty( $state ) ) {
						$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_shipping_country_condition_match_empty_state', false, $data, $args );
					// State selected is in those defined in condition?
					} elseif ( in_array( $state, $data[ 'states' ][ $country ] ) ) {
						$is_matching = true;
					}
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

			// Country defined in condition not selected?
			if ( ! in_array( $country, $formatted_condition_value ) ) {

				$is_matching = true;

			// States defined in condition?
			} elseif ( ! empty( $data[ 'states' ][ $country ] ) ) {

				// No state selected?
				if ( empty( $state ) ) {
					$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_shipping_country_condition_match_empty_state', true, $data, $args );
				// State selected is not in those defined in condition?
				} elseif ( ! in_array( $state, $data[ 'states' ][ $country ] ) ) {
					$is_matching = true;
				}
			}
		}

		return $is_matching;
	}

	/**
	 * Validate, process and return condition field data.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( ! empty( $posted_condition_data[ 'value' ] ) ) {

			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'value' ]        = array_map( 'stripslashes', $posted_condition_data[ 'value' ] );
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

			if ( ! empty( $posted_condition_data[ 'states' ] ) ) {

				$processed_condition_data[ 'states' ] = array();

				$states_per_country = array_map( 'stripslashes', $posted_condition_data[ 'states' ] );

				// $current_state_key has this format "GR:I".
				foreach ( $states_per_country as $country_state_key ) {
					$country_state_key = explode( ':', $country_state_key );
					$country_key       = current( $country_state_key );
					$state_key         = end( $country_state_key );

					$processed_condition_data[ 'states' ][ $country_key ][] = $state_key;
				}
			}

			return $processed_condition_data;
		}

		return false;
	}

	/**
	 * Get shipping countries condition content for product-level restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return void
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		// Contains all selected continents.
		$selected_continents = array();

		// Contains all selected countries.
		$selected_countries  = array();

		// Contains all selected states.
		$selected_states     = array();

		// Contains the selected modifier.
		$selected_modifier   = '';

		// Parse condition data and separate continents and countries.
		if ( ! empty( $condition_data[ 'value' ] ) ) {
			$data = $condition_data[ 'value' ];

			foreach ( $data as $value ) {
				if ( false !== strpos( $value, 'country:' ) ) {
					$value                = str_replace( 'country:', '', $value );
					$selected_countries[] = $value;
				} elseif ( false !== strpos( $value, 'continent:' ) ) {
					$value                 = str_replace( 'continent:', '', $value );
					$selected_continents[] = $value;
				} else {
					// Backwards compatible.
					$selected_countries[] = $value;
				}
			}
		}

		if ( ! empty( $condition_data[ 'states' ] ) ) {
			$selected_states = $condition_data[ 'states' ];
		}

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$selected_modifier = $condition_data[ 'modifier' ];
		}

		// Contains all available Shipping Countries.
		$shipping_countries_directory  = WC()->countries->get_shipping_countries();

		// Contains all available Shipping States.
		$shipping_states_directory     = WC()->countries->get_shipping_country_states();

		// Contains all available Shipping Continents.
		$shipping_continents_directory = WC()->countries->get_shipping_continents();

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]">
						<option value="in" <?php selected( $selected_modifier, 'in', true ); ?>><?php esc_html_e( 'is', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $selected_modifier, 'not-in', true ); ?>><?php esc_html_e( 'is not', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value select-field">
				<select class="csp_shipping_countries multiselect sw-select2" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select shipping countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $shipping_continents_directory as $continent_code => $continent ) {
							echo '<option value="continent:' . esc_attr( $continent_code ) . '"' . selected( in_array( $continent_code, $selected_continents ), true, false ) . '>' . esc_html( $continent[ 'name' ] ) . '</option>';

							$countries_per_continent = array_intersect( array_keys( $shipping_countries_directory ), $continent[ 'countries' ] );

							foreach ( $countries_per_continent as $country_code ) {
								echo '<option value="country:' . esc_attr( $country_code ) . '"' . selected( in_array( $country_code, $selected_countries ), true, false ) . '>' . esc_html( '&nbsp;&nbsp; ' . $shipping_countries_directory[ $country_code ] ) . '</option>';
							}
						}
					?>
				</select>
				<div class="condition_form_row">
					<a class="wccsp_select_all button" href="#"><?php esc_html_e( 'All', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php esc_html_e( 'None', 'woocommerce' ); ?></a>
				</div>
			</div>
		</div>

		<div class="condition_row_inner">
			<div class="condition_modifier">
			</div>
			<div class="condition_value excluded_states select-field">
				<select class="csp_shipping_states multiselect sw-select2" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][states][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Limit restriction to specific states or regions&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php

						// If condition value includes a continent, then map it to the countries it contains.
						if ( ! empty( $selected_continents ) ) {
							$selected_countries = $this->populate_continent_countries( $selected_continents, $selected_countries, $shipping_continents_directory, $shipping_countries_directory );
						}

						// Remove duplicate countries. A country can be added both as part of a continent + as standalone.
						$selected_countries = array_unique( $selected_countries );

						// Associate the country codes with the country names.
						if ( ! empty( $selected_countries ) ) {

							foreach ( $selected_countries as $country_code ) {
								if ( ! isset( $shipping_countries_directory[ $country_code ] ) ) {
									continue;
								}
								$selected_countries[ $country_code ] = $shipping_countries_directory[ $country_code ];
							}
						}

						// Sort countries based on their name -- not code.
						asort( $selected_countries );

						// Print the states for each of the selected countries that has states.
						$this->print_states( $selected_countries, $selected_states, $shipping_states_directory );

					?>
				</select>
				<div class="condition_form_row">
					<a class="wccsp_select_all button" href="#"><?php esc_html_e( 'All', 'woocommerce' ); ?></a>
					<a class="wccsp_select_none button" href="#"><?php esc_html_e( 'None', 'woocommerce' ); ?></a>
				</div>
			</div>
		</div><?php
	}

	/*
	*
	* Helpers.
	*
	*/

	/**
	 * Update countries array to include the countries of each selected continent.
	 *
	 * @param  array  $selected_continents
	 * @param  array  $selected_countries
	 * @param  array  $shipping_continents_directory
	 * @param  array  $shipping_countries_directory
	 *
	 * @return array
	 */
	public function populate_continent_countries( $selected_continents, $selected_countries, $shipping_continents_directory, $shipping_countries_directory ) {

		foreach ( $selected_continents as $continent_id ) {
			$current_continent       = $shipping_continents_directory[ $continent_id ];
			$countries_per_continent = array_intersect( array_keys( $shipping_countries_directory ), $current_continent[ 'countries' ] );

			foreach ( $countries_per_continent as $countries_list ) {
				$selected_countries[] = $countries_list;
			}
		}

		return $selected_countries;
	}

	/**
	 * Prints states per country.
	 *
	 * @param  array  $selected_countries
	 * @param  array  $selected_states
	 * @param  array  $shipping_states_directory
	 */
	public function print_states( $selected_countries, $selected_states, $shipping_states_directory ) {

		foreach ( $selected_countries as $country_code => $country_name ) {

			if ( ! isset( $shipping_states_directory[ $country_code ] ) || empty( $shipping_states_directory[ $country_code ] ) ) {
				continue;
			}

			$states_per_country = $shipping_states_directory[ $country_code ];

			if ( empty( $states_per_country ) ) {
				continue;
			}

			echo '<optgroup label="' . esc_attr( $country_name ) . '">';
			foreach ( $states_per_country as $state_key => $state_value ) {
				echo '<option value="' . esc_attr( $country_code . ':' . $state_key ) . '"';
				if ( ! empty( $selected_states[ $country_code ] ) && in_array( $state_key, $selected_states[ $country_code ] ) ) {
					echo ' selected';
				}
				echo '>' . esc_html( $country_name . ' &ndash; ' . $state_value ) . '</option>';
			}
			echo '</optgroup>';
		}
	}
}
