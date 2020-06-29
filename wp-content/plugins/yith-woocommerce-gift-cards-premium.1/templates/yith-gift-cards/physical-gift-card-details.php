<?php
/**
 * Gift Card product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

?>

<input type="hidden" name="ywgc-is-physical" value="1" />

<div class="gift-card-content-editor step-content">

    <?php if ( 'yes' == get_option('ywgc_ask_sender_name_physical', 'no') ) : ?>
        <label for="ywgc-sender-info-title">
            <?php echo get_option( 'ywgc_sender_info_title' , __( "YOUR INFO", 'yith-woocommerce-gift-cards') ); ;?>
        </label>

        <div class="ywgc-sender-name">
            <label for="ywgc-sender-name"><?php echo apply_filters('ywgc_sender_name_label',__( "Name: ", 'yith-woocommerce-gift-cards' )); ?></label>
            <input type="text" name="ywgc-sender-name" id="ywgc-sender-name" value="<?php echo apply_filters('ywgc_sender_name_value','') ?>"
                   placeholder="<?php _e( "ENTER YOUR NAME", 'yith-woocommerce-gift-cards' ); ?>">
        </div>
        <div class="ywgc-recipient-name">
            <label for="ywgc-recipient-name"><?php echo apply_filters('ywgc_recipient_name_label',__( "Recipient's name: ", 'yith-woocommerce-gift-cards' )); ?></label>
            <input type="text" name="ywgc-recipient-name" id="ywgc-recipient-name" value="<?php echo apply_filters('ywgc_recipient_name_value','') ?>"
                   placeholder="<?php _e( "ENTER RECIPIENT NAME", 'yith-woocommerce-gift-cards' ); ?>">
        </div>
    <?php endif; ?>

    <?php if ( 'yes' == get_option('ywgc_allow_printed_message', 'no' ) ) : ?>
    <div class="ywgc-message">
        <label for="ywgc-edit-message"><?php echo apply_filters('ywgc_edit_message_label',__( "Message: ", 'yith-woocommerce-gift-cards' )); ?></label>
        <textarea id="ywgc-edit-message" name="ywgc-edit-message" rows="5" placeholder="<?php echo  get_option( 'ywgc_sender_message_placeholder' , __( 'ENTER A MESSAGE FOR THE RECIPIENT', 'yith-woocommerce-gift-cards' ) ); ?>" ></textarea>
    </div>
    <?php endif; ?>

</div>