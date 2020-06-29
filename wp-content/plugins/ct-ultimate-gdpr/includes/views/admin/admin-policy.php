<?php

/**
 * The template for displaying policy controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<div class="ct-ultimate-gdpr-wrap">

    <div class="ct-ultimate-gdpr-branding">
        <div class="ct-ultimate-gdpr-img">
            <img src="<?php echo ct_ultimate_gdpr_url() . '/assets/css/images/branding.jpg' ?>">
        </div>
        <div class="text">
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>


    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-terms' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Terms and Conditions', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Privacy Policy', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>

	<form method="post" action="options.php">

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Policy::ID );
		do_settings_sections( CT_Ultimate_GDPR_Controller_Policy::ID );

		?>

        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
            <p><?php echo esc_html__('Press "Save changes" before downloading logs if you changed any options','ct-ultimate-gdpr'); ?></p>
			<?php
			submit_button();
			?>
        </div>

	</form>
</div>

<form method="post">
    <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-log"
           value="<?php echo esc_html__( 'Download consents log', 'ct-ultimate-gdpr' ); ?>"/>
</form>
