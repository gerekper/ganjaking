<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$default_gift_product = wc_get_product( get_option ( YWGC_PRODUCT_PLACEHOLDER ) );

do_action( 'yith_ywgc_gift_card_preview_end', $default_gift_product ); //Load the modal content

?>

<form class="gift-cards_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( yit_get_prop($default_gift_product, 'id' )); ?>">

    <input type="hidden" name="ywgc-is-digital" value="1" />
    <input type='hidden' name='ywgc_has_custom_design' value='1' />


    <?php if ( ! $product->is_purchasable() ) : ?>
		<p class="gift-card-not-valid">
			<?php _e( "This product cannot be purchased", 'yith-woocommerce-gift-cards' ); ?>
		</p>
	<?php else : ?>

        <?php do_action( 'yith_ywgc_gift_card_design_section', $default_gift_product ); ?>

        <?php do_action( 'yith_ywgc_gift_card_delivery_info_section', $default_gift_product ); ?>

	<?php endif; ?>

</form>
