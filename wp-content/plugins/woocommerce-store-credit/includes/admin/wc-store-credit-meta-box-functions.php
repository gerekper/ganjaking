<?php
/**
 * Meta box functions.
 *
 * @package WC_Store_Credit/Admin/Functions
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Outputs a meta box field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_field( $field ) {
	if ( isset( $field['class'] ) && is_array( $field['class'] ) ) {
		$field['class'] = join( ' ', $field['class'] );
	}

	$callback = wc_store_credit_meta_box_field_callback( $field );

	if ( is_callable( $callback ) ) {
		call_user_func( $callback, $field );
	}
}

/**
 * Gets the callback used to output the meta box field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 * @return mixed
 */
function wc_store_credit_meta_box_field_callback( $field ) {
	$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );

	switch ( $type ) {
		case 'text':
		case 'hidden':
		case 'textarea':
			$callback = 'woocommerce_wp_' . $type . '_input';
			break;
		case 'select':
		case 'radio':
		case 'checkbox':
			$callback = 'woocommerce_wp_' . $type;
			break;
		default:
			$callback = 'wc_store_credit_meta_box_' . $type . '_field';
			break;
	}

	/**
	 * Filters the callback used to output the meta box field.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $callback The output callback.
	 * @param array $field    The field data.
	 */
	return apply_filters( 'wc_store_credit_meta_box_field_callback', $callback, $field );
}

/**
 * Outputs a multi-select field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_multiselect_field( $field ) {
	$field = wp_parse_args(
		$field,
		array(
			'options'           => array(),
			'custom_attributes' => array(),
		)
	);

	$field['name']  = ( isset( $field['name'] ) ? $field['name'] : $field['id'] ) . '[]';
	$field['class'] = ( ! empty( $field['class'] ) ? $field['class'] : 'select short' ) . ' multiselect';

	$field['custom_attributes']['multiple'] = 'multiple';

	woocommerce_wp_select( $field );
}

/**
 * Outputs a 'product_search' field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_product_search_field( $field ) {
	$field = wp_parse_args(
		$field,
		array(
			'class'             => 'wc-product-search',
			'style'             => 'width: 50%;',
			'options'           => array(),
			'custom_attributes' => array(
				'data-placeholder' => _x( 'Search for a product&hellip;', 'setting placeholder', 'woocommerce-store-credit' ),
				'data-allow_clear' => true,
			),
		)
	);

	$value       = ( isset( $field['value'] ) ? $field['value'] : array() );
	$product_ids = ( is_array( $value ) ? $value : array( $value ) );

	$field['options'] = array_combine( $product_ids, array_map( 'wc_store_credit_get_product_choice_label', $product_ids ) );

	$multiple   = ( isset( $field['multiple'] ) && $field['multiple'] );
	$variations = ( isset( $field['variations'] ) && $field['variations'] );

	// Exclude these parameters from the merge.
	unset( $field['multiple'], $field['variations'] );

	$field['custom_attributes']['data-multiple'] = ( $multiple ? 'true' : 'false' );
	$field['custom_attributes']['data-action']   = 'woocommerce_json_search_products' . ( $variations ? '_and_variations' : '' );

	if ( $multiple ) {
		wc_store_credit_meta_box_multiselect_field( $field );
	} else {
		woocommerce_wp_select( $field );
	}
}

/**
 * Outputs a 'product_categories' field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_product_categories_field( $field ) {
	$field = wp_parse_args(
		$field,
		array(
			'type'              => 'multiselect',
			'class'             => 'wc-enhanced-select',
			'style'             => 'width: 50%;',
			'options'           => wc_store_credit_get_product_categories_choices( true ),
			'custom_attributes' => array(
				'data-placeholder' => _x( 'Select product categories', 'setting placeholder', 'woocommerce-store-credit' ),
			),
		)
	);

	wc_store_credit_meta_box_multiselect_field( $field );
}

/**
 * Outputs a 'time_period' field.
 *
 * @since 3.2.0
 *
 * @global string  $thepostid The current post ID.
 * @global WP_Post $post      The current post.
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_time_period_field( $field ) {
	global $thepostid, $post;

	$thepostid = ( empty( $thepostid ) ? $post->ID : $thepostid );

	$field = wp_parse_args(
		$field,
		array(
			'desc_tip'      => false,
			'wrapper_class' => 'time-period',
			'placeholder'   => '',
			'value'         => get_post_meta( $thepostid, $field['id'], true ),
			'name'          => ( isset( $field['name'] ) ? $field['name'] : $field['id'] ),
			'options'       => wc_store_credit_get_time_period_choices( 'plural' ),
		)
	);

	$value = wp_parse_args(
		is_array( $field['value'] ) ? $field['value'] : array(),
		array(
			'number' => '',
			'period' => '',
		)
	);

	$wrapper_class = "form-field {$field['id']}_field {$field['wrapper_class']}";
	$tooltip       = ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '' );
	$description   = ( ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '' );
	?>
	<p class="<?php echo esc_attr( $wrapper_class ); ?>">
		<label for="<?php echo esc_attr( $field['id'] ); ?>_number"><?php echo wp_kses_post( $field['label'] ); ?></label>

		<?php if ( $tooltip ) : ?>
			<?php echo wc_help_tip( $tooltip ); ?>
		<?php endif; ?>

		<span class="fields-container">
			<?php
			printf(
				'<input type="text" id="%1$s_number" name="%2$s[number]" placeholder="%3$s" value="%4$s" />',
				esc_attr( $field['id'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $value['number'] )
			);
			?>

			<label for="<?php echo esc_attr( $field['id'] ); ?>_period" class="screen-reader-text"><?php echo wp_kses_post( $field['period_label'] ); ?></label>
			<select id="<?php echo esc_attr( $field['id'] ); ?>_period" name="<?php echo esc_attr( $field['name'] ); ?>[period]">
			<?php
			foreach ( $field['options'] as $key => $label ) :
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $key ),
					selected( $key, $value['period'], false ),
					esc_html( $label )
				);
			endforeach;
			?>
			</select>
		</span>

		<?php if ( $description ) : ?>
			<span class="description"><?php echo wp_kses_post( $description ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Outputs an 'options_group' field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 */
