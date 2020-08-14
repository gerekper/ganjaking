<?php
/**
 * @var int                 $store_id
 * @var YITH_POS_Register[] $registers
 */
global $register, $register_id;
?>


<div id="yith-pos-store-metabox-registers-list__wrapper">
    <h3 class="title"><?php _e( 'Registers of this store', 'yith-point-of-sale-for-woocommerce' ) ?></h3>
    <div id="yith-pos-store-metabox-registers-list">
        <?php foreach ( $registers as $register ) {
            $register_id = $register->get_id();
            yith_pos_get_view( 'metabox/store-registers-list-single.php' );
        } ?>
    </div>

    <div id="yith-pos-store-metabox-registers-list__create-form-wrapper">
        <input type="hidden" id="yith-pos-store-metabox-create-register-nonce" value="<?php echo wp_create_nonce( 'yith-pos-create-register' ) ?>">
        <span id="yith-pos-store-metabox-registers-list__new" class="yith-add-button" data-template="<?php
        $register_id = 'NEW_REGISTER_ID';
        $register    = new YITH_POS_Register( 0 );
        $register->set_store_id( $store_id );

        $payment_methods    = WC()->payment_gateways()->payment_gateways();
        $payment_method_ids = wp_list_pluck( $payment_methods, 'id' );

        $register->set_payment_methods( $payment_method_ids );

        ob_start();
        yith_pos_get_view( 'metabox/store-registers-list-single.php' );
        echo esc_attr( ob_get_clean() );
        ?>"
              data-text="<?php _e( 'Create new Register', 'yith-point-of-sale-for-woocommerce' ) ?>"
              data-close-text="<?php _e( 'Close new Register creation', 'yith-point-of-sale-for-woocommerce' ) ?>"
        ><?php _e( 'Create new register', 'yith-point-of-sale-for-woocommerce' ) ?></span>
    </div>
</div>
