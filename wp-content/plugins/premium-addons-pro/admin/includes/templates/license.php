<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PremiumAddonsPro\Admin\Includes\Admin_Helper;

// Premium Addons Classes
use PremiumAddons\Includes\Helper_Functions;

$theme = wp_get_theme();

$theme_name = $theme->parent() ? $theme->parent()->get( 'Name' ) : $theme->get( 'Name' );

$theme_name = sanitize_key( $theme_name );

$account_link = sprintf( 'https://my.leap13.com/?utm_source=license-page&utm_medium=wp-dash&utm_campaign=your-account&utm_term=%s', $theme_name );
$get_license  = sprintf( 'https://premiumaddons.com/pro/?utm_source=license-page&utm_medium=wp-dash&utm_campaign=get-pro&utm_term=%s', $theme_name );

$status = Admin_Helper::get_license_status();

?>

<div class="pa-section-content">
	<div class="row">
		<div class="col-full">
			<form class="pa-license-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php settings_fields( 'papro_license' ); ?>
				<div class="pa-section-info-wrap">
					<div class="pa-section-info">
						<b><?php echo __( 'Enter your license key here, to activate Premium Addons Pro, and enable feature updates, Premium Templates, white labeling options and premium support.', 'premium-addons-pro' ); ?></b>

						<ol>
							<li><span><?php echo __( 'Log in to ', 'premium-addons-pro' ); ?><a href="<?php echo esc_url( $account_link ); ?>" target="_blank"><?php echo __( 'your account', 'premium-addons-pro' ); ?></a><?php echo __( ' to get your license key', 'premium-addons-pro' ); ?></span></li>
							<li><span><?php echo __( 'If you don\'t have a license key yet, ', 'premium-addons-pro' ); ?><a href="<?php echo esc_url( $get_license ); ?>" target="_blank"><?php echo __( 'get Premium Addons Pro now.', 'premium-addons-pro' ); ?></a></span></li>
							<li><span><?php echo __( 'Copy the license key from your account and paste it below.', 'premium-addons-pro' ); ?></span></li>
							<li><span><?php echo __( 'Click on Activate to activate the license.', 'premium-addons-pro' ); ?></span></li>
						</ol>
						<label for="papro-license-key"><?php _e( 'License Key' ); ?></label>
						<input id="papro-license-key" <?php echo ( $status !== false && $status == 'valid' ) ? 'disabled' : ''; ?> name="papro_license_key" placeholder="<?php echo __( 'Please enter your license key here', 'premium-addons-pro' ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( Admin_Helper::get_encrypted_key() ); ?>" />
						<?php
							wp_nonce_field( 'papro_nonce', 'papro_nonce' );
						if ( $status !== false && $status == 'valid' ) {
							?>
								<input type="hidden" name="action" value="papro_license_deactivate" />
								<?php submit_button( __( 'Deactivate', 'premium-addons-pro' ), 'primary', 'submit', false ); ?>
								<span style="color:green;"><?php echo __( 'Active', 'premium-addons-pro' ); ?></span>
							<?php } else { ?>
								<input type="hidden" name="action" value="papro_license_activate" />
								<?php submit_button( __( 'Activate', 'premium-addons-pro' ), 'primary', 'submit', false ); ?>
								<span style="color:red;"><?php echo __( 'License not valid', 'premium-addons-pro' ); ?></span>
							<?php
							}
							?>

					</div>
				</div>
			</form>
		</div>
	</div>
</div> <!-- End Section Content -->
