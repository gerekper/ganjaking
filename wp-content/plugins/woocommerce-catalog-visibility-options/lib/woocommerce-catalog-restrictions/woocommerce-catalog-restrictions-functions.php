<?php

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


	if ( apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' ) ) {

		$field .= '<option value="">' . __( 'Select your country / state&hellip;', 'wc_catalog_restrictions' ) . '</option>';

		$all_states = WC()->countries->get_allowed_country_states();
		foreach ( $woocommerce->countries->get_allowed_countries() as $ckey => $cvalue ) {

			$states = isset( $all_states[ $ckey ] ) ? $all_states[ $ckey ] : false;
			if ( $states && ! empty( $states ) ) {
				$field .= '<optgroup label="' . __( $cvalue, 'woocommerce' ) . '">';

				foreach ( $states as $skey => $state ) {
					$cs_value = $ckey . $skey;
					$selected = '';
					if ( is_array( $value ) ) {
						$selected = in_array( $cs_value, $value ) ? 'selected="selected"' : '';
					} else {
						$selected = $cs_value == $value ? 'selected="selected"' : '';
					}

					$field .= '<option value="' . $cs_value . '" ' . $selected . '>' . __( $state, 'woocommerce' ) . '</option>';
				}
				$field .= '</optgroup>';
			} else {
				$selected = '';
				if ( is_array( $value ) ) {
					$selected = in_array( $ckey, $value ) ? 'selected="selected"' : '';
				} else {
					$selected = $ckey == $value ? 'selected="selected"' : '';
				}

				$field .= '<option value="' . $ckey . '" ' . $selected . '>' . __( $cvalue, 'woocommerce' ) . '</option>';

			}

		}
	} else {
		$field .= '<option value="">' . __( 'Select your country&hellip;', 'wc_catalog_restrictions' ) . '</option>';

		foreach ( $woocommerce->countries->get_allowed_countries() as $ckey => $cvalue ) {
			$selected = '';
			if ( is_array( $value ) ) {
				$selected = in_array( $ckey, $value ) ? 'selected="selected"' : '';
			} else {
				$selected = $ckey == $value ? 'selected="selected"' : '';
			}

			$field .= '<option value="' . $ckey . '" ' . $selected . '>' . __( $cvalue, 'woocommerce' ) . '</option>';
		}
	}


	$field .= '</select>';

	echo $field;
}

function woocommerce_catalog_restrictions_country_multiselect_options( $selected_values = '', $escape = false ) {

	if ( ! is_array( $selected_values ) ) {
		$selected_values = array( $selected_values );
	}

	if ( apply_filters( 'woocommerce_catalog_restrictions_locations_include_states', get_option( '_wc_restrictions_locations_type' ) == 'states' ) ) {
		$all_states = WC()->countries->get_allowed_country_states();
		$countries  = WC()->countries->get_allowed_countries();
		foreach ( $countries as $ckey => $cval ) {
			$states = isset( $all_states[ $ckey ] ) ? $all_states[ $ckey ] : false;
			if ( $states && ! empty( $states ) ) {
				echo '<optgroup label="' . esc_attr( $cval ) . '">';
				foreach ( $states as $skey => $sval ) {
					$cs_value = $ckey . $skey;
					echo '<option ' . selected( in_array( $cs_value, $selected_values ), true, false ) . 'value="' . $cs_value . '">' . $sval . '</option>';
				}
				echo '</optgroup>';
			} else {
				echo '<option ' . selected( in_array( $ckey, $selected_values ), true, false ) . ' value="' . $ckey . '">' . ( $escape ? esc_js( $cval ) : $cval ) . '</option>';
			}
		}

	} else {

		$countries = WC()->countries->get_allowed_countries();
		foreach ( $countries as $key => $val ) {
			echo '<option ' . selected( in_array( $key, $selected_values ), true, false ) . ' value="' . $key . '">' . ( $escape ? esc_js( $val ) : $val ) . '</option>';
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
        <br />
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


