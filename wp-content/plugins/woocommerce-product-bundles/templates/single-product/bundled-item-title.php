<?php
/**
 * Bundled Item Title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-title.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * Note: Bundled product properties are accessible via '$bundled_item->product'.
 *
 * @version 5.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $title === '' ) {
	return;
}

?><h4 class="bundled_product_title product_title"><?php
		$optional = $optional && $bundle->contains( 'mandatory' ) ? apply_filters( 'woocommerce_bundles_optional_bundled_item_suffix', __( 'optional', 'woocommerce-product-bundles' ), $bundled_item, $bundle ) : '';
		$title    = '<span class="bundled_product_title_inner">' . WC_PB_Helpers::format_product_shop_title( $title, $quantity, '', $optional ) . '</span>';
		$link     = $permalink ? apply_filters( 'woocommerce_bundled_item_link_html', ' <span class="bundled_product_title_link"><a class="bundled_product_permalink" href="' . $permalink . '" target="_blank" aria-label="' . __( 'View product', 'woocommerce-product-bundles' ) . '"></a></span>', $bundled_item, $bundle ) : '';
		echo $title . $link;
?></h4>
