<?php
/**
 * The template for displaying the product element thumbnails for the builder mode
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
$checked_option = [];
?>
<li class="tc-epo-element-product-li-container tm-hidden">
<?php
if ( is_array( $options ) ) {
	foreach ( $options as $option_key => $option ) {
		if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
			if ( $option['selected'] === $option['current'] ) {
				$checked_option = $option;
				break;
			}
		}
	}
	if ( ! empty( $checked_option ) ) {
		$option = $checked_option;
	}
}
require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php';
require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php';
?>
</li>
<?php
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
			if ( ! empty( $labelclass_start ) ) {
				echo '<span class="tc-epo-style-wrapper ' . esc_attr( $labelclass_start ) . '">';
			}
			$input_args = [
				'nodiv'      => 1,
				'type'       => 'input',
				'default'    => $product_id,
				'input_type' => 'radio',
				'tags'       => [
					'id'                   => $forid,
					'name'                 => $name,
					'class'                => $fieldtype . ' tc-epo-field-product tm-epo-field tmcp-radio',
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
			if ( isset( $option['counter'] ) && isset( $option['data_type'] ) ) {
				$input_args['tags']['data-counter'] = $option['counter'];
				$input_args['tags']['data-type']    = $option['data_type'];
			}
			if ( apply_filters( 'wc_epo_radio_print_required_attribute', true ) && isset( $required ) && ! empty( $required ) ) {
				$input_args['tags']['required'] = true;
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

			wc_get_template(
				'products/template-image.php',
				[
					'product_id' => $product_id,
				],
				THEMECOMPLETE_EPO_DISPLAY()->get_template_path(),
				THEMECOMPLETE_EPO_DISPLAY()->get_default_path()
			);

			echo '<span class="tc-label-wrap">';
			echo '<span class="tc-label tm-label">';
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '</span>';
			echo '</span>';

			$_product = wc_get_product( $product_id );
			do_action( 'wc_epo_product_thumbnail_before_price', $_product, $product_id );
			if ( '' !== $_product->get_price_suffix() ) {
				$textafterprice = '&nbsp;' . wp_kses_post( $_product->get_price_suffix() );
			}
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php';
			do_action( 'wc_epo_product_thumbnail_before_quantity', $_product, $product_id );
			include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php';
			do_action( 'wc_epo_product_thumbnail_after_quantity', $_product, $product_id );
			unset( $_product );
			?>
			</label>
		</li>
		<?php
	endforeach;
endif;
