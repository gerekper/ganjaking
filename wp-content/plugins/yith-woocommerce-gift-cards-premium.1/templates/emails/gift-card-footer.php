<?php
/**
 * Add a footer for the gift card email
 *
 * @author YITHEMES
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$shop_name = apply_filters( 'yith_ywgc_email_shop_name', $shop_name );
$shop_link = apply_filters( 'yith_ywgc_email_shop_link', get_permalink( wc_get_page_id( 'shop' ) ) );
if ( ! $shop_name || ! $shop_link ) {
	return;
}
?>

<div class="ywgc-footer">
	<a target="_blank" class="center-email"
	   href="<?php echo $shop_link; ?>"><?php echo $shop_name; ?></a>
</div>
