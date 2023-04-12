<?php
/**
 * The template for displaying the product element checkboxes for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-checkbox.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $options ) ) {
	$options = [];
}
if ( is_array( $options ) ) :
	foreach ( $options as $option_key => $option ) :

		$product_id = $option['value_to_show'];

		$forid   = uniqid( $id . '_' );
		$checked = false;
		if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
			if ( $option['selected'] === $option['current'] ) {
				$checked = true;
			}
		}
		?>
		<li class="tmcp-field-wrap tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>">
			<label class="tm-epo-field-label" for="<?php echo esc_attr( $forid ); ?>">
			<?php
			$input_args = [
				'nodiv'   => 1,
				'default' => $option['_default_value_counter'],
				'type'    => 'hidden',
				'tags'    => [
					'name'  => $name . '_' . $option['_default_value_counter'] . '_counter',
					'class' => 'tc-epo-field-product-counter',
				],
			];
			THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );

			if ( ! empty( $labelclass_start ) ) {
				echo '<span class="tc-epo-style-wrapper ' . esc_attr( $labelclass_start ) . '">';
			}
			$input_args = [
				'nodiv'      => 1,
				'type'       => 'input',
				'default'    => $product_id,
				'input_type' => 'checkbox',
				'tags'       => [
					'id'                   => $forid,
					'name'                 => $name . '_' . $option['_default_value_counter'],
					'class'                => $fieldtype . ' tc-epo-field-product tc-epo-field-product-checkbox tm-epo-field tmcp-checkbox',
					'data-price'           => $option['data_price'],
					'data-price-html'      => $option['data_price_html'],
					'data-rules'           => $option['data_rules'],
					'data-original-rules'  => $option['data_original_rules'],
					'data-rulestype'       => $option['data_rulestype'],
					'data-no-price-change' => '1',
					'data-no-price'        => ( ! $priced_individually ),
				],
			];
			if ( ! empty( $option['tax_obj'] ) ) {
				$input_args['tags']['data-tax-obj'] = $option['tax_obj'];
			}
			if ( apply_filters( 'wc_epo_checkbox_print_required_attribute', true ) && isset( $required ) && ! empty( $required ) ) {
				$input_args['tags']['required'] = true;
			}
			if ( isset( $option['data_type'] ) && isset( $option['_default_value_counter'] ) ) {
				$input_args['tags']['data-counter'] = $option['_default_value_counter'];
				$input_args['tags']['data-type']    = $option['data_type'];
			}
			if ( true === $checked ) {
				$input_args['tags']['checked'] = 'checked';
			}
			if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
				$input_args['tags'] = array_merge( $input_args['tags'], $element_data_attr );
			}
			THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );

			if ( ! empty( $labelclass ) ) {
				echo '<span';
				echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
				echo ' data-for="' . esc_attr( $forid ) . '"></span>';
			}
			if ( ! empty( $labelclass_end ) ) {
				echo '</span>';
			}
			echo '<span class="tc-label-wrap">';
			echo '<span class="tc-label tm-label">';
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</span>';
			echo '</span>';
			?>
			</label>
			<?php
			$_product = wc_get_product( $product_id );
			if ( '' !== $_product->get_price_suffix() ) {
				$textafterprice = '&nbsp;' . wp_kses_post( $_product->get_price_suffix() );
			}
			unset( $_product );
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php';
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php';
			?>
			<div class="tc-epo-element-product-li-container tm-hidden">
			<?php
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php';
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php';
			?>
			</div>
		</li>
		<?php
	endforeach;
endif;
?>
