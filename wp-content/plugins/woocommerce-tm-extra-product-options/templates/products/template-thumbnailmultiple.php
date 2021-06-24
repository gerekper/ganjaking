<?php
/**
 * The template for displaying the product element thumbnails (multiple) for the builder mode
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0.12.13
 */

defined( 'ABSPATH' ) || exit;

if ( is_array( $options ) ) :
	foreach ( $options as $option_key => $option ) :

		$product_id = $option['value_to_show'];

		$forid   = uniqid( $id . '_' );
		$checked = FALSE;
		if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
			if ( $option['selected'] === $option['current'] ) {
				$checked = TRUE;
			}
		}
		?>
        <li class="tmcp-field-wrap tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>">
            <label class="tm-epo-field-label" for="<?php echo esc_attr( $forid ); ?>">
				<?php if ( ! empty( $labelclass_start ) ) : ?>
                <span class="tm-epo-style-wrapper <?php echo esc_attr( $labelclass_start ); ?>">
        		<?php endif; ?>
                    <input type='hidden' class="tc-epo-field-product-counter" name="<?php echo esc_attr( $name ); ?>_<?php echo esc_attr( $option['_default_value_counter'] ); ?>_counter" value="<?php echo esc_attr( $option['_default_value_counter'] ); ?>" />
                    <input class="<?php echo esc_attr( $fieldtype ); ?> tc-epo-field-product tc-epo-field-product-checkbox tm-epo-field tmcp-checkbox" name="<?php echo esc_attr( $name ); ?>_<?php echo esc_attr( $option['_default_value_counter'] ); ?>" data-no-price-change="1" data-no-price="<?php echo esc_attr( ! $priced_individually ); ?>" data-price="<?php echo esc_attr( $option['data_price'] ); ?>" data-rules="<?php echo esc_attr( $option['data_rules'] ); ?>" data-original-rules="<?php echo esc_attr( $option['data_original_rules'] ); ?>" data-rulestype="<?php echo esc_attr( $option['data_rulestype'] ); ?>"<?php if ( isset( $option['data_type'] ) ) {
						?> data-counter="<?php echo esc_attr( $option['_default_value_counter'] ); ?>" data-type="<?php echo esc_attr( $option['data_type'] ); ?>"<?php
					}
					if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
						THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
					} 
					if ( isset( $required ) && ! empty( $required ) ) { 
						echo ' required '; 
					} ?> value="<?php echo esc_attr( $product_id ); ?>" id="<?php echo esc_attr( $forid ); ?>" type="checkbox" <?php
					checked( $checked, TRUE );
					?> />
					<?php
					if ( ! empty( $labelclass ) ) {
						echo '<span';
						echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
						echo ' data-for="' . esc_attr( $forid ) . '"></span>';
					}
					if ( ! empty( $labelclass_end ) ) {
						echo '</span>';
					}

					include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-image.php' );

					echo '<span class="tc-label-wrap">';

					echo '<span class="tc-label tm-label">';

					echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], FALSE );

					echo '</span>';

					echo '</span>';
					$_product = wc_get_product( $product_id );
					$textafterprice = wp_kses_post( $_product->get_price_suffix() );
					unset($_product);
					include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' );					
					include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php' );
					?>
            </label>
            <div class="tc-epo-element-product-li-container tm-hidden"><?php 
            include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php' );
            include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php' );
            ?></div>
        </li>
		<?php
	endforeach;
endif;
