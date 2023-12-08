<?php
/**
 * The template for displaying the product element radio buttons for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-radio.php
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
$saved_option = [];
if ( isset( $options ) && is_array( $options ) && isset( $element_id ) && isset( $layout_mode ) && isset( $name ) && isset( $fieldtype ) && isset( $priced_individually ) ) :
	foreach ( $options as $option_key => $option ) :

		$product_id = $option['value_to_show'];

		$forid   = uniqid( $element_id . '_' );
		$checked = false;
		if ( isset( $option['selected'] ) && isset( $option['current'] ) ) {
			if ( $option['selected'] === $option['current'] ) {
				$checked      = true;
				$saved_option = $option;
			}
		}
		?>
		<li class="tmcp-field-wrap tc-epo-element-product-holder tc-epo-element-product-<?php echo esc_attr( $layout_mode ); ?>">
			<div class="tmcp-field-wrap-inner">
				<div class="tc-col tc-field-label-wrap">
				<label class="tc-col tm-epo-field-label" for="<?php echo esc_attr( $forid ); ?>">
				<?php
				echo '<span class="tc-label-wrap">';
				if ( ! empty( $labelclass_start ) ) {
					echo '<span class="tc-epo-style-wrapper tc-first ' . esc_attr( $labelclass_start ) . ' tc-radio">';
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
				if ( apply_filters( 'wc_epo_radio_print_required_attribute', true ) && isset( $required ) && ! empty( $required ) ) {
					$input_args['tags']['required'] = true;
				}
				if ( isset( $option['counter'] ) && isset( $option['data_type'] ) ) {
					$input_args['tags']['data-counter'] = $option['counter'];
					$input_args['tags']['data-type']    = $option['data_type'];
				}
				if ( true === $checked ) {
					$input_args['tags']['checked'] = 'checked';
				}
				if ( isset( $element_data_attr ) && is_array( $element_data_attr ) ) {
					$input_args['tags'] = array_merge( $input_args['tags'], $element_data_attr );
				}
				echo '<span class="tc-input-wrap tc-epo-style-space">';
				THEMECOMPLETE_EPO_HTML()->create_field( $input_args, true );
				echo '</span>';
				if ( ! empty( $labelclass ) ) {
					echo '<span';
					echo ' class="tc-label tm-epo-style ' . esc_attr( $labelclass ) . '"';
					echo ' data-for="' . esc_attr( $forid ) . '"></span>';
				}
				if ( ! empty( $labelclass_end ) ) {
					echo '</span>';
				}
				echo '<span class="tc-col tc-label radio-image-label">';
				echo '<span class="tc-label-inner tcwidth tcwidth-100">';
				echo '<span class="tc-label-text">';
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $option['text'] ), $option['text'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</span>';
				echo '</span>';
				echo '</span>';
				echo '</span>';
				?>
				</label>
				<?php require THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php'; ?>
				</div>
				<?php include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-quantity-hidden.php'; ?>
			</div>
		</li>
		<?php
	endforeach;
endif;
?>
<li class="tc-epo-element-product-li-container tm-hidden">
<?php
$option = $saved_option;
require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-variation.php';
require THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-container.php';
?>
</li>
