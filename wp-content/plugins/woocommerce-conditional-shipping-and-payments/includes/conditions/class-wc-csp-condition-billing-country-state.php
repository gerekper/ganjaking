<?php
/**
 * WC_CSP_Condition_Billing_Country_State class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Selected Billing Country Condition.
 *
 * @class    WC_CSP_Condition_Billing_Country_State
 * @version  1.15.0
 */
class WC_CSP_Condition_Billing_Country_State extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'billing_country';
		$this->title                          = __( 'Billing Country/State', 'woocommerce-conditional-shipping-and-payments' );
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

		$country = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();

		if ( empty( $data[ 'states' ] ) || empty( $data[ 'states' ][ $country ] ) ) {
			$mismatch_string = __( 'Country', 'woocommerce-conditional-shipping-and-payments' );
		} else {
			$locale          = WC()->countries->get_country_locale();
			$mismatch_string = isset( $locale[ $country ][ 'state' ][ 'label' ] ) ? $locale[ $country ][ 'state' ][ 'label' ] : __( 'State / County', 'woocommerce' );
		}

		$message = sprintf( __( 'choose a different billing %s', 'woocommerce-conditional-shipping-and-payments' ), $mismatch_string );

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) ) {
			return true;
		}

		$billing_country = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_country() : WC()->customer->get_country();
		$billing_state   = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? WC()->customer->get_billing_state() : WC()->customer->get_state();

		$is_matching      = false;
		$showing_excluded = ! empty( $args[ 'restriction_data' ][ 'show_excluded' ] ) && 'yes' === $args[ 'restriction_data' ][ 'show_excluded' ];

		if ( ! $showing_excluded && empty( $billing_state ) ) {
			$billing_state = apply_filters( 'woocommerce_csp_billing_country_condition_default_state', $billing_state, $data, $args );
		}

		$billing_continents        = $this->get_billing_continents();
		$formatted_condition_value = array();

		// Map continents to the countries they contain.
		foreach ( $data[ 'value' ] as $value ) {

			if ( strpos( $value, 'country:' ) !== false ) {
				$value                       = str_replace( 'country:', '', $value );
				$formatted_condition_value[] = $value;

			} elseif ( strpos( $value, 'continent:' ) !== false ) {
				$value                   = str_replace( 'continent:', '', $value );
				$countries_per_continent = $billing_continents[ $value ][ 'countries' ];

				foreach ( $countries_per_continent as $country_in_continent ) {
					$formatted_condition_value[] = $country_in_continent;
				}
			} else {
				// Backwards compatible.
				$formatted_condition_value[] = $value;
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( in_array( $billing_country, $formatted_condition_value ) ) {

				// No states defined in condition?
				if ( empty( $data[ 'states' ] ) || empty( $data[ 'states' ][ $billing_country ] ) ) {

					$is_matching = true;

				} else {

					// No state selected?
					if ( empty( $billing_state ) ) {
						$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_billing_country_condition_match_empty_state', false, $data, $args );
					// State selected is in those defined in condition?
					} elseif ( in_array( $billing_state, $data[ 'states' ][ $billing_country ] ) ) {
						$is_matching = true;
					}
				}
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in') ) ) {

			// Country defined in condition not selected?
			if ( ! in_array( $billing_country, $formatted_condition_value ) ) {

				$is_matching = true;

			// States defined in condition?
			} elseif ( ! empty( $data[ 'states' ] ) && ! empty( $data[ 'states' ][ $billing_country ] ) ) {

				// No state selected?
				if ( empty( $billing_state ) ) {
					$is_matching = ! $showing_excluded && apply_filters( 'woocommerce_csp_billing_country_condition_match_empty_state', true, $data, $args );
				// State selected is not in those defined in condition?
				} elseif ( ! in_array( $billing_state, $data[ 'states' ][ $billing_country ] ) ) {
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
	 * Get billing countries condition content for restriction metaboxes.
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

		// Contains all available Billing Countries.
		$billing_countries_directory = WC()->countries->get_allowed_countries();

		// Contains all available Billing States.
		$billing_states_directory    = WC()->countries->get_allowed_country_states();

		// Contains all available Billing continents.
		$billing_continents_directory = $this->get_billing_continents();

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
				<select class="csp_billing_countries multiselect sw-select2" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][value][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select billing countries&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php
						foreach ( $billing_continents_directory as $continent_code => $continent ) {
							echo '<option value="continent:' . esc_attr( $continent_code ) . '"' . selected( in_array( $continent_code, $selected_continents ), true, false ) . '>' . esc_html( $continent[ 'name' ] ) . '</option>';

							$countries_per_continent = array_intersect( array_keys( $billing_countries_directory ), $continent[ 'countries' ] );

							foreach ( $countries_per_continent as $country_code ) {
								echo '<option value="country:' . esc_attr( $country_code ) . '"' . selected( in_array( $country_code, $selected_countries ), true, false ) . '>' . esc_html( '&nbsp;&nbsp; ' . $billing_countries_directory[ $country_code ] ) . '</option>';
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
				<select class="csp_billing_states multiselect sw-select2" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][states][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Limit restriction to specific states or regions&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
					<?php

						// If condition value includes a continent, then map it to the countries it contains.
						if ( ! empty( $selected_continents ) ) {
							$selected_countries = $this->populate_continent_countries( $selected_continents, $selected_countries, $billing_continents_directory, $billing_countries_directory );
						}

						// Remove duplicate countries. A country can be added both as part of a continent + as standalone.
						$selected_countries = array_unique( $selected_countries );

						// Associate the country codes with the country names.
						if ( ! empty( $selected_countries ) ) {

							foreach ( $selected_countries as $country_code ) {
								if ( ! isset( $billing_countries_directory[ $country_code ] ) ) {
									continue;
								}
								$selected_countries[ $country_code ] = $billing_countries_directory[ $country_code ];
							}
						}

						// Sort countries based on their name -- not code.
						asort( $selected_countries );

						// Print the states for each of the selected countries that has states.
						$this->print_states( $selected_countries, $selected_states, $billing_states_directory );

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
	 * @param  array  $billing_continents_directory
	 * @param  array  $billing_countries_directory
	 *
	 * @return array
	 */
	public function populate_continent_countries( $selected_continents, $selected_countries, $billing_continents_directory, $billing_countries_directory ) {

		foreach ( $selected_continents as $continent_id ) {
			$current_continent       = $billing_continents_directory[ $continent_id ];
			$countries_per_continent = array_intersect( array_keys( $billing_countries_directory ), $current_continent[ 'countries' ] );

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
	 * @param  array  $billing_states_directory
	 */
	public function print_states( $selected_countries, $selected_states, $billing_states_directory ) {

		foreach ( $selected_countries as $country_code => $country_name ) {

			if ( ! isset( $billing_states_directory[ $country_code ] ) || empty( $billing_states_directory[ $country_code ] ) ) {
				continue;
			}

			$states_per_country = $billing_states_directory[ $country_code ];

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

	/**
	 * Get continents that the store sells to in a format that includes billing countries.
	 *
	 * @return array
	 */
	public function get_billing_continents() {
		$continents            = WC()->countries->get_continents();
		$billing_countries     = WC()->countries->get_allowed_countries();
		$billing_country_codes = array_keys( $billing_countries );
		$billing_continents    = array();

		foreach ( $continents as $continent_code => $continent ) {
			if ( count( array_intersect( $continent['countries'], $billing_country_codes ) ) ) {
				$billing_continents[ $continent_code ] = $continent;
			}
		}

		return $billing_continents;
	}
}
