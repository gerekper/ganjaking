<?php
/**
 * The template for displaying the date element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-date.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author        themeComplete
 * @package       WooCommerce Extra Product Options/Templates
 * @version       5.0
 */

defined( 'ABSPATH' ) || exit;

?>
<li class="tmcp-field-wrap">
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php' ); ?>
	<?php
	// $picker_html contains internal generated HTML code 
	// that is already escaped where needed 
	echo apply_filters( 'wc_epo_kses', wp_kses( $picker_html, array(
		"label"  => array(
			"for" => TRUE,
		),
		"select" => array(
			"id"           => TRUE,
			"class"        => TRUE,
			"name"         => TRUE,
			"data-tm-date" => TRUE,
		),
		"option" => array(
			"selected" => TRUE,
			"value"    => TRUE,
		),
	) ), $picker_html, FALSE );
	?>
    <label for="<?php echo esc_attr( $id ); ?>" class="tm-epo-field-label tm-epo-datepicker-label-container">
        <input type="<?php echo esc_attr( $input_type ); ?>"
               class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-date tm-epo-datepicker"
               data-date-showon="<?php echo esc_attr( $showon ); ?>"
               data-date-defaultdate="<?php echo esc_attr( $defaultdate ); ?>"
			<?php
			if ( isset( $date_mask ) && ! empty( $date_mask ) ) { ?>
                data-mask="<?php echo esc_attr( $date_mask ); ?>"
			<?php }
			if ( isset( $date_placeholder ) && ! empty( $date_placeholder ) ) { ?>
                data-mask-placeholder="<?php echo esc_attr( $date_placeholder ); ?>"
			<?php }
			?>
               data-start-year="<?php echo esc_attr( $start_year ); ?>"
               data-end-year="<?php echo esc_attr( $end_year ); ?>"
               data-min-date="<?php echo esc_attr( $min_date ); ?>"
               data-max-date="<?php echo esc_attr( $max_date ); ?>"
               data-disabled-dates="<?php echo esc_attr( $disabled_dates ); ?>"
               data-enabled-only-dates="<?php echo esc_attr( $enabled_only_dates ); ?>"
               data-disabled-weekdays="<?php echo esc_attr( $disabled_weekdays ); ?>"
               data-disabled-months="<?php echo esc_attr( $disabled_months ); ?>"
               data-date-format="<?php echo esc_attr( $date_format ); ?>"
               data-date-theme="<?php echo esc_attr( $date_theme ); ?>"
               data-date-theme-size="<?php echo esc_attr( $date_theme_size ); ?>"
               data-date-theme-position="<?php echo esc_attr( $date_theme_position ); ?>"
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