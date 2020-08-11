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
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'tm_before_builder_element', isset( $tm_element_settings ) ? $tm_element_settings : array() );

$extra_class = ( ! empty( $extra_class ) ) ? " " . $extra_class : "";
$class       = ( ! empty( $class ) ) ? " " . $class : "";
$divclass    = ( ! empty( $class ) ) ? $class . "-div" : "";
if ( ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]["no_frontend_display"] ) ) {
	$divclass .= " tm-hidden";
}
if ( ! empty( $clear_options ) ) {
	$divclass .= " tm-has-clearbutton";
}
$ulclass = ( ! empty( $class ) ) ? $class . "-ul" : "";
$class   = "";

$logic               = ( ! $haslogic ) ? "" : $logic;
$limit               = ( ! empty( $limit ) ) ? " " . $limit : "";
$exactlimit          = ( ! empty( $exactlimit ) ) ? " " . $exactlimit : "";
$minimumlimit        = ( ! empty( $minimumlimit ) ) ? " " . $minimumlimit : "";
$tm_product_id_class = ( ! empty( $tm_product_id ) ) ? " tm-product-id-" . $tm_product_id : "";


$fb_label_show = isset( $tm_element_settings['hide_element_label_in_floatbox'] ) ? $tm_element_settings['hide_element_label_in_floatbox'] : '';
$fb_value_show = isset( $tm_element_settings['hide_element_value_in_floatbox'] ) ? $tm_element_settings['hide_element_value_in_floatbox'] : '';

$description = apply_filters( "wc_epo_content", $description );
$description = apply_filters( "wc_epo_subtitle", $description );

$use = " " . $class_id;
if ( ! empty( $use_images ) ) {
	switch ( $use_images ) {
		case "images":
		case "start":
		case "end":
			$use .= " use_images_container";
			break;
	}
}
if ( ! empty( $use_colors ) ) {
	switch ( $use_colors ) {
		case "color":
		case "start":
		case "end":
			$use .= " use_colors_container";
			break;
	}
}
if ( ! empty( $use_url ) ) {
	switch ( $use_url ) {
		case "url":
			$use .= " use_url_container";
			break;
	}
}

if ( $tm_element_settings['type'] == 'radio' ) {
	switch ( THEMECOMPLETE_EPO()->tm_epo_global_radio_undo_button ) {
		case 'enable':
			$clear_options = 'yes';
			break;
		case 'disable':
			$clear_options = '';
			break;
	}
}

if ( ! empty( $tm_variation_undo_button ) || ! empty( $clear_options ) ) {
	$class .= " " . 'tm-has-undo-button';
}
if ( $required ) {
	$class .= " " . 'tm-has-required';
}

if ( empty( $title ) && ! empty( $required ) ) {
	$title = '&nbsp;';
}

?>
<div data-uniqid="<?php echo esc_attr( $uniqid ); ?>"
     data-logic="<?php echo esc_attr( $logic ); ?>"
     data-haslogic="<?php echo esc_attr( $haslogic ); ?>"
     data-fblabelshow="<?php echo esc_attr( $fb_label_show ); ?>"
     data-fbvalueshow="<?php echo esc_attr( $fb_value_show ); ?>"
     class="tc-container cpf_hide_element tc-cell <?php echo esc_attr( $column ); ?> cpf-type-<?php echo esc_attr( $type . $divclass . $tm_product_id_class. $extra_class ); ?>"
     <?php 
     if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
		THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
	 }
     if ( ! empty( $container_id ) ){
     	echo ' id="' . esc_attr( $container_id ) . '"';
     }
     ?>><div class="tc-element-inner-wrap"><div class="tc-row">
<?php

