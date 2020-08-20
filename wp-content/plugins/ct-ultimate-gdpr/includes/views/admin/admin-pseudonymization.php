<?php

/**
 * The template for displaying pseudonymization controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<?php if ( isset( $options['notices'] ) ) : ?>
	<?php foreach ( $options['notices'] as $notice ) : ?>

        <div class="ct-ultimate-gdpr notice-info notice">
			<?php echo esc_html( $notice ); ?>
        </div>

	<?php endforeach; endif; ?>

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

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Pseudonymization::ID );
		do_settings_sections( CT_Ultimate_GDPR_Controller_Pseudonymization::ID );

		?>

        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
            <p><?php echo esc_html__( "Press 'Save changes' before pressing encryption buttons if you changed any options", 'ct-ultimate-gdpr' ); ?></p>
		    <?php
		    submit_button();
		    ?>
        </div>

    </form>
</div>

<form method="post">
    <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-pseudo-encrypt-all"
           value="<?php echo esc_html__( 'Encrypt selected', 'ct-ultimate-gdpr' ); ?>"/>
    <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-pseudo-decrypt-all"
           value="<?php echo esc_html__( 'Decrypt selected', 'ct-ultimate-gdpr' ); ?>"/>
</form>


<script type="text/javascript">
    jQuery('.ct-ultimate-gdpr-field').on('change', function () {
        jQuery('input[name^=ct-ultimate-gdpr-pseudo-]').attr("disabled", "disabled");
    });
</script>