<?php
/**
 * Form preview on product page
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div class="ywgc-main-form-preview-container">

        <div class="ywgc-form-preview-title-container">
            <span class="ywgc-form-preview-title"><?php echo $product->get_title(); ?></span>
            <div class="ywgc-form-preview-amount"></div>

        </div>

        <hr style="margin-top: 20px;">

        <div class="ywgc-form-preview-from-to-container">

            <?php if ( 'yes' == get_option('ywgc_ask_sender_name' , 'yes' ) ) : ?>
                <span class="ywgc-form-preview-from"><?php echo apply_filters( "ywgc_form_preview_from_text",  esc_html__( 'From: ', 'yith-woocommerce-gift-cards' ) ); ?></span>
                <span class="ywgc-form-preview-from-content"></span>
            <?php endif; ?>
            <br>
            <span class="ywgc-form-preview-to"><?php echo apply_filters( "ywgc_form_preview_to_text", esc_html__( 'To: ', 'yith-woocommerce-gift-cards' ) ); ?></span>
            <span class="ywgc-form-preview-to-content"></span>
        </div>

        <div class="ywgc-form-preview-separator"></div>

        <div class="ywgc-form-preview-message-container">
            <p class="ywgc-form-preview-message"></p>
        </div>


</div>
