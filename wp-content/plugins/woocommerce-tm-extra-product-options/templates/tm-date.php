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
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

?>
<li class="tmcp-field-wrap">
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php'; ?>
	<?php
	// $picker_html contains internal generated HTML code
	// that is already escaped where needed
	// see: /woocommerce-tm-extra-product-options/includes/fields/class-themecomplete-epo-fields-date.php
	// phpcs:ignore WordPress.Security.EscapeOutput
	echo apply_filters(
		'wc_epo_kses',
		wp_kses(
			$picker_html,
			[
				'label'  => [
					'for' => true,
				],
				'select' => [
					'id'           => true,
					'class'        => true,
					'name'         => true,
					'data-tm-date' => true,
				],
				'option' => [
					'selected' => true,
					'value'    => true,
				],
			]
		),
		$picker_html,
		false
	);
	?>
	<label for="<?php echo esc_attr( $id ); ?>" class="tm-epo-field-label tm-epo-datepicker-label-container<?php echo esc_attr( $class_label ); ?>">
	<?php
	$input_args = [
		'nodiv'   => 1,
		'default' => $get_default_value,
		'type'    => $input_type,
		'tags'    => [
			'id'                       => $id,
			'name'                     => $name,
			'class'                    => $fieldtype . ' tm-epo-field tmcp-date tm-epo-datepicker',
			'data-price'               => '',
			'data-rules'               => $rules,
			'data-original-rules'      => $original_rules,
			'data-date-showon'         => $showon,
			'data-date-defaultdate'    => $defaultdate,
			'data-start-year'          => $start_year,
			'data-end-year'            => $end_year,
			'data-min-date'            => $min_date,
			'data-max-date'            => $max_date,
			'data-disabled-dates'      => $disabled_dates,
			'data-enabled-only-dates'  => $enabled_only_dates,
			'data-exlude-disabled'     => $exlude_disabled,
			'data-disabled-weekdays'   => $disabled_weekdays,
			'data-disabled-months'     => $disabled_months,
			'data-date-format'         => $date_format,
			'data-date-theme'          => $date_theme,
			'data-date-theme-size'     => $date_theme_size,
			'data-date-theme-position' => $date_theme_position,
			'inputmode'                => 'none',
		],
	];
	if ( isset( $required ) && ! empty( $required ) ) {
		$input_args['tags']['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$input_args['tags']['data-tax-obj'] = $tax_obj;
	}
	if ( isset( $date_mask ) && ! empty( $date_mask ) ) {
		$input_args['tags']['data-mask'] = $date_mask;
	}
	if ( isset( $date_placeholder ) && ! empty( $date_placeholder ) ) {
		$input_args['tags']['data-mask-placeholder'] = $date_placeholder;
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
