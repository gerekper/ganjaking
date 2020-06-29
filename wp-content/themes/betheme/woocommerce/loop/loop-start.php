<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version		3.3.0
 */

$shop_classes = array();

if( is_woocommerce() ){

	// shop layout aplies ONLY for archives page (Shop)

	if( ! is_product() ){

		// layout

		if( $_GET && key_exists( 'mfn-shop', $_GET ) ){
			$shop_layout = esc_html( $_GET[ 'mfn-shop' ] ); // demo
		} else {
			$shop_layout = mfn_opts_get( 'shop-layout', 'grid' );
		}
		$shop_classes[] = $shop_layout;

		// isotope
		
		if( $shop_layout == 'masonry' ) $shop_classes[] = 'isotope';

	}

}

$shop_classes = implode( ' ', $shop_classes );
?>

<div class="products_wrapper isotope_wrapper">
	<ul class="products <?php echo esc_attr($shop_classes); ?>">
