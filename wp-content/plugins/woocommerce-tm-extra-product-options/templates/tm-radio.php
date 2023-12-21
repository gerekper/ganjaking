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
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $li_class, $label_to_display, $element_id, $replacement_mode, $border_type, $value, $name, $fieldtype, $use, $rules, $original_rules, $rules_type, $url, $image, $imagec, $imagep, $imagel, $image_variations, $checked, $swatch_position, $swatch_class ) ) :
	$li_class         = (string) $li_class;
	$label_to_display = (string) $label_to_display;
	$element_id       = (string) $element_id;
	$replacement_mode = (string) $replacement_mode;
	$border_type      = (string) $border_type;
	$value            = (string) $value;
	$name             = (string) $name;
	$fieldtype        = (string) $fieldtype;
	$use              = (string) $use;
	$rules            = (string) $rules;
	$original_rules   = (string) $original_rules;
	$rules_type       = (string) $rules_type;
	$url              = (string) $url;
	$image            = (string) $image;
	$imagec           = (string) $imagec;
	$imagep           = (string) $imagep;
	$imagel           = (string) $imagel;
	$image_variations = (string) $image_variations;
	$swatch_position  = (string) $swatch_position;
	$swatch_class     = (string) $swatch_class;
	$class_label      = ! empty( $class_label ) ? (string) $class_label : '';
	if ( ! isset( $is_separator ) ) {
		$is_separator = false;
	}
	$liclass = 'tmcp-field-wrap' . $li_class . ( ! empty( $label_mode ) ? ' tc-mode-' . $label_mode : ' tc-mode-normal' );
	if ( ! $is_separator ) {
		$liclass .= ' ' . $border_type;
	}

	if ( ! $is_separator && 'text' === $replacement_mode ) {
		$liclass .= ' tc-epo-text-wrapper';
	}
	?>
