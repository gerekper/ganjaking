<?php
/**
 * @var bool|int $user_editing
 * @var bool|int $register_id
 */
$stores       = yith_pos_get_allowed_store_registers_by_user();
$stores_count = count( $stores );

if ( !$stores ) {
    wp_die( __( 'Sorry, you are not allowed to see this content', 'yith-point-of-sale-for-woocommerce' ) );
}
$stores_json = wp_json_encode( $stores );
$stores_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $stores_json ) : _wp_specialchars( $stores_json, ENT_QUOTES, 'UTF-8', true );


$logo = get_option( 'yith_pos_login_logo' );
?>

<div id="yith-pos-store-register-form" class="yith-pos-form" data-stores="<?php echo $stores_attr ?>">
    <div class="yith-pos-form-wrap">
        <h1>
            <?php if ( $logo ): ?><img  src="<?php echo $logo ?>"/><?php endif ?>
            <?php _e( 'Choose Store and Register', 'yith-point-of-sale-for-woocommerce' ) ?>
        </h1>
        <form method="post">
            <?php if ( $register_id && $user_editing ):
                yith_pos_register_logout();
                ?>
                <div class="yith-pos-form-row yith-pos-change-register-or-take-over">
                    <?php
                    $register_name  = "<strong>" . get_the_title( $register_id ) . "</strong>";
                    $take_over_link = add_query_arg( array( 'register' => $register_id, 'yith-pos-take-over-nonce' => wp_create_nonce( 'yith-pos-take-over' ) ) );
                    $take_over_text = sprintf( __( 'Take over %s', 'yith-point-of-sale-for-woocommerce' ), $register_name );
                    echo implode( '<br />', array(
                                              sprintf( __( '%s is currently in use by %s.', 'yith-point-of-sale-for-woocommerce' ),
                                                       $register_name,
                                                       "<strong>" . get_userdata( $user_editing )->display_name . "</strong>"
                                              ),
                                              sprintf( __( 'Choose another Register or %s', 'yith-point-of-sale-for-woocommerce' ),
                                                       "<a href='{$take_over_link}'>{$take_over_text}</a>" )
                                          )
                    );

                    ?>
                </div>
            <?php endif; ?>
            <div class="yith-pos-form-row with-select">
                <select id="yith-pos-store-register-form__store" name="store">
                    <?php if ( $stores_count > 1 ): ?>
                        <option value=""  class="placeholder"><?php esc_html_e( 'Choose a Store', 'yith-point-of-sale-for-woocommerce' ); ?></option>
                    <?php endif; ?>
                    <?php foreach ( $stores as $store ): ?>
                        <option value="<?php echo $store[ 'id' ] ?>"><?php echo $store[ 'name' ] ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="float-label" for="yith-pos-store-register-form__store"><?php esc_html_e( 'Store', 'yith-point-of-sale-for-woocommerce' ); ?></label>
            </div>
            <div class="yith-pos-form-row with-select" >
                <select id="yith-pos-store-register-form__register" name="register">
                    <option value="" class="placeholder"><?php esc_html_e( 'Choose a Register', 'yith-point-of-sale-for-woocommerce' ); ?></option>
                </select>
                <label class="float-label" for="yith-pos-store-register-form__register"><?php esc_html_e( 'Register', 'yith-point-of-sale-for-woocommerce' ); ?></label>
            </div>

            <div class="yith-pos-form-row">
                <button type="submit" class="submit"><?php _e( 'Open Register', 'yith-point-of-sale-for-woocommerce' ) ?></button>
            </div>
            <?php wp_nonce_field( 'yith-pos-register-login', 'yith-pos-register-login-nonce' ); ?>

        </form>
    </div>
</div>