function wc_store_credit_meta_box_options_group_field( $field ) {
	$field = wp_parse_args(
		$field,
		array(
			'class'       => 'options_group',
			'style'       => '',
			'title'       => '',
			'title_class' => 'options_group_heading',
		)
	);

	$wrapper_attributes = array(
		'class' => $field['class'],
		'style' => $field['style'],
	);

	echo '</div>';

	if ( ! empty( $field['title'] ) ) {
		echo '<h3 class="' . esc_attr( $field['title_class'] ) . '">' . esc_html( $field['title'] ) . '</h3>';
	}

	echo '<div ' . wc_implode_html_attributes( $wrapper_attributes ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Sanitizes a meta box field.
 *
 * @since 3.2.0
 *
 * @param array $field The field data.
 * @param mixed $value Optional. The field value. Default null.
 * @return mixed
 */
function wc_store_credit_sanitize_meta_box_field( $field, $value = null ) {
	// phpcs:disable WordPress.Security.NonceVerification
	if ( is_null( $value ) ) {
		if ( 'checkbox' === $field['type'] ) {
			$value = ( isset( $_POST[ $field['id'] ] ) );
		} else {
			$value = ( isset( $_POST[ $field['id'] ] ) ? wc_clean( wp_unslash( $_POST[ $field['id'] ] ) ) : '' );
		}
	}

	switch ( $field['type'] ) {
		case 'checkbox':
			$value = wc_bool_to_string( $value );
			break;
		case 'product_search':
			if ( isset( $field['multiple'] ) && $field['multiple'] ) {
				if ( ! is_array( $value ) ) {
					$value = array();
				}

				$value = array_filter( array_map( 'intval', $value ) );
			} elseif ( $value ) {
				$value = intval( $value );
			}
			break;
		case 'product_categories':
			if ( ! is_array( $value ) ) {
				$value = array();
			}

			$value = array_filter( array_map( 'intval', $value ) );
			break;

		case 'time_period':
			// Clear field if the parameter 'number' is empty.
			if ( ! is_array( $value ) || empty( $value['number'] ) ) {
				$value = array();
			}
			break;
		default:
			/**
			 * Sanitizes a meta box field by type.
			 *
			 * The dynamic portion of the hook name, $type, refers to the field type.
			 *
			 * @since 3.2.0
			 *
			 * @param mixed $value The field value.
			 * @param array $field The field data.
			 */
			apply_filters( "wc_store_credit_sanitize_{$field['type']}_meta_box_field", $value, $field );
			break;
	}

	/**
	 * Sanitizes a meta box field.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $value The field value.
	 * @param array $field The field data.
	 */
	return apply_filters( 'wc_store_credit_sanitize_meta_box_field', $value, $field );
	// phpcs:enable WordPress.Security.NonceVerification
}
