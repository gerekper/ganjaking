<?php

/**
 * Get locations that can be used to restrict catalog visibility.
 *
 * @return array List of locations that can be used to restrict catalog visibility.
 *             Each location has a title, code, and states (if applicable).
 *             The states are an array of state codes and titles.
 * @uses WC()->countries->get_allowed_country_states()
 *
 * @uses WC()->countries->get_allowed_countries()
 */
function woocommerce_catalog_restrictions_get_locations(): array {
	// List of available locations the user can choose from. This is a list of countries and potentially states.
	$available_locations = [];

	// Get all allowed countries
	$allowed_countries = WC()->countries->get_allowed_countries();

	// Get all allowed states
	$allowed_states = WC()->countries->get_allowed_country_states();

	foreach ( $allowed_countries as $country_code => $country_value ) {
		$available_locations[ $country_code ] = [
			'title'  => $country_value, // 'title' is the country name, e.g. 'United States
			'code'   => $country_code,
			'states' => []
		];

		// If we're using states, add them to the list of available locations
		$states = $allowed_states[ $country_code ] ?? false;
		if ( ! empty( $states ) ) {
			foreach ( $states as $state_code => $state ) {
				$available_locations[ $country_code ]['states'][ $state_code ] = [
					'title' => $state, // 'title' is the state name, e.g. 'California'
					'code'  => $state_code
				];
			}
		}
	}

	/**
	 * Filter the list of available locations the user can choose from. This is a list of countries and potentially states.
	 *
	 * @param array $available_locations An array of available locations.
	 * @param array $allowed_countries An array of allowed countries.
	 * @param array $allowed_states An array of allowed states.
	 * @param bool $use_states Whether to use states.
	 *
	 * @return array
	 *
	 * @example
	 * add_filter( 'woocommerce_catalog_restrictions_available_locations', function( $available_locations, $allowed_countries, $allowed_states, $use_states ) {
	 * $available_locations = [
	 *    'US' => [
	 *        'title' => 'United States',
	 *        'code' => 'US',
	 *        'states' => [
	 *            'CA' => [
	 *                'title' => 'California',
	 *                'code' => 'CA'
	 *            ],
	 *         ]
	 *     ]
	 * ];
	 * return $available_locations;
	 * }, 10, 4 );
	 */
	$use_states          = apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' );

	return apply_filters( 'woocommerce_catalog_restrictions_available_locations', $available_locations, $allowed_countries, $allowed_states, $use_states );
}

function woocommerce_catalog_restrictions_country_input( $value = '', $args = '' ) {
	global $woocommerce;

	$key = 'location';

	$args = wp_parse_args( $args, array(
		'class'       => array(),
		'id'          => 'location',
		'label_class' => array(),
		'label'       => __( 'Select your location', 'wc_catalog_restrictions' )
	) );

	$field = '<label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) . '">' . $args['label'] . '</label>
              <select name="' . $key . '" id="' . $key . '" class="country_to_state ' . implode( ' ', $args['class'] ) . '">';


	$available_locations = woocommerce_catalog_restrictions_get_locations();
	$use_states          = apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' );
	if ( $use_states ) {
		$field .= '<option value="">' . __( 'Select your country / state&hellip;', 'wc_catalog_restrictions' ) . '</option>';

		foreach ( $available_locations as $country_code => $country_data ) {

			$states = $country_data['states'] ?? false;
			if ( ! empty( $states ) ) {
				$field .= '<optgroup label="' . __( $country_data['title'], 'woocommerce' ) . '">';

				foreach ( $states as $skey => $state ) {
					$country_state_code = $country_code . $skey;
					if ( is_array( $value ) ) {
						$selected = in_array( $country_state_code, $value ) ? 'selected="selected"' : '';
					} else {
						$selected = $country_state_code == $value ? 'selected="selected"' : '';
					}

					$field .= '<option value="' . $country_state_code . '" ' . $selected . '>' . __( $state['title'], 'woocommerce' ) . '</option>';
				}
				$field .= '</optgroup>';
			} else {
				if ( is_array( $value ) ) {
					$selected = in_array( $country_code, $value ) ? 'selected="selected"' : '';
				} else {
					$selected = $country_code == $value ? 'selected="selected"' : '';
				}

				$field .= '<option value="' . $country_code . '" ' . $selected . '>' . __( $country_data['title'], 'woocommerce' ) . '</option>';

			}
		}
	} else {
		$field .= '<option value="">' . __( 'Select your country&hellip;', 'wc_catalog_restrictions' ) . '</option>';
		foreach ( $available_locations as $country_code => $country_data ) {
			if ( is_array( $value ) ) {
				$selected = in_array( $country_code, $value ) ? 'selected="selected"' : '';
			} else {
				$selected = $country_code == $value ? 'selected="selected"' : '';
			}

			$field .= '<option value="' . $country_code . '" ' . $selected . '>' . __( $country_data['title'], 'woocommerce' ) . '</option>';
		}
	}

	$field .= '</select>';
	echo $field;
}

function woocommerce_catalog_restrictions_country_multiselect_options( $selected_values = '', $escape = false ) {

	if ( ! is_array( $selected_values ) ) {
		$selected_values = array( $selected_values );
	}

	$use_states          = apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' );
	$available_locations = woocommerce_catalog_restrictions_get_locations();
	if ( $use_states ) {
		foreach ( $available_locations as $ckey => $cval ) {
			$states = $cval['states'] ?? false;
			if ( ! empty( $states ) ) {
				echo '<optgroup label="' . esc_attr( $cval['title'] ) . '">';
				foreach ( $states as $skey => $sval ) {
					$cs_value = $ckey . $skey;
					echo '<option ' . selected( in_array( $cs_value, $selected_values ), true, false ) . ' value="' . $cs_value . '">' . $sval . '</option>';
				}
				echo '</optgroup>';
			} else {
				echo '<option ' . selected( in_array( $ckey, $selected_values ), true, false ) . ' value="' . $ckey . '">' . ( $escape ? esc_js( $cval['title'] ) : $cval['title'] ) . '</option>';
			}
		}

	} else {

		foreach ( $available_locations as $key => $val ) {
			echo '<option ' . selected( in_array( $val['code'], $selected_values ), true, false ) . ' value="' . $key . '">' . ( $escape ? esc_js( $val['title'] ) : $val['title'] ) . '</option>';
		}
	}
}


/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function woocommerce_catalog_restrictions_wp_select( $field ) {

	$field = wp_parse_args(
		$field, array(
			'class'             => 'select short',
			'style'             => '',
			'wrapper_class'     => '',
			'value'             => '',
			'name'              => $field['id'],
			'desc_tip'          => false,
			'custom_attributes' => array(),
		)
	);

	$wrapper_attributes = array(
		'class' => $field['wrapper_class'] . " form-field {$field['id']}_field",
	);

	$label_attributes = array(
		'for' => $field['id'],
	);

	$field_attributes          = (array) $field['custom_attributes'];
	$field_attributes['style'] = $field['style'];
	$field_attributes['id']    = $field['id'];
	$field_attributes['name']  = $field['name'];
	$field_attributes['class'] = $field['class'];

	$tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
	$description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';
	?>
	<p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
		<label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?></label>
		<?php if ( $tooltip ) : ?>
			<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
		<?php endif; ?>
		<br/>
		<select <?php echo wc_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
			<?php
			foreach ( $field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $field['value'] ) . '>' . esc_html( $value ) . '</option>';
			}
			?>
		</select>
		<?php if ( $description ) : ?>
			<span class="description"><?php echo wp_kses_post( $description ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}


