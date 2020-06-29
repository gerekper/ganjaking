<?php
/**
 * The template for displaying the product element radio buttons for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-radio.php
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
                    <input class="<?php echo esc_attr( $fieldtype ); ?> tc-epo-field-product tm-epo-field tmcp-radio" name="<?php echo esc_attr( $name ); ?>" data-no-price-change="1" data-no-price="<?php echo esc_attr( ! $priced_individually ); ?>" data-price="<?php echo esc_attr( $option['data_price'] ); ?>" data-rules="<?php echo esc_attr( $option['data_rules'] ); ?>" data-original-rules="<?php echo esc_attr( $option['data_original_rules'] ); ?>" data-rulestype="<?php echo esc_attr( $option['data_rulestype'] ); ?>"<?php if ( isset( $option['data_type'] ) ) {
						?> data-type="<?php echo esc_attr( $option['data_type'] ); ?>"<?php
					}
					if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
						THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
					} ?> value="<?php echo esc_attr( $product_id ); ?>" id="<?php echo esc_attr( $forid ); ?>" type="radio" <?php
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

					echo '<span class="tc-label-wrap">';

					echo '<span class="tc-label tm-label">';

					echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], FALSE );

					echo '</span>';

					echo '</span>'; ?>
            </label>
			<?php
			include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' );
			include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php' );
			?>
        </li>
		<?php
	endforeach;
endif;
?>
<li class="tc-epo-element-product-li-container tm-hidden"><?php
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php' );
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php' );
	?></li>