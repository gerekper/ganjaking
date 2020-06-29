<?php
/**
 * Group container template
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$collapse_feature = apply_filters( 'yith_wapo_enable_collapse_feature', get_option( 'yith_wapo_settings_enable_collapse_feature' ) == 'yes' );
$addons_collapsed = apply_filters( 'yith_wapo_show_addons_collapsed', get_option( 'yith_wapo_settings_show_addons_collapsed' ) == 'yes' );

$price_suffix = '';
if ( get_option( 'woocommerce_price_display_suffix' ) ) {
	$price_suffix = ' <small>' . get_option( 'woocommerce_price_display_suffix' ) . '</small>';
}

?>

<div id="yith_wapo_groups_container" class="yith_wapo_groups_container<?php
		echo $collapse_feature ? ' enable-collapse-feature' : '';
		echo apply_filters( 'yith_wapo_enable_alternate_collapse', false ) ? ' enable-alternate-collapse' : '';
		echo $addons_collapsed ? ' show-addons-collapsed' : '';
	?>"
	style="<?php echo apply_filters( 'yith_wapo_hide_groups_container', false ) ? 'display: none;' : '';?>">

	<?php

        do_action( 'yith_wapo_start_addon_list' );

		foreach ( $types_list as $single_type ) {
			$yith_wapo_frontend->printSingleGroupType( $product, $single_type );
		}

		$product_id = yit_get_base_product_id( $product );

		if ( function_exists('YITH_WCTM') && ! YITH_WCTM()->check_price_hidden(false,$product_id) ) {
			$product_display_price = yit_get_display_price( $product );
		} elseif ( !function_exists('YITH_WCTM') ) {
			$product_display_price = yit_get_display_price( $product );
		}

	    $product_tax_rates = wc_tax_enabled() ? WC_Tax::get_rates( $product->get_tax_class() ) : array();
		$current_tax_rate  = !! $product_tax_rates && is_array( $product_tax_rates ) ? current( $product_tax_rates ) : array();
		$tax_rates         = is_array( $current_tax_rate ) && isset( $current_tax_rate[ 'rate' ] ) ? $current_tax_rate[ 'rate' ] : false;

	?>

	<!--googleoff: index-->
	<div class="yith_wapo_group_total
		<?php echo ( get_option( 'yith_wapo_settings_show_add_ons_price_table' , 'no' ) == 'yes' ? 'yith_wapo_keep_show' : '' ); ?>"
		data-product-price="<?php echo esc_attr( $product_display_price ); ?>"
		data-product-id="<?php echo esc_attr( $product_id ); ?>"
		<?php if ( $tax_rates > 0 && apply_filters( 'wapo_enable_tax_string', false ) ) : ?>
			data-tax-rate="<?php echo esc_attr( $tax_rates ); ?>"
			data-tax-string="<?php echo apply_filters( 'wapo_total_tax_string', __( ' including VAT ', 'yith-woocommerce-product-add-ons' ) ); ?>"
		<?php endif; ?>
		>
		<table>
			<tr class="ywapo_tr_product_base_price">
				<td><?php echo apply_filters( 'yith_wapo_product_price_label', __( 'Product price' , 'yith-woocommerce-product-add-ons' ) ); ?></td>
				<td><div class="yith_wapo_group_product_price_total"><span class="price amount"></span><?php echo $price_suffix; ?></div></td>
			</tr>
			<tr class="ywapo_tr_additional_options">
				<td><?php echo apply_filters( 'yith_wapo_options_total_label', __( 'Additional options total:' , 'yith-woocommerce-product-add-ons' ) ); ?></td>
				<td><div class="yith_wapo_group_option_total"><span class="price amount"></span><?php echo $price_suffix; ?></div></td>
			</tr>
			<tr class="ywapo_tr_order_totals">
				<td><?php echo apply_filters( 'yith_wapo_order_total_label', __( 'Order total:' , 'yith-woocommerce-product-add-ons' ) ); ?></td>
				<td><div class="yith_wapo_group_final_total"><span class="price amount"></span><?php echo $price_suffix; ?></div></td>
			</tr>
		</table>
	</div>
	<!--googleon: index-->

	<!-- Hidden input for checking single page -->
	<input type="hidden" name="yith_wapo_is_single" id="yith_wapo_is_single" value="1">

</div>