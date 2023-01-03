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
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="tmcp-field-wrap">
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php'; ?>
	<label for="<?php echo esc_attr( $id ); ?>" class="tm-epo-field-label tm-epo-timepicker-label-container<?php echo esc_attr( $class_label ); ?>">
	<?php
	$input_args = [
		'nodiv'      => 1,
		'default'    => $get_default_value,
		'type'       => 'input',
		'input_type' => $input_type,
		'tags'       => [
			'id'                       => $id,
			'name'                     => $name,
			'class'                    => $fieldtype . $button_style . ' tm-epo-field tmcp-time',
			'data-price'               => '',
			'data-rules'               => $rules,
			'data-original-rules'      => $original_rules,
			'data-mask'                => $time_mask,
			'data-mask-placeholder'    => $time_placeholder,
			'data-min-time'            => $min_time,
			'data-max-time'            => $max_time,
			'data-time-format'         => $time_format,
			'data-custom-time-format'  => $custom_time_format,
			'data-time-theme'          => $time_theme,
			'data-time-theme-size'     => $time_theme_size,
			'data-time-theme-position' => $time_theme_position,
			'data-tranlation-hour'     => $tranlation_hour,
			'data-tranlation-minute'   => $tranlation_minute,
			'data-tranlation-second'   => $tranlation_second,
			'inputmode'                => 'none',
		],
	];
	if ( 'time' === $input_type ) {
		unset( $input_args['tags']['data-mask'] );
		unset( $input_args['tags']['data-mask-placeholder'] );
	}
	if ( isset( $required ) && ! empty( $required ) ) {
		$input_args['tags']['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$input_args['tags']['data-tax-obj'] = $tax_obj;
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

	THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
	?>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</li>
