<?php
/**
 * The template for displaying the product element when the mode is single product
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-hidden.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $layout_mode, $class_label, $element_id, $name, $fieldtype, $placeholder, $priced_individually, $options, $quantity_min ) ) :
	$layout_mode         = (string) $layout_mode;
	$class_label         = (string) $class_label;
	$element_id          = (string) $element_id;
	$name                = (string) $name;
	$fieldtype           = (string) $fieldtype;
	$placeholder         = (string) $placeholder;
	$quantity_min        = (string) $quantity_min;
	$priced_individually = (bool) $priced_individually;

	$checked_option = [];
	?>
<li class="tmcp-field-wrap tc-product-hidden"><div class="tmcp-field-wrap-inner">
	<div class="tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>">
		<label class="tm-epo-field-label<?php echo esc_attr( $class_label ); ?>" for="<?php echo esc_attr( $element_id ); ?>">
		<?php
		$input_args = [
			'nodiv'      => 1,
			'type'       => 'input',
			'input_type' => 'checkbox',
			'tags'       => [
				'id'                   => $element_id,
				'name'                 => $name,
				'class'                => $fieldtype . ' tc-epo-field-product tc-epo-field-product-hidden tm-epo-field tmcp-checkbox',
				'data-price'           => '',
				'data-rules'           => '',
				'data-original-rules'  => '',
				'data-placeholder'     => $placeholder,
				'data-no-price-change' => '1',
				'data-no-price'        => ( ! $priced_individually ),
			],
		];

		if ( apply_filters( 'wc_epo_checkbox_print_required_attribute', true ) && isset( $required ) && ! empty( $required ) ) {
			$input_args['tags']['required'] = true;
		}

		if ( is_array( $options ) ) {
			foreach ( $options as $option ) {

				$input_args['default'] = $option['value_to_show'];
				if ( ! empty( $option['tax_obj'] ) ) {
					$input_args['tags']['data-tax-obj'] = $option['tax_obj'];
				}

				$checked = false;
				if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
					if ( $option['selected'] === $option['current'] || $quantity_min > 0 ) {
						$checked = true;
						if ( ! isset( $_REQUEST[ $name . '_quantity' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( '' === $quantity_min ) {
								$_REQUEST[ $name . '_quantity' ] = 1;
							} else {
								$_REQUEST[ $name . '_quantity' ] = $quantity_min;
							}
						}
					}
				}
				if ( $checked ) {
					$checked_option = $option;
				}
				if ( true === $checked ) {
					$input_args['tags']['checked'] = 'checked';
				}

				if ( isset( $option['data_price'] ) ) {
					$input_args['tags']['data-price'] = $option['data_price'];
				}
				if ( isset( $option['tm_tooltip_html'] ) && ! empty( $option['tm_tooltip_html'] ) ) {
					$input_args['tags']['data-tm-tooltip-html'] = $option['tm_tooltip_html'];
				}
				if ( isset( $option['data_rules'] ) ) {
					$input_args['tags']['data-rules'] = $option['data_rules'];
				}
				if ( isset( $option['data_original_rules'] ) ) {
					$input_args['tags']['data-original-rules'] = $option['data_original_rules'];
				}
				if ( isset( $option['data_rulestype'] ) ) {
					$input_args['tags']['data-rulestype'] = $option['data_rulestype'];
				}
				if ( isset( $option['data_text'] ) ) {
					$input_args['tags']['data-text'] = $option['data_text'];
				}
				if ( isset( $option['data_type'] ) ) {
					$input_args['tags']['data-type'] = $option['data_type'];
				}
				if ( isset( $option['data_hide_amount'] ) ) {
					$input_args['tags']['data-hide-amount'] = $option['data_hide_amount'];
				}
			}
			if ( ! empty( $checked_option ) ) {
				$option = $checked_option;
			}
		}
		if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
			$input_args['tags'] = array_merge( $input_args['tags'], $element_data_attr );
		}
		THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
		?>
		</label>
		<?php
		require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php';
		require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php';
		?>
	</div>
</div></li>
<li class="tc-epo-element-product-li-container tm-hidden">
	<?php
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php';
	require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php';
	?>
</li>
	<?php
endif;
