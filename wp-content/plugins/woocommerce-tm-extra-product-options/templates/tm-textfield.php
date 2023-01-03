<?php
/**
 * The template for displaying the textfield element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-textfield.php
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
	<label class="tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $id ); ?>">
		<?php
		$input_args = [
			'nodiv'   => 1,
			'default' => $get_default_value,
			'type'    => 'decimal' === $input_type ? 'number' : $input_type,
			'tags'    => [
				'id'                  => $id,
				'name'                => $name,
				'class'               => $fieldtype . ' tm-epo-field tmcp-textfield',
				'data-price'          => '',
				'data-rules'          => $rules,
				'data-original-rules' => $original_rules,
				'data-rulestype'      => $rules_type,
				'data-freechars'      => $freechars,
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
		if ( 'number' === $input_type || 'decimal' === $input_type ) {
			$input_args['tags']['step'] = 'any';
			if ( 'decimal' === $input_type ) {
				$input_args['tags']['pattern']   = '[0-9]*';
				$input_args['tags']['inputmode'] = 'decimal';
			} else {
				$input_args['tags']['pattern']   = '[0-9]';
				$input_args['tags']['inputmode'] = 'numeric';
			}
			if ( isset( $min ) && '' !== $min ) {
				$input_args['tags']['min'] = $min;
			}
			if ( isset( $max ) && '' !== $max ) {
				$input_args['tags']['max'] = $max;
			}
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
