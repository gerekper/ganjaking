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


<?php do_action ( 'woocommerce_email_footer' ); ?>
