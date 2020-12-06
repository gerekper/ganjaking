<?php
/**
 * HTML struture of License Manage page.
 * @since 1.0.0
 * @version 2.2.2
 * @return HTML
 */
$loginpress_pro_license = '';
if ( 'valid' == LoginPress_Pro::get_registered_license_status() && null != get_option( 'loginpress_pro_license_key' ) ) {
  $loginpress_pro_license = LoginPress_Pro::mask_license( get_option( 'loginpress_pro_license_key' ) );
} ?>

<div class="wrap">
  <h2><?php _e('Activate your License'); ?></h2>
  <form method="post" action="options.php">

    <?php settings_fields('loginpress_pro_license'); ?>

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e('License Key'); ?>
          </th>
          <td>
            <input id="loginpress_pro_license_key" placeholder="<?php esc_html_e( 'Enter your license key', 'loginpress-pro' ); ?>" name="loginpress_pro_license_key" type="text" class="regular-text" value="<?php echo esc_html( $loginpress_pro_license ); ?>" />
            <label class="description" for="loginpress_pro_license_key"><?php _e( 'Validating license key is mandatory to use automatic updates and plugin support.', 'loginpress-pro' ); ?></label>
          </td>
        </tr>

          <tr valign="top">
            <th scope="row" valign="top">
            </th>
            <td>
              <?php if( LoginPress_Pro::is_registered() ) { ?>

                <?php wp_nonce_field( 'loginpress_pro_license_nonce', 'loginpress_pro_license_nonce' ); ?>
                <input type="submit" class="button-secondary" name="loginpress_pro_license_deactivate" value="<?php _e( 'Deactivate License', 'loginpress-pro' ); ?>"/>
              <?php } else {
                wp_nonce_field( 'loginpress_pro_license_nonce', 'loginpress_pro_license_nonce' ); ?>
                <input type="submit" class="button-secondary" name="loginpress_pro_license_activate" value="<?php _e( 'Activate License', 'loginpress-pro' ); ?>"/>
            </td>
          </tr>
        <?php } ?>
        <tr><th></th><td>
            <?php

            if ( LoginPress_Pro::is_registered() ) {
              $expiration_date = LoginPress_Pro::get_expiration_date();

              if ( 'lifetime' == $expiration_date ) {
                $license_desc = esc_html__( 'You have a lifetime licenses, it will never expire.', 'loginpress-pro' );
              }
              else {
                $license_desc = sprintf(
                  esc_html__( 'Your (%2$s) license key is valid until %s.', 'loginpress-pro' ),
                  '<strong>' . date_i18n( get_option( 'date_format' ), strtotime( $expiration_date, current_time( 'timestamp' ) ) ) . '</strong>', LoginPress_Pro::get_license_type()
                );
              }

              if ( 'lifetime' != $expiration_date ) {
              $license_tooltip_desc  = sprintf(
                  esc_html__( 'The license will automatically renew, if you have an active subscription to the LoginPress Pro - at %s', 'loginpress-pro' ),
                  '<a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/">WPBrigade.com</a>'
                );
              } else {
                $license_tooltip_desc = '';
              }

              if ( LoginPress_Pro::has_license_expired() ) {
                $license_desc = sprintf(
                  esc_html__( 'Your license key expired on %s. Please input a valid non-expired license key. If you think, that this license has not yet expired (was renewed already), then please save the settings, so that the license will verify again and get the up-to-date expiration date.', 'loginpress-pro' ),
                  '<strong>' . date_i18n( get_option( 'date_format' ), strtotime( $expiration_date, current_time( 'timestamp' ) ) ) . '</strong>'
                );
                $license_tooltip_title = '';
                $license_tooltip_desc  = '';

              }

              echo $license_desc .'<br /><i>' . $license_tooltip_desc .'</i>';
            }else{

              echo LoginPress_Pro::get_registered_license_status();
            }
            ?>
          </td></tr>
      </tbody>
    </table>
  </form>
</div>
