<?php
/**
 * Email for customer notification of gift card recevied
 *
 * @author YITHEMES
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php do_action ( 'woocommerce_email_header', $email_heading, $email ); ?>

<p class="center-email">
    <?php echo apply_filters ( 'ywgc_gift_card_email_before_preview', $introductory_text, $gift_card ); ?>
</p>

<div class="ywgc-delivered-gift-card-image-container">

	<img class="ywgc-delivered-gift-card-image" src="<?php echo YITH_YWGC_ASSETS_IMAGES_URL . 'delivered-gift-card.png'?>" alt="">

</div>


<?php do_action ( 'woocommerce_email_footer' ); ?>
