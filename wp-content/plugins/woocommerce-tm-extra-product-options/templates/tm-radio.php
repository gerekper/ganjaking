<?php
/**
 * The template for displaying the radio button element for the builder/local modes
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-radio.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.3
 */

defined( 'ABSPATH' ) || exit;

?>
<li class="tmcp-field-wrap<?php echo esc_attr( $li_class ); ?><?php echo esc_attr( ( ! empty( $label_mode ) ? ' tc-mode-' . $label_mode : '' ) ); ?>">
<?php if ( isset( $is_separator ) && $is_separator ) : ?>
	<?php
	echo '<span class="tc-label-wrap tc-separator"><span class="tc-label tm-label">';
	$separator_html = '-1' === $label_to_display || '' === $label_to_display ? '<hr>' : $label_to_display;
	$separator_html = apply_filters( 'wc_epo_choice_separator_html', $separator_html );
	echo apply_filters( 'wc_epo_kses', wp_kses_post( $separator_html ), $separator_html ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</span></span>';
	if ( isset( $tm_element_settings ) && isset( $tm_element_settings['cdescription'] ) && isset( $field_counter ) && isset( $tm_element_settings['cdescription'][ $field_counter ] ) ) {
		if ( ! empty( $tm_element_settings['cdescription'][ $field_counter ] ) || ( ( isset( $tm_element_settings['cdescription'] ) && is_array( $tm_element_settings['cdescription'] ) && count( $tm_element_settings['cdescription'] ) > 1 ) && ( isset( $tm_element_settings['type'] ) && 'select' === $tm_element_settings['type'] ) ) ) {
			if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_description_inline ) {
				echo '<div class="tc-inline-description">' . apply_filters( 'wc_epo_kses', wp_kses_post( $tm_element_settings['cdescription'][ $field_counter ] ), $tm_element_settings['cdescription'][ $field_counter ] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo '<i data-tm-tooltip-html="' . esc_attr( apply_filters( 'wc_epo_kses', $tm_element_settings['cdescription'][ $field_counter ], $tm_element_settings['cdescription'][ $field_counter ] ) ) . '" class="tm-tooltip tc-tooltip tcfa tcfa-question-circle"></i>';
			}
		}
	}
	?>
<?php else : ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php'; ?>
	<label class="tm-epo-field-label" for="<?php echo esc_attr( $id ); ?>">
	<?php
	if ( ! empty( $border_type ) ) {
		$border_type = $border_type . ' ';
	}
	if ( 'text' === $replacement_mode ) {
		echo '<span class="' . esc_attr( $border_type ) . 'tc-epo-text-wrapper">';
	}
	if ( ! empty( $labelclass_start ) ) {
		echo '<span class="tc-epo-style-wrapper ' . esc_attr( $labelclass_start ) . '">';
	}
	$input_args = [
		'nodiv'      => 1,
		'type'       => 'input',
		'default'    => $value,
		'input_type' => 'radio',
		'tags'       => [
			'id'                    => $id,
			'name'                  => $name,
			'class'                 => $fieldtype . ' tm-epo-field tmcp-radio' . $use,
			'data-price'            => '',
			'data-rules'            => $rules,
			'data-original-rules'   => $original_rules,
			'data-rulestype'        => $rules_type,
			'data-url'              => $url,
			'data-image'            => $image,
			'data-imagec'           => $imagec,
			'data-imagep'           => $imagep,
			'data-imagel'           => $imagel,
			'data-image-variations' => $image_variations,
		],
	];
	if ( apply_filters( 'wc_epo_radio_print_required_attribute', true ) && isset( $required ) && ! empty( $required ) ) {
		$input_args['tags']['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$input_args['tags']['data-tax-obj'] = $tax_obj;
	}
	if ( ! empty( $changes_product_image ) ) {
		$input_args['tags']['data-changes-product-image'] = $changes_product_image;
	}
	if ( true === $checked ) {
		$input_args['tags']['checked'] = 'checked';
	}
	if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
		$input_args['tags'] = array_merge( $input_args['tags'], $element_data_attr );
	}
	if ( THEMECOMPLETE_EPO()->associated_per_product_pricing === 0 ) {
		$input_args['tags']['data-no-price'] = true;
	}

	$input_args = apply_filters(
		'wc_element_input_args',
		$input_args,
		isset( $tm_element_settings ) && isset( $tm_element_settings['type'] ) ? $tm_element_settings['type'] : '',
		isset( $args ) ? $args : [],
	);

	THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );

	if ( ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) || ( ( 'image' === $replacement_mode || 'color' === $replacement_mode ) && 'center' !== $swatch_position ) ) {
		if ( ! empty( $labelclass ) ) {
			echo '<span';
			echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
			echo ' data-for="' . esc_attr( $id ) . '"></span>';
		}
		if ( ! empty( $labelclass_end ) ) {
			echo '</span>';
		}
	}
	echo '<span class="tc-label-wrap' . ( empty( $hexclass ) ? '' : ' ' . esc_attr( sanitize_html_class( $hexclass ) ) ) . '">';
	if ( ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) || ( ( 'image' === $replacement_mode || 'color' === $replacement_mode ) && 'center' !== $swatch_position ) ) {
		if ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) {
			echo '<span class="tc-label tm-label">';
		}
	}

	if ( isset( $label_mode ) && ! empty( $label_mode ) && 'text' !== $label_mode ) {

		$src = '';
		if ( isset( $altsrc ) && is_array( $altsrc ) ) {
			foreach ( $altsrc as $k => $v ) {
				$src .= esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '" ';
			}
		}

		$swatch_html = '';
		if ( isset( $swatch ) && is_array( $swatch ) ) {
			foreach ( $swatch as $_swatch ) {
				foreach ( $_swatch as $k => $v ) {
					$swatch_html .= esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
				}
			}
		}

		$tmlazy = THEMECOMPLETE_EPO()->tm_epo_no_lazy_load === 'no' ? ' tmlazy ' : '';

		// $src && swatch_html are generated above.
		switch ( $label_mode ) {
			case 'images':
				if ( ! empty( $src ) ) {
					echo '<img class="' . esc_attr( $border_type ) . esc_attr( $tmlazy ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
						. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
					echo wp_kses_post( $src );
					echo wp_kses_post( $swatch_html );
					echo ' />';
				}
				echo '<span class="tc-label radio-image-label">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				break;
			case 'startimages':
				if ( ! empty( $src ) ) {
					echo '<img class="' . esc_attr( $border_type ) . esc_attr( $tmlazy ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
						. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
					echo wp_kses_post( $src );
					echo wp_kses_post( $swatch_html );
					echo ' />';
				}
				if ( ! empty( $label_to_display ) ) {
					echo '<span class="tc-label radio-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}

				break;
			case 'endimages':
				if ( ! empty( $label_to_display ) ) {
					echo '<span class="tc-label radio-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( ! empty( $src ) ) {
					echo '<img class="' . esc_attr( $border_type ) . esc_attr( $tmlazy ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
						. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
					echo wp_kses_post( $src );
					echo wp_kses_post( $swatch_html );
					echo ' />';
				}
				break;

			case 'color':
				echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
					. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
				echo wp_kses_post( $swatch_html );
				echo '></span>'
					. '<span class="tc-label radio-image-label">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				break;
			case 'startcolor':
				echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
					. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
				echo wp_kses_post( $swatch_html );
				echo '></span>';
				if ( ! empty( $label_to_display ) ) {
					echo '<span class="tc-label radio-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				break;
			case 'endcolor':
				if ( ! empty( $label_to_display ) ) {
					echo '<span class="tc-label radio-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . 'radio-image' . esc_attr( $swatch_class ) . '" '
					. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
				echo wp_kses_post( $swatch_html );
				echo '></span>';
				break;
		}
	} else {
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</span>';
	if ( ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) || ( ( 'image' === $replacement_mode || 'color' === $replacement_mode ) && 'center' !== $swatch_position ) ) {
		if ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) {
			echo '</span>';
		}
	}
	if ( 'text' === $replacement_mode ) {
		echo '</span>';
	}
	?>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
<?php endif; ?>
</li>
