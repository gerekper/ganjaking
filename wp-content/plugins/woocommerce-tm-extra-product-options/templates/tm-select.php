<?php
/**
 * The template for displaying the select element for the builder/local modes
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-select.php
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
<li class="tmcp-field-wrap">
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php' ); ?>
    <label class="tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $id ); ?>">
        <select class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-select"
                name="<?php echo esc_attr( $name ); ?>"
                data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
                data-price=""
                data-rules=""
                data-original-rules=""
                id="<?php echo esc_attr( $id ); ?>"<?php 
                if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
                    THEMECOMPLETE_EPO_HTML()->create_attribute_list( $element_data_attr );
                }
                ?>>
			<?php 

            if ( is_array($options) ){
                
                foreach ($options as $option) {
                    ?>
            <option <?php if ( isset( $option['selected'] ) && isset( $option['current'] ) ) { selected( $option['selected'], $option['current'] ); } ?> value="<?php echo esc_attr( $option['value_to_show'] ); ?>" 
                <?php if ( isset( $option['css_class'] ) ) { ?>
                class="tc-multiple-option tc-select-option<?php echo esc_attr( $option['css_class'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_url'] ) && ! empty( $option['data_url'] ) ) { ?>
                data-url="<?php echo esc_attr( $option['data_url'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_imagep'] ) ) { ?>
                data-imagep="<?php echo esc_attr( $option['data_imagep'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_price'] ) ) { ?>
                data-price="<?php echo esc_attr( $option['data_price'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['tm_tooltip_html'] ) && ! empty( $option['tm_tooltip_html'] ) ) { ?>
                data-tm-tooltip-html="<?php echo esc_attr( $option['tm_tooltip_html'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['image_variations'] ) ) { ?>
                data-image-variations="<?php echo esc_attr( $option['image_variations'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_rules'] ) ) { ?>
                data-rules="<?php echo esc_attr( $option['data_rules'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_original_rules'] ) ) { ?>
                data-original-rules="<?php echo esc_attr( $option['data_original_rules'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_rulestype'] ) ) { ?>
                data-rulestype="<?php echo esc_attr( $option['data_rulestype'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_text'] ) ) { ?>
                data-text="<?php echo esc_attr( $option['data_text'] ); ?>" 
                <?php } ?>
                <?php if ( isset( $option['data_hide_amount'] ) ) { ?>
                data-hide-amount="<?php echo esc_attr( $option['data_hide_amount'] ); ?>"
                <?php } ?> ><?php 
                // $option['text'] may contain HTML code
                echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], FALSE );
                ?></option>
                <?php 
                }
            }
            ?>
        </select></label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>