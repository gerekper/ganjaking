<?php
/**
 * Variable product add to cart
 *
 * @author  Yithemes
 * @package YITH WooCommerce Gift Cards
 *
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

do_action ( 'yith_gift_cards_template_before_add_to_cart_form' );

$icon = get_option( 'ywgc_gift_this_product_icon', 'no' );

?>

<div class="gift-this-product-main-container" style="border: 1px solid black; padding: 2em 2em 2em 2em;">

    <div class="gift-this-product-message">

        <?php if ( $icon == 'yes' ) {?>
            <img src="<?php echo YITH_YWGC_ASSETS_IMAGES_URL . 'card_giftcard_icon.svg' ?>" class="material-icons ywgc_woocommerce_message_icon"  style="margin-right: 6px;width: 50px;margin-top: -15px;">
        <?php } ?>

        <h2 class="gift-this-product-title" style="display: inline; margin-left: 12px;"><?php echo esc_html__( get_option('ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ) ), 'yith-woocommerce-gift-cards' ); ?></h2>
        <p class="gift-this-product-title-message"><?php echo esc_html__( get_option('ywgc_gift_this_product_label_description',  esc_html__( 'Do you feel this product is perfect for a friend or a loved one? You can buy a gift card for this item!', 'yith-woocommerce-gift-cards' ) ), 'yith-woocommerce-gift-cards' ); ?></p><?php

        //Gift this product as button
        if ( get_option('ywgc_gift_this_product_button_style', 'ywgc_gift_this_product_button_style_text') == 'ywgc_gift_this_product_button_style_button'){
            if ( $product->get_type() == 'variable'){
                ?><button id="give-as-present" class="btn btn-ghost give-as-present variable-gift-this-product"><?php echo get_option('ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ) ); ?></button><?php
            }
            else{
                ?><button id="give-as-present" class="btn btn-ghost give-as-present"><?php echo get_option('ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ) ); ?></button><?php
            }
        }

        //Gift this product as text link
        else{
            if ( $product->get_type() == 'variable'){
                ?><a id="give-as-present" class="btn btn-ghost give-as-present variable-gift-this-product"><?php echo get_option( 'ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' )); ?></a><span class="dashicons dashicons-arrow-down-alt2"></span>
                <?php
            }
            else{
                ?><a id="give-as-present" class="btn btn-ghost give-as-present"><?php echo get_option('ywgc_gift_this_product_label',  esc_html__( 'Gift this product', 'yith-woocommerce-gift-cards' ) ); ?><span class="dashicons dashicons-arrow-down-alt2"></span></a><?php
            }
        } ?>

    </div>

    <div class="yith-ywgc-gift-this-product-form-container" style="display: none">
        <?php wc_get_template( 'single-product/add-to-cart/gift-this-product-form.php', '', '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) ); ?>
    </div>

</div>