<li class="<?php echo esc_attr( $liclass ); ?>"><div class="tmcp-field-wrap-inner">
	<?php if ( $is_separator ) : ?>
		<?php
		echo '<span class="tc-label-wrap tc-separator"><span class="tc-col tc-label tc-separator-label">';
		$separator_html = '-1' === $label_to_display || '' === $label_to_display ? '<hr>' : $label_to_display;
		$separator_html = apply_filters( 'wc_epo_choice_separator_html', $separator_html );
		echo '<span class="tc-label-text">';
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $separator_html ), $separator_html ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</span>';
		echo '</span></span>';
		?>
	<?php else : ?>
		<div class="tc-col tc-field-label-wrap">
		<label class="tc-col tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
		<?php
		if ( 'text' === $replacement_mode ) {
			echo '<div class="tc-epo-text-label-wrapper">';
		}

		echo '<span class="tc-label-wrap' . ( empty( $hexclass ) ? '' : ' ' . esc_attr( sanitize_html_class( $hexclass ) ) ) . '">';

		if ( ! empty( $labelclass_start ) ) {
			echo '<span class="tc-epo-style-wrapper tc-first ' . esc_attr( $labelclass_start ) . ' tc-radio">';
		}
		$input_args = [
			'nodiv'      => 1,
			'type'       => 'input',
			'default'    => $value,
			'input_type' => 'radio',
			'tags'       => [
				'id'                    => $element_id,
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
		if ( apply_filters( 'wc_epo_radio_print_required_attribute', true ) && 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_enable_validation && isset( $required ) && ! empty( $required ) ) {
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
		echo '<span class="tc-input-wrap tc-epo-style-space">';
		THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
		echo '</span>';
		if ( ! in_array( $replacement_mode, [ 'image', 'color' ], true ) || ( in_array( $replacement_mode, [ 'image', 'color' ], true ) && 'center' !== $swatch_position ) ) {
			if ( ! empty( $labelclass ) ) {
				echo '<span';
				echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
				echo ' data-for="' . esc_attr( $element_id ) . '"></span>';
			}
			if ( ! empty( $labelclass_end ) ) {
				echo '</span>';
			}
		}

		$text_label_class = 'tm-label';

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

			$tmlazy = THEMECOMPLETE_EPO()->tm_epo_no_lazy_load === 'no' ? 'tmlazy ' : '';

			$text_label_class = 'radio-image-label' . ( ( THEMECOMPLETE_EPO_HELPER()->str_startswith( $label_mode, 'start' ) || THEMECOMPLETE_EPO_HELPER()->str_startswith( $label_mode, 'end' ) ) ? '-inline' : '' );
			switch ( $label_mode ) {
				case 'images':
				case 'startimages':
				case 'endimages':
					if ( ! empty( $src ) ) {
						$img_classes = [ 'tc-image', 'radio-image' ];
						if ( 'images' === $label_mode ) {
							$img_classes[] = 'tc-center';
						} else {
							$img_classes[] = 'tc-image-inline';
						}
						if ( 'startimages' === $label_mode ) {
							$img_classes[] = 'tc-left';
						}
						if ( 'endimages' === $label_mode ) {
							$img_classes[] = 'tc-right';
						}
						$img_classes = array_merge( $img_classes, array_filter( [ $border_type, $tmlazy, $swatch_class ] ) );
						$img_classes = implode( ' ', $img_classes );
						echo '<img class="' . esc_attr( $img_classes ) . '" '
						. 'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $src );
						echo wp_kses_post( $swatch_html );
						echo '>';
					}
					break;
				case 'color':
				case 'startcolor':
				case 'endcolor':
					$img_classes = [ 'tmhexcolorimage', 'radio-image' ];
					if ( 'color' === $label_mode ) {
						$img_classes[] = 'tc-center';
					} else {
						$img_classes[] = 'tmhexcolorimage-inline';
					}
					if ( 'startcolor' === $label_mode ) {
						$img_classes[] = 'tc-left';
					}
					if ( 'endcolor' === $label_mode ) {
						$img_classes[] = 'tc-right';
					}
					$img_classes[] = 'tc-epo-style-space';
					$img_classes   = array_merge( $img_classes, array_filter( [ $border_type, $swatch_class ] ) );
					$img_classes   = implode( ' ', $img_classes );
					echo '<span class="' . esc_attr( $img_classes ) . '" ' .
					'alt="' . esc_attr( wp_strip_all_tags( $label_to_display ) ) . '" ' .
					wp_kses_post( $swatch_html ) .
					'><span class="tc-image' . ( 'color' !== $label_mode ? ' tc-image-inline' : '' ) . '"></span></span>';
					break;
			}
		}
		$desc_inline          = 'yes' === THEMECOMPLETE_EPO()->tm_epo_description_inline;
		$tc_label_inner_class = 'tc-label-inner tcwidth tcwidth-100';
		if ( $desc_inline ) {
			$tc_label_inner_class .= ' desc-inline';
		} else {
			$has_desc = isset( $tm_element_settings ) && isset( $tm_element_settings['cdescription'] ) && isset( $field_counter ) && ! empty( $tm_element_settings['cdescription'][ $field_counter ] );
			if ( ! $has_desc ) {
				$tc_label_inner_class .= ' no-desc';
			}
		}
		echo '<span class="tc-col tc-label ' . esc_attr( $text_label_class ) . '">';
		echo '<span class="' . esc_attr( $tc_label_inner_class ) . '">';
		if ( ! empty( $label_to_display ) ) {
			echo '<span class="tc-label-text">';
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</span>';
		}
		if ( ! $desc_inline ) {
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_choice_description.php';
		}
		require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php';
		echo '</span>';
		if ( $desc_inline ) {
			require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_choice_description.php';
		}
		echo '</span>';

		echo '</span>';
		?>
		</label>
		<?php
		if ( 'text' === $replacement_mode ) {
			echo '</div>';
		}
		?>
		</div>
		<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
		<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
	<?php endif; ?>
</div></li>
	<?php
endif;
