<div class="pafe-license">
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
        <?php
        $disable_ssl_verify_license = esc_attr( get_option( 'piotnet_addons_for_elementor_pro_disable_ssl_verify_license' ) );
        $beta_version = esc_attr( get_option( 'piotnet_addons_for_elementor_pro_beta_version' ) );
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
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>