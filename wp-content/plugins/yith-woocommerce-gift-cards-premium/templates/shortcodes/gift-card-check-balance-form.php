<?php

if ( isset( $_POST["ywgc-check-code"] ) ){

    $code =  $_POST["ywgc-check-code"];

    $args = array(
        'gift_card_number' => $code,
    );

    $gift_card = new YITH_YWGC_Gift_Card( $args );

    if ( is_object( $gift_card ) && $gift_card->ID != 0 ){
        echo '<div style="font-weight: bolder">' . esc_html__( "Gift card balance: ", 'yith-woocommerce-gift-cards' ) . wc_price( $gift_card->get_balance() ) . '</div>';
        echo '<br>';
    }
    else{
        echo '<div style="font-weight: bolder">' . esc_html__( "The code added is not associated to any existing gift card.", 'yith-woocommerce-gift-cards' ) . '</div>';
        echo '<br>';
    }
}

?>

<form method="post" class="form-check-gift-card-balance" name="form-check-gift-card-balance">
        <label for="ywgc-check-code"><?php _e ( "Gift Card code: ", 'yith-woocommerce-gift-cards' ); ?></label>
        <input type="text" name="ywgc-check-code" id="ywgc-check-code" value="" placeholder="<?php _e ( "Enter code", 'yith-woocommerce-gift-cards' ); ?>">
        <button type="submit"><?php _e ( "Submit", 'yith-woocommerce-gift-cards' ); ?></button>
</form>

