<?php
/**
 * The template for displaying the start of a section in the builder mode options
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-builder-section-start.php
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

if ( isset( $sections_type ) && 'popup' === $sections_type ) {
	$sections_class .= ' section_popup';
}
if ( ! $haslogic ) {
	$logic = '';
}
$tm_product_id_class = '';
if ( ! empty( $tm_product_id ) ) {
	$tm_product_id_class = ' tm-product-id-' . $tm_product_id;
}
if ( 'slider' === $sections_type ) {
	$column .= ' tm-owl-slider-section';
}
if ( 'tabs' === $sections_type ) {
	$column .= ' tc-tabs-section';
}
?>
<div data-uniqid="<?php echo esc_attr( $uniqid ); ?>"
	data-logic="<?php echo esc_attr( $logic ); ?>"
	data-haslogic="<?php echo esc_attr( $haslogic ); ?>"
	class="cpf-section tc-cell <?php echo esc_attr( $column ); ?> <?php echo esc_attr( $sections_class . $tm_product_id_class ); ?>">
	<div class="tc-section-inner-wrap"><div class="tc-row">
	<?php

	if ( isset( $sections_type ) && 'popup' === $sections_type ) {
		$_popuplinkitle = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_popup_section_button_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_popup_section_button_text : esc_html__( 'Open', 'woocommerce-tm-extra-product-options' );
		if ( isset( $sections_popupbuttontext ) && '' !== $sections_popupbuttontext ) {
			$_popuplinkitle = $sections_popupbuttontext;
		}
		switch ( $sections_popupbutton ) {
			case 'button':
				// $_popuplinkitle may contain HTML code
				echo '<div class="tc-cell tc-col-auto">';
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => $_popuplinkitle,
						'tags' => [
							'data-title'     => $label,
							'data-sectionid' => $uniqid,
							'class'          => 'button tm-section-link',
						],
					],
					true
				);
				echo '</div>';
				break;

			case 'buttonalt':
				// $_popuplinkitle may contain HTML code
				echo '<div class="tc-cell tc-col-auto">';
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => $_popuplinkitle,
						'tags' => [
							'data-title'     => $label,
							'data-sectionid' => $uniqid,
							'class'          => 'button alt tm-section-link',
						],
					],
					true
				);
				echo '</div>';
				break;

			case '':
			default:
				// $_popuplinkitle may contain HTML code
				THEMECOMPLETE_EPO_HTML()->create_button(
					[
						'text' => $_popuplinkitle,
						'tags' => [
							'type'           => 'a',
							'href'           => '#',
							'data-title'     => $label,
							'data-sectionid' => $uniqid,
							'class'          => 'tc-cell tcwidth-100 tm-section-link',
						],
					],
					true
				);
				break;
		}

		echo '<div class="tm-section-pop tc-cell tcwidth-100">';
	}

	$icon                = false;
	$toggler             = '';
	$css                 = '';
	$descriptionclass    = '';
	$sectionbgcolorclass = '';
	if ( ! empty( $label_color ) ) {
		$css .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $label_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $label_color ) ) . ';}';
	}
	if ( ! empty( $label_background_color ) ) {
		$css .= '.bgcolor-' . esc_attr( sanitize_hex_color_no_hash( $label_background_color ) ) . '{background:' . esc_attr( sanitize_hex_color( $label_background_color ) ) . ';}';
	}
	if ( ! empty( $description_color ) ) {
		$css              .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $description_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $description_color ) ) . ';}';
		$descriptionclass .= ' color-' . sanitize_hex_color_no_hash( $description_color );
	}
	if ( ! empty( $description_background_color ) ) {
		$css              .= '.bgcolor-' . esc_attr( sanitize_hex_color_no_hash( $description_background_color ) ) . '{background:' . esc_attr( sanitize_hex_color( $description_background_color ) ) . ';}';
		$descriptionclass .= ' bgcolor-' . sanitize_hex_color_no_hash( $description_background_color );
	}
	if ( '' !== $style && ! empty( $sections_background_color ) ) {
		$css                .= '.bgcolor-' . esc_attr( sanitize_hex_color_no_hash( $sections_background_color ) ) . '{background:' . esc_attr( sanitize_hex_color( $sections_background_color ) ) . ';}';
		$sectionbgcolorclass = ' bgcolor-' . sanitize_hex_color_no_hash( $sections_background_color );
	}

	THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css );

	if ( 'box' === $style ) {
		echo '<div class="tm-box' . esc_attr( $sectionbgcolorclass ) . '">';
	}
	if ( 'collapse' === $style || 'collapseclosed' === $style || 'accordion' === $style ) {
		echo '<div class="tm-collapse' . ( 'accordion' === $style ? ' tmaccordion' : '' ) . esc_attr( $sectionbgcolorclass ) . '">';
		$icon    = true;
		$toggler = ' tm-toggle';
		if ( '' === $label ) {
			$label = '&nbsp;';
		}
	}

	if ( ( ! empty( $label ) && 'disable' !== $label_position ) || ( ! empty( $description ) && ( 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) ) ) {
		echo '<' . esc_attr( $label_size );

		$class = '';
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
		if ( ! empty( $label_background_color ) ) {
			$class .= ' bgcolor-' . sanitize_hex_color_no_hash( $label_background_color );
		}
		if ( ! empty( $description ) && ! empty( $description_position ) && 'tooltip' === $description_position ) {
			echo ' data-tm-tooltip-swatch="on"';
		}
		echo ' class="tc-cell tc-col-auto tc-epo-label tm-section-label' . esc_attr( $toggler . $class ) . '">';
		if ( 'icontooltipleft' === $description_position ) {
			echo '<i data-tm-tooltip-swatch="on" class="tm-tooltip tc-tooltip tc-tooltip-left tcfa tcfa-question-circle"></i>';
		}
		if ( ! empty( $label ) && 'disable' !== $label_position ) {
			// $label may contain HTML code
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $label ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '&nbsp;';
		}
		if ( 'icontooltipright' === $description_position ) {
			echo '<i data-tm-tooltip-swatch="on" class="tm-tooltip tc-tooltip tc-tooltip-right tcfa tcfa-question-circle"></i>';
		}

		if ( $icon ) {
			echo '<span class="tcfa tcfa-angle-down tm-arrow"></span>';
		}

		echo '</' . esc_attr( $label_size ) . '>';
	}

	echo '<div class="tc-cell tc-col">';

	if ( ! empty( $description ) && ( empty( $description_position ) || 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) ) {
		echo '<div class="tm-section-description tm-description' . ( 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ? ' tm-tip-html' : '' ) . esc_attr( $descriptionclass ) . '">';
		// $description contains HTML code
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}

	switch ( $divider ) {
		case 'hr':
			echo '<hr>';
			break;
		case 'divider':
			echo '<div class="tm_divider"></div>';
			break;
		case 'padding':
			echo '<div class="tm_padding"></div>';
			break;
	}

	if ( 'collapse' === $style ) {
		echo '<div class="tm-collapse-wrap">';
	}
	if ( 'collapseclosed' === $style ) {
		echo '<div class="tm-collapse-wrap closed">';
	}
	if ( 'accordion' === $style ) {
		echo '<div class="tm-collapse-wrap closed">';
	}

	echo '<div class="tc-row">';
