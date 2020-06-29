<?php
/**
 * Show selected item
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 *
 * @author 		YITHEMES
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'wc_get_product_attachment_props' ) ) {
	return;
}

$is_less_than_2_7 = version_compare( WC()->version, '2.7', '<' );

$wcp_component_item = $composite_product->getComponentItemByKey( $key );

$available_variations = array();

$product_parent_id = yit_get_base_product_id( $product );

$post_object = get_post ( $product->get_id() );

$product_type = $product->get_type();

if( $product_type == 'variable' ) {
	$get_variations       = sizeof( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', get_option( 'yith_wcp_settings_ajax_variation_treshold' , 30 ), $product );
	$available_variations = $get_variations ? $product->get_available_variations() : false;
	$attributes           = $product->get_variation_attributes();
	$attribute_keys       = array_keys( $attributes );
	$selected_attributes  = ( $is_less_than_2_7 ) ? $product->get_variation_default_attributes() : apply_filters( 'woocommerce_product_default_attributes', array_filter( (array) maybe_unserialize( $product->get_default_attributes() ) ), $product );
}

echo '<div class="ywcp_inner_selected_container product" data-product_id="'.absint( $product_parent_id ).'" data-product_variations="'.htmlspecialchars( json_encode( $available_variations ) ).'" data-selected-price="'.esc_attr( yit_get_display_price( $product ) ).'" data-product-type="'.$product_type.'">';

	echo '<div class="ywcp_image_container images">';

	$props = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
	$image            = get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
		'title'	 => $props['title'],
		'alt'    => $props['alt'],
	) );

	echo apply_filters( 'woocommerce_single_product_image_html', $image, $post->ID );

	echo '</div>';
	
	echo '<div class="ywcp_product_info">';
		echo '<div class="ywcp_product_title">'.$product->get_title().'</div>';
		echo '<div class="ywcp_product_price">'.$product->get_price_html().'</div>';
		echo YITH_WCP_Frontend::getAvailabilityHtml( $product );
		echo '<div class="ywcp_product_link">';
		if( apply_filters( 'ywcp_use_quick_view' , defined('YITH_WCQV_PREMIUM') ) ) {
			YITH_WCQV_Frontend()->yith_add_quick_view_button( $product_parent_id );
		} else {
			echo '<a href="'.esc_url( $product->get_permalink() ).'" target="_blank">'.apply_filters( 'yith_wcp_product_link_text' , __( 'show the product page' , 'yith-composite-products-for-woocommerce' ) ).'</a>';
		}
		echo '</div>';
		echo $product->get_sku() != '' ? '<div class="ywcp_product_sku">' . __( 'SKU', 'woocommerce' ) . ': '. apply_filters( 'yith_wcp_product_sku', $product->get_sku() ) . '</div>' : '';
		echo '<div class="ywcp_product_short_description">'. apply_filters( 'yith_wcp_product_description', trim( strip_tags( ( $post_object->post_excerpt ) ) ), $post_object ) . '</div>';
	echo '</div>';

	if ( $product_type == 'variable' ) : ?>
	
		<table class="variations" cellspacing="0">
			<tbody>
			<?php foreach ( $attributes as $attribute_name => $options ): ?>
				<tr>
					<td class="label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
					<td class="value">
						<?php
						$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name );
						wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
						echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
						?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	
		<div class="woocommerce-variation single_variation"></div>
		<div class="woocommerce-variation-add-to-cart variations_button">
			<input type="hidden" class="variation_id" name="ywcp_variation_id[<?php echo esc_attr( $key ); ?>]" value=""/>
	
	<?php elseif ( $product_type == 'variation' ) : ?>

		<table class="variations" cellspacing="0">
			<tbody>
			<?php foreach ( $product->variation_data as $attribute_name => $option ) :  ?>
				<tr>
					<td class="label"><label ><?php echo wc_attribute_label( str_replace( 'attribute_' , '' , $attribute_name ) ); ?></label></td>
					<td class="value">
						<?php echo $option?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>

		<div class="woocommerce-variation single_variation"></div>
		<div class="woocommerce-variation-add-to-cart variations_button">
			<input type="hidden" class="variation_id" name="ywcp_variation_id[<?php echo esc_attr( $key ); ?>]" value=""/>

	<?php endif;

	if ( ! $product->is_sold_individually() && ! ( $wcp_component_item['min_quantity'] == 1 && $wcp_component_item['max_quantity'] == 1 ) ) {

		woocommerce_quantity_input( array(
			'min_value'   => apply_filters( 'ywcp_woocommerce_quantity_input_min', max( $wcp_component_item['min_quantity'], 1 ), $product ),
			'max_value'   => apply_filters( 'ywcp_woocommerce_quantity_input_max', $wcp_component_item['max_quantity'], $product ),
			'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : max( $wcp_component_item['min_quantity'], 1 ) ),
			'input_name'  => 'ywcp_quantity[' . esc_attr( $key ) . ']',
			'is_composite_products' => true, // Requested for WooCommerce Advanced Quantity support
		), $product );
	
	} else { echo '<input type="hidden" class="qty" name="' . 'ywcp_quantity[' . esc_attr( $key ) . ']' . '" value="1"/>'; }
	
	echo '<input type="hidden" name="ywcp_selected_product_value['.esc_attr( $key ).']" value="'.$post->ID.'" />';

echo '</div>';
