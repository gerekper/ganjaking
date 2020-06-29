<?php
/**
 * The template for displaying the time element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-time.php
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
    <label for="<?php echo esc_attr( $id ); ?>" class="tm-epo-field-label tm-epo-timepicker-label-container">
        <input type="<?php echo esc_attr( $input_type ); ?>"
               class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-time tm-epo-timepicker"
               data-mask="<?php echo esc_attr( $time_mask ); ?>"
               data-mask-placeholder="<?php echo esc_attr( $time_placeholder ); ?>"
               data-min-time="<?php echo esc_attr( $min_time ); ?>"
               data-max-time="<?php echo esc_attr( $max_time ); ?>"
               data-time-format="<?php echo esc_attr( $time_format ); ?>"
               data-custom-time-format="<?php echo esc_attr( $custom_time_format ); ?>"
               data-time-theme="<?php echo esc_attr( $time_theme ); ?>"
               data-time-theme-size="<?php echo esc_attr( $time_theme_size ); ?>"
               data-time-theme-position="<?php echo esc_attr( $time_theme_position ); ?>"
               data-tranlation-hour="<?php echo esc_attr( $tranlation_hour ); ?>"
               data-tranlation-minute="<?php echo esc_attr( $tranlation_minute ); ?>"
               data-tranlation-second="<?php echo esc_attr( $tranlation_second ); ?>"
               data-price=""
               data-rules="<?php echo esc_attr( $rules ); ?>"
               data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
               data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
               id="<?php echo esc_attr( $id ); ?>"
               value="<?php echo esc_attr( $get_default_value ); ?>"
			<?php if ( ! empty( $tax_obj ) ) {
				echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
			} ?>
               name="<?php echo esc_attr( $name ); ?>"/>
    </label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>