<?php
/**
 * The template for displaying the upload element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-upload.php
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
if ( isset( $element_id, $class_label, $style, $name, $fieldtype, $rules, $original_rules, $rules_type, $saved_value, $allowed_mimes, $max_file_size_text ) ) :
	$element_id     = (string) $element_id;
	$class_label    = (string) $class_label;
	$style          = (string) $style;
	$name           = (string) $name;
	$fieldtype      = (string) $fieldtype;
	$rules          = (string) $rules;
	$original_rules = (string) $original_rules;
	$rules_type     = (string) $rules_type;
	$saved_value    = (string) $saved_value;
	?>
<li class="tmcp-field-wrap"><div class="tmcp-field-wrap-inner">
	<label class="tc-col tm-epo-field-label<?php echo esc_attr( $style ); ?><?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
		<span class="cpf-upload-wrap">
		<?php
		if ( ! empty( $upload_text ) ) {
			echo '<span class="cpf-upload-text">' . esc_html( $upload_text ) . '</span>';
		}
		$input_args = [
			'nodiv'   => 1,
			'default' => '',
			'type'    => 'file',
			'tags'    => [
				'id'                  => $element_id,
				'name'                => $name,
				'class'               => $fieldtype . ' tm-epo-field tmcp-upload',
				'data-price'          => '',
				'data-rules'          => $rules,
				'data-original-rules' => $original_rules,
				'data-rulestype'      => $rules_type,
				'data-file'           => $saved_value,
				'accept'              => $allowed_mimes,
			],
		];
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
			isset( $tm_element_settings ) ? $tm_element_settings['type'] : '',
			isset( $args ) ? $args : [],
		);

		THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
		?>
		</span>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity.php'; ?>
	<small class="tc-max-file-size"><?php echo esc_html( $max_file_size_text ); ?></small>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</div></li>
	<?php
endif;
