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

?>
    <h3 class="ywgc_select_amount_title"><?php echo get_option( 'ywgc_select_amount_title' , esc_html__( "Set an amount", 'yith-woocommerce-gift-cards') ); ?></h3>


    <?php if ( $amounts ) : ?>


    <?php do_action( 'yith_gift_card_amount_selection_first_option', $product ); ?>
    <?php foreach ( $amounts as $value => $item ) : ?>
        <button class="ywgc-predefined-amount-button ywgc-amount-buttons" value="<?php echo $item['amount']; ?>"
                data-price="<?php echo $item['price']; ?>"
                data-wc-price="<?php echo strip_tags(wc_price($item['price'])); ?>">
            <?php echo apply_filters( 'yith_gift_card_select_amount_values' , $item['title'], $item ); ?>
        </button>

        <input type="hidden" class="ywgc-predefined-amount-button ywgc-amount-buttons" value="<?php echo $item['amount']; ?>"
               data-price="<?php echo $item['price']; ?>"
               data-wc-price="<?php echo strip_tags(wc_price($item['price'])); ?>" >

    <?php endforeach; ?>
<?php
endif;

do_action( 'yith_gift_card_amount_selection_last_option', $product );
do_action( 'yith_gift_cards_template_after_amounts', $product );
