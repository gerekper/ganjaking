<div class="pafe-license">
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
        <?php
        $disable_ssl_verify_license = esc_attr( get_option( 'piotnet_addons_for_elementor_pro_disable_ssl_verify_license' ) );
        $beta_version = esc_attr( get_option( 'piotnet_addons_for_elementor_pro_beta_version' ) );
        $hide_wooCommerce_checkout = esc_attr( get_option( 'pafe_hide_wooCommerce_checkout' ) );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Disable verify SSL when validate License','pafe'); ?></th>
                <td><input type="checkbox" name="piotnet_addons_for_elementor_pro_disable_ssl_verify_license" value="true" <?php if ($disable_ssl_verify_license == 'true') {echo 'checked';}; ?>/>Only use it when you have trouble with validating license (SSL certificate problem)</td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Subscribe to Beta updates','pafe'); ?></th>
                <td><input type="checkbox" name="piotnet_addons_for_elementor_pro_beta_version" value="yes" <?php if ($beta_version == 'yes') {echo 'checked';}; ?>/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Hide meta box','pafe'); ?></th>
                <td>
                    <input type="checkbox" id="hide-woocommerce-checkout-metabox" name="pafe_hide_wooCommerce_checkout" value="yes" <?php if ($hide_wooCommerce_checkout == 'yes') {echo 'checked';}; ?>/>
                    <label for="hide-woocommerce-checkout-metabox">Hide meta box Piotnet Addons WooCommerce Checkout in all post type</label>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>