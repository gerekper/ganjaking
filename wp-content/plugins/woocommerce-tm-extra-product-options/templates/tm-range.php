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
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $min, $max, $step, $pips, $noofpips, $show_picker_value, $element_id, $get_default_value, $name, $fieldtype, $rules, $original_rules, $rules_type ) ) :
	$min               = (string) $min;
	$max               = (string) $max;
	$step              = (string) $step;
	$pips              = (string) $pips;
	$noofpips          = (string) $noofpips;
	$show_picker_value = (string) $show_picker_value;
	$element_id        = (string) $element_id;
	$get_default_value = (string) $get_default_value;
	$name              = (string) $name;
	$fieldtype         = (string) $fieldtype;
	$rules             = (string) $rules;
	$original_rules    = (string) $original_rules;
	$rules_type        = (string) $rules_type;
	$liclass           = 'tmcp-field-wrap' . ( ( ! empty( $show_picker_value ) ) ? ' tm-show-picker-' . $show_picker_value : '' );
	?>
<li class="<?php echo esc_attr( $liclass ); ?>"><div class="tmcp-field-wrap-inner">
	<?php
	$input_args = [
		'nodiv' => 1,
		'type'  => 'div',
		'tags'  => [
			'class'                  => 'tc-col tm-range-picker',
			'data-min'               => $min,
			'data-max'               => $max,
			'data-step'              => $step,
			'data-pips'              => $pips,
			'data-noofpips'          => $noofpips,
			'data-show-picker-value' => $show_picker_value,
			'data-field-id'          => $element_id,
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
	<label class="tc-col tm-epo-field-label tm-show-picker-value" for="<?php echo esc_attr( $element_id ); ?>"></label>
	<?php
	$input_args = [
		'nodiv'   => 1,
		'default' => $get_default_value,
		'type'    => 'hidden',
		'tags'    => [
			'id'                  => $element_id,
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
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</div></li>
	<?php
endif;
