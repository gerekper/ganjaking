<?php
/**
 * Component List
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

global $product;

$product_id					= yit_get_base_product_id( $product );
$product_display_price		= yit_get_display_price( $product );
$component_data				= $product->getComponentsData();
$product_per_item_pricing	= $product->isPerItemPricing();
$layout_options				= $product->getLayoutOptions();

$re_edit_cart_key_data = array();
if ( isset( $_REQUEST['ywcp_cart_item_key'] ) ) {
	if( isset( WC()->cart->cart_contents[ $_REQUEST['ywcp_cart_item_key'] ] ) ) {
		$re_edit_cart_key_data = WC()->cart->cart_contents[ $_REQUEST['ywcp_cart_item_key'] ][ 'yith_wcp_component_data' ];
	}
}

$title_tag = esc_html( apply_filters( 'ywcp_composite_list_title_tag', 'h3' ) );
 
?>

<div class="ywcp_components_container ywcp_<?php echo esc_attr( $layout_options ) ?>"
	data-product-id="<?php echo $product_id; ?>"
	data-per-item-price="<?php echo $product_per_item_pricing ? 1 : 0;?>"
	data-layout-option="<?php echo esc_attr( $layout_options ); ?>"
	data-dependencies="<?php echo esc_attr( json_encode( $product->getDependenciesDataComponentsDetails() ) ); ?>">

	<?php

	$component_index = 0;
	foreach ( $component_data as $key => $component_item ) {
		
		// Variations Discount Fix
		global $ywcp_variations_discount_fix_info;
		$ywcp_variations_discount_fix_info = array(
			'composite_product_id'	=> $product_id,
			'composite_item_key'	=> $key
		);

		$is_first_opened = $component_index == 0 && $layout_options == 'accordion';

		$thumb = isset( $component_item['thumb'] ) && $component_item['thumb'] ? 1 : 0;
		$required = $component_item['required'] ? 1 : 0;
		$exclusive = $component_item['exclusive'] ? 1 : 0;
		$sold_individually = isset( $component_item['sold_individually'] ) && $component_item['sold_individually'] ? 1 : 0;

		$title = wptexturize($component_item['name']);
		$required_title = apply_filters( 'yith_wcp_required_title', '<abbr class="required" title="required">* <span>' . _x( 'Required', 'front end tag abbr text', 'yith-composite-products-for-woocommerce' ) . '</span></abbr>' );
		$optional_title = apply_filters( 'yith_wcp_optional_title', '<abbr class="optional" title="optional">* <span>' . _x( 'Optional', 'front end tag abbr text', 'yith-composite-products-for-woocommerce' ) . '</span></abbr>' );
		$html_title = $title . ( $required ? $required_title : $optional_title );

		echo '<div id="ywcp_component_' . $key . '" class="ywcp_components_single_item '
				. ( $required ? 'ywcp_components_required' : '' ) . ' '
				. ( $is_first_opened ? 'ywcp_selection_opened' : '' )
				. '" data-component-key="' . $key
				. '" data-required="' . $required
				. '" data-exclusive="' . $exclusive
				. '" data-title="' . esc_attr( $title )
				. '" data-discount="' . $component_item['discount']
				. '" data-sold-individually="' . $sold_individually . '">';

			echo '<' . $title_tag . '>' . $html_title;
				// Accordion button
				if ( $layout_options == 'accordion' ) {
					$show_composite_options_img  = apply_filters( 'ywcp_composite_img_toggle_open', YITH_WCP_ASSETS_URL . '/css/images/down.png' );
					$close_composite_options_img = apply_filters( 'ywcp_composite_img_toggle_close', YITH_WCP_ASSETS_URL . '/css/images/up.png' );
					echo '<a href="#" class="ywcp_selection_open"><img alt="" src="' . $show_composite_options_img . '" /></a>';
					echo '<a href="#" class="ywcp_selection_close"><img alt="" src="' . $close_composite_options_img . '" /></a>';
				}
			echo '</' . $title_tag . '>';

			if ( $component_item['description'] ) {
				echo '<p class="ywcp_component_description">'.$component_item['description'].'</p>';
			}

			// Options
			echo '<div class="ywcp_component_options_list_container">';
				$re_edit_name_parent = 'ywcp_edit_cart_item_parent_'.esc_attr( $key );
				$re_edit_name_variation = 'ywcp_edit_cart_item_variation_'.esc_attr( $key );
				if ( isset( $re_edit_cart_key_data['selection_data'][ $key ] ) ) {
					echo '<input type="hidden" class="'.$re_edit_name_parent.'" name="'.$re_edit_name_parent.'" value="'.esc_attr( $re_edit_cart_key_data['selection_data'][ $key ] ).'"/>';
				}
				if ( ! empty( $re_edit_cart_key_data['selection_variation_data'][ $key ] ) ) {
					echo '<input type="hidden" class="'.$re_edit_name_variation.'" name="'.$re_edit_name_variation.'" value="'.esc_attr( $re_edit_cart_key_data['selection_variation_data'][ $key ] ).'"/>';
				}
				YITH_WCP()->frontend->printOptions( $product_id, $key, $component_item, 1, $re_edit_cart_key_data );
			echo '</div>';

			// Selection
			echo '<div class="ywcp_component_options_selection_container' . ( $thumb ? ' ywcp_thumb_replace' : '' ) . '"></div>';
			echo '<a href="#" class="ywcp_selection_clear">' . __( 'Clear selection', 'yith-composite-products-for-woocommerce' ) . '</a>';
			echo '<div class="ywcp_component_subtotal">' . __( 'Subtotal:', 'yith-composite-products-for-woocommerce' ) . ' <span class="amount"></span></div>';

		 echo '</div>';

		$component_index++;

	}

	if ( $layout_options == 'step' ) : ?>
		
		<div class="ywcp_step_navigation">
			<div class="ywcp_step_current_info"></div>
			<div class="ywcp_step_prev"><a href="#"><?php echo apply_filters( 'ywcp_step_prev_label', __( 'Prev', 'yith-composite-products-for-woocommerce' ) ) ?></a></div>
			<div class="ywcp_step_next"><a href="#"><?php echo apply_filters( 'ywcp_step_next_label', __( 'Next', 'yith-composite-products-for-woocommerce' ) ) ?></a></div>
			<div class="ywcp_clear"></div>
		</div>
		
	<?php endif; ?>

</div>

<div class="ywcp_wcp_group_total" data-productprice="<?php echo esc_attr( $product_display_price ); ?>" data-product-price="<?php echo esc_attr( $product_display_price ); ?>">
	<table>
		<?php if ( $product_display_price > 0 ) : ?>
			<tr>
				<td><?php echo apply_filters( 'ywcp_product_base_price_text', __( 'Product Base Price:', 'yith-composite-products-for-woocommerce' ) ); ?></td>
				<td><div class="yith_wcp_group_option_total_base_price"><?php echo wc_price( $product_display_price ); ?></div></td>
			</tr>
		<?php endif; ?>
		<tr id="ywcp_wcp_tr_wapo_option_total" class="ywcp_wapo_total_hided">
			<td><?php _e( 'Add-Ons Option Total:', 'yith-composite-products-for-woocommerce' ) ?></td>
			<td><div class="yith_wcp_wapo_add_ons_total"><span class="woocommerce-Price-amount amount"></span></div></td>
		</tr>
		<?php if ( $product_per_item_pricing ) : ?>
			<tr id="ywcp_wcp_tr_component_total">
				<td><?php _e( 'Component Options Total:', 'yith-composite-products-for-woocommerce' ) ?></td>
				<td><div class="yith_wcp_component_total"><span class="woocommerce-Price-amount amount"></span></div></td>
			</tr>
		<?php endif; ?>
		<tr id="ywcp_wcp_tr_order_total">
			<td><?php _e( 'Order Total:', 'yith-composite-products-for-woocommerce' ) ?></td>
			<td><div class="yith_wcp_group_final_total"><span class="woocommerce-Price-amount amount"></span></div></td>
		</tr>
	</table>
</div>

<div class="ywcp_customer_advice">
	<p><?php _e( 'The add to cart button is disabled because the following component is not selected:', 'yith-composite-products-for-woocommerce' ) ?><span class="ywcp_customer_advice_component_list"></span></p>
</div>

<?php $ywcp_variations_discount_fix_info = null; ?>
