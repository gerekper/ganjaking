<?php
/**
 * The template for displaying the textarea element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-textarea.php
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
if ( isset( $class_label, $element_id, $get_default_value, $name, $fieldtype, $rules, $original_rules, $rules_type, $freechars ) ) :
	$class_label       = (string) $class_label;
	$element_id        = (string) $element_id;
	$get_default_value = (string) $get_default_value;
	$name              = (string) $name;
	$fieldtype         = (string) $fieldtype;
	$rules             = (string) $rules;
	$original_rules    = (string) $original_rules;
	$rules_type        = (string) $rules_type;
	$freechars         = (string) $freechars;
	?>
<li class="tmcp-field-wrap"><div class="tmcp-field-wrap-inner">
	<label class="tc-col tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
	<?php
	$input_args = [
		'nodiv'   => 1,
		'default' => $get_default_value,
		'type'    => 'textarea',
		'tags'    => [
			'id'                  => $element_id,
			'name'                => $name,
			'class'               => $fieldtype . ' tm-epo-field tmcp-textarea',
			'data-price'          => '',
			'data-rules'          => $rules,
			'data-original-rules' => $original_rules,
			'data-rulestype'      => $rules_type,
			'data-freechars'      => $freechars,
			'rows'                => 5,
			'cols'                => 20,
		],
	];
	if ( isset( $placeholder ) && '' !== $placeholder ) {
		$input_args['tags']['placeholder'] = $placeholder;
	}
	if ( isset( $min_chars ) && '' !== $min_chars ) {
		$input_args['tags']['minlength'] = $min_chars;
	}
	if ( isset( $max_chars ) && '' !== $max_chars ) {
		$input_args['tags']['maxlength'] = $max_chars;
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
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</div></li>
	<?php
endif;
