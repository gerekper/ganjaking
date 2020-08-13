<?php

/**
 * The template for displaying age controller view in wp-admin
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
        <a href="#" id="age-popup" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Age verification popup', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" id="age-preference" class="nav-tab">
			<?php echo esc_html__( 'Preferences', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>


    <form method="post" action="options.php" enctype="multipart/form-data">

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Age::ID );
		ct_ultimate_gdpr_do_settings_sections( CT_Ultimate_GDPR_Controller_Age::ID );

		?>

        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
		    <?php
		    submit_button();
		    ?>
        </div>

	</form>
</div>