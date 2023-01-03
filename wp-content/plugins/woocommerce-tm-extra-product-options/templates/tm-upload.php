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
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="tmcp-field-wrap">
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php'; ?>
	<label class="tm-epo-field-label<?php echo esc_attr( $style ); ?>" for="<?php echo esc_attr( $id ); ?>">
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
				'id'                  => $id,
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
		<small class="tc-max-file-size"><?php echo esc_html( $max_file_size_text ); ?></small>
	</label>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
	<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php'; ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
</li>
