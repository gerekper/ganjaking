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
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $sections_type ) && $sections_type == "popup" ) {
	$sections_class .= " section_popup";
}
if ( ! $haslogic ) {
	$logic = "";
}
$tm_product_id_class = "";
if ( ! empty( $tm_product_id ) ) {
	$tm_product_id_class = " tm-product-id-" . $tm_product_id;
}
if ( $sections_type == "slider" ) {
	$column .= " tm-owl-slider-section";
}
?>
<div data-uniqid="<?php echo esc_attr( $uniqid ); ?>"
     data-logic="<?php echo esc_attr( $logic ); ?>"
     data-haslogic="<?php echo esc_attr( $haslogic ); ?>"
     class="cpf-section tc-cell <?php echo esc_attr( $column ); ?> <?php echo esc_attr( $sections_class . $tm_product_id_class ); ?>">
	<div class="tc-section-inner-wrap"><div class="tc-row">
	<?php

	if ( isset( $sections_type ) && $sections_type == "popup" ) {
		$_popuplinkitle = ( ! empty( THEMECOMPLETE_EPO()->tm_epo_additional_options_text ) ) ? THEMECOMPLETE_EPO()->tm_epo_additional_options_text : esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' );
		if ( ! empty ( $title ) ) {
			$_popuplinkitle = $title;
		}
		// $_popuplinkitle may contain HTML code
		echo '<a class="tc-cell tcwidth-100 tm-section-link" href="#" data-title="' . esc_attr( $_popuplinkitle ) . '" data-sectionid="' . esc_attr( $uniqid ) . '">'
		     . apply_filters( 'wc_epo_kses', wp_kses_post( $_popuplinkitle ), $_popuplinkitle, FALSE ) . '</a>'
		     . '<div class="tm-section-pop tc-cell tcwidth-100">';

	}

	$icon    = FALSE;
	$toggler = '';

	if ( $style == "box" ) {
		echo '<div class="tm-box">';
	}
	if ( $style == "collapse" || $style == "collapseclosed" || $style == "accordion" ) {
		echo '<div class="tm-collapse' . ( $style == "accordion" ? ' tmaccordion' : '' ) . '">';
		$icon    = TRUE;
		$toggler = ' tm-toggle';
		if ( $title == '' ) {
			$title = '&nbsp;';
		}
	}

	$css = '';
	$descriptionclass = "";
	if ( ! empty( $title_color ) ) {
		$css .= '.color-'. esc_attr( sanitize_hex_color_no_hash( $title_color ) ) . '{color:'.esc_attr( sanitize_hex_color($title_color) ).';}';
	}
	if ( ! empty( $description_color ) ) {
		$css .= '.color-'. esc_attr( sanitize_hex_color_no_hash( $description_color ) ) . '{color:'.esc_attr( sanitize_hex_color($description_color) ).';}';
		$descriptionclass = " color-". sanitize_hex_color_no_hash( $description_color );
	}
	THEMECOMPLETE_EPO_DISPLAY()->add_inline_style($css);

	if ( ( ! empty( $title ) && $title_position != "disable" ) || ( ! empty( $description ) && ( $description_position == "icontooltipright" | $description_position == "icontooltipleft" ) ) ) {
		echo '<' . esc_attr( $title_size );

		$class = '';
		if ( ! empty( $description ) && $description_position == "tooltip" ) {
			$class = " tm-tooltip";
		}
		if ( ! empty( $title_position ) ) {
			$class .= " tc-col-auto tm-" . $title_position;
		} else {
			$class .= " tcwidth-100";
		}
		if ( ! empty( $title_color ) ) {
			$class .= " color-". sanitize_hex_color_no_hash( $title_color );
		}
		if ( ! empty( $description ) && ! empty( $description_position ) && $description_position == "tooltip" ) {
			echo ' data-tm-tooltip-swatch="on"';
		}
		echo ' class="tc-cell tc-col-auto tm-epo-element-label tm-section-label' . esc_attr( $toggler . $class ) . '">';
		if ( $description_position == "icontooltipleft" ) {
			echo '<i data-tm-tooltip-swatch="on" class="tm-tooltip tc-tooltip tc-tooltip-left tcfa tcfa-question-circle"></i>';
		}
		if ( ! empty( $title ) && $title_position != "disable" ) {
			// $title may contain HTML code
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $title ), $title );
		} else {
			echo '&nbsp;';
		}
		if ( $description_position == "icontooltipright" ) {
			echo '<i data-tm-tooltip-swatch="on" class="tm-tooltip tc-tooltip tc-tooltip-right tcfa tcfa-question-circle"></i>';
		}
		
		if ( $icon ){
			echo '<span class="tcfa tcfa-angle-down tm-arrow"></span>';
		}

		echo '</' . esc_attr( $title_size ) . '>';
	}

	echo '<div class="tc-cell tc-col">';

	if ( ! empty( $description ) && ( empty( $description_position ) || $description_position == "tooltip" || $description_position == "icontooltipright" | $description_position == "icontooltipleft" ) ) {
		echo '<div class="tm-description' . ( $description_position == "tooltip" || $description_position == "icontooltipright" || $description_position == "icontooltipleft" ? " tm-tip-html" : "" ) . esc_attr($descriptionclass) . '">';
		// $description contains HTML code
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description );
		echo '</div>';
	}

	switch ( $divider ) {
		case "hr":
			echo '<hr>';
			break;
		case "divider":
			echo '<div class="tm_divider"></div>';
			break;
		case "padding":
			echo '<div class="tm_padding"></div>';
			break;
	}

	if ( $style == "collapse" ) {
		echo '<div class="tm-collapse-wrap">';
	}
	if ( $style == "collapseclosed" ) {
		echo '<div class="tm-collapse-wrap closed">';
	}
	if ( $style == "accordion" ) {
		echo '<div class="tm-collapse-wrap closed">';
	}
	?>
	<div class="tc-row">