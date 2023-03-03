<?php

/**
 * The template for displaying cookie controller view in wp-admin
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
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR & CCPA', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>


	<?php if ( isset( $options['notices'] ) ) : ?>
		<?php foreach ( $options['notices'] as $notice ) : ?>

            <div class="ct-ultimate-gdpr notice-info notice">
				<?php echo $notice; ?>
            </div>

		<?php endforeach; endif; ?>

	<?php if ( isset( $options['warnings'] ) ) : ?>
		<?php foreach ( $options['warnings'] as $notice ) : ?>

            <div class="ct-ultimate-gdpr warning-info warning">
                <h3><?php echo $notice; ?></h3>
            </div>

		<?php endforeach; endif; ?>

	<?php if ( isset( $options['errors'] ) ) : ?>
		<?php foreach ( $options['errors'] as $notice ) : ?>

            <div class="ct-ultimate-gdpr error-info error">
                <h3><?php echo $notice; ?></h3>
            </div>

		<?php endforeach; endif; ?>


    <h2 class="nav-tab-wrapper">
        <a href="#" id="cookie-popup" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Cookie popup', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" id="cookie-preference" class="nav-tab">
			<?php echo esc_html__( 'Preferences', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" id="cookie-advanced" class="nav-tab">
			<?php echo esc_html__( 'Advanced settings', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>


    <form method="post" action="options.php" enctype="multipart/form-data">

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Cookie::ID );
		ct_ultimate_gdpr_do_settings_sections( CT_Ultimate_GDPR_Controller_Cookie::ID );

		?>

        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
            <p><?php echo esc_html__("Press 'Save changes' before downloading logs if you changed any options","ct-ultimate-gdpr"); ?></p>
		    <?php
		    submit_button();
		    ?>
        </div>

	</form>
</div>

<form method="post">
    <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-log"
           value="<?php echo esc_html__( 'Download consents log as CSV file', 'ct-ultimate-gdpr' ); ?>"/>
    <input type="hidden" class="button button-secondary" name="ct-ultimate-gdpr-action" value="download-csv"/>
</form>
<div style="margin-top:10px;">
    <form method="post">
        <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-log" 
            value="<?php echo esc_html__( 'Download consents log as text file', 'ct-ultimate-gdpr' ); ?>"/>
        <input type="hidden" class="button button-secondary" name="ct-ultimate-gdpr-action" value="download-txt"/>
    </form>
</div>
<div style="margin-top:10px;">
    <form method="post">
        <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-delete-log"
        value="<?php echo esc_html__( 'Delete consents log', 'ct-ultimate-gdpr' ); ?>"/>
        <p><?php echo esc_html__( 'NOTE: Once it is deleted you cannot retrieved the consent logs again.', 'ct-ultimate-gdpr'  ); ?></p>
    </form>
</div>

<div class="submit">
    <form method="post">
        <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-check-cookies"
               value="<?php echo esc_html__( 'Scan for cookies', 'ct-ultimate-gdpr' ); ?>"/>

        <?php if( get_option( 'ct_gdpr_check_last_cookies_scan' ) ) { ?>
            <p style = "display:inline-block;margin-left:15px;">
                <?php echo esc_html__( 'Last scan: ', 'ct-ultimate-gdpr'  ); ?>
                <?php echo get_option( 'ct_gdpr_check_last_cookies_scan' ); ?>
            </p>
        <?php }else{
            ?>
            <p><?php echo esc_html__( 'The website has not been scanned yet.', 'ct-ultimate-gdpr'  ); ?></p>
            <?php
        } ?>

    </form>
    <p>
        <?php echo esc_html__( 'Your website should be publicly accessible so that the Cookie Detector can work properly.', 'ct-ultimate-gdpr' ); ?>
    </p>
    <?php
        $url = admin_url( "admin.php?page=ct-ultimate-gdpr" );
        echo "<p>";
        printf(
            wp_kses_post(__( "Make sure that you're using, <a href=%s>valid purchase code</a> to use the cookie scanner.", 'ct-ultimate-gdpr' ) ),
            $url
        );
        echo "</p>";
    ?>
</div>

<?php
    ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template('admin/includes/cookie-scanner', true));
?>