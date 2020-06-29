<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
$description = get_option( 'ywf_user_privacy_description' );
$user_meta = get_user_meta( $user_id, '_ywf_agree_to_send_email', true );
?>
<fieldset>
    <legend><?php esc_html_e( __( 'Account Funds Policy', 'yith-woocommerce-account-funds' ) ); ?></legend>
    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-wide">
        <label for="ywf_agree_send_email">
            <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
                   name="ywf_agree_send_email" id="ywf_agree_send_email" <?php checked( true, $user_meta );?>/>
            <span class="woocommerce-terms-and-conditions-checkbox-text"><?php esc_html_e( $description ); ?></span>&nbsp;
        </label>
    </p>
</fieldset>
<div class="clear"></div>
