<?php
/**
 * The template for displaying the checkbox element for the builder/local modes
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-checkbox.php
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
?>
<li class="tmcp-field-wrap<?php echo esc_attr( $grid_break . $li_class ); ?><?php if( ! empty( $label_mode ) ) {echo ' tc-mode-' . esc_attr( $label_mode ); } ?>">
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php' ); ?>
    <label class="tm-epo-field-label" for="<?php echo esc_attr( $id ); ?>">
		<?php
		if ( ! empty( $labelclass_start ) ) { ?>
        <span class="tm-epo-style-wrapper <?php echo esc_attr( $labelclass_start ); ?>">
		<?php } ?>
            <input class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-checkbox<?php echo esc_attr( $use ); ?>"
                   name="<?php echo esc_attr( $name ); ?>"
                   data-limit="<?php echo esc_attr( $limit ); ?>"
                   data-exactlimit="<?php echo esc_attr( $exactlimit ); ?>"
                   data-minimumlimit="<?php echo esc_attr( $minimumlimit ); ?>"
                   data-image="<?php echo esc_attr( $image ); ?>"
                   data-imagec="<?php echo esc_attr( $imagec ); ?>"
                   data-imagep="<?php echo esc_attr( $imagep ); ?>"
                   data-imagel="<?php echo esc_attr( $imagel ); ?>"
                   data-image-variations="<?php echo esc_attr( $image_variations ); ?>"
                   data-price=""
                   data-rules="<?php echo esc_attr( $rules ); ?>"
                   data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
                   data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"
                   id="<?php echo esc_attr( $id ); ?>"
                   type="checkbox"
				<?php if ( ! empty( $tax_obj ) ) {
					echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
				} ?>
				<?php
				if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
					THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
				}
				?>
				<?php checked( $checked, TRUE ); ?> />
			<?php
			if ( empty( $use_images ) || ( isset( $use_images ) && $use_images != "images" ) ) {
				if ( ! empty( $labelclass ) ) {
					echo '<span';
					echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
					echo ' data-for="' . esc_attr( $id ) . '"></span>';
				}
				if ( ! empty( $labelclass_end ) ) {
					echo '</span>';
				}
			}
			echo '<span class="tc-label-wrap' . ( empty( $hexclass ) ? '' : ' ' . $hexclass ) . '">';
			if ( empty( $use_images ) || ( isset( $use_images ) && $use_images != "images" ) ) {
				if ( empty( $use_images ) ) {
					echo '<span class="tc-label tm-label">';
				}
			}

			if ( isset( $label_mode ) && ! empty( $label_mode ) ) {

				$src = '';
				if ( isset( $altsrc ) && is_array( $altsrc ) ) {
					foreach ( $altsrc as $k => $v ) {
						$src .= esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '" ';
					}
				}

				$swatch_html = '';
				if ( isset( $swatch ) && is_array( $swatch ) ) {
					foreach ( $swatch as $s ) {
						foreach ( $s as $k => $v ) {
							$swatch_html .= esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
						}
					}
				}

				// $src && swatch_html are generated above
				switch ( $label_mode ) {
					case 'images':
						echo '<img class="tmlazy ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $src );
						echo wp_kses_post( $swatch_html );
						echo ' />';
						echo '<span class="tc-label checkbox-image-label">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						break;
					case 'startimages':
						echo '<img class="tmlazy ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $src );
						echo wp_kses_post( $swatch_html );
						echo ' />';
						if ( ! empty( $label_to_display ) ) {
							echo '<span class="tc-label checkbox-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						}
						break;
					case 'endimages':
						if ( ! empty( $label_to_display ) ) {
							echo '<span class="tc-label checkbox-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						}
						echo '<img class="tmlazy ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $src );
						echo wp_kses_post( $swatch_html );
						echo ' />';
						break;

					case 'color':
						echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $swatch_html );
						echo '></span>'
						     . '<span class="tc-label checkbox-image-label">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						break;
					case 'startcolor':
						echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $swatch_html );
						echo '></span>';
						if ( ! empty( $label_to_display ) ) {
							echo '<span class="tc-label checkbox-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						}
						break;
					case 'endcolor':
						if ( ! empty( $label_to_display ) ) {
							echo '<span class="tc-label checkbox-image-label-inline">' . apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display ) . '</span>';
						}
						echo '<span class="tmhexcolorimage ' . esc_attr( $border_type ) . ' checkbox_image' . esc_attr( $swatch_class ) . '" '
						     . 'alt="' . esc_attr( strip_tags( $label_to_display ) ) . '" ';
						echo wp_kses_post( $swatch_html );
						echo '></span>';
						break;
				}
			} else {
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $label_to_display ), $label_to_display );
			}
			echo '</span>';
			if ( empty( $use_images ) || ( isset( $use_images ) && $use_images != "images" ) ) {
				if ( empty( $use_images ) ) {
					echo '</span>';
				}
			}
			?>
    </label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>