if ( $element != "divider" ) {

	$css              = '';
	$descriptionclass = "";
	if ( ! empty( $title_color ) ) {
		$css .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $title_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $title_color ) ) . ';}';
	}
	if ( ! empty( $description_color ) ) {
		$css              .= '.color-' . esc_attr( sanitize_hex_color_no_hash( $description_color ) ) . '{color:' . esc_attr( sanitize_hex_color( $description_color ) ) . ';}';
		$descriptionclass = " color-" . sanitize_hex_color_no_hash( $description_color );
	}
	THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css );

	if ( ( ! empty( $title ) && ( $title_position == "left" || $title_position == "right" ) ) && ( ! empty( $description ) && ( $description_position !== "below" ) ) ) {
		echo '<div';
		echo ' class="tc-cell tc-width100 tm-description' . ( $description_position == "tooltip" || $description_position == "icontooltipright" || $description_position == "icontooltipleft" ? " tm-tip-html" : "" ) . esc_attr( $descriptionclass ) . '">';
		// $description contains HTML code
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description );
		echo '</div>';
	}

	if ( $title_position != "disable" ) {

		if ( ! empty( $title ) || ! empty( $required ) || ( ! empty( $description ) && ( $description_position == "icontooltipright" | $description_position == "icontooltipleft" ) ) ) {

			echo '<' . esc_attr( $title_size );

			if ( $element == 'header' && ! empty( $class ) ) {
				$class = " " . $class;
			}
			if ( ! empty( $description ) && $description_position == "tooltip" ) {
				$class = " tm-tooltip";
			}
			if ( ! empty( $title_position ) ) {
				$class .= " tc-col-auto tm-" . $title_position;
			} else {
				$class .= " tcwidth-100";
			}
			if ( ! empty( $title_color ) ) {
				$class .= " color-" . sanitize_hex_color_no_hash( $title_color );
			}
			if ( ! empty( $description ) && ! empty( $description_position ) && $description_position == "tooltip" ) {
				echo ' data-tm-tooltip-swatch="on"';
			}
			echo ' class="tc-cell tm-epo-element-label' . esc_attr( $class ) . '">';
			if ( $required && ! empty( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) && THEMECOMPLETE_EPO()->tm_epo_global_required_indicator_position == 'left' ) {
				// THEMECOMPLETE_EPO()->tm_epo_global_required_indicator may contain HTML code
				echo '<span class="tm-epo-required">' . apply_filters( 'wc_epo_kses', wp_kses_post( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ), THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) . '</span>';
			}
			if ( $description_position == "icontooltipleft" ) {
				echo '<i data-tm-tooltip-swatch="on" class="tc-icontooltipleft tm-tooltip tc-tooltip tc-tooltip-left tcfa tcfa-question-circle"></i>';
			}

			if ( ! empty( $title ) && $title_position != "disable" ) {
				// $title contains HTML code
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $title ), $title );
			} else {
				echo '&nbsp;';
			}
			if ( $required && ! empty( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) && THEMECOMPLETE_EPO()->tm_epo_global_required_indicator_position == 'right' ) {
				// THEMECOMPLETE_EPO()->tm_epo_global_required_indicator may contain HTML code
				echo '<span class="tm-epo-required">' . apply_filters( 'wc_epo_kses', wp_kses_post( THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ), THEMECOMPLETE_EPO()->tm_epo_global_required_indicator ) . '</span>';
			}

			if ( ! empty( $tm_variation_undo_button ) ) { ?>
                <span data-tm-for-variation="<?php echo esc_attr( $tm_variation_undo_button ); ?>" class="tm-epo-reset-variation"><i class="tcfa tcfa-undo"></i></span>
				<?php
			}

			if ( $description_position == "icontooltipright" ) {
				echo '<i data-tm-tooltip-swatch="on" class="tc-icontooltipright tm-tooltip tc-tooltip tc-tooltip-right tcfa tcfa-question-circle"></i>';
			}
 
			echo '</' . esc_attr( $title_size ) . '>';
		}
	}

	if ( ! empty( $clear_options ) ) {
		echo '<span class="tm-epo-reset-radio tm-hidden">' . apply_filters( 'tm_undo_radio_text', '<i class="tcfa tcfa-times"></i>' ) . '</span>';
	}

	if ( ! ( $title_position == "left" || $title_position == "right" ) && ! empty( $description ) && ( empty( $description_position ) || $description_position == "tooltip" || $description_position == "icontooltipright" || $description_position == "icontooltipleft" ) ) {
		echo '<div';
		echo ' class="tc-cell tc-width100 tm-description' . ( $description_position == "tooltip" || $description_position == "icontooltipright" || $description_position == "icontooltipleft" ? " tm-tip-html" : "" ) . esc_attr( $descriptionclass ) . '">';
		// $description contains HTML code
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description );
		echo '</div>';
	}

}

if ( ( $element != "divider" && ( $title_position === "" || $title_position === "disable" ) ) || $element === "divider" ) {
	switch ( $divider ) {
		case "hr":
			echo '<hr class="hr_divider tc-cell tc-width100' . esc_attr( $divider_class ) . '">';
			break;
		case "divider":
			echo '<div class="tm_divider tc-cell tc-width100' . esc_attr( $divider_class ) . '"></div>';
			break;
		case "padding":
			echo '<div class="tm_padding tc-cell tc-width100' . esc_attr( $divider_class ) . '"></div>';
			break;
	}
}

if ( ! in_array( $element, array( 'header', 'divider' ) ) && empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $tm_element_settings['type'] ]["no_frontend_display"] ) ) {
	?>
    <div class="tc-cell tc-col tm-extra-product-options-container">
<ul data-rules="<?php echo esc_attr( $rules ); ?>"
    data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
    data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
    <?php if ( ! empty( $tm_validation ) ) { ?>data-tm-validation="<?php echo esc_attr( $tm_validation ); ?>" <?php } ?>
    class="tmcp-ul-wrap tmcp-elements tm-extra-product-options-<?php echo esc_attr( $type . $use . $ulclass . $limit . $exactlimit . $minimumlimit ); ?>">
	<?php
}
