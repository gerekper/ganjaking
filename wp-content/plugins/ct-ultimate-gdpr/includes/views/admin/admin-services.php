<?php

/**
 * The template for displaying services controller view in wp-admin
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


	<form method="post" action="options.php">

        <?php settings_fields( 'ct-ultimate-gdpr-services' ); ?>

        <div id="accordion">

		<?php

		// This prints out all hidden setting fields

		ct_ultimate_gdpr_do_settings_sections( 'ct-ultimate-gdpr-services' );
		submit_button();

		?>

        </div>
	</form>
</div>

<form method="post">
    <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-log"
           value="<?php echo esc_html__( 'Download all consents log', 'ct-ultimate-gdpr' ); ?>"/>
</form>
