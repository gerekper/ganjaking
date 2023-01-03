<?php
/**
 * The template for displaying the range picker element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-range.php
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
<li class="tmcp-field-wrap <?php echo esc_attr( ( ! empty( $show_picker_value ) ) ? ' tm-show-picker-' . $show_picker_value : '' ); ?>">
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php'; ?>
	<?php
	$input_args = [
		'nodiv' => 1,
		'type'  => 'div',
		'tags'  => [
			'class'                  => 'tm-range-picker',
			'data-min'               => $min,
			'data-max'               => $max,
			'data-step'              => $step,
			'data-pips'              => $pips,
			'data-noofpips'          => $noofpips,
			'data-show-picker-value' => $show_picker_value,
			'data-field-id'          => $id,
			'data-start'             => $get_default_value,
		],
	];
	if ( 'yes' === $pips ) {
		$input_args['tags']['class'] .= ' pips';
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
	<label class="tm-epo-field-label tm-show-picker-value" for="<?php echo esc_attr( $id ); ?>"></label>
	<?php
	$input_args = [
		'nodiv'   => 1,
		'default' => $get_default_value,
		'type'    => 'hidden',
		'tags'    => [
			'id'                  => $id,
			'name'                => $name,
			'class'               => $fieldtype . ' tm-epo-field tmcp-textfield tmcp-range',
			'data-price'          => '',
			'data-rules'          => $rules,
			'data-original-rules' => $original_rules,
			'data-rulestype'      => $rules_type,
		],
	];
	if ( isset( $required ) && ! empty( $required ) ) {
		$input_args['tags']['required'] = true;
	}
	if ( ! empty( $tax_obj ) ) {
		$input_args['tags']['data-tax-obj'] = $tax_obj;
	}
	THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
	?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</li>
