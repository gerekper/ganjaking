<?php
/**
 * The template for displaying the product element when the mode is single product
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-hidden.php
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
<li class="tmcp-field-wrap tc-product-hidden">
    <div class="tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>">
        <label class="tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $id ); ?>">
            <input type="checkbox" class="<?php echo esc_attr( $fieldtype ); ?> tc-epo-field-product tm-epo-field tmcp-checkbox" name="<?php echo esc_attr( $name ); ?>" data-no-price-change="1" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-price="" data-rules="" data-original-rules="" data-no-price="<?php echo esc_attr( ! $priced_individually ); ?>" id="<?php echo esc_attr( $id ); ?>" <?php
			if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
				THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
			}
			
				if ( is_array( $options ) ):
					foreach ( $options as $option ) :?>
                        <?php
                        $checked = FALSE;
						if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
							if ( $option['selected'] === $option['current'] ) {
								$checked = TRUE;
								if ( isset($_GET[ $name . '_quantity' ])){
									$_REQUEST[ $name . '_quantity' ] = $_GET[ $name . '_quantity' ];
								}
								elseif (! isset($_REQUEST[ $name . '_quantity' ])){
									if ($quantity_min === ''){
										$_REQUEST[ $name . '_quantity' ] = 1;
									} else {
										$_REQUEST[ $name . '_quantity' ] = $quantity_min;
									}
								}
							}
						} 
						checked( $checked, TRUE );						
						?> value="<?php echo esc_attr( $option['value_to_show'] ); ?>"
						data-no-price="<?php echo esc_attr( ! $priced_individually ); ?>"<?php
						if ( isset( $option['data_price'] ) ) {
							?> data-price="<?php echo esc_attr( $option['data_price'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['tm_tooltip_html'] ) && ! empty( $option['tm_tooltip_html'] ) ) {
							?> data-tm-tooltip-html="<?php echo esc_attr( $option['tm_tooltip_html'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_rules'] ) ) {
							?> data-rules="<?php echo esc_attr( $option['data_rules'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_original_rules'] ) ) {
							?> data-original-rules="<?php echo esc_attr( $option['data_original_rules'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_rulestype'] ) ) {
							?> data-rulestype="<?php echo esc_attr( $option['data_rulestype'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_text'] ) ) {
							?> data-text="<?php echo esc_attr( $option['data_text'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_type'] ) ) {
							?> data-type="<?php echo esc_attr( $option['data_type'] ); ?>"<?php
						} ?><?php
						if ( isset( $option['data_hide_amount'] ) ) {
							?> data-hide-amount="<?php echo esc_attr( $option['data_hide_amount'] ); ?>"<?php
						}
					endforeach;
				endif; ?>
            />
        </label>
		<?php 
		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' );
		include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php' );
		?>
    </div>
</li>
<li class="tc-epo-element-product-li-container tm-hidden"><?php 
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php' );
	include( THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php' );
	?></li>