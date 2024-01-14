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
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $sections_class, $haslogic, $column, $uniqid, $logic, $sections_popupbutton, $style, $description_position, $label_position, $label_size, $divider, $labelbgclass, $descriptionclass, $sectionbgcolorclass ) ) :
	$sections_class       = (string) $sections_class;
	$haslogic             = (string) $haslogic;
	$column               = (string) $column;
	$uniqid               = (string) $uniqid;
	$logic                = (string) $logic;
	$sections_popupbutton = (string) $sections_popupbutton;
	$style                = (string) $style;
	$description_position = (string) $description_position;
	$label_position       = (string) $label_position;
	$label_size           = (string) $label_size;
	$divider              = (string) $divider;
	$labelbgclass         = (string) $labelbgclass;
	$descriptionclass     = (string) $descriptionclass;
	$sectionbgcolorclass  = (string) $sectionbgcolorclass;

	if ( ! isset( $label ) ) {
		$label = '';
	}
	if ( ! isset( $sections_type ) ) {
		$sections_type = '';
	}
	if ( 'popup' === $sections_type ) {
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
	<div class="tc-section-inner-wrap">
	<?php

	$row_classes = [];
	$icon        = false;
	$toggler     = '';

	if ( 'box' === $style ) {
		$row_classes[] = 'tm-box';
		$row_classes[] = $sectionbgcolorclass;
	}
	if ( 'collapse' === $style || 'collapseclosed' === $style || 'accordion' === $style ) {
		$row_classes[] = 'tm-collapse';
		$row_classes[] = $sectionbgcolorclass;
		if ( 'accordion' === $style ) {
			$row_classes[] = 'tmaccordion';
		}
		$icon    = true;
		$toggler = ' tm-toggle';
		if ( '' === $label ) {
			$label = '&nbsp;';
		}
	}

	$row_classes = trim( join( ' ', $row_classes ) );
	if ( ! empty( $row_classes ) ) {
		$row_classes = ' ' . $row_classes;
	}

	echo '<div class="tc-row' . esc_attr( $row_classes ) . '">';

	if ( 'popup' === $sections_type ) {
		$_popuplinkitle = esc_html__( 'Open', 'woocommerce-tm-extra-product-options' );
		if ( ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_popup_section_button_text' ) ) ) {
			if ( '%auto%' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_popup_section_button_text' ) ) {
				$_popuplinkitle = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_popup_section_button_text' );
			} elseif ( '%auto%' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_popup_section_button_text' ) && '' !== $label ) {
				$_popuplinkitle = $label;
			}
		}
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
							'class'          => 'tc-cell tcwidth tcwidth-100 tm-section-link',
						],
					],
					true
				);
				break;
		}

		echo '<div class="tm-section-pop tc-cell tcwidth tcwidth-100">';
	}

	if ( ( ! empty( $label ) && 'disable' !== $label_position ) || ( ! empty( $description ) && ( 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) ) ) {
		echo '<' . esc_attr( $label_size );

		$class = '';
		if ( ( ! empty( $label_position ) && 'disable' !== $label_position ) || 'tooltip' === $description_position ) {
			$class .= ' tc-col-auto';
			if ( ! empty( $label_position ) ) {
				$class .= ' tc-' . $label_position;
			}
		} else {
			$class .= ' tcwidth tcwidth-100';
		}
		if ( ! empty( $label_color ) ) {
			$class .= ' color-' . themecomplete_sanitize_hex_color_no_hash( $label_color );
		}
		if ( ! empty( $labelbgclass ) ) {
			$class .= $labelbgclass;
		}
		echo ' class="tc-cell tc-epo-label tm-section-label' . esc_attr( $toggler . $class ) . '">';

		// Icon tooltip.
		if ( 'icontooltipleft' === $description_position || 'icontooltipright' === $description_position ) {
			echo '<i data-tm-tooltip-swatch="on" class="tc-' . esc_attr( $description_position ) . ' tm-tooltip tc-tooltip tcfa tcfa-question-circle tc-epo-style-space"></i>';
		}

		// Label text.
		$section_label_text_class = 'tc-section-label-text';
		if ( ! empty( $description ) && 'tooltip' === $description_position ) {
			$section_label_text_class .= ' tm-tooltip';
		}
		echo '<span class="' . esc_attr( $section_label_text_class ) . '"';
		if ( ! empty( $description ) && ! empty( $description_position ) && 'tooltip' === $description_position ) {
			echo ' data-tm-tooltip-swatch="on"';
		}
		echo '>';
		if ( ! empty( $label ) && 'disable' !== $label_position ) {
			// $label may contain HTML code
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $label ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '&nbsp;';
		}
		echo '</span>';

		if ( $icon ) {
			echo '<span class="tcfa tcfa-angle-down tm-arrow"></span>';
		}

		echo '</' . esc_attr( $label_size ) . '>';
	}

	if ( ! empty( $description ) ) {
		$descriptionclass = 'tc-cell tm-section-description tm-description' . $descriptionclass;
		if ( ! empty( $label_position ) && ! empty( $description_position ) && 'below' !== $description_position ) {
			$descriptionclass .= ' tc-col';
		} else {
			$descriptionclass .= ' tcwidth tcwidth-100';
			if ( 'below' !== $description_position && ( 'left' === $label_position || 'right' === $label_position ) ) {
				$descriptionclass .= ' tc-first';
			}
		}
		if ( 'tooltip' === $description_position || 'icontooltipright' === $description_position || 'icontooltipleft' === $description_position ) {
			$descriptionclass .= ' tm-tip-html';
		}
		if ( ! empty( $description_position ) && 'tooltip' !== $description_position ) {
			$descriptionclass .= ' tc-' . $description_position;
		}
		echo '<div class="' . esc_attr( $descriptionclass ) . '">';
		// $description contains HTML code
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $description ), $description ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	}

	if ( ! empty( $label_position ) && 'disable' !== $label_position ) {
		$fields_class = 'tc-cell tc-col';
	} else {
		$fields_class = 'tc-cell tcwidth tcwidth-100';
	}
	echo '<div class="' . esc_attr( $fields_class ) . '">';

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

endif;
