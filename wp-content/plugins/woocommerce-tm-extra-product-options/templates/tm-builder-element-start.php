<?php
/**
 * The template for displaying the start of an element in the builder mode options
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-builder-element-start.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'tm_before_builder_element', isset( $tm_element_settings ) ? $tm_element_settings : [] );

$do_start = true;
if ( isset( $get_posted_key_count ) && isset( $get_posted_key ) && $get_posted_key_count > 1 ) {
	if ( 0 !== $get_posted_key ) {
		$do_start = false;
	}
}

$extra_class = ( ! empty( $extra_class ) ) ? ' ' . $extra_class : '';
$class       = ( ! empty( $class ) ) ? ' ' . $class : '';
$array_class = explode( ' ', $class );
$ul_class    = explode( ' ', $class );
foreach ( $array_class as $array_class_key => $array_class_value ) {
	if ( '' !== $array_class_value ) {
		$array_class [ $array_class_key ] = $array_class_value . '-div';
		$ul_class [ $array_class_key ]    = $array_class_value . '-ul';
	}
}
if ( $repeater ) {
	$array_class[] = 'tc-repeater';
	if ( $repeater_quantity ) {
		$array_class[] = 'tc-repeater-quantity';
	}
	if ( ! isset( $element_data_attr ) ) {
		$element_data_attr = [];
	}
	if ( $repeater_min_rows ) {
		$element_data_attr['data-repeater-min-rows'] = $repeater_min_rows;
	}
	if ( $repeater_max_rows ) {
		$element_data_attr['data-repeater-max-rows'] = $repeater_max_rows;
	}
}

$divclass = implode( ' ', $array_class );
if ( ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]->no_frontend_display ) ) {
	$divclass .= ' tm-hidden';
}
if ( ! empty( $clear_options ) ) {
	$divclass .= ' tm-has-clearbutton';
}

$ulclass = implode( ' ', $ul_class );
$class   = '';

$logic               = ( ! $haslogic ) ? '' : $logic;
$limit               = ( ! empty( $limit ) ) ? ' ' . $limit : '';
$exactlimit          = ( ! empty( $exactlimit ) ) ? ' ' . $exactlimit : '';
$minimumlimit        = ( ! empty( $minimumlimit ) ) ? ' ' . $minimumlimit : '';
$tm_product_id_class = ( ! empty( $tm_product_id ) ) ? ' tm-product-id-' . $tm_product_id : '';


$fb_label_show = isset( $tm_element_settings['hide_element_label_in_floatbox'] ) ? $tm_element_settings['hide_element_label_in_floatbox'] : '';
$fb_value_show = isset( $tm_element_settings['hide_element_value_in_floatbox'] ) ? $tm_element_settings['hide_element_value_in_floatbox'] : '';

$description = apply_filters( 'wc_epo_content', $description );
$description = apply_filters( 'wc_epo_subtitle', $description );

$use = ' ' . $class_id;
if ( ! empty( $replacement_mode ) ) {
	switch ( $replacement_mode ) {
		case 'image':
			$use .= ' tc-images-container';
			break;
		case 'color':
			$use .= ' tc-colors-container';
			break;
		case 'text':
			$use .= ' tc-text-container';
			break;
	}
}

if ( ! empty( $use_url ) ) {
	switch ( $use_url ) {
		case 'url':
			$use .= ' tc-url-container';
			break;
	}
}

if ( 'radio' === $tm_element_settings['type'] ) {
	switch ( THEMECOMPLETE_EPO()->tm_epo_global_radio_undo_button ) {
		case 'enable':
			$clear_options = '1';
			break;
		case 'disable':
			$clear_options = '';
			break;
	}
}

if ( ! empty( $tm_variation_undo_button ) || ! empty( $clear_options ) ) {
	$class .= ' tm-has-undo-button';
}
if ( $required ) {
	$class    .= ' tm-has-required';
	$divclass .= ' tc-is-required';
}

if ( empty( $label ) && ! empty( $required ) ) {
	$label = '&nbsp;';
}

if ( $do_start ) {
	?>
<div data-uniqid="<?php echo esc_attr( $uniqid ); ?>"
	data-logic="<?php echo esc_attr( $logic ); ?>"
	data-haslogic="<?php echo esc_attr( $haslogic ); ?>"
	data-fblabelshow="<?php echo esc_attr( $fb_label_show ); ?>"
	data-fbvalueshow="<?php echo esc_attr( $fb_value_show ); ?>"
	<?php
	if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
		THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
	}
	if ( ! empty( $container_id ) ) {
		echo ' id="' . esc_attr( $container_id ) . '"';
	}
	?>
	class="tc-container cpf-element tc-cell <?php echo esc_attr( $column ); ?> cpf-type-<?php echo esc_attr( $type . $divclass . $tm_product_id_class . $extra_class ); ?>">
	<div class="tc-element-inner-wrap">
	<div class="tc-row">
	<?php
	if ( 'divider' !== $element ) {
		$css              = '';
		$descriptionclass = '';
		if ( ! empty( $label_color ) ) {
			$css .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $label_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $label_color ) ) . ';}';
		}
		if ( ! empty( $description_color ) ) {
			$css             .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $description_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $description_color ) ) . ';}';
			$descriptionclass = ' color-' . sanitize_hex_color_no_hash( $description_color );
		}
		THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css );

		if ( ( ! empty( $label ) && ( 'left' === $label_position || 'right' === $label_position ) ) && ( ! empty( $description ) && ( 'below' !== $description_position ) ) ) {
			echo '<div class="tc-cell tc-width100 tm-element-description tm-description' . ( 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ? ' tm-tip-html' : '' ) . esc_attr( $descriptionclass ) . '">';
			// $description contains HTML code
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</div>';
		}

		if ( 'disable' !== $label_position ) {

			if ( ! empty( $label ) || ! empty( $required ) || ( ! empty( $description ) && ( 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) ) ) {

				echo '<' . esc_attr( $label_size );

				if ( 'header' === $element && ! empty( $class ) ) {
					$class = ' ' . $class;
				}
				if ( ! empty( $description ) && 'tooltip' === $description_position ) {
					$class = ' tm-tooltip';
				}
				if ( ! empty( $label_position ) ) {
					$class .= ' tc-col-auto tm-' . $label_position;
				} else {
					$class .= ' tcwidth-100';
				}
				if ( ! empty( $label_color ) ) {
					$class .= ' color-' . sanitize_hex_color_no_hash( $label_color );
				}
				if ( ! empty( $description ) && ! empty( $description_position ) && 'tooltip' === $description_position ) {
					echo ' data-tm-tooltip-swatch="on"';
				}
				echo ' class="tc-cell tc-epo-label tm-epo-element-label' . esc_attr( $class ) . '">';
				if ( $required && ! empty( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) && 'left' === THEMECOMPLETE_EPO()->tm_epo_global_required_indicator_position ) {
					// THEMECOMPLETE_EPO()->tm_epo_global_required_indicator may contain HTML code.
					echo '<span class="tm-epo-required">' . apply_filters( 'wc_epo_kses', wp_kses_post( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ), THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( 'icontooltipleft' === $description_position ) {
					echo '<i data-tm-tooltip-swatch="on" class="tc-icontooltipleft tm-tooltip tc-tooltip tc-tooltip-left tcfa tcfa-question-circle"></i>';
				}

				if ( ! empty( $label ) && 'disable' !== $label_position ) {
					// $label contains HTML code.
					echo apply_filters( 'wc_epo_kses', wp_kses_post( $label ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo '&nbsp;';
				}
				if ( $required && ! empty( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) && 'right' === THEMECOMPLETE_EPO()->tm_epo_global_required_indicator_position ) {
					// THEMECOMPLETE_EPO()->tm_epo_global_required_indicator may contain HTML code.
					echo '<span class="tm-epo-required">' . apply_filters( 'wc_epo_kses', wp_kses_post( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ), THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}

				if ( ! empty( $tm_variation_undo_button ) ) {
					echo '<span data-tm-for-variation="' . esc_attr( $tm_variation_undo_button ) . '" class="tm-epo-reset-variation"><i class="tcfa tcfa-undo"></i></span>';
				}

				if ( 'icontooltipright' === $description_position ) {
					echo '<i data-tm-tooltip-swatch="on" class="tc-icontooltipright tm-tooltip tc-tooltip tc-tooltip-right tcfa tcfa-question-circle"></i>';
				}
				echo '</' . esc_attr( $label_size ) . '>';
			}
		}

		if ( ! empty( $clear_options ) ) {
			echo '<span class="tm-epo-reset-radio tm-hidden">' . apply_filters( 'tm_undo_radio_text', '<i class="tcfa tcfa-times"></i>' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		if ( ! ( 'left' === $label_position || 'right' === $label_position ) && ! empty( $description ) && ( empty( $description_position ) || 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) ) {
			echo '<div class="tc-cell tc-width100 tm-element-description tm-description' . ( 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ? ' tm-tip-html' : '' ) . esc_attr( $descriptionclass ) . '">';
			// $description contains HTML code
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</div>';
		}
	}

	if ( ( 'divider' !== $element && ( '' === $label_position || 'disable' === $label_position ) ) || 'divider' === $element ) {
		switch ( $divider ) {
			case 'hr':
				echo '<hr class="hr_divider tc-cell tc-width100' . esc_attr( $divider_class ) . '">';
				break;
			case 'divider':
				echo '<div class="tm_divider tc-cell tc-width100' . esc_attr( $divider_class ) . '"></div>';
				break;
			case 'padding':
				echo '<div class="tm_padding tc-cell tc-width100' . esc_attr( $divider_class ) . '"></div>';
				break;
		}
	}
}
?>
<?php if ( ! in_array( $element, [ 'header', 'divider' ], true ) && empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]->no_frontend_display ) ) : ?>
	<?php if ( $do_start ) : ?>
	<div class="tc-cell tc-col tc-element-container">
	<?php endif; ?>
	<?php if ( $repeater ) : ?>
	<div class="tc-repeater-element">
	<?php endif; ?>
	<ul data-rules="<?php echo esc_attr( $rules ); ?>"
		data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
		data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
		<?php if ( ! empty( $tm_validation ) ) : ?>
		data-tm-validation="<?php echo esc_attr( $tm_validation ); ?>" 
		<?php endif; ?>
		<?php if ( isset( $tm_element_settings ) && isset( $tm_element_settings['connector'] ) && '' !== $tm_element_settings['connector'] ) : ?>
		data-tm-connector="<?php echo esc_attr( $tm_element_settings['connector'] ); ?>" 
		<?php endif; ?>
		class="tmcp-ul-wrap tmcp-elements tm-extra-product-options-<?php echo esc_attr( $type . $use . $ulclass . $limit . $exactlimit . $minimumlimit ); ?>">
<?php endif; ?>